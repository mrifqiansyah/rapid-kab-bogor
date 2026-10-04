<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Bidang;
use App\Models\Dinas;
use App\Models\DokumenNotulen;
use App\Models\Jabatan;
use App\Models\Kecamatan;
use App\Models\Pegawai;
use App\Models\RuangRapat;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Services\SirapiMailer;
use App\Services\MasterInstansiService;

class PegawaiAuthController extends Controller
{
    private const TIMEZONE = 'Asia/Jakarta';
    private const PASSWORD_OTP_SESSION_KEY = 'pegawai_password_otp';
    private const PASSWORD_OTP_TTL_MINUTES = 10;
    private const PASSWORD_OTP_RESEND_SECONDS = 60;
    private const FORGOT_PASSWORD_OTP_SESSION_KEY = 'pegawai_forgot_password_otp';

    public function showLoginForm(Request $request)
    {
        return redirect()->route('pegawai.register');
    }

    public function showRegisterForm()
    {
        $dinasList = $this->queryOrDefault(function () {
            $list = Dinas::orderBy('nama_dinas')->get();
            return $list->isNotEmpty() ? $list : $this->fallbackDinasList();
        }, $this->fallbackDinasList());

        $kecamatanList = $this->queryOrDefault(function () {
            $list = Kecamatan::orderBy('nama_kecamatan')->get();
            return $list->isNotEmpty() ? $list : $this->fallbackKecamatanList();
        }, $this->fallbackKecamatanList());

        $masterInstansiData = MasterInstansiService::getAllOptionsMap($dinasList, $kecamatanList);

        $selectedInstansi = old('instansi');
        if ($selectedInstansi) {
            $selectedData = MasterInstansiService::getOptionsForInstansi($selectedInstansi);
            $jabatanOptions = $selectedData['jabatan'] ?? [];
            $bidangOptions = $selectedData['bidang'] ?? [];
        } else {
            $jabatanOptions = [];
            $bidangOptions = [];
        }

        return view('auth.register_pegawai.index', compact(
            'bidangOptions',
            'jabatanOptions',
            'dinasList',
            'kecamatanList',
            'masterInstansiData'
        ));
    }

