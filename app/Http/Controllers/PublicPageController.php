<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Agenda;
use App\Models\DataAduan;
use App\Models\Dinas;
use App\Models\DokumenNotulen;
use App\Models\Galeri;
use App\Models\Kecamatan;
use App\Models\Kunjungan;
use App\Models\Logbook;
use App\Models\Pegawai;
use App\Models\QRCode;
use App\Models\UlangTahun;
use App\Services\AppSetting;
use App\Services\NewsApiService;
use App\Services\SirapiMailer;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PublicPageController extends Controller
{
    private const PUBLIC_TIMEZONE = 'Asia/Jakarta';
    public const KUNJUNGAN_OTP_SESSION_KEY = 'kunjungan_otp_verification';
    public const KUNJUNGAN_OTP_RESEND_SECONDS = 60;
    public const KUNJUNGAN_OTP_TTL_MINUTES = 10;

    public function index(NewsApiService $newsService)
    {
        $today = Carbon::today(self::PUBLIC_TIMEZONE);
        // 1. Ambil kandidat agenda aktif (hari ini dan masa depan)
        $rawAgendas = $this->queryOrDefault(fn () => Agenda::with('dinas')
            ->whereDate('tanggal', '>=', $today)
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->take(20)
            ->get(), collect());

        // Fallback jika belum ada agenda hari ini atau mendatang sama sekali, ambil riwayat terbaru
        if ($rawAgendas->isEmpty()) {
            $rawAgendas = $this->queryOrDefault(fn () => Agenda::with('dinas')
                ->orderBy('tanggal', 'desc')
                ->orderBy('waktu', 'desc')
                ->take(6)
                ->get(), collect());
        }

        // 2. Terapkan Smart Live Priority Sorting: Berlangsung > Mendatang Hari Ini > Mendatang Esok > Selesai
        $agendaBeranda = Agenda::sortSmartLivePriority($rawAgendas)->take(3);
        $totalAgendaHariIni = $this->queryOrDefault(fn () => Agenda::whereDate('tanggal', $today)->count(), 0);

        $agendaHariIni = $agendaBeranda;
        $agendaTerbaru = $agendaBeranda;

        // 3. Label & Deskripsi Cerdas Beranda
        $hasBerlangsung = $agendaBeranda->contains(fn ($a) => $a->isBerlangsung());
        $hasMendatang = $agendaBeranda->contains(fn ($a) => $a->isMendatang());

        $agendaBerandaLabel = $hasBerlangsung ? 'Agenda Berlangsung & Terdekat' : 'Agenda Terdekat';
        if ($hasBerlangsung) {
            $agendaBerandaDescription = 'Ada kegiatan rapat yang sedang berlangsung saat ini';
        } elseif ($hasMendatang) {
            $agendaBerandaDescription = 'Jadwal kegiatan terdekat yang akan segera berlangsung';
        } elseif ($totalAgendaHariIni > 0) {
            $agendaBerandaDescription = $today->translatedFormat('l, d F Y') . ' • Seluruh agenda hari ini telah selesai';
        } else {
            $agendaBerandaDescription = 'Menampilkan agenda terkini';
        }
        $beritaTerbaru = $newsService->getLatest(3);
        $galeri = $this->queryOrDefault(fn () => $this->dokumentasiAgendaGaleri()->take(4), collect());
        $totalGaleri = $this->queryOrDefault(fn () => $this->dokumentasiAgendaGaleri()->count(), 0);
        $ulangTahun = $this->queryOrDefault(fn () => UlangTahun::tampilkanUlangTahunPegawai(), collect());
        $ulangTahunHariIni = $ulangTahun->first(fn ($item) => $item->tanggal?->format('m-d') === $today->format('m-d'));
        $masukan = $this->queryOrDefault(fn () => DataAduan::with('dinas')->latest('id_dataaduan')->take(5)->get(), collect());
        $youtubeEmbedUrl = $this->defaultYoutubeEmbedUrl();

        // Data Dinas & Kecamatan dari Database untuk Peta GIS
        $dinasListMap = $this->queryOrDefault(fn () => Dinas::whereNotNull('gps_lat')
            ->whereNotNull('gps_long')
            ->get(['id_dinas', 'kode_dinas', 'nama_dinas', 'alamat', 'gps_lat', 'gps_long']), collect());

        $kecamatanListMap = $this->queryOrDefault(fn () => Kecamatan::whereNotNull('gps_lat')
            ->whereNotNull('gps_long')
            ->get(['id_kecamatan', 'kode_kecamatan', 'nama_kecamatan', 'alamat_kantor', 'gps_lat', 'gps_long']), collect());

        // Seluruh Agenda Rapat untuk Modal Peta Lokasi
        $allMapAgendas = $this->queryOrDefault(fn () => Agenda::with('dinas')
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->get()
            ->map(function ($ag) {
                return [
                    'id_agenda' => $ag->id_agenda,
                    'nama_agenda' => $ag->nama_agenda,
                    'tanggal' => $ag->tanggal ? Carbon::parse($ag->tanggal)->translatedFormat('d M Y') : '-',
                    'waktu' => $ag->waktu ? Carbon::parse($ag->waktu)->format('H:i') . ' WIB' : '-',
                    'lokasi' => $ag->lokasi ?? '',
                    'id_dinas' => $ag->id_dinas,
                    'id_kecamatan' => $ag->id_kecamatan,
                    'kategori' => strtolower((string)($ag->kategori_surat ?? 'internal')),
                    'kategori_label' => match(strtolower((string)($ag->kategori_surat ?? ''))) {
                        'masuk' => 'Surat Masuk',
                        'keluar' => 'Surat Keluar',
                        default => 'Surat Internal',
                    },
                    'detailUrl' => route('publik.agenda.detail', $ag->id_agenda),
                ];
            }), collect());

        return view('publik.beranda.index', compact(
            'agendaHariIni',
            'agendaBeranda',
            'agendaBerandaLabel',
            'agendaBerandaDescription',
            'totalAgendaHariIni',
            'agendaTerbaru',
            'beritaTerbaru',
            'galeri',
            'totalGaleri',
            'ulangTahun',
            'ulangTahunHariIni',
            'masukan',
            'youtubeEmbedUrl',
            'dinasListMap',
            'kecamatanListMap',
            'allMapAgendas'
        ));
    }

    public function agenda(Request $request)
    {
        $today = Carbon::today(self::PUBLIC_TIMEZONE);
        $keyword = $request->query('keyword');
        $tab = $request->query('tab', 'semua');

        $agenda = $this->queryOrDefault(function () use ($today, $keyword, $tab) {
            return Agenda::with('dinas')
                ->when($keyword, function ($query, $keyword) {
                    $query->where(function ($search) use ($keyword) {
                        $search->where('nama_agenda', 'like', "%{$keyword}%")
                            ->orWhere('lokasi', 'like', "%{$keyword}%")
                            ->orWhere('ditugaskan', 'like', "%{$keyword}%");
                    });
                })
                ->when($tab === 'hari-ini', fn ($q) => $q->whereDate('tanggal', '=', $today))
                ->when($tab === 'mendatang', fn ($q) => $q->whereDate('tanggal', '>', $today))
                ->when($tab === 'selesai', fn ($q) => $q->whereDate('tanggal', '<', $today))
                ->orderByRaw('tanggal >= ? desc', [$today->toDateString()])
                ->orderByRaw('CASE WHEN tanggal >= ? THEN tanggal END ASC', [$today->toDateString()])
                ->orderByRaw('CASE WHEN tanggal < ? THEN tanggal END DESC', [$today->toDateString()])
                ->orderBy('waktu', 'asc')
                ->get();
        }, collect());

        return view('publik.agenda.index', compact('agenda', 'keyword', 'tab'));
    }

    public function agendaDetail(?int $id = null)
    {
        $today = Carbon::today(self::PUBLIC_TIMEZONE);
        $agenda = $this->queryOrDefault(fn () => $id
            ? Agenda::with('dinas')->findOrFail($id)
            : Agenda::with('dinas')->whereDate('tanggal', '>=', $today)->orderBy('tanggal')->orderBy('waktu')->first());
        $qrCode = $agenda
            ? $this->queryOrDefault(fn () => QRCode::where('id_agenda', $agenda->id_agenda)->first())
            : null;
        $dokumenList = $agenda
            ? $this->queryOrDefault(fn () => DokumenNotulen::where('id_agenda', $agenda->id_agenda)->get(), collect())
            : collect();

        $notulen = $dokumenList->firstWhere('jenis_dokumen', 'notulen');
        $dokumentasi = $dokumenList->where('jenis_dokumen', 'dokumentasi')->values();

        return view('publik.agenda.detail', compact('agenda', 'qrCode', 'notulen', 'dokumentasi'));
    }

    public function lampiranAgenda(int $id)
    {
        $agenda = Agenda::findOrFail($id);
        $lampiran = trim((string) $agenda->lampiran);

        abort_if($lampiran === '', 404);

        $path = $this->publicStoragePath($lampiran);

        if ($path === null) {
            return redirect()->away($lampiran);
        }

        abort_if(str_contains($path, '..') || ! Storage::disk('public')->exists($path), 404);

        $fileUrl = route('publik.agenda.lampiran.file', $agenda->id_agenda);
        $fileName = basename($path);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
        $isPdf = $extension === 'pdf';

        return view('publik.agenda.lampiran', compact('agenda', 'fileUrl', 'fileName', 'extension', 'isImage', 'isPdf'));
    }

    public function fileLampiranAgenda(int $id)
    {
        $agenda = Agenda::findOrFail($id);
        $lampiran = trim((string) $agenda->lampiran);

        abort_if($lampiran === '', 404);

        $path = $this->publicStoragePath($lampiran);

        if ($path === null) {
            return redirect()->away($lampiran);
        }

        abort_if(str_contains($path, '..') || ! Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, basename($path));
    }

    public function berita(Request $request, NewsApiService $newsService)
    {
        $keyword = $request->query('keyword');
        $sumber = $request->query('sumber', 'semua');

        $allNews = $newsService->getNews([
            'keyword' => $keyword,
            'sumber' => $sumber,
        ]);

        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 9;
        $currentPageItems = $allNews->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $berita = new LengthAwarePaginator(
            $currentPageItems,
            $allNews->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $availableSources = $newsService->getAvailableSources();

        return view('publik.berita.index', compact('berita', 'keyword', 'sumber', 'availableSources'));
    }

    public function beritaDetail(NewsApiService $newsService, ?string $id = null)
    {
        $berita = $newsService->findNews($id);

        $beritaTerkait = $newsService->getNews()
            ->filter(fn ($item) => (string) $item->id_berita !== (string) ($berita?->id_berita ?? ''))
            ->take(4);

        return view('publik.berita.detail', compact('berita', 'beritaTerkait'));
    }

    public function galeri()
    {
        $galeri = $this->queryOrDefault(fn () => $this->dokumentasiAgendaGaleri(), collect());

        return view('publik.galeri.index', compact('galeri'));
    }

    public function video(NewsApiService $newsService)
    {
        $youtubeEmbedUrl = $this->defaultYoutubeEmbedUrl();
        $youtubeChannelUrl = AppSetting::get('sirapi_youtube_channel_url', config('sirapi.youtube_channel_url', 'https://youtube.com/@kabupatenbogor?si=PAPn9ARUMrvRwMYy'));
        $today = Carbon::today(self::PUBLIC_TIMEZONE);
        $agendaTerbaru = $this->queryOrDefault(fn () => Agenda::whereDate('tanggal', '>=', $today)->orderBy('tanggal')->orderBy('waktu')->take(6)->get(), collect());
        $beritaTerbaru = $newsService->getLatest(6);

        return view('publik.video.index', compact('youtubeEmbedUrl', 'youtubeChannelUrl', 'agendaTerbaru', 'beritaTerbaru'));
    }

    public function ulangTahun()
    {
        $today = Carbon::today(self::PUBLIC_TIMEZONE);
        $ulangTahun = $this->queryOrDefault(fn () => UlangTahun::tampilkanUlangTahunPegawai(), collect());
        $ulangTahunHariIni = $ulangTahun->first(fn ($item) => $item->tanggal?->format('m-d') === $today->format('m-d'));

        return view('publik.ulang_tahun.index', compact('ulangTahun', 'ulangTahunHariIni'));
    }

    public function masukan()
    {
        $dinasList = $this->queryOrDefault(function () {
            $list = Dinas::orderBy('nama_dinas', 'asc')->get();
            if ($list->isEmpty()) {
                return collect([
                    (object) ['id_dinas' => 1, 'nama_dinas' => 'Dinas Komunikasi dan Informatika (Diskominfo)'],
                    (object) ['id_dinas' => 2, 'nama_dinas' => 'Dinas Pendidikan (Disdik)'],
                    (object) ['id_dinas' => 3, 'nama_dinas' => 'Dinas Kesehatan (Dinkes)'],
                    (object) ['id_dinas' => 4, 'nama_dinas' => 'Dinas Perhubungan (Dishub)'],
                    (object) ['id_dinas' => 5, 'nama_dinas' => 'Dinas Pekerjaan Umum dan Penataan Ruang (PUPR)'],
                    (object) ['id_dinas' => 6, 'nama_dinas' => 'Badan Perencanaan Pembangunan Daerah (Bappedalitbang)'],
                    (object) ['id_dinas' => 7, 'nama_dinas' => 'Badan Pengelolaan Pendapatan Daerah (Bappenda)'],
                    (object) ['id_dinas' => 8, 'nama_dinas' => 'Satuan Polisi Pamong Praja (Satpol PP)'],
                ]);
            }
            return $list;
        }, collect());

        $aduans = $this->queryOrDefault(fn () => DataAduan::with('dinas')->latest('id_dataaduan')->get(), collect());

        return view('publik.masukan.index', compact('aduans', 'dinasList'));
    }

    public function petaSitus()
    {
        return view('publik.sitemap.index');
    }

    public function riwayatAduan()
    {
        $masukan = $this->queryOrDefault(fn () => DataAduan::with('dinas')->latest('id_dataaduan')->paginate(10), collect());

        return view('publik.masukan.riwayat', compact('masukan'));
    }

    public function cuacaApi()
    {
        $cachedData = Cache::remember('sirapi_weather_data_v2', 1800, function () {
            try {
                $response = Http::withoutVerifying()->timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => -6.481,
                    'longitude' => 106.854,
                    'timezone' => 'Asia/Jakarta',
                    'forecast_days' => 3,
                    'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m,wind_direction_10m,precipitation,cloud_cover',
                    'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,wind_speed_10m_max',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $current = $data['current'] ?? [];
                    $daily = $data['daily'] ?? [];

                    return [
                        'success' => true,
                        'source' => 'Open-Meteo',
                        'attribution' => 'Weather data by Open-Meteo.com',
                        'location' => 'Cibinong, Kabupaten Bogor',
                        'updated_at' => isset($current['time']) ? Carbon::parse($current['time'])->translatedFormat('d M Y, H:i') : now()->translatedFormat('d M Y, H:i'),
                        'current' => [
                            'temperature' => $current['temperature_2m'] ?? 30,
                            'apparent_temperature' => $current['apparent_temperature'] ?? 32,
                            'humidity' => $current['relative_humidity_2m'] ?? 75,
                            'weather_code' => $current['weather_code'] ?? 1,
                            'condition' => $this->weatherCodeLabel($current['weather_code'] ?? 1),
                            'wind_speed' => $current['wind_speed_10m'] ?? 10,
                            'wind_direction' => $current['wind_direction_10m'] ?? 120,
                            'precipitation' => $current['precipitation'] ?? 0,
                            'cloud_cover' => $current['cloud_cover'] ?? 20,
                        ],
                        'daily' => collect($daily['time'] ?? [])->map(fn ($date, $index) => [
                            'date' => Carbon::parse($date)->translatedFormat('d M Y'),
                            'condition' => $this->weatherCodeLabel($daily['weather_code'][$index] ?? 1),
                            'weather_code' => $daily['weather_code'][$index] ?? 1,
                            'temperature_max' => $daily['temperature_2m_max'][$index] ?? 32,
                            'temperature_min' => $daily['temperature_2m_min'][$index] ?? 24,
                            'precipitation_sum' => $daily['precipitation_sum'][$index] ?? 0,
                            'wind_speed_max' => $daily['wind_speed_10m_max'][$index] ?? 15,
                        ])->values(),
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('PublicPageController: Weather API fetch warning: ' . $e->getMessage());
            }

            // Fallback estimation for Cibinong if server firewall blocks outbound connections
            return [
                'success' => true,
                'source' => 'BMKG Estimasi Cibinong',
                'attribution' => 'Prakiraan Wilayah Cibinong',
                'location' => 'Cibinong, Kabupaten Bogor',
                'updated_at' => now()->translatedFormat('d M Y, H:i'),
                'current' => [
                    'temperature' => 30,
                    'apparent_temperature' => 32,
                    'humidity' => 75,
                    'weather_code' => 1,
                    'condition' => 'Cerah Berawan',
                    'wind_speed' => 12,
                    'wind_direction' => 110,
                    'precipitation' => 0,
                    'cloud_cover' => 25,
                ],
                'daily' => [
                    [
                        'date' => now()->translatedFormat('d M Y'),
                        'condition' => 'Cerah Berawan',
                        'weather_code' => 1,
                        'temperature_max' => 32,
                        'temperature_min' => 24,
                        'precipitation_sum' => 0,
                        'wind_speed_max' => 14,
                    ],
                    [
                        'date' => now()->addDay()->translatedFormat('d M Y'),
                        'condition' => 'Berawan',
                        'weather_code' => 3,
                        'temperature_max' => 31,
                        'temperature_min' => 24,
                        'precipitation_sum' => 2,
                        'wind_speed_max' => 12,
                    ],
                    [
                        'date' => now()->addDays(2)->translatedFormat('d M Y'),
                        'condition' => 'Hujan Lokal',
                        'weather_code' => 80,
                        'temperature_max' => 29,
                        'temperature_min' => 23,
                        'precipitation_sum' => 5,
                        'wind_speed_max' => 15,
                    ],
                ],
            ];
        });

        return response()->json($cachedData);
    }

    public function presensiPegawai(Request $request)
    {
        $agenda = $this->agendaPresensi($request);

        return view('pegawai.presensi_wajah.index', compact('agenda'));
    }

    public function presensiTamu(Request $request)
    {
        $agendaId = $request->input('agenda_id');
        $agenda = $agendaId ? Agenda::find($agendaId) : $this->agendaPresensi($request);

        return view('publik.presensi.tamu', compact('agenda'));
    }

    public function formKunjungan()
    {
        $pegawaiList = $this->queryOrDefault(fn () => Pegawai::orderBy('nama_pegawai')->get(), collect());

        $defaultDinas = collect([
            (object) ['id_dinas' => 1, 'nama_dinas' => 'Dinas Komunikasi dan Informatika (Diskominfo)'],
            (object) ['id_dinas' => 2, 'nama_dinas' => 'Badan Perencanaan Pembangunan Daerah (Bappedalitbang)'],
            (object) ['id_dinas' => 3, 'nama_dinas' => 'Dinas Pendidikan'],
            (object) ['id_dinas' => 4, 'nama_dinas' => 'Dinas Kesehatan'],
            (object) ['id_dinas' => 5, 'nama_dinas' => 'Dinas Pekerjaan Umum dan Penataan Ruang (PUPR)'],
            (object) ['id_dinas' => 6, 'nama_dinas' => 'Dinas Perhubungan'],
            (object) ['id_dinas' => 7, 'nama_dinas' => 'Badan Pengelolaan Pendapatan Daerah (Bappenda)'],
            (object) ['id_dinas' => 8, 'nama_dinas' => 'Satuan Polisi Pamong Praja (Satpol PP)'],
        ]);

        $dinasList = $this->queryOrDefault(function () use ($defaultDinas) {
            $list = Dinas::orderBy('nama_dinas')->get();
            return $list->isNotEmpty() ? $list : $defaultDinas;
        }, $defaultDinas);

        $defaultKecamatan = collect([
            (object) ['id_kecamatan' => 1, 'nama_kecamatan' => 'Kecamatan Cibinong'],
            (object) ['id_kecamatan' => 2, 'nama_kecamatan' => 'Kecamatan Ciawi'],
            (object) ['id_kecamatan' => 3, 'nama_kecamatan' => 'Kecamatan Cisarua'],
            (object) ['id_kecamatan' => 4, 'nama_kecamatan' => 'Kecamatan Babakan Madang'],
            (object) ['id_kecamatan' => 5, 'nama_kecamatan' => 'Kecamatan Citeureup'],
            (object) ['id_kecamatan' => 6, 'nama_kecamatan' => 'Kecamatan Gunung Putri'],
        ]);

        $kecamatanList = $this->queryOrDefault(function () use ($defaultKecamatan) {
            $list = Kecamatan::orderBy('nama_kecamatan')->get();
            return $list->isNotEmpty() ? $list : $defaultKecamatan;
        }, $defaultKecamatan);

        return view('publik.kunjungan.index', compact('pegawaiList', 'dinasList', 'kecamatanList'));
    }

    public function kirimOtpKunjungan(Request $request)
    {
        $validated = $request->validate([
            'email_pengunjung' => 'required|email|max:255',
        ], [
            'email_pengunjung.required' => 'Silakan isi alamat email Anda terlebih dahulu.',
            'email_pengunjung.email' => 'Format email tidak valid.',
        ]);

        $email = strtolower($validated['email_pengunjung']);
        $existingOtp = session(self::KUNJUNGAN_OTP_SESSION_KEY);
        $now = now();

        if (
            ($existingOtp['email'] ?? null) === $email
            && ($existingOtp['sent_at'] ?? 0) > $now->copy()->subSeconds(self::KUNJUNGAN_OTP_RESEND_SECONDS)->timestamp
        ) {
            $remaining = self::KUNJUNGAN_OTP_RESEND_SECONDS - ($now->timestamp - ($existingOtp['sent_at'] ?? 0));
            return response()->json([
                'success' => false,
                'message' => "Kode OTP sudah dikirim. Harap tunggu {$remaining} detik sebelum meminta kode baru.",
            ], 429);
        }

        $otp = (string) random_int(100000, 999999);

        try {
            SirapiMailer::send(
                $email,
                'Kode OTP Verifikasi Kunjungan Kerja RAPID',
                "Yth. Pengunjung,\n\nKode OTP verifikasi pendaftaran kunjungan kerja Anda di RAPID Kabupaten Bogor adalah:\n\n{$otp}\n\nKode ini berlaku selama " . self::KUNJUNGAN_OTP_TTL_MINUTES . " menit. Mohon untuk tidak memberikan kode ini kepada pihak lain.\n\nJika Anda tidak merasa mengajukan kunjungan, abaikan email ini."
            );
        } catch (\Throwable $e) {
            Log::error('PublicPageController: Gagal mengirim OTP kunjungan ke ' . $email . ': ' . $e->getMessage(), ['exception' => $e]);
            $msg = 'Gagal mengirim OTP ke email. Periksa koneksi atau konfigurasi email.';
            if (config('app.debug')) {
                $msg .= ' (' . $e->getMessage() . ')';
            }
            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 500);
        }

        session()->put(self::KUNJUNGAN_OTP_SESSION_KEY, [
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'expires_at' => $now->copy()->addMinutes(self::KUNJUNGAN_OTP_TTL_MINUTES)->timestamp,
            'sent_at' => $now->timestamp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP 6 digit berhasil dikirim ke email Anda.',
        ]);
    }

    private function validateKunjunganOtp(string $email, string $otp): void
    {
        $otpSession = session(self::KUNJUNGAN_OTP_SESSION_KEY);
        $normalizedEmail = strtolower($email);

        if (! $otpSession) {
            throw ValidationException::withMessages([
                'otp' => 'Silakan klik tombol "Kirim OTP" ke email terlebih dahulu.',
            ]);
        }

        if (($otpSession['email'] ?? null) !== $normalizedEmail) {
            throw ValidationException::withMessages([
                'otp' => 'Alamat email tidak cocok dengan email yang menerima kode OTP.',
            ]);
        }

        if (($otpSession['expires_at'] ?? 0) < now()->timestamp) {
            session()->forget(self::KUNJUNGAN_OTP_SESSION_KEY);

            throw ValidationException::withMessages([
                'otp' => 'Kode OTP sudah kedaluwarsa (berlaku 10 menit). Silakan klik "Kirim OTP" kembali.',
            ]);
        }

        if (! Hash::check($otp, $otpSession['otp_hash'] ?? '')) {
            throw ValidationException::withMessages([
                'otp' => 'Kode OTP yang Anda masukkan salah. Periksa kembali email Anda.',
            ]);
        }
    }

    public function simpanKunjungan(Request $request)
    {
        $namaPegawai = $request->input('nama_pegawai') ?: $request->input('nama_pejabat');
        $request->merge(['nama_pegawai' => $namaPegawai]);

        $idDinas = $request->input('id_dinas');
        $idKecamatan = $request->input('id_kecamatan');
        $tujuanInstansi = $request->input('tujuan_instansi');

        if (!empty($tujuanInstansi)) {
            if (str_starts_with($tujuanInstansi, 'dinas_')) {
                $idDinas = (int) str_replace('dinas_', '', $tujuanInstansi);
                $idKecamatan = null;
            } elseif (str_starts_with($tujuanInstansi, 'kecamatan_')) {
                $idKecamatan = (int) str_replace('kecamatan_', '', $tujuanInstansi);
                $idDinas = null;
            }
        }

        $validated = $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'nama_pengunjung' => 'required|string|max:255',
            'asal_instansi' => 'required|string|max:255',
            'nomorhp_pengunjung' => 'required|string|max:13|regex:/^[0-9]+$/',
            'email_pengunjung' => 'required|email|max:255',
            'otp' => 'required|digits:6',
            'keperluan' => 'required|string',
            'id_dinas' => 'nullable|integer',
            'id_kecamatan' => 'nullable|integer',
        ], [
            'nama_pegawai.required' => 'Pilih pihak / pegawai yang ingin Anda tuju.',
            'nama_pengunjung.required' => 'Nama lengkap tamu wajib diisi.',
            'asal_instansi.required' => 'Instansi / asal wajib diisi.',
            'nomorhp_pengunjung.required' => 'No. HP / WhatsApp wajib diisi.',
            'email_pengunjung.required' => 'Alamat email wajib diisi.',
            'email_pengunjung.email' => 'Format email tidak valid.',
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits' => 'Kode OTP harus berupa 6 digit angka.',
            'keperluan.required' => 'Keperluan kunjungan wajib diisi.',
        ]);

        $this->validateKunjunganOtp($validated['email_pengunjung'], $validated['otp']);
        unset($validated['otp']);

        $now = $this->nowWib();
        $validated['id_dinas'] = $idDinas ?: null;
        $validated['id_kecamatan'] = $idKecamatan ?: null;
        $validated['nama_pejabat'] = $validated['nama_pegawai'];
        $validated['tanggal_kunjungan'] = $now->toDateString();
        $validated['waktu'] = $now->format('H:i:s');
        $validated['id_admin'] = $this->queryOrDefault(fn () => Admin::first()?->id_admin);

        if (! \Illuminate\Support\Facades\Schema::hasColumn('sirapi_md_kunjungan', 'id_dinas')) {
            unset($validated['id_dinas']);
        }
        if (! \Illuminate\Support\Facades\Schema::hasColumn('sirapi_md_kunjungan', 'id_kecamatan')) {
            unset($validated['id_kecamatan']);
        }

        try {
            Kunjungan::create($validated);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                // If legacy DB schema unique index exists on email_pengunjung, fallback
                $validated['email_pengunjung'] = $validated['email_pengunjung'] . '.' . time();
                Kunjungan::create($validated);
            } else {
                throw $e;
            }
        }

        session()->forget(self::KUNJUNGAN_OTP_SESSION_KEY);

        return back()->with('success', 'Form kunjungan Anda telah berhasil dikirim dan diverifikasi. Terima kasih!');
    }

    public function qrHadir(Agenda $agenda)
    {
        if ($agenda->status_qr !== 'aktif') {
            return view('publik.presensi.qr_result', [
                'success' => false,
                'agenda' => $agenda,
                'message' => 'QR presensi agenda ini belum diaktifkan oleh admin.',
            ]);
        }

        if ($agenda->status_label === Agenda::STATUS_MENDATANG) {
            return view('publik.presensi.qr_result', [
                'success' => false,
                'agenda' => $agenda,
                'message' => 'Presensi belum dibuka. Agenda rapat baru dimulai pada pukul ' . (substr((string) $agenda->waktu, 0, 5) ?: '-') . ' WIB (' . ($agenda->tanggal?->translatedFormat('d F Y') ?? '-') . ').',
            ]);
        }

        if ($agenda->status_label === Agenda::STATUS_SELESAI) {
            return view('publik.presensi.qr_result', [
                'success' => false,
                'agenda' => $agenda,
                'message' => 'Presensi untuk agenda rapat ini telah ditutup karena waktu rapat telah berakhir.',
            ]);
        }

        $qrWindow = $this->qrWindow($agenda);
        if (! $this->nowWib()->betweenIncluded($qrWindow['start'], $qrWindow['end'])) {
            return view('publik.presensi.qr_result', [
                'success' => false,
                'agenda' => $agenda,
                'message' => 'QR presensi hanya aktif pada ' . $qrWindow['start']->translatedFormat('d F Y H:i') . ' sampai ' . $qrWindow['end']->translatedFormat('H:i') . ' WIB.',
            ]);
        }

        if (Auth::guard('pegawai')->check()) {
            $pegawaiUser = Auth::guard('pegawai')->user();
            if (! $agenda->canPegawaiPresensi($pegawaiUser)) {
                return view('publik.presensi.qr_result', [
                    'success' => false,
                    'agenda' => $agenda,
                    'message' => 'Presensi ditolak. Agenda surat masuk ini hanya dikhususkan untuk pegawai yang ditugaskan (' . ($agenda->ditugaskan ?: '-') . ').',
                ]);
            }
            if (! $agenda->isPegawaiSudahHadir($pegawaiUser) && $agenda->isKuotaPenuh()) {
                return view('publik.presensi.qr_result', [
                    'success' => false,
                    'agenda' => $agenda,
                    'message' => 'Presensi ditolak karena kuota peserta agenda ini sudah penuh.',
                ]);
            }
        } elseif ($agenda->isKuotaPenuh()) {
            return view('publik.presensi.qr_result', [
                'success' => false,
                'agenda' => $agenda,
                'message' => 'Presensi ditolak karena kuota peserta agenda ini sudah penuh.',
            ]);
        }

        $qrCode = $this->queryOrDefault(fn () => QRCode::where('id_agenda', $agenda->id_agenda)->first());

        if (! $qrCode) {
            return view('publik.presensi.qr_result', [
                'success' => false,
                'agenda' => $agenda,
                'message' => 'QR presensi belum dibuat oleh admin.',
            ]);
        }

        Logbook::create([
            'id_agenda' => $agenda->id_agenda,
            'catatan' => 'Hadir lewat Scan QR pegawai.',
            'waktu_isi' => $this->nowWib(),
        ]);

        return view('publik.presensi.qr_result', [
            'success' => true,
            'agenda' => $agenda,
            'message' => 'Scan QR berhasil, kehadiran sudah dicatat.',
        ]);
    }

    private function queryOrDefault(callable $query, mixed $default = null): mixed
    {
        try {
            return $query();
        } catch (QueryException $e) {
            Log::warning('PublicPageController: QueryException tertangkap: ' . $e->getMessage());
            return $default;
        } catch (\Throwable $e) {
            Log::error('PublicPageController: Exception tertangkap: ' . $e->getMessage());
            return $default;
        }
    }

    private function dokumentasiAgendaGaleri()
    {
        $dokumentasi = DokumenNotulen::with('agenda')
            ->where('jenis_dokumen', 'dokumentasi')
            ->latest('id_dokumen')
            ->get()
            ->filter(fn ($item) => in_array(strtolower(pathinfo((string) $item->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true));

        $galeri = Galeri::latest('id_galeri')->get()->map(function ($item) {
            $item->file_path = $item->gambar;
            return $item;
        });

        return $dokumentasi->concat($galeri)->values();
    }

    private function agendaPresensi(Request $request): ?Agenda
    {
        $id = $request->query('agenda_id');
        $today = Carbon::today(self::PUBLIC_TIMEZONE);

        return $this->queryOrDefault(fn () => Agenda::query()
            ->when($id, fn ($query) => $query->whereKey($id))
            ->whereDate('tanggal', '>=', $today)
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->first());
    }

    private function publicStoragePath(string $path): ?string
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $urlPath = parse_url($path, PHP_URL_PATH);

            if (! is_string($urlPath) || $urlPath === '') {
                return null;
            }

            $path = rawurldecode($urlPath);

            if (! str_starts_with(ltrim($path, '/'), 'storage/')) {
                return null;
            }
        }

        $path = str_replace('\\', '/', trim($path));
        $path = ltrim($path, '/');

        foreach (['storage/', 'public/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
            }
        }

        return ltrim($path, '/') ?: null;
    }

    private function qrWindow(Agenda $agenda): array
    {
        $date = $agenda->tanggal?->toDateString() ?? Carbon::today(self::PUBLIC_TIMEZONE)->toDateString();
        $startTime = substr((string) $agenda->waktu, 0, 5) ?: '00:00';
        $endTime = substr((string) $agenda->waktu_selesai, 0, 5);
        $start = Carbon::parse($date . ' ' . $startTime, self::PUBLIC_TIMEZONE);
        $end = $endTime ? Carbon::parse($date . ' ' . $endTime, self::PUBLIC_TIMEZONE) : $start->copy()->addHour();

        if ($end->lessThanOrEqualTo($start)) {
            $end = $start->copy()->addHour();
        }

        return compact('start', 'end');
    }

    private function nowWib(): Carbon
    {
        return Carbon::now(self::PUBLIC_TIMEZONE);
    }

    private function defaultYoutubeEmbedUrl(): string
    {
        $playlistId = AppSetting::get('sirapi_youtube_playlist_id', config('sirapi.youtube_playlist_id', 'UUJlX_73GqPvJlerJFN4cRgA'));

        return 'https://www.youtube.com/embed/videoseries?list=' . $playlistId;
    }

    private function weatherCodeLabel(mixed $code): string
    {
        return match ((int) $code) {
            0 => 'Cerah',
            1, 2 => 'Cerah Berawan',
            3 => 'Berawan',
            45, 48 => 'Berkabut',
            51, 53, 55 => 'Gerimis',
            61, 63, 65 => 'Hujan',
            66, 67 => 'Hujan Es',
            71, 73, 75, 77 => 'Salju',
            80, 81, 82 => 'Hujan Lokal',
            85, 86 => 'Hujan Salju',
            95 => 'Badai Petir',
            96, 99 => 'Badai Petir dengan Es',
            default => 'Tidak diketahui',
        };
    }

    public function presensiPegawaiPilih(Request $request)
    {
        $agendaId = $request->input('agenda_id');
        $agenda = Agenda::find($agendaId);
        if (!$agenda) {
            return redirect()->route('publik.agenda')->withErrors(['agenda' => 'Agenda tidak ditemukan.']);
        }

        // Cek pegawai yang sedang login atau dari sesi presensi terbaru
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai && $request->session()->has('presensi_sukses_' . $agenda->id_agenda)) {
            $sessData = $request->session()->get('presensi_sukses_' . $agenda->id_agenda);
            $pegawai = Pegawai::find($sessData['id_pegawai'] ?? null);
        }

        // Cari data kehadiran pegawai ini untuk agenda ini
        $kehadiran = null;
        if ($pegawai) {
            $kehadiran = DB::table('sirapi_md_kehadiran')
                ->join('sirapi_md_peserta', 'sirapi_md_kehadiran.id_peserta', '=', 'sirapi_md_peserta.id_peserta')
                ->where('sirapi_md_kehadiran.id_agenda', $agenda->id_agenda)
                ->where(function ($q) use ($pegawai) {
                    $q->where('sirapi_md_peserta.email', $pegawai->email)
                      ->orWhereRaw('LOWER(sirapi_md_peserta.email) = ?', [strtolower($pegawai->email)]);
                })
                ->select(
                    'sirapi_md_kehadiran.*',
                    'sirapi_md_peserta.nama',
                    'sirapi_md_peserta.jabatan',
                    'sirapi_md_peserta.instansi',
                    'sirapi_md_peserta.nomor_hp',
                    'sirapi_md_peserta.email'
                )
                ->orderByDesc('sirapi_md_kehadiran.id_kehadiran')
                ->first();
        }

        // Fallback jika ada last_id_kehadiran di sesi
        if (!$kehadiran && $request->session()->has('last_id_kehadiran_' . $agenda->id_agenda)) {
            $lastId = $request->session()->get('last_id_kehadiran_' . $agenda->id_agenda);
            $kehadiran = DB::table('sirapi_md_kehadiran')
                ->join('sirapi_md_peserta', 'sirapi_md_kehadiran.id_peserta', '=', 'sirapi_md_peserta.id_peserta')
                ->where('sirapi_md_kehadiran.id_kehadiran', $lastId)
                ->where('sirapi_md_kehadiran.id_agenda', $agenda->id_agenda)
                ->select(
                    'sirapi_md_kehadiran.*',
                    'sirapi_md_peserta.nama',
                    'sirapi_md_peserta.jabatan',
                    'sirapi_md_peserta.instansi',
                    'sirapi_md_peserta.nomor_hp',
                    'sirapi_md_peserta.email'
                )
                ->orderByDesc('sirapi_md_kehadiran.id_kehadiran')
                ->first();

            if ($kehadiran && !$pegawai) {
                $pegawai = Pegawai::where('email', $kehadiran->email)
                    ->orWhereRaw('LOWER(email) = ?', [strtolower($kehadiran->email)])
                    ->first();
            }
        }

        return view('pegawai.presensi_pilih.index', compact('agenda', 'pegawai', 'kehadiran'));
    }

    public function presensiPegawaiWajah(Request $request)
    {
        $agendaId = $request->input('agenda_id');
        $agenda = Agenda::find($agendaId);
        if (!$agenda) {
            return redirect()->route('publik.agenda')->withErrors(['agenda' => 'Agenda tidak ditemukan.']);
        }

        // Cek jika pegawai sudah login dan sudah hadir, langsung alihkan ke halaman bukti presensi
        $pegawai = Auth::guard('pegawai')->user();
        if ($pegawai) {
            $sudahHadir = DB::table('sirapi_md_kehadiran')
                ->join('sirapi_md_peserta', 'sirapi_md_kehadiran.id_peserta', '=', 'sirapi_md_peserta.id_peserta')
                ->where('sirapi_md_kehadiran.id_agenda', $agenda->id_agenda)
                ->where('sirapi_md_peserta.email', $pegawai->email)
                ->exists();

            if ($sudahHadir) {
                return redirect()->route('publik.presensi.pegawai', ['agenda_id' => $agenda->id_agenda]);
            }
        }

        return view('pegawai.presensi_wajah.index', compact('agenda'));
    }
}