    public function login(Request $request)
    {
        return redirect()->route('pegawai.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_pegawai' => ['required', 'string', 'max:255'],
            'instansi' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!str_starts_with($value, 'dinas_') && !str_starts_with($value, 'kecamatan_')) {
                    $fail('Pilihan instansi tidak valid.');
                    return;
                }
                if (str_starts_with($value, 'dinas_')) {
                    $id = (int) substr($value, 6);
                    if (!Dinas::where('id_dinas', $id)->exists()) {
                        $fail('Dinas yang dipilih tidak ditemukan.');
                    }
                } else {
                    $id = (int) substr($value, 10);
                    if (!Kecamatan::where('id_kecamatan', $id)->exists()) {
                        $fail('Kecamatan yang dipilih tidak ditemukan.');
                    }
                }
            }],
            'nip' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', 'unique:sirapi_md_pegawai,nip'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jabatan' => ['required', 'string', 'max:255'],
            'bidang' => ['nullable', 'string', 'max:255'],
            'nomor_hp' => ['required', 'string', 'max:13', 'regex:/^[0-9]+$/'],
            'email' => ['required', 'email', 'max:255', 'unique:sirapi_md_pegawai,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'face_descriptor' => ['required', 'string'],
            'foto_wajah' => ['required', 'string'],
        ], [
            'instansi.required' => 'Silakan pilih instansi (Dinas atau Kecamatan) tempat Anda bertugas.',
            'face_descriptor.required' => 'Perekaman biometrik wajah (Face Recognition) wajib dilakukan.',
            'foto_wajah.required' => 'Perekaman foto wajah (Face Recognition) wajib dilakukan.',
        ]);

        if (str_starts_with($validated['instansi'], 'dinas_')) {
            $validated['id_dinas'] = (int) substr($validated['instansi'], 6);
            $validated['id_kecamatan'] = null;
        } else {
            $validated['id_dinas'] = null;
            $validated['id_kecamatan'] = (int) substr($validated['instansi'], 10);
        }
        unset($validated['instansi']);

        // Simpan foto hasil scan wajah dan otomatis jadikan sebagai foto profil
        if (!empty($request->input('foto_wajah'))) {
            $imageParts = explode(';base64,', $request->input('foto_wajah'));
            if (count($imageParts) === 2) {
                $imageTypeAux = explode('image/', $imageParts[0]);
                $imageType = $imageTypeAux[1] ?? 'jpeg';
                $imageBase64 = base64_decode($imageParts[1]);
                $cleanNip = preg_replace('/[^0-9]/', '', $validated['nip']) ?: 'face';
                $fileName = 'face_scan_' . $cleanNip . '_' . time() . '.' . $imageType;

                Storage::disk('public')->put('pegawai/' . $fileName, $imageBase64);
                $fotoWajahPath = 'pegawai/' . $fileName;
                $validated['foto_wajah'] = $fotoWajahPath;
                $validated['foto'] = $fotoWajahPath; // Foto profil otomatis dari hasil scan face recognition
            }
        }

        if (!empty($request->input('face_descriptor'))) {
            $validated['face_descriptor'] = $request->input('face_descriptor');
        }

        $validated['status_verifikasi'] = Pegawai::STATUS_PENDING;

        Pegawai::create($validated);

        return redirect()->route('pegawai.register')->with('status', 'Pendaftaran akun berhasil! Data dan perekaman biometrik wajah Anda telah tersimpan dan sedang menunggu verifikasi Administrator. Setelah diverifikasi, Anda dapat langsung melakukan presensi rapat menggunakan Scan Wajah (Face Recognition) di lokasi rapat.');
    }

    public function kirimOtpLupaPassword(Request $request)
    {
        $validated = $request->validate([
            'reset_email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower($validated['reset_email']);
        $existingOtp = session(self::FORGOT_PASSWORD_OTP_SESSION_KEY);
        $now = now();

        if (
            ($existingOtp['email'] ?? null) === $email
            && ($existingOtp['sent_at'] ?? 0) > $now->copy()->subSeconds(self::PASSWORD_OTP_RESEND_SECONDS)->timestamp
        ) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP sudah dikirim. Tunggu 60 detik sebelum meminta kode baru.',
                ], 429);
            }

            return back()
                ->withErrors(['reset_email' => 'OTP sudah dikirim. Tunggu 60 detik sebelum meminta kode baru.'])
                ->withInput($request->only('reset_email'))
                ->with('forgot_open', true);
        }

        $otp = (string) random_int(100000, 999999);

        try {
            SirapiMailer::send(
                $email,
                'Kode OTP Reset Password RAPID',
                "Kode OTP reset password RAPID Anda: {$otp}\n\nKode ini berlaku selama " . self::PASSWORD_OTP_TTL_MINUTES . " menit. Abaikan email ini jika Anda tidak meminta reset password."
            );
        } catch (\Throwable $e) {
            Log::error('PegawaiAuthController: Gagal mengirim OTP reset password ke ' . $email . ': ' . $e->getMessage(), ['exception' => $e]);
            $msg = 'OTP gagal dikirim. Periksa konfigurasi email aplikasi.';
            if (config('app.debug')) {
                $msg .= ' (Detail: ' . $e->getMessage() . ')';
            }
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 500);
            }

            return back()
                ->withErrors(['reset_email' => $msg])
                ->withInput($request->only('reset_email'))
                ->with('forgot_open', true);
        }

        session()->put(self::FORGOT_PASSWORD_OTP_SESSION_KEY, [
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'expires_at' => $now->copy()->addMinutes(self::PASSWORD_OTP_TTL_MINUTES)->timestamp,
            'sent_at' => $now->timestamp,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kode OTP reset password sudah dikirim ke email tujuan.',
            ]);
        }

        return back()
            ->with('status', 'Kode OTP reset password sudah dikirim ke email tujuan.')
            ->withInput($request->only('reset_email'))
            ->with('forgot_open', true);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'reset_email' => ['required', 'email', 'max:255'],
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = strtolower($validated['reset_email']);
        $this->validateForgotPasswordOtp($email, $validated['otp']);

        if (! Schema::hasTable((new Pegawai())->getTable())) {
            return back()
                ->withErrors(['reset_email' => 'Tabel pegawai belum tersedia. Jalankan migration sirapi_md terlebih dahulu.'])
                ->withInput($request->only('reset_email'))
                ->with('forgot_open', true);
        }

        $pegawai = Pegawai::where('email', $email)->first();

        if (! $pegawai) {
            return back()
                ->withErrors(['reset_email' => 'OTP valid, tetapi email ini belum terdaftar sebagai akun pegawai.'])
                ->withInput($request->only('reset_email'))
                ->with('forgot_open', true);
        }

        $pegawai->update([
            'password' => $validated['password'],
        ]);

        session()->forget(self::FORGOT_PASSWORD_OTP_SESSION_KEY);

        return redirect()
            ->route('pegawai.login')
            ->with('status', 'Password berhasil diubah. Silakan login dengan password baru.');
    }

    public function presensi(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        $agenda = $this->agendaPresensi($request);
        $dokumen = $this->dokumenAgenda($agenda);
        $kehadiran = $agenda ? $this->kehadiranPegawai($agenda->id_agenda, $pegawai->email) : null;
        [$bidangOptions, $jabatanOptions] = $this->masterPegawaiOptions();
        $isDitugaskan = $agenda ? $agenda->canPegawaiPresensi($pegawai) : true;
        $daftarAgenda = $this->queryOrDefault(fn () => Agenda::latest('id_agenda')->take(20)->get(), collect());

        return view('pegawai.presensi.index', compact('pegawai', 'agenda', 'dokumen', 'kehadiran', 'bidangOptions', 'jabatanOptions', 'isDitugaskan', 'daftarAgenda'));
    }

    public function simpanPresensi(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        $agenda = $this->agendaPresensi($request);

        if (! $agenda) {
            return back()->withErrors(['presensi' => 'Agenda presensi belum tersedia.']);
        }

        if ($agenda->status_label === Agenda::STATUS_MENDATANG) {
            return back()->withErrors(['presensi' => 'Presensi belum dibuka. Agenda rapat baru dimulai pada pukul ' . (substr((string) $agenda->waktu, 0, 5) ?: '-') . ' WIB.']);
        }

        if ($agenda->status_label === Agenda::STATUS_SELESAI) {
            return back()->withErrors(['presensi' => 'Agenda rapat telah selesai. Presensi sudah ditutup.']);
        }

        if (! $agenda->canPegawaiPresensi($pegawai)) {
            return back()->withErrors(['presensi' => 'Presensi ditolak. Agenda surat masuk ini hanya dikhususkan untuk pegawai yang ditugaskan (' . ($agenda->ditugaskan ?: '-') . ').']);
        }

        $kehadiran = $this->kehadiranPegawai($agenda->id_agenda, $pegawai->email);
        if ($kehadiran) {
            $waktuHadir = $kehadiran->created_at ? Carbon::parse($kehadiran->created_at)->timezone(self::TIMEZONE)->format('H:i') : null;
            return redirect()
                ->route('pegawai.presensi.index', ['agenda_id' => $agenda->id_agenda])
                ->with('info', 'Anda telah melakukan presensi sebelumnya' . ($waktuHadir ? ' pada pukul ' . $waktuHadir . ' WIB.' : '.'));
        }

        if ($agenda->isKuotaPenuh()) {
            return back()->withErrors(['presensi' => 'Presensi ditolak karena kuota peserta agenda ini sudah penuh.']);
        }

        $fotoScanPath = null;
        if ($request->filled('foto_kehadiran')) {
            try {
                $imageParts = explode(';base64,', (string) $request->input('foto_kehadiran'));
                $imageTypeAux = explode('image/', $imageParts[0] ?? '');
                $imageType = $imageTypeAux[1] ?? 'jpeg';
                if (isset($imageParts[1])) {
                    $imageBase64 = base64_decode($imageParts[1]);
                    $fileName = 'presensi_face_' . $agenda->id_agenda . '_' . $pegawai->id_pegawai . '_' . time() . '.' . $imageType;
                    Storage::disk('public')->put('presensi/' . $fileName, $imageBase64);
                    $fotoScanPath = 'presensi/' . $fileName;
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal menyimpan foto scan presensi: ' . $e->getMessage());
            }
        }

        if (! $fotoScanPath) {
            $fotoScanPath = $pegawai->foto_wajah ?: $pegawai->foto;
        }

        $this->catatKehadiranPegawai($agenda, $pegawai, 'Hadir lewat Scan Wajah (Face Recognition)', $request->input('lokasi_presensi'), $fotoScanPath);

        return redirect()
            ->route('pegawai.presensi.index', ['agenda_id' => $agenda->id_agenda])
            ->with('success', 'Presensi telah berhasil dicatat.');
    }

    public function updateProfil(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        $validated = $request->validate([
            'nama_pegawai' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', 'unique:sirapi_md_pegawai,nip,' . $pegawai->id_pegawai . ',id_pegawai'],
            'jabatan' => ['required', 'string', 'max:255'],
            'bidang' => ['nullable', 'string', 'max:255'],
            'nomor_hp' => ['nullable', 'string', 'max:13', 'regex:/^[0-9]+$/'],
            'email' => ['required', 'email', 'max:255', 'unique:sirapi_md_pegawai,email,' . $pegawai->id_pegawai . ',id_pegawai'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_otp' => ['required_with:password', 'nullable', 'digits:6'],
        ]);

        if (! empty($validated['password'])) {
            $this->validatePasswordOtp($pegawai->email, $validated['password_otp'] ?? '');
            session()->forget(self::PASSWORD_OTP_SESSION_KEY);
        } else {
            unset($validated['password']);
        }

        unset($validated['password_otp']);

        if ($request->hasFile('foto')) {
            if ($pegawai->foto && ! str_starts_with($pegawai->foto, 'assets/foto/') || str_starts_with($pegawai->foto, 'foto/') && Storage::disk('public')->exists($pegawai->foto)) {
                Storage::disk('public')->delete($pegawai->foto);
            }

            $validated['foto'] = $request->file('foto')->store('pegawai', 'public');
        }

        $pegawai->update($validated);

        return back()->with('profile_success', 'Profil berhasil diperbarui.');
    }

    public function kirimOtpPassword(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (! $pegawai || ! $pegawai->email) {
            return response()->json([
                'success' => false,
                'message' => 'Email pegawai tidak ditemukan.',
            ], 422);
        }

        $email = strtolower($pegawai->email);
        $existingOtp = session(self::PASSWORD_OTP_SESSION_KEY);
        $now = now();

        if (
            ($existingOtp['email'] ?? null) === $email
            && ($existingOtp['sent_at'] ?? 0) > $now->copy()->subSeconds(self::PASSWORD_OTP_RESEND_SECONDS)->timestamp
        ) {
            return response()->json([
                'success' => false,
                'message' => 'OTP sudah dikirim. Tunggu 60 detik sebelum meminta kode baru.',
            ], 429);
        }

        $otp = (string) random_int(100000, 999999);

        try {
            SirapiMailer::send(
                $email,
                'Kode OTP Ubah Password RAPID',
                "Kode OTP ubah password RAPID Anda: {$otp}\n\nKode ini berlaku selama " . self::PASSWORD_OTP_TTL_MINUTES . " menit. Abaikan email ini jika Anda tidak meminta perubahan password."
            );
        } catch (\Throwable $e) {
            Log::error('PegawaiAuthController: Gagal mengirim OTP ubah password ke ' . $email . ': ' . $e->getMessage(), ['exception' => $e]);
            $msg = 'OTP gagal dikirim. Periksa konfigurasi email aplikasi.';
            if (config('app.debug')) {
                $msg .= ' (Detail: ' . $e->getMessage() . ')';
            }
            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 500);
        }

        session()->put(self::PASSWORD_OTP_SESSION_KEY, [
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'expires_at' => $now->copy()->addMinutes(self::PASSWORD_OTP_TTL_MINUTES)->timestamp,
            'sent_at' => $now->timestamp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP sudah dikirim ke email akun Anda.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('pegawai')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pegawai.login')->with('status', 'Anda telah logout.');
    }

    private function agendaPresensi(Request $request): ?Agenda
    {
        $id = $request->query('agenda_id');
        $today = Carbon::today(self::TIMEZONE)->toDateString();

        return $this->queryOrDefault(fn () => Agenda::query()
            ->when($id, fn ($query) => $query->whereKey($id))
            ->orderByRaw('tanggal >= ? desc', [$today])
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->first());
    }

    private function dokumenAgenda(?Agenda $agenda)
    {
        if (! $agenda) {
            return collect();
        }

        return $this->queryOrDefault(fn () => DokumenNotulen::where('id_agenda', $agenda->id_agenda)
            ->orderBy('jenis_dokumen')
            ->orderBy('id_dokumen')
            ->get(), collect());
    }

    private function kehadiranPegawai(int $idAgenda, string $email): mixed
    {
        return $this->queryOrDefault(fn () => DB::table('sirapi_md_kehadiran')
            ->join('sirapi_md_peserta', 'sirapi_md_kehadiran.id_peserta', '=', 'sirapi_md_peserta.id_peserta')
            ->where('sirapi_md_kehadiran.id_agenda', $idAgenda)
            ->where('sirapi_md_peserta.email', $email)
            ->select('sirapi_md_kehadiran.*')
            ->first());
    }

    private function validatePasswordOtp(string $email, string $otp): void
    {
        $otpSession = session(self::PASSWORD_OTP_SESSION_KEY);
        $normalizedEmail = strtolower($email);

        if (! $otpSession) {
            throw ValidationException::withMessages([
                'password_otp' => 'Silakan kirim OTP ke email terlebih dahulu.',
            ]);
        }

        if (($otpSession['email'] ?? null) !== $normalizedEmail) {
            throw ValidationException::withMessages([
                'password_otp' => 'Email akun tidak sama dengan email yang menerima OTP.',
            ]);
        }

        if (($otpSession['expires_at'] ?? 0) < now()->timestamp) {
            session()->forget(self::PASSWORD_OTP_SESSION_KEY);

            throw ValidationException::withMessages([
                'password_otp' => 'Kode OTP sudah kedaluwarsa. Silakan minta OTP baru.',
            ]);
        }

        if (! Hash::check($otp, $otpSession['otp_hash'] ?? '')) {
            throw ValidationException::withMessages([
                'password_otp' => 'Kode OTP tidak valid.',
            ]);
        }
    }

    private function validateForgotPasswordOtp(string $email, string $otp): void
    {
        $otpSession = session(self::FORGOT_PASSWORD_OTP_SESSION_KEY);
        $normalizedEmail = strtolower($email);

        if (! $otpSession) {
            throw ValidationException::withMessages([
                'otp' => 'Silakan kirim OTP ke email terlebih dahulu.',
            ]);
        }

        if (($otpSession['email'] ?? null) !== $normalizedEmail) {
            throw ValidationException::withMessages([
                'otp' => 'Email tidak sama dengan email yang menerima OTP.',
            ]);
        }

        if (($otpSession['expires_at'] ?? 0) < now()->timestamp) {
            session()->forget(self::FORGOT_PASSWORD_OTP_SESSION_KEY);

            throw ValidationException::withMessages([
                'otp' => 'Kode OTP sudah kedaluwarsa. Silakan minta OTP baru.',
            ]);
        }

        if (! Hash::check($otp, $otpSession['otp_hash'] ?? '')) {
            throw ValidationException::withMessages([
                'otp' => 'Kode OTP tidak valid.',
            ]);
        }
    }

    private function masterPegawaiOptions(): array
    {
        $bidang = $this->queryOrDefault(fn () => Bidang::orderBy('nama_bidang')->pluck('nama_bidang'), collect());
        $jabatan = $this->queryOrDefault(fn () => Jabatan::orderByRaw("
                CASE
                    WHEN kategori = 'Struktural' THEN 0
                    WHEN kategori = 'Jabatan Fungsional' THEN 1
                    ELSE 2
                END
            ")
            ->orderBy('nama_jabatan')
            ->pluck('nama_jabatan'), collect());

        return [$bidang, $jabatan];
    }

    private function queryOrDefault(callable $query, mixed $default = null): mixed
    {
        try {
            return $query();
        } catch (\Throwable) {
            return $default;
        }
    }

    private function fallbackDinasList(): \Illuminate\Support\Collection
    {
        $abbreviations = [
            11 => 'BKPSDM', 10 => 'Bakesbangpol', 12 => 'BPBD', 13 => 'BPKAD', 7 => 'Bappenda',
            6 => 'Bappedalitbang', 15 => 'DAPD', 17 => 'Disbudpar', 19 => 'Disdukcapil', 3 => 'Dinkes',
            26 => 'DKP', 1 => 'Diskominfo', 21 => 'Diskopukm', 27 => 'DLH', 23 => 'Disparekraf',
            5 => 'PUPR', 14 => 'Damkar', 30 => 'DPMD', 28 => 'DP3AP2KB', 24 => 'Dispora',
            31 => 'DPMPTSP', 2 => 'Disdik', 18 => 'Disdagin', 4 => 'Dishub', 20 => 'Diskanak',
            32 => 'DPTR', 29 => 'DPKP', 16 => 'Dinsos', 25 => 'Distanhorbun', 22 => 'Disnaker',
            33 => 'Inspektorat', 34 => 'RSUD Ciawi', 35 => 'RSUD Cibinong', 36 => 'RSUD Cileungsi',
            37 => 'RSUD Leuwiliang', 8 => 'Satpol PP', 38 => 'Setda', 39 => 'Setwan', 40 => 'Pol',
        ];

        return collect([
            (object) ['id_dinas' => 11, 'nama_dinas' => 'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia'],
            (object) ['id_dinas' => 10, 'nama_dinas' => 'Badan Kesatuan Bangsa dan Politik'],
            (object) ['id_dinas' => 12, 'nama_dinas' => 'Badan Penanggulangan Bencana Daerah'],
            (object) ['id_dinas' => 13, 'nama_dinas' => 'Badan Pengelolaan Keuangan dan Aset Daerah'],
            (object) ['id_dinas' => 7, 'nama_dinas' => 'Badan Pengelolaan Pendapatan Daerah'],
            (object) ['id_dinas' => 6, 'nama_dinas' => 'Badan Perencanaan Pembangunan, Riset dan Inovasi Daerah'],
            (object) ['id_dinas' => 15, 'nama_dinas' => 'Dinas Arsip dan Perpustakaan Daerah'],
            (object) ['id_dinas' => 17, 'nama_dinas' => 'Dinas Kebudayaan dan Kepariwisataan'],
            (object) ['id_dinas' => 19, 'nama_dinas' => 'Dinas Kependudukan dan Pencatatan Sipil'],
            (object) ['id_dinas' => 3, 'nama_dinas' => 'Dinas Kesehatan'],
            (object) ['id_dinas' => 26, 'nama_dinas' => 'Dinas Ketahanan Pangan'],
            (object) ['id_dinas' => 1, 'nama_dinas' => 'Dinas Komunikasi dan Informatika'],
            (object) ['id_dinas' => 21, 'nama_dinas' => 'Dinas Koperasi, Usaha Kecil dan Menengah'],
            (object) ['id_dinas' => 27, 'nama_dinas' => 'Dinas Lingkungan Hidup'],
            (object) ['id_dinas' => 23, 'nama_dinas' => 'Dinas Pariwisata dan Kebudayaan'],
            (object) ['id_dinas' => 5, 'nama_dinas' => 'Dinas Pekerjaan Umum dan Penataan Ruang'],
            (object) ['id_dinas' => 14, 'nama_dinas' => 'Dinas Pemadam Kebakaran'],
            (object) ['id_dinas' => 30, 'nama_dinas' => 'Dinas Pemberdayaan Masyarakat dan Desa'],
            (object) ['id_dinas' => 28, 'nama_dinas' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak, Pengendalian Penduduk dan Keluarga Berencana'],
            (object) ['id_dinas' => 24, 'nama_dinas' => 'Dinas Pemuda dan Olahraga'],
            (object) ['id_dinas' => 31, 'nama_dinas' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu'],
            (object) ['id_dinas' => 2, 'nama_dinas' => 'Dinas Pendidikan'],
            (object) ['id_dinas' => 18, 'nama_dinas' => 'Dinas Perdagangan dan Perindustrian'],
            (object) ['id_dinas' => 4, 'nama_dinas' => 'Dinas Perhubungan'],
            (object) ['id_dinas' => 20, 'nama_dinas' => 'Dinas Perikanan dan Peternakan'],
            (object) ['id_dinas' => 32, 'nama_dinas' => 'Dinas Pertanahan dan Tata Ruang'],
            (object) ['id_dinas' => 29, 'nama_dinas' => 'Dinas Perumahan, Kawasan Permukiman dan Pertanahan'],
            (object) ['id_dinas' => 16, 'nama_dinas' => 'Dinas Sosial'],
            (object) ['id_dinas' => 25, 'nama_dinas' => 'Dinas Tanaman Pangan, Hortikultura dan Perkebunan'],
            (object) ['id_dinas' => 22, 'nama_dinas' => 'Dinas Tenaga Kerja'],
            (object) ['id_dinas' => 33, 'nama_dinas' => 'Inspektorat Daerah'],
            (object) ['id_dinas' => 34, 'nama_dinas' => 'RSUD Idham Chalid Ciawi'],
            (object) ['id_dinas' => 35, 'nama_dinas' => 'RSUD Bakti Pajajaran Cibinong'],
            (object) ['id_dinas' => 36, 'nama_dinas' => 'RSUD RH. Satibi Cileungsi'],
            (object) ['id_dinas' => 37, 'nama_dinas' => 'RSUD R. Moh. Noh Nur Leuwiliang'],
            (object) ['id_dinas' => 8, 'nama_dinas' => 'Satuan Polisi Pamong Praja'],
            (object) ['id_dinas' => 38, 'nama_dinas' => 'Sekretariat Daerah'],
            (object) ['id_dinas' => 39, 'nama_dinas' => 'Sekretariat DPRD'],
        ])->map(function ($item) use ($abbreviations) {
            $abbr = $abbreviations[$item->id_dinas] ?? null;
            $item->nama_lengkap = $abbr ? "{$item->nama_dinas} ({$abbr})" : $item->nama_dinas;
            return $item;
        });
    }

    private function fallbackKecamatanList(): \Illuminate\Support\Collection
    {
        return collect([
            (object) ['id_kecamatan' => 5, 'nama_kecamatan' => 'Kecamatan Babakan Madang'],
            (object) ['id_kecamatan' => 13, 'nama_kecamatan' => 'Kecamatan Bojong Gede'],
            (object) ['id_kecamatan' => 27, 'nama_kecamatan' => 'Kecamatan Caringin'],
            (object) ['id_kecamatan' => 8, 'nama_kecamatan' => 'Kecamatan Cariu'],
            (object) ['id_kecamatan' => 15, 'nama_kecamatan' => 'Kecamatan Ciampea'],
            (object) ['id_kecamatan' => 24, 'nama_kecamatan' => 'Kecamatan Ciawi'],
            (object) ['id_kecamatan' => 1, 'nama_kecamatan' => 'Kecamatan Cibinong'],
            (object) ['id_kecamatan' => 16, 'nama_kecamatan' => 'Kecamatan Cibungbulang'],
            (object) ['id_kecamatan' => 38, 'nama_kecamatan' => 'Kecamatan Cigombong'],
            (object) ['id_kecamatan' => 22, 'nama_kecamatan' => 'Kecamatan Cigudeg'],
            (object) ['id_kecamatan' => 28, 'nama_kecamatan' => 'Kecamatan Cijeruk'],
            (object) ['id_kecamatan' => 7, 'nama_kecamatan' => 'Kecamatan Cileungsi'],
            (object) ['id_kecamatan' => 29, 'nama_kecamatan' => 'Kecamatan Ciomas'],
            (object) ['id_kecamatan' => 25, 'nama_kecamatan' => 'Kecamatan Cisarua'],
            (object) ['id_kecamatan' => 33, 'nama_kecamatan' => 'Kecamatan Ciseeng'],
            (object) ['id_kecamatan' => 3, 'nama_kecamatan' => 'Kecamatan Citeureup'],
            (object) ['id_kecamatan' => 30, 'nama_kecamatan' => 'Kecamatan Dramaga'],
            (object) ['id_kecamatan' => 2, 'nama_kecamatan' => 'Kecamatan Gunung Putri'],
            (object) ['id_kecamatan' => 11, 'nama_kecamatan' => 'Kecamatan Gunung Sindur'],
            (object) ['id_kecamatan' => 19, 'nama_kecamatan' => 'Kecamatan Jasinga'],
            (object) ['id_kecamatan' => 6, 'nama_kecamatan' => 'Kecamatan Jonggol'],
            (object) ['id_kecamatan' => 12, 'nama_kecamatan' => 'Kecamatan Kemang'],
            (object) ['id_kecamatan' => 32, 'nama_kecamatan' => 'Kecamatan Klapanunggal'],
            (object) ['id_kecamatan' => 14, 'nama_kecamatan' => 'Kecamatan Leuwiliang'],
            (object) ['id_kecamatan' => 39, 'nama_kecamatan' => 'Kecamatan Leuwisadeng'],
            (object) ['id_kecamatan' => 26, 'nama_kecamatan' => 'Kecamatan Megamendung'],
            (object) ['id_kecamatan' => 21, 'nama_kecamatan' => 'Kecamatan Nanggung'],
            (object) ['id_kecamatan' => 17, 'nama_kecamatan' => 'Kecamatan Pamijahan'],
            (object) ['id_kecamatan' => 10, 'nama_kecamatan' => 'Kecamatan Parung'],
            (object) ['id_kecamatan' => 20, 'nama_kecamatan' => 'Kecamatan Parung Panjang'],
            (object) ['id_kecamatan' => 34, 'nama_kecamatan' => 'Kecamatan Rancabungur'],
            (object) ['id_kecamatan' => 18, 'nama_kecamatan' => 'Kecamatan Rumpin'],
            (object) ['id_kecamatan' => 35, 'nama_kecamatan' => 'Kecamatan Sukajaya'],
            (object) ['id_kecamatan' => 9, 'nama_kecamatan' => 'Kecamatan Sukamakmur'],
            (object) ['id_kecamatan' => 4, 'nama_kecamatan' => 'Kecamatan Sukaraja'],
            (object) ['id_kecamatan' => 37, 'nama_kecamatan' => 'Kecamatan Tajurhalang'],
            (object) ['id_kecamatan' => 31, 'nama_kecamatan' => 'Kecamatan Tamansari'],
            (object) ['id_kecamatan' => 36, 'nama_kecamatan' => 'Kecamatan Tanjungsari'],
            (object) ['id_kecamatan' => 23, 'nama_kecamatan' => 'Kecamatan Tenjo'],
            (object) ['id_kecamatan' => 40, 'nama_kecamatan' => 'Kecamatan Tenjolaya'],
        ]);
    }

    public function updateFace(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        $validated = $request->validate([
            'face_descriptor' => ['required', 'string'],
            'foto_wajah' => ['nullable', 'string'],
        ]);

        $fotoWajahPath = null;
        if (!empty($validated['foto_wajah'])) {
            $imageParts = explode(';base64,', $validated['foto_wajah']);
            if (count($imageParts) == 2) {
                $imageTypeAux = explode('image/', $imageParts[0]);
                $imageType = $imageTypeAux[1] ?? 'jpeg';
                $imageBase64 = base64_decode($imageParts[1]);
                $fileName = 'face_scan_' . $pegawai->id_pegawai . '_' . time() . '.' . $imageType;
                
                \Storage::disk('public')->put('pegawai/' . $fileName, $imageBase64);
                $fotoWajahPath = 'pegawai/' . $fileName;
            }
        }

        DB::table('sirapi_md_pegawai')
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->update([
                'face_descriptor' => $validated['face_descriptor'],
                'foto_wajah' => $fotoWajahPath,
                'foto' => $fotoWajahPath,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Data wajah berhasil didaftarkan.']);
    }

    public function getRegisteredFaces()
    {
        $pegawai = DB::table('sirapi_md_pegawai')
            ->where('status_verifikasi', Pegawai::STATUS_AKTIF)
            ->whereNotNull('face_descriptor')
            ->select('id_pegawai', 'nama_pegawai', 'face_descriptor', 'foto_wajah', 'foto')
            ->get()
            ->map(function ($p) {
                $fotoPath = $p->foto_wajah ?: $p->foto;
                $cleanPath = $fotoPath ? ltrim(str_replace('\\', '/', $fotoPath), '/') : null;
                if ($cleanPath && str_starts_with($cleanPath, 'storage/')) {
                    $cleanPath = substr($cleanPath, 8);
                }
                return [
                    'id_pegawai' => $p->id_pegawai,
                    'nama_pegawai' => $p->nama_pegawai,
                    'face_descriptor' => $p->face_descriptor,
                    'foto_url' => $cleanPath ? route('storage.media', ['path' => $cleanPath]) : null,
                ];
            });

        return response()->json($pegawai);
    }

    public function simpanPresensiFace(Request $request)
    {
        $validated = $request->validate([
            'id_pegawai' => 'required|integer',
            'id_agenda' => 'required|integer',
        ]);

        $pegawai = DB::table('sirapi_md_pegawai')->where('id_pegawai', $validated['id_pegawai'])->first();
        $agenda = Agenda::find($validated['id_agenda']);

        if (! $pegawai || ! $agenda) {
            return response()->json(['success' => false, 'message' => 'Data tidak valid.'], 400);
        }

        if (($pegawai->status_verifikasi ?? 'aktif') !== Pegawai::STATUS_AKTIF) {
            return response()->json([
                'success' => false,
                'message' => 'Presensi ditolak. Akun pegawai Anda belum aktif atau belum diverifikasi oleh Administrator.'
            ], 403);
        }

        if ($agenda->status_label === Agenda::STATUS_MENDATANG) {
            return response()->json([
                'success' => false,
                'message' => 'Presensi belum dibuka. Agenda rapat baru dimulai pada pukul ' . (substr((string) $agenda->waktu, 0, 5) ?: '-') . ' WIB.'
            ], 400);
        }

        if ($agenda->status_label === Agenda::STATUS_SELESAI) {
            return response()->json(['success' => false, 'message' => 'Agenda rapat telah selesai. Presensi sudah ditutup.'], 400);
        }

        if (! $agenda->canPegawaiPresensi($pegawai)) {
            return response()->json([
                'success' => false,
                'message' => 'Presensi ditolak. Agenda surat masuk ini hanya dikhususkan untuk pegawai yang ditugaskan (' . ($agenda->ditugaskan ?: '-') . ').'
            ], 403);
        }

        $pegawaiModel = Pegawai::find($pegawai->id_pegawai);
        if ($pegawaiModel) {
            Auth::guard('pegawai')->login($pegawaiModel);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }
        }

        $fotoScanPath = null;
        if ($request->filled('foto_scan')) {
            try {
                $imageParts = explode(';base64,', (string) $request->input('foto_scan'));
                $imageTypeAux = explode('image/', $imageParts[0] ?? '');
                $imageType = $imageTypeAux[1] ?? 'jpeg';
                if (isset($imageParts[1])) {
                    $imageBase64 = base64_decode($imageParts[1]);
                    $fileName = 'presensi_face_' . $agenda->id_agenda . '_' . $pegawai->id_pegawai . '_' . time() . '.' . $imageType;
                    Storage::disk('public')->put('presensi/' . $fileName, $imageBase64);
                    $fotoScanPath = 'presensi/' . $fileName;
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal menyimpan foto scan wajah: ' . $e->getMessage());
            }
        }

        if (! $fotoScanPath) {
            $fotoScanPath = $pegawai->foto_wajah ?: $pegawai->foto;
        }

        $kehadiran = $this->kehadiranPegawai($agenda->id_agenda, $pegawai->email);
        if ($kehadiran) {
            // Update foto bukti kehadiran dari hasil scan kamera saat ini jika tersedia
            if ($fotoScanPath) {
                DB::table('sirapi_md_kehadiran')
                    ->where('id_kehadiran', $kehadiran->id_kehadiran)
                    ->update([
                        'foto_kehadiran' => $fotoScanPath,
                        'lokasi_presensi' => $request->input('lokasi_presensi') ?: $kehadiran->lokasi_presensi,
                        'updated_at' => Carbon::now(self::TIMEZONE),
                    ]);
            }

            $waktuHadir = $kehadiran->created_at ? Carbon::parse($kehadiran->created_at)->timezone(self::TIMEZONE)->format('H:i') : null;
            $pesan = "Presensi Wajah berhasil untuk {$pegawai->nama_pegawai}!";
            if ($request->hasSession()) {
                $request->session()->put('presensi_sukses_' . $agenda->id_agenda, [
                    'id_pegawai' => $pegawai->id_pegawai,
                ]);
                $request->session()->put('last_id_kehadiran_' . $agenda->id_agenda, $kehadiran->id_kehadiran);
                $request->session()->flash('success', $pesan);
            }

            return response()->json([
                'success' => true,
                'already_present' => true,
                'message' => $pesan,
                'redirect_url' => route('publik.presensi.pegawai', ['agenda_id' => $agenda->id_agenda]),
            ]);
        }

        if ($agenda->isKuotaPenuh()) {
            return response()->json([
                'success' => false,
                'message' => 'Presensi ditolak karena kuota peserta agenda ini sudah penuh.'
            ], 403);
        }

        $this->catatKehadiranPegawai(
            $agenda,
            $pegawai,
            'Hadir lewat Scan Wajah (Face Recognition)',
            $request->input('lokasi_presensi'),
            $fotoScanPath
        );

        $newKehadiran = $this->kehadiranPegawai($agenda->id_agenda, $pegawai->email);

        if ($request->hasSession()) {
            $request->session()->put('presensi_sukses_' . $agenda->id_agenda, [
                'id_pegawai' => $pegawai->id_pegawai,
            ]);
            if ($newKehadiran) {
                $request->session()->put('last_id_kehadiran_' . $agenda->id_agenda, $newKehadiran->id_kehadiran);
            }
            $request->session()->flash('success', "Presensi berhasil dicatat untuk {$pegawai->nama_pegawai}!");
        }

        return response()->json([
            'success' => true,
            'message' => "Presensi Wajah berhasil untuk {$pegawai->nama_pegawai}!",
            'redirect_url' => route('publik.presensi.pegawai', ['agenda_id' => $agenda->id_agenda]),
        ]);
    }

    private function catatKehadiranPegawai(Agenda $agenda, mixed $pegawai, string $catatan = 'Presensi pegawai otomatis', ?string $lokasiPresensi = null, ?string $fotoKehadiran = null): void
    {
        $now = Carbon::now(self::TIMEZONE);
        $nama = is_object($pegawai) ? ($pegawai->nama_pegawai ?? $pegawai->nama ?? '-') : '-';
        $jabatan = is_object($pegawai) ? ($pegawai->jabatan ?? '-') : '-';
        $instansi = is_object($pegawai) ? ($pegawai->bidang ?? 'Diskominfo Kabupaten Bogor') : 'Diskominfo Kabupaten Bogor';
        $noHp = is_object($pegawai) ? ($pegawai->nomor_hp ?? '-') : '-';
        $email = is_object($pegawai) ? ($pegawai->email ?? '') : '';
        $lokasi = $lokasiPresensi ?: request()->input('lokasi_presensi');
        if (! $lokasi) {
            $lokasi = $agenda->lokasi ?: 'Dinas Komunikasi dan Informasi Kabupaten Bogor, Jalan Tegar Beriman, Pakansari, Cibinong, Bogor, Jawa Barat';
        }
        $fotoBukti = $fotoKehadiran ?: request()->input('foto_kehadiran');
        if (! $fotoBukti) {
            $fotoBukti = is_object($pegawai) ? ($pegawai->foto_wajah ?? ($pegawai->foto ?? null)) : null;
        }

        DB::transaction(function () use ($agenda, $nama, $jabatan, $instansi, $noHp, $email, $now, $catatan, $lokasi, $fotoBukti) {
            $pesertaData = [
                'nama' => $nama,
                'jabatan' => $jabatan ?: '-',
                'instansi' => $instansi ?: 'Diskominfo Kabupaten Bogor',
                'jenis_peserta' => 'pegawai',
                'nomor_hp' => $noHp ?: '-',
                'email' => $email,
                'updated_at' => $now,
            ];

            $peserta = DB::table('sirapi_md_peserta')
                ->where('email', $email)
                ->where('jenis_peserta', 'pegawai')
                ->first();

            if ($peserta) {
                DB::table('sirapi_md_peserta')
                    ->where('id_peserta', $peserta->id_peserta)
                    ->update($pesertaData);
                $idPeserta = $peserta->id_peserta;
            } else {
                $pesertaData['created_at'] = $now;
                $idPeserta = DB::table('sirapi_md_peserta')->insertGetId($pesertaData);
            }

            $existingKehadiran = DB::table('sirapi_md_kehadiran')
                ->where('id_agenda', $agenda->id_agenda)
                ->where('id_peserta', $idPeserta)
                ->first();

            // Pastikan jam presensi asli (created_at) dan logbook tidak tertimpa
            if (! $existingKehadiran) {
                $idLog = DB::table('sirapi_md_logbook')->insertGetId([
                    'Id_agenda' => $agenda->id_agenda,
                    'catatan' => $catatan . ': ' . $nama . ' [Lokasi: ' . $lokasi . ']',
                    'waktu_isi' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('sirapi_md_kehadiran')->insert([
                    'id_agenda' => $agenda->id_agenda,
                    'id_peserta' => $idPeserta,
                    'id_log' => $idLog,
                    'lokasi_presensi' => $lokasi,
                    'foto_kehadiran' => $fotoBukti,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }

    // =========================================================================
    // PORTAL PEGAWAI: Dashboard, Pengajuan, Booking, History, Profil
    // =========================================================================

    /**
     * Helper untuk mengambil daftar ruang rapat sesuai instansi pegawai (dinas / kecamatan).
     */
    private function getRuangRapatForPegawai($pegawai)
    {
        $query = RuangRapat::withoutGlobalScopes();

        if ($pegawai->id_dinas) {
            $query->where('id_dinas', $pegawai->id_dinas);
        } elseif ($pegawai->id_kecamatan) {
            $query->where('id_kecamatan', $pegawai->id_kecamatan);
        }

        return $query->orderBy('nama_ruang')->get();
    }

    /**
     * Helper untuk mengambil query agenda sesuai instansi pegawai (dinas / kecamatan).
     */
    private function getAgendaQueryForPegawai($pegawai)
    {
        $query = Agenda::query();

        if ($pegawai->id_dinas) {
            $query->where(function ($q) use ($pegawai) {
                $q->where('id_dinas', $pegawai->id_dinas)
                  ->orWhereHas('ruangRapat', function ($rq) use ($pegawai) {
                      $rq->where('id_dinas', $pegawai->id_dinas);
                  });
            });
        } elseif ($pegawai->id_kecamatan) {
            $query->where(function ($q) use ($pegawai) {
                $q->where('id_kecamatan', $pegawai->id_kecamatan)
                  ->orWhereHas('ruangRapat', function ($rq) use ($pegawai) {
                      $rq->where('id_kecamatan', $pegawai->id_kecamatan);
                  });
            });
        }

        return $query;
    }

    public function dashboard(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        // Hitung total rapat yang diikuti pegawai ini
        $peserta = DB::table('sirapi_md_peserta')
            ->where('email', $pegawai->email)
            ->first();

        $totalRapatDiikuti = $peserta
            ? DB::table('sirapi_md_kehadiran')->where('id_peserta', $peserta->id_peserta)->count()
            : 0;

        // Ruang Rapat & Agenda mendatang sesuai instansi pegawai
        $ruangList = $this->getRuangRapatForPegawai($pegawai);
        $agendaMendatang = $this->getAgendaQueryForPegawai($pegawai)
            ->with('ruangRapat')
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->take(6)
            ->get();

        return view('pegawai.dashboard.index', compact(
            'pegawai',
            'totalRapatDiikuti',
            'ruangList',
            'agendaMendatang'
        ));
    }

    public function bookingRuang(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        $ruangList = $this->getRuangRapatForPegawai($pegawai);

        // Ambil agenda 30 hari ke depan & 7 hari ke belakang untuk kalender
        $startDate = now()->subDays(7)->toDateString();
        $endDate = now()->addDays(30)->toDateString();

        $agendas = $this->getAgendaQueryForPegawai($pegawai)
            ->with('ruangRapat')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();

        // Group by date for the JS calendar
        $agendaByDate = [];

        foreach ($agendas as $agenda) {
            $dateKey = \Carbon\Carbon::parse($agenda->tanggal)->format('Y-m-d');
            $agendaByDate[$dateKey][] = [
                'id_agenda' => 'agenda_' . $agenda->id_agenda,
                'nama_agenda' => $agenda->nama_agenda,
                'tanggal' => $dateKey,
                'waktu' => $agenda->waktu ? \Carbon\Carbon::parse($agenda->waktu)->format('H:i') : null,
                'waktu_selesai' => $agenda->waktu_selesai ? \Carbon\Carbon::parse($agenda->waktu_selesai)->format('H:i') : null,
                'id_ruangrapat' => $agenda->id_ruangrapat,
                'nama_ruang' => $agenda->ruangRapat?->nama_ruang,
                'status_label' => 'Agenda Resmi',
                'status_badge' => 'disetujui',
            ];
        }

        return view('pegawai.booking.index', compact('pegawai', 'ruangList', 'agendaByDate'));
    }

    public function historyRapat(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        $historyList = DB::table('sirapi_md_kehadiran as k')
            ->join('sirapi_md_peserta as p', 'k.id_peserta', '=', 'p.id_peserta')
            ->join('sirapi_md_agenda as a', 'k.id_agenda', '=', 'a.id_agenda')
            ->leftJoin('sirapi_md_ruangrapat as r', 'a.id_ruangrapat', '=', 'r.id_ruangrapat')
            ->leftJoin('sirapi_md_pegawai as peg', 'p.email', '=', 'peg.email')
            ->where('p.email', $pegawai->email)
            ->select(
                'a.nama_agenda',
                'a.tanggal',
                'a.waktu',
                'r.nama_ruang',
                'k.lokasi_presensi',
                DB::raw('COALESCE(k.foto_kehadiran, peg.foto_wajah, peg.foto) as foto_kehadiran'),
                'k.created_at as waktu_presensi'
            )
            ->orderByDesc('a.tanggal')
            ->orderByDesc('a.waktu')
            ->get();

        return view('pegawai.history.index', compact('pegawai', 'historyList'));
    }

    public function profilPegawai(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        return view('pegawai.profil.index', compact('pegawai'));
    }
}
