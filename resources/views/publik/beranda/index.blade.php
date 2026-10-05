<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Publik - RAPID</title>
    @include('publik.layout.theme_script')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'ijo-tua': '#35635b',
                        'ijo-semitua': '#2b4f49',
                        'ijo-muda': '#4e857b',
                        'ijo-sangatmuda': '#e3eeea',
                        'oren-utama': '#D89B3C',
                        'biru-muda': '#DCEEF5',
                        'biru-tua': '#1E6E8C',
                        'oren-muda': '#FBEBD1',
                        'oren-tua': '#B87A1E',
                    }
                }
            }
        }
    </script>
    <!-- Leaflet CSS & JS for GIS Map Sebaran -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        html {
            scroll-behavior: smooth;
        }
        .leaflet-popup-content-wrapper {
            border-radius: 16px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.18) !important;
            background-color: #ffffff !important;
            color: #111827 !important;
        }
        .leaflet-popup-content {
            margin: 14px 16px !important;
            line-height: 1.4 !important;
            color: #111827 !important;
        }
        .leaflet-popup-content h4,
        .leaflet-popup-content h5 {
            color: #111827 !important;
        }
        .leaflet-popup-content p {
            color: #374151 !important;
        }
        .leaflet-container a.leaflet-popup-btn {
            color: #ffffff !important;
            text-decoration: none !important;
        }
        .leaflet-container a.leaflet-popup-btn:hover {
            color: #ffffff !important;
            opacity: 0.92;
        }
        .leaflet-container a.leaflet-popup-close-button {
            color: #4b5563 !important;
            padding: 6px 8px 0 0 !important;
        }
        .leaflet-container a.leaflet-popup-close-button:hover {
            color: #111827 !important;
        }

        /* ===== HERO BANNER FULL-WIDTH BREAKOUT ===== */
        .hero-fullwidth-breakout {
            width: 100%;
            position: relative;
            box-sizing: border-box;
        }

        /* ===== HERO BUTTON: Primary (Lihat Agenda) ===== */
        .hero-btn-primary {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            font-size: 0.9rem;
            font-weight: 800;
            color: #ffffff;
            background: linear-gradient(135deg, #35635b 0%, #2b4f49 50%, #1e3934 100%);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 9999px;
            cursor: pointer;
            text-decoration: none;
            overflow: hidden;
            transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s ease;
            box-shadow: 0 4px 20px rgba(53,99,91,0.45), 0 0 0 0 rgba(255,255,255,0);
            letter-spacing: 0.01em;
        }
        .hero-btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.22) 50%, transparent 60%);
            background-size: 200% 100%;
            background-position: -100% 0;
            transition: background-position 0.5s ease;
            border-radius: inherit;
        }
        .hero-btn-primary:hover::before {
            background-position: 200% 0;
        }
        .hero-btn-primary:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 8px 32px rgba(53,99,91,0.6), 0 0 0 4px rgba(255,255,255,0.12);
        }
        .hero-btn-primary:active {
            transform: translateY(0px) scale(0.98);
        }

        /* ===== HERO BUTTON: Ghost (Peserta Magang) ===== */
        .hero-btn-ghost {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 13px 32px;
            font-size: 0.9rem;
            font-weight: 800;
            color: #ffffff;
            background: rgba(255,255,255,0.08);
            border: 1.5px solid rgba(255,255,255,0.35);
            border-radius: 9999px;
            cursor: pointer;
            text-decoration: none;
            overflow: hidden;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), box-shadow 0.25s ease, background 0.25s ease, border-color 0.25s ease;
            box-shadow: 0 2px 12px rgba(0,0,0,0.25), inset 0 1px 0 rgba(255,255,255,0.15);
            letter-spacing: 0.01em;
        }
        .hero-btn-ghost::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.12) 50%, transparent 60%);
            background-size: 200% 100%;
            background-position: -100% 0;
            transition: background-position 0.5s ease;
            border-radius: inherit;
        }
        .hero-btn-ghost:hover::before {
            background-position: 200% 0;
        }
        .hero-btn-ghost:hover {
            transform: translateY(-3px) scale(1.03);
            background: rgba(255,255,255,0.16);
            border-color: rgba(255,255,255,0.55);
            box-shadow: 0 8px 28px rgba(0,0,0,0.3), 0 0 0 4px rgba(255,255,255,0.06), inset 0 1px 0 rgba(255,255,255,0.2);
        }
        .hero-btn-ghost:active {
            transform: translateY(0px) scale(0.98);
        }

        /* ===== HERO FLOATING BADGE ===== */
        @keyframes hero-badge-pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.85; }
        }
        .hero-live-badge {
            animation: hero-badge-pulse 2.5s ease-in-out infinite;
        }

        /* ===== HERO CONTENT FADE-IN ===== */
        @keyframes heroFadeUp {
            from { opacity: 0; transform: translateY(45px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .hero-fade-up-1 { animation: heroFadeUp 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .hero-fade-up-2 { animation: heroFadeUp 1.2s 0.25s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .hero-fade-up-3 { animation: heroFadeUp 1.2s 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .hero-fade-up-4 { animation: heroFadeUp 1.2s 0.75s cubic-bezier(0.16, 1, 0.3, 1) both; }

        /* ===== SCROLL REVEAL ANIMATION ===== */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(55px);
            transition: opacity 1.3s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 1.3s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .scroll-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        /* Stagger children inside grid cards */
        .scroll-reveal-child {
            opacity: 0;
            transform: translateY(35px);
            transition: opacity 1.0s cubic-bezier(0.16, 1, 0.3, 1),
                        transform 1.0s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .scroll-reveal-child.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-[#F8F7F4] dark:bg-[#0d1614] font-sans antialiased text-gray-800 dark:text-slate-100 flex flex-col min-h-screen transition-colors duration-200 overflow-x-hidden">
    @include('publik.layout.navbarpublik')

    <main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 pt-6 sm:pt-8 space-y-16 sm:space-y-20">
        @php
            $agendaItems = collect($agendaBeranda ?? $agendaHariIni ?? [])->take(3);
            $agendaTerbaruItems = collect($agendaTerbaru ?? []);
            $beritaItems = collect($beritaTerbaru ?? []);
            $galeriItems = collect($galeri ?? []);
            $ulangTahunItems = collect($ulangTahun ?? []);
            $masukanItems = collect($masukan ?? []);
            $ulangTahunUtama = $ulangTahunHariIni ?? $ulangTahunItems->first();
            $infoItems = $agendaTerbaruItems->pluck('nama_agenda')->merge($beritaItems->pluck('judul'))->take(4);
            $initial = fn ($name) => collect(explode(' ', trim((string) $name)))->filter()->take(2)->map(fn ($word) => strtoupper(substr($word, 0, 1)))->join('') ?: 'DB';
            $imageUrl = function ($path, $fallback = 'assets/foto/Agendahariini.png') {
                if (! $path) {
                    return asset($fallback);
                }

                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    return $path;
                }

                $path = ltrim($path, '/');

                if (str_starts_with($path, 'assets/foto/') || str_starts_with($path, 'foto/') || str_starts_with($path, 'uploads/')) {
                    return asset($path);
                }

                return asset('storage/' . $path);
            };
            $statusClass = fn ($status) => match (strtolower((string) $status)) {
                'selesai' => 'bg-ijo-sangatmuda dark:bg-emerald-950/60 text-ijo-tua dark:text-emerald-300 dark:border dark:border-emerald-800/40',
                'diproses', 'proses' => 'bg-biru-muda dark:bg-sky-950/60 text-biru-tua dark:text-sky-300 dark:border dark:border-sky-800/40',
                default => 'bg-oren-muda dark:bg-amber-950/60 text-oren-tua dark:text-amber-300 dark:border dark:border-amber-800/40',
            };
            $maskEmail = function ($email) {
                if (! $email || ! str_contains($email, '@')) {
                    return '-';
                }

                [$local, $domain] = explode('@', $email, 2);
                $visible = substr($local, 0, min(2, strlen($local)));

                return $visible . '***@' . $domain;
            };
            $getFotoUrl = function($path) {
                if (empty($path) || $path === 'aduan/default.jpg') return null;
                if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
                $cleanPath = ltrim(str_replace('\\', '/', $path), '/');
                if (str_starts_with($cleanPath, 'storage/')) {
                    $cleanPath = substr($cleanPath, 8);
                }
                return route('storage.media', ['path' => $cleanPath]);
            };
            $aduanDetailItems = $masukanItems->mapWithKeys(fn ($aduan) => [
                $aduan->id_dataaduan => [
                    'nama_pengadu' => $aduan->nama_pengadu,
                    'email' => $maskEmail($aduan->email),
                    'dinas_nama' => $aduan->dinas?->nama_dinas ?? 'Dinas Komunikasi dan Informatika',
                    'isi_aduan' => $aduan->isi_aduan,
                    'balasan_admin' => $aduan->balasan_admin ?: 'Belum ada balasan dari admin.',
                    'status' => (strtolower((string) ($aduan->status ?? '')) === 'pending' || empty($aduan->status)) ? 'Menunggu' : $aduan->status,
                    'tanggal' => $aduan->created_at ? \Carbon\Carbon::parse($aduan->created_at)->translatedFormat('d F Y, H:i') : '-',
                    'foto_url' => $getFotoUrl($aduan->foto),
                ],
            ])->all();
        @endphp

        <!-- Header Grid (Ingin Bertemu Kami & Widget Cuaca) -->
        <div id="section-layanan-data" class="scroll-reveal grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
            <!-- Sisi Kiri: Banner Kunjungan -->
            <div class="lg:col-span-8 relative overflow-hidden bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <div>
                        <p class="text-ijo-tua dark:text-emerald-400 text-[10px] font-bold uppercase tracking-widest">Layanan Kunjungan Resmi</p>
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg md:text-xl leading-snug mt-0.5">Ingin Bertemu Kami?</h3>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1 max-w-lg leading-relaxed">
                            Ajukan pendaftaran kunjungan kerja, audiensi, atau konsultasi resmi dengan jajaran pimpinan Pemkab Bogor.
                        </p>
                    </div>
                </div>
                <a href="{{ route('publik.form-kunjungan') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-xs md:text-sm font-bold text-white bg-ijo-tua hover:bg-ijo-semitua dark:bg-[#107050] dark:hover:bg-[#0c5940] px-5 py-3 rounded-full transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 shrink-0 whitespace-nowrap">
                    <span>Isi Form Kunjungan</span>
                </a>
            </div>

            <!-- Sisi Kanan: Cuaca Minimalis (Tanpa Card Box) -->
            <button type="button" id="open-weather-modal" class="lg:col-span-4 flex items-center justify-between p-4 md:p-5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.04] transition-all text-left cursor-pointer group">
                <div class="flex-1 min-w-0 space-y-1">
                    <div class="flex items-center gap-1.5">
                        <p id="home-weather-location" class="text-[11px] font-bold text-ijo-tua dark:text-emerald-400 tracking-wider uppercase truncate">Cibinong, Kab. Bogor</p>
                    </div>
                    <div class="flex items-baseline gap-2.5">
                        <h2 id="home-weather-temp" class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white tracking-tight leading-none">-</h2>
                        <p id="home-weather-condition" class="text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-200 truncate">Memuat cuaca...</p>
                    </div>
                    <p id="home-weather-humidity" class="text-[10px] sm:text-[11px] font-medium text-gray-500 dark:text-gray-400 pt-0.5">
                        Kelembapan - &bull; Klik untuk detail
                    </p>
                </div>
                <div id="home-weather-icon" class="text-4xl md:text-5xl shrink-0 group-hover:scale-110 transition-transform duration-300 ml-3"></div>
            </button>
        </div>

        <!-- 3. Section Agenda Hari Ini -->
        <section class="scroll-reveal space-y-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white leading-tight truncate">{{ $agendaBerandaLabel ?? 'Agenda Hari Ini' }}</h3>
                </div>
                <a href="{{ route('publik.agenda') }}" class="shrink-0 inline-flex items-center gap-1.5 text-xs font-semibold text-ijo-tua dark:text-emerald-400 bg-ijo-sangatmuda dark:bg-emerald-950/50 border border-ijo-tua/20 dark:border-emerald-800/40 hover:bg-ijo-tua hover:text-white dark:hover:bg-emerald-800/50 px-3.5 py-1.5 rounded-full transition-all whitespace-nowrap">
                    <span class="hidden sm:inline">Selengkapnya</span>
                    <span class="sm:hidden">Lihat</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($agendaItems as $agenda)
                    @php
                        $isBerlangsung = $agenda->isBerlangsung();
                        $isSelesai = $agenda->isSelesai();
                    @endphp
                    <div class="rounded-xl p-5 flex flex-col justify-between space-y-4 shadow-md transition-all duration-300 relative overflow-hidden
                        {{ $isBerlangsung 
                            ? 'bg-gradient-to-br from-emerald-50/95 via-teal-50/90 to-white dark:from-[#132c25] dark:via-[#16382d] dark:to-[#12241f] border-2 border-emerald-500/90 dark:border-emerald-400/90 shadow-emerald-500/10 dark:shadow-emerald-950/40 ring-2 ring-emerald-500/20 dark:ring-emerald-400/30 scale-[1.01]' 
                            : ($isSelesai 
                                ? 'bg-gray-100/70 dark:bg-[#0d1715]/70 border border-gray-200/80 dark:border-[#1a2b27] opacity-80 hover:opacity-100' 
                                : 'bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] hover:shadow-xl hover:-translate-y-0.5') }}">
                        
                        @if ($isBerlangsung)
                            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>
                        @endif

                        <div class="space-y-2 relative z-10">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold {{ $isBerlangsung ? 'text-emerald-900 dark:text-emerald-300' : ($isSelesai ? 'text-gray-400 dark:text-gray-400' : 'text-gray-500 dark:text-gray-300') }}">
                                    {{ substr((string) $agenda->waktu, 0, 5) ?: '-' }} WIB
                                </span>

                                @if ($isBerlangsung)
                                    <span class="bg-emerald-600 text-white font-extrabold text-[10px] px-3 py-0.5 rounded-full inline-flex items-center space-x-1.5 shadow-xs">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                        </span>
                                        <span>Berlangsung</span>
                                    </span>
                                @elseif ($isSelesai)
                                    <span class="bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-300/60 dark:border-gray-700 font-medium text-[10px] px-2.5 py-0.5 rounded-full">
                                        Selesai
                                    </span>
                                @else
                                    <span class="bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-blue-200 dark:border-blue-800/40">
                                        {{ $agenda->status_label }}
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-1">
                                <p class="text-xs font-bold text-[#35635b] dark:text-emerald-400">{{ $agenda->dinas?->nama_dinas ?? 'Diskominfo Kab. Bogor' }}</p>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h4 class="font-bold text-sm leading-snug {{ $isBerlangsung ? 'text-emerald-950 dark:text-white' : ($isSelesai ? 'text-gray-600 dark:text-gray-400' : 'text-gray-900 dark:text-white') }}">
                                        {{ $agenda->nama_agenda }}
                                    </h4>
                                    @if (strtolower((string) ($agenda->kategori_surat ?? 'internal')) === 'internal')
                                        <span class="inline-flex items-center text-[9px] font-bold text-blue-700 dark:text-sky-300 bg-blue-50 dark:bg-sky-950/60 px-2 py-0.5 rounded-md border border-blue-100 dark:border-sky-800/40">Khusus Pegawai</span>
                                    @elseif (strtolower((string) ($agenda->kategori_surat ?? '')) === 'masuk')
                                        <span class="inline-flex items-center text-[9px] font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-md border border-amber-100 dark:border-amber-800/40">Pegawai Ditugaskan</span>
                                    @else
                                        <span class="inline-flex items-center text-[9px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md border border-emerald-100 dark:border-emerald-800/40">Pegawai & Tamu</span>
                                    @endif
                                </div>
                                <p class="text-xs {{ $isSelesai ? 'text-gray-400 dark:text-gray-400' : 'text-gray-500 dark:text-gray-300' }}">{{ $agenda->lokasi_display ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t {{ $isBerlangsung ? 'border-emerald-200 dark:border-emerald-800/50' : 'border-gray-100 dark:border-[#233a34]' }} pt-3 text-xs relative z-10">
                            @if (strtolower((string) ($agenda->kategori_surat ?? '')) !== 'masuk')
                                <span class="bg-oren-muda dark:bg-amber-950/60 text-oren-tua dark:text-amber-300 dark:border dark:border-amber-700/40 font-bold px-3 py-1 rounded-full text-[10px]">{{ $agenda->kuota ?? 0 }} Peserta</span>
                            @else
                                <span></span>
                            @endif
                            <a href="{{ route('publik.agenda.detail', $agenda->id_agenda) }}" class="{{ $isBerlangsung ? 'bg-emerald-600 hover:bg-emerald-700 text-white font-bold' : ($isSelesai ? 'bg-gray-200 dark:bg-[#1a2925] text-gray-600 dark:text-gray-300 hover:bg-gray-300' : 'bg-ijo-tua hover:bg-ijo-semitua dark:bg-[#1b3832] text-white dark:text-emerald-300') }} px-3.5 py-1 rounded-lg text-[10px] transition-colors">
                                {{ $isBerlangsung ? 'Ikuti / Detail' : 'Detail' }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-3 bg-white dark:bg-[#152420] rounded-xl p-8 text-center text-gray-500 dark:text-gray-400 text-sm border border-gray-100 dark:border-[#233a34]">
                        Belum ada agenda kegiatan.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 4. Section Peta Sebaran -->
        <section class="scroll-reveal space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white leading-tight">Peta Sebaran Agenda</h3>
                </div>
            </div>

            <!-- Card Container Peta -->
            <div class="w-full bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] rounded-xl p-4 md:p-6 shadow-lg space-y-4">
                <!-- Leaflet Container -->
                <div class="relative z-10 isolate w-full h-[480px] md:h-[550px] rounded-xl overflow-hidden border border-gray-200/80 dark:border-[#284c43] shadow-inner">
                    <!-- Map Search Bar Overlay (Top Right) -->
                    <div class="absolute top-3 right-3 left-3 sm:left-auto sm:w-80 z-20 pointer-events-auto">
                        <div class="relative">
                            <div class="relative flex items-center">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input type="text" id="map-search-input" placeholder="Cari Dinas / Kecamatan (contoh: Diskominfo, Cibinong)..." 
                                       class="w-full pl-9 pr-8 py-2.5 bg-white/95 dark:bg-[#152420]/95 backdrop-blur-md border border-gray-200/90 dark:border-[#284c43] rounded-xl text-xs font-medium text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 shadow-md focus:outline-none focus:ring-2 focus:ring-[#35635b] dark:focus:ring-emerald-500 transition-all">
                                <button type="button" id="map-search-clear" onclick="clearMapSearch()" class="hidden absolute right-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                    ✕
                                </button>
                            </div>

                            <!-- Search Results Dropdown -->
                            <div id="map-search-results" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white/95 dark:bg-[#152420]/95 backdrop-blur-md border border-gray-200/90 dark:border-[#284c43] rounded-xl shadow-xl max-h-60 overflow-y-auto z-30 divide-y divide-gray-100 dark:divide-[#233a34]">
                            </div>
                        </div>
                    </div>

                    <div id="beranda-map" class="w-full h-full z-0 bg-[#e5e3df]"></div>

                    <!-- Overlay Legend -->
                    <div class="absolute bottom-3 left-3 z-20 bg-white/95 dark:bg-[#152420]/95 backdrop-blur-md border border-gray-200/80 dark:border-[#233a34] rounded-xl p-3 shadow-lg text-[11px] space-y-2 pointer-events-auto">
                        <p class="font-bold text-gray-900 dark:text-white text-xs border-b border-gray-100 dark:border-[#284c43] pb-1.5">Keterangan Peta</p>
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 flex items-center justify-center shrink-0">
                                <span class="w-5 h-1.5 rounded-full bg-[#10b981] shadow-2xs inline-block" style="width: 18px; height: 5px; background-color: #10b981; border-radius: 9999px; display: inline-block;"></span>
                            </div>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">Batas Kecamatan Kab. Bogor</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 flex items-center justify-center shrink-0">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="w-3.5 h-3.5 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 relative overflow-hidden shadow-xs border border-white dark:border-[#152420]">
                                        <span class="absolute top-0.5 left-0.5 w-1.5 h-1 bg-white/70 rounded-full rotate-[-30deg] z-20"></span>
                                    </span>
                                    <span class="w-[2px] h-1.5 bg-gray-800 dark:bg-gray-300 -mt-[1px] rounded-b-xs"></span>
                                </div>
                            </div>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">Kantor Kecamatan</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 flex items-center justify-center shrink-0">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="w-3.5 h-3.5 rounded-full bg-gradient-to-br from-red-500 to-red-700 relative overflow-hidden shadow-xs border border-white dark:border-[#152420]">
                                        <span class="absolute top-0.5 left-0.5 w-1.5 h-1 bg-white/70 rounded-full rotate-[-30deg] z-20"></span>
                                    </span>
                                    <span class="w-[2px] h-1.5 bg-gray-800 dark:bg-gray-300 -mt-[1px] rounded-b-xs"></span>
                                </div>
                            </div>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">Kantor Dinas & Pemkab</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Section Berita Terkini -->
        <section class="scroll-reveal space-y-5">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white leading-tight">Berita Terkini</h3>
                </div>
                <a href="{{ route('publik.berita') }}" class="shrink-0 inline-flex items-center gap-1.5 text-xs font-semibold text-ijo-tua dark:text-emerald-400 bg-ijo-sangatmuda dark:bg-emerald-950/50 border border-ijo-tua/20 dark:border-emerald-800/40 hover:bg-ijo-tua hover:text-white dark:hover:bg-emerald-800/50 px-3.5 py-1.5 rounded-full transition-all whitespace-nowrap">
                    <span class="hidden sm:inline">Selengkapnya</span>
                    <span class="sm:hidden">Lihat</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @forelse ($beritaItems->take(3) as $berita)
                    <div class="group bg-white dark:bg-[#152420] rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 dark:border-[#233a34] flex flex-col transition-all duration-300 hover:-translate-y-1">
                        <!-- Image with overlay -->
                        <div class="relative h-44 overflow-hidden bg-gradient-to-br from-ijo-muda to-ijo-tua">
                            <div class="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-105" style="background-image: url('{{ $imageUrl($berita->gambar) }}')"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex items-center gap-1 bg-white/90 backdrop-blur-sm text-blue-700 font-bold text-[10px] px-2.5 py-1 rounded-full shadow">
                                    Berita
                                </span>
                            </div>
                            <div class="absolute bottom-3 left-3 right-3">
                                <p class="text-white/80 text-[10px] font-semibold">{{ $berita->tanggal?->translatedFormat('d F Y') ?? '-' }}</p>
                            </div>
                        </div>
                        <!-- Content -->
                        <div class="p-5 flex flex-col flex-grow space-y-3">
                            <h4 class="font-bold text-gray-900 dark:text-white text-sm leading-snug group-hover:text-ijo-tua dark:group-hover:text-emerald-400 transition-colors line-clamp-2">{{ $berita->judul }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2 flex-grow">{{ \Illuminate\Support\Str::limit(strip_tags($berita->isi_berita), 100) }}</p>
                            <a href="{{ route('publik.berita.detail', $berita->id_berita) }}" class="mt-auto inline-flex items-center gap-1.5 text-xs font-bold text-ijo-tua dark:text-emerald-400 hover:gap-2.5 transition-all">
                                Baca Selengkapnya
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-3 bg-white dark:bg-[#152420] rounded-2xl p-8 border border-gray-100 dark:border-[#233a34] text-sm font-medium text-gray-500 dark:text-gray-400 text-center">
                        Belum ada berita terkini di database.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 6. Section Daftar Aduan -->
        <section class="scroll-reveal space-y-5">
            <!-- Header -->
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white leading-tight">Daftar Aduan</h3>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('publik.riwayat-aduan') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-ijo-tua dark:text-emerald-400 bg-ijo-sangatmuda dark:bg-emerald-950/50 border border-ijo-tua/20 dark:border-emerald-800/40 hover:bg-ijo-tua hover:text-white dark:hover:bg-emerald-800/50 px-3.5 py-1.5 rounded-full transition-all whitespace-nowrap">
                        <span class="hidden sm:inline">Selengkapnya</span>
                        <span class="sm:hidden">Lihat</span>
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-[#152420] rounded-2xl border border-gray-100 dark:border-[#233a34] shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-gray-100/50 dark:from-[#0f1c19] dark:to-[#111f1c] text-gray-500 dark:text-gray-400 uppercase text-[10px] tracking-widest border-b border-gray-100 dark:border-[#233a34]">
                                <th class="px-5 py-3.5 font-extrabold">Nama Pengadu</th>
                                <th class="px-5 py-3.5 font-extrabold">Isi Aduan</th>
                                <th class="px-5 py-3.5 font-extrabold">Balasan Admin</th>
                                <th class="px-5 py-3.5 font-extrabold text-center">Status</th>
                                <th class="px-5 py-3.5 font-extrabold text-right">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-[#1d2e2b] font-medium text-gray-700 dark:text-gray-200">
                            @forelse ($masukanItems as $aduan)
                                <tr class="home-aduan-row cursor-pointer hover:bg-ijo-sangatmuda/40 dark:hover:bg-white/[0.03] transition-colors duration-150" data-aduan-id="{{ $aduan->id_dataaduan }}" title="Klik untuk melihat detail aduan">
                                    <td class="px-5 py-3.5 font-bold text-gray-900 dark:text-white">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-ijo-sangatmuda dark:bg-[#1b3832] text-ijo-tua dark:text-emerald-400 text-[10px] font-black flex items-center justify-center shrink-0 border border-ijo-tua/20 dark:border-emerald-700/30">{{ strtoupper(substr($aduan->nama_pengadu, 0, 1)) }}</div>
                                            <span class="truncate max-w-[120px] sm:max-w-none">{{ $aduan->nama_pengadu }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-500 dark:text-gray-300 max-w-[140px] sm:max-w-[180px]">
                                        <div class="flex items-center gap-1.5">
                                            @if(!empty($aduan->foto) && $aduan->foto !== 'aduan/default.jpg' && (file_exists(public_path('storage/' . $aduan->foto)) || \Illuminate\Support\Facades\Storage::disk('public')->exists($aduan->foto)))
                                                <span class="inline-flex items-center text-ijo-semitua dark:text-emerald-400 shrink-0" title="Memiliki lampiran foto">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </span>
                                            @endif
                                            <span class="line-clamp-1">{{ \Illuminate\Support\Str::limit($aduan->isi_aduan, 50) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-500 dark:text-gray-300 max-w-[140px] sm:max-w-[180px] hidden md:table-cell"><span class="line-clamp-1">{{ $aduan->balasan_admin ? \Illuminate\Support\Str::limit($aduan->balasan_admin, 50) : 'Belum ada balasan' }}</span></td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="{{ $statusClass($aduan->status) }} font-bold px-3 py-1 rounded-full text-[10px] whitespace-nowrap">{{ (strtolower((string) ($aduan->status ?? '')) === 'pending' || empty($aduan->status)) ? 'Menunggu' : $aduan->status }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-gray-400 dark:text-gray-400 whitespace-nowrap hidden sm:table-cell">{{ $aduan->created_at ? \Carbon\Carbon::parse($aduan->created_at)->translatedFormat('d M Y') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <p class="text-sm text-gray-400 dark:text-gray-500 font-medium">Belum ada aduan di database.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-3.5 bg-gray-50/60 dark:bg-[#0f1c19]/60 border-t border-gray-100 dark:border-[#233a34] flex items-center justify-between gap-3 flex-wrap">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Klik baris untuk melihat detail aduan</p>
                    <a href="{{ route('publik.masukan') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-oren-utama hover:bg-oren-tua dark:bg-[#d97706] dark:hover:bg-[#b45309] px-3.5 py-1.5 rounded-full transition-all shadow-xs hover:shadow-md hover:-translate-y-0.5">
                        <span>Tambah Aduan</span>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Modal Detail Aduan Publik di Beranda (Z-Index 2000 untuk menutupi sticky navbar z-1001) -->
    <div id="home-aduan-modal" class="fixed inset-0 z-[2000] hidden items-center justify-center bg-slate-950/70 backdrop-blur-md p-3 sm:p-6 overflow-y-auto transition-all duration-300">
        <div class="relative my-auto flex max-h-[92vh] sm:max-h-[85vh] w-full max-w-2xl flex-col rounded-3xl bg-white dark:bg-[#152420] text-gray-800 dark:text-slate-100 shadow-2xl overflow-hidden border border-gray-100 dark:border-[#233a34] transform transition-all animate-in fade-in zoom-in-95 duration-200">
            <!-- Header Modal -->
            <div class="bg-gradient-to-r from-ijo-tua to-ijo-semitua dark:from-[#0f1c19] dark:to-[#152420] text-white p-5 sm:p-6 flex items-start justify-between gap-4 border-b border-white/10 dark:border-[#233a34] shrink-0">
                <div class="space-y-1 min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white/15 dark:bg-emerald-950/80 text-emerald-100 dark:text-emerald-300 border border-white/20 dark:border-emerald-800/40">
                            Detail Aduan Publik
                        </span>
                    </div>
                    <h2 id="home-aduan-title" class="text-base sm:text-lg font-bold text-white leading-snug line-clamp-2 mt-1.5">-</h2>
                    <p id="home-aduan-date" class="text-xs text-emerald-100/80 dark:text-gray-300 flex items-center gap-1.5 pt-0.5">
                        <span>-</span>
                    </p>
                </div>
                <button type="button" id="home-aduan-close" onclick="closeHomeAduanModal()" class="shrink-0 w-9 h-9 rounded-2xl bg-white/10 hover:bg-white/20 dark:bg-white/5 dark:hover:bg-white/15 flex items-center justify-center text-white text-base font-bold transition cursor-pointer shadow-xs" title="Tutup Modal">
                    ✕
                </button>
            </div>

            <!-- Body Modal -->
            <div class="flex-1 min-h-0 overflow-y-auto p-5 sm:p-6 space-y-4 sm:space-y-5">
                <!-- Info Cards Grid (Nama, Email, Dinas, Status) -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                    <div class="rounded-2xl bg-gray-50/80 dark:bg-[#0f1c19] border border-gray-100 dark:border-[#233a34] p-4 space-y-1">
                        <p class="text-[10px] uppercase font-extrabold tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1">
                            <span>Nama Pengadu</span>
                        </p>
                        <p id="home-aduan-name" class="font-bold text-gray-900 dark:text-white truncate text-xs sm:text-sm">-</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50/80 dark:bg-[#0f1c19] border border-gray-100 dark:border-[#233a34] p-4 space-y-1">
                        <p class="text-[10px] uppercase font-extrabold tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1">
                            <span>Email</span>
                        </p>
                        <p id="home-aduan-email" class="font-bold text-gray-900 dark:text-white truncate text-xs sm:text-sm font-mono">-</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50/80 dark:bg-[#0f1c19] border border-gray-100 dark:border-[#233a34] p-4 space-y-1">
                        <p class="text-[10px] uppercase font-extrabold tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1">
                            <span>Dinas Tujuan</span>
                        </p>
                        <p id="home-aduan-dinas" class="font-bold text-ijo-tua dark:text-emerald-400 truncate text-xs sm:text-sm">-</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50/80 dark:bg-[#0f1c19] border border-gray-100 dark:border-[#233a34] p-4 space-y-1">
                        <p class="text-[10px] uppercase font-extrabold tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1">
                            <span>Status</span>
                        </p>
                        <div id="home-aduan-status-badge" class="mt-0.5">
                            <p id="home-aduan-status" class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm">-</p>
                        </div>
                    </div>
                </div>

                <!-- Card Isi Aduan -->
                <div class="rounded-2xl border border-gray-100 dark:border-[#233a34] bg-white dark:bg-[#0f1c19] p-5 space-y-2 shadow-2xs">
                    <p class="text-[10px] uppercase font-extrabold tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1.5">
                        <span>Isi Aduan / Masukan</span>
                    </p>
                    <p id="home-aduan-body" class="text-xs sm:text-sm leading-relaxed text-gray-700 dark:text-gray-200 whitespace-pre-line font-medium">-</p>
                </div>

                <!-- Card Lampiran Foto -->
                <div id="home-aduan-photo-container" class="hidden rounded-2xl border border-gray-100 dark:border-[#233a34] bg-white dark:bg-[#0f1c19] p-4 sm:p-5 space-y-2.5 shadow-2xs">
                    <p class="text-[10px] uppercase font-extrabold tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1.5">
                        <span>Lampiran Foto Aduan</span>
                    </p>
                    <button type="button" 
                            onclick="openPhotoModal(currentHomePhotoUrl, currentHomePhotoAuthor)" 
                            class="group relative w-full overflow-hidden rounded-xl bg-gray-50 dark:bg-[#0a1210] border border-gray-200/80 dark:border-[#284c43] flex items-center justify-center cursor-pointer transition hover:border-[#35635b] dark:hover:border-emerald-500/60 focus:outline-none"
                            title="Klik untuk melihat foto dalam ukuran penuh">
                        <img id="home-aduan-photo-img" src="" alt="Lampiran Foto Aduan" class="w-full max-h-72 sm:max-h-80 object-contain rounded-xl transition duration-300 group-hover:scale-[1.01]">
                        <div class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-black/65 group-hover:bg-black/80 backdrop-blur-xs text-white text-[11px] font-bold transition shadow-md pointer-events-none">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path></svg>
                            <span>Perbesar Foto</span>
                        </div>
                    </button>
                </div>

                <!-- Card Balasan Admin -->
                <div class="rounded-2xl bg-emerald-50/70 dark:bg-[#1a332d] border border-emerald-200/60 dark:border-[#284c43] p-5 space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] uppercase font-extrabold tracking-wider text-ijo-tua dark:text-emerald-400 flex items-center gap-1.5">
                            <span>Tanggapan Resmi Admin</span>
                        </p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300">
                            Diskominfo Bogor
                        </span>
                    </div>
                    <p id="home-aduan-reply" class="text-xs sm:text-sm leading-relaxed text-gray-800 dark:text-gray-100 whitespace-pre-line font-medium">-</p>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="px-5 sm:px-6 py-3 bg-gray-50/80 dark:bg-[#0f1c19] border-t border-gray-100 dark:border-[#233a34] flex items-center justify-between shrink-0">
                <span class="text-[11px] text-gray-400 dark:text-gray-400 flex items-center gap-1">
                    <span>Klik di luar area atau tekan ESC untuk menutup</span>
                </span>
                <button type="button" onclick="closeHomeAduanModal()" class="px-5 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 dark:bg-white/10 dark:hover:bg-white/20 text-gray-700 dark:text-gray-200 text-xs font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Preview Foto Lampiran Pop-Up (Z-Index 3000) -->
    <div id="photo-preview-modal" class="fixed inset-0 z-[3000] hidden items-center justify-center bg-black/80 backdrop-blur-xs p-3 sm:p-6 transition-all duration-300">
        <div class="relative w-full max-w-5xl rounded-2xl bg-white dark:bg-[#152420] shadow-2xl overflow-hidden flex flex-col max-h-[94vh] animate-in fade-in zoom-in-95 duration-200 border border-transparent dark:border-[#233a34]">
            <!-- Header Modal -->
            <div class="bg-ijo-tua dark:bg-[#0f1c19] text-white px-5 py-3.5 flex flex-wrap items-center justify-between gap-3 shrink-0 border-b border-white/10 dark:border-[#233a34]">
                <div class="flex items-center space-x-2.5">
                    <span class="text-base">🖼️</span>
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-white leading-tight">Lampiran Foto Aduan</h3>
                        <p id="modal-photo-author" class="text-[10px] text-white/70 dark:text-emerald-400">Pengadu: -</p>
                    </div>
                </div>

                <!-- Zoom Controls & Close Button -->
                <div class="flex items-center space-x-2">
                    <div class="flex items-center bg-white/10 dark:bg-white/5 rounded-lg p-1 space-x-1 border border-white/10 dark:border-[#284c43]">
                        <button type="button" onclick="zoomOut()" class="w-7 h-7 rounded-md bg-transparent hover:bg-white/20 text-white flex items-center justify-center text-xs font-bold transition cursor-pointer" title="Perkecil (Zoom Out)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                        </button>
                        <span id="zoom-level-badge" class="px-2 text-[11px] font-mono font-bold text-white min-w-[44px] text-center">100%</span>
                        <button type="button" onclick="zoomIn()" class="w-7 h-7 rounded-md bg-transparent hover:bg-white/20 text-white flex items-center justify-center text-xs font-bold transition cursor-pointer" title="Perbesar (Zoom In)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>
                        <button type="button" onclick="resetZoom()" class="px-2 h-7 rounded-md bg-transparent hover:bg-white/20 text-white flex items-center justify-center text-[10px] font-bold transition cursor-pointer" title="Reset Zoom">
                            Reset
                        </button>
                    </div>

                    <button type="button" onclick="closePhotoModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/25 text-white flex items-center justify-center text-sm font-bold transition cursor-pointer ml-1" title="Tutup">
                        ✕
                    </button>
                </div>
            </div>

            <!-- Konten Gambar (Lebar & Bersih dengan Drag/Pan Bebas ke Segala Arah) -->
            <div id="photo-container" class="relative p-4 sm:p-6 bg-[#161d1b] flex-1 flex items-center justify-center overflow-hidden min-h-[55vh] max-h-[76vh] select-none">
                <div class="transition-transform duration-100 ease-out origin-center flex items-center justify-center will-change-transform" id="zoom-wrapper">
                    <img id="modal-photo-img" 
                         src="" 
                         alt="Lampiran Foto Aduan" 
                         ondblclick="toggleZoom()"
                         class="max-h-[72vh] w-auto max-w-full object-contain rounded-lg shadow-lg cursor-grab transition-all">
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="px-5 py-3 bg-white dark:bg-[#152420] border-t border-gray-100 dark:border-[#233a34] flex flex-wrap items-center justify-between gap-3 shrink-0">
                <p class="text-[11px] text-gray-500 dark:text-gray-400 flex items-center space-x-1.5">
                    <span>💡</span>
                    <span>Gunakan tombol <span class="font-bold text-gray-700 dark:text-gray-200">Zoom</span> / Scroll mouse, lalu <span class="font-bold text-gray-700 dark:text-gray-200">drag (geser mouse)</span> bebas ke segala arah.</span>
                </p>
                <div class="flex items-center space-x-2.5">
                    <a id="modal-photo-download" href="#" target="_blank" download class="text-xs text-ijo-semitua dark:text-emerald-400 hover:text-ijo-tua dark:hover:text-emerald-300 font-bold px-3.5 py-2 rounded-lg hover:bg-ijo-sangatmuda/50 dark:hover:bg-white/5 border border-ijo-muda/30 dark:border-[#284c43] transition-colors flex items-center space-x-1.5">
                        <span>Unduh Gambar</span>
                    </a>
                    <button type="button" onclick="closePhotoModal()" class="bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 text-gray-700 dark:text-gray-200 text-xs font-bold px-4 py-2 rounded-lg transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Weather Modal (Z-Index 2000) -->
    <div id="weather-modal" class="fixed inset-0 z-[2000] hidden items-center justify-center bg-slate-950/70 backdrop-blur-md p-3 sm:p-4 overflow-y-auto">
        <div class="my-auto flex max-h-[calc(100dvh-1.5rem)] sm:max-h-[calc(100vh-2rem)] w-full max-w-2xl flex-col rounded-3xl bg-white dark:bg-[#152420] text-gray-800 dark:text-slate-100 shadow-2xl overflow-hidden border border-gray-100 dark:border-[#233a34]">
            <div class="bg-ijo-tua dark:bg-[#0f1c19] text-white p-5 sm:p-6 flex items-start justify-between gap-4 border-b border-transparent dark:border-[#233a34] shrink-0">
                <div>
                    <p class="text-xs uppercase tracking-wider text-white/70 dark:text-emerald-400 font-bold">Info Cuaca</p>
                    <h2 id="weather-location" class="text-lg sm:text-xl font-bold mt-1 text-white">Cibinong, Kabupaten Bogor</h2>
                    <p id="weather-updated" class="text-xs text-white/70 dark:text-gray-300 mt-1">Memuat data...</p>
                </div>
                <button type="button" id="close-weather-modal" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 dark:bg-white/5 dark:hover:bg-white/10 flex items-center justify-center text-lg font-bold cursor-pointer">x</button>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto p-5 sm:p-6 space-y-4 sm:space-y-5">
                <div id="weather-error" class="hidden rounded-2xl bg-oren-muda dark:bg-amber-950/50 text-oren-tua dark:text-amber-200 px-4 py-3 text-xs font-bold"></div>

                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">Cuaca Terkini</h3>
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-2xl bg-ijo-sangatmuda dark:bg-[#0f1c19] border border-transparent dark:border-[#233a34] p-5">
                            <p class="text-[10px] uppercase font-bold text-ijo-tua dark:text-emerald-400">Suhu</p>
                            <p id="weather-temp" class="text-3xl font-bold text-ijo-tua dark:text-emerald-400 mt-1">-</p>
                            <p id="weather-condition" class="text-xs text-gray-600 dark:text-gray-300 mt-1">-</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 dark:bg-[#0f1c19] border border-transparent dark:border-[#233a34] p-5">
                            <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-400">Kelembapan</p>
                            <p id="weather-humidity" class="text-2xl font-bold text-gray-900 dark:text-white mt-1">-</p>
                            <p id="weather-cloud" class="text-xs text-gray-500 dark:text-gray-300 mt-1">Awan -</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 dark:bg-[#0f1c19] border border-transparent dark:border-[#233a34] p-5">
                            <p class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-400">Angin</p>
                            <p id="weather-wind" class="text-2xl font-bold text-gray-900 dark:text-white mt-1">-</p>
                            <p id="weather-rain" class="text-xs text-gray-500 dark:text-gray-300 mt-1">Hujan -</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">Prakiraan 3 Hari</h3>
                    <div id="weather-daily" class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3"></div>
                </div>

                <p id="weather-source" class="text-[11px] text-gray-400">Sumber: Open-Meteo</p>
            </div>
        </div>
    </div>

    @include('publik.layout.footer')
    <script>
        const homeAduanDetails = @json($aduanDetailItems);
        const homeAduanModal = document.getElementById('home-aduan-modal');
        const homeAduanPhotoContainer = document.getElementById('home-aduan-photo-container');
        const homeAduanPhotoImg = document.getElementById('home-aduan-photo-img');
        const weatherModal = document.getElementById('weather-modal');
        const weatherOpen = document.getElementById('open-weather-modal');
        const weatherClose = document.getElementById('close-weather-modal');
        const weatherError = document.getElementById('weather-error');
        const weatherDaily = document.getElementById('weather-daily');
        let weatherLoaded = false;
        let currentHomePhotoUrl = '';
        let currentHomePhotoAuthor = '';
        let currentHomePhotoDate = '';

        function setHomeAduanText(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value || '-';
            }
        }

        function updateHomeAduanStatusBadge(statusText) {
            const badgeEl = document.getElementById('home-aduan-status-badge');
            if (!badgeEl) return;
            const st = (statusText || '').toLowerCase().trim();
            if (st === 'selesai') {
                badgeEl.innerHTML = `<span class="inline-flex items-center gap-1 bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 text-[11px] font-extrabold px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-800/40">Selesai</span>`;
            } else if (st === 'diproses' || st === 'proses') {
                badgeEl.innerHTML = `<span class="inline-flex items-center gap-1 bg-sky-100 dark:bg-sky-950/80 text-sky-800 dark:text-sky-300 text-[11px] font-extrabold px-2.5 py-1 rounded-full border border-sky-200 dark:border-sky-800/40">Diproses</span>`;
            } else {
                badgeEl.innerHTML = `<span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 text-[11px] font-extrabold px-2.5 py-1 rounded-full border border-amber-200 dark:border-amber-700/40">Menunggu</span>`;
            }
        }

        function closeHomeAduanModal() {
            if (homeAduanModal) {
                homeAduanModal.classList.add('hidden');
                homeAduanModal.classList.remove('flex');
            }
        }

        // Logic Modal Preview Foto Lampiran Aduan (Identik dengan Riwayat Aduan)
        const photoModal = document.getElementById('photo-preview-modal');
        const photoContainer = document.getElementById('photo-container');
        const modalPhotoImg = document.getElementById('modal-photo-img');
        const zoomWrapper = document.getElementById('zoom-wrapper');
        const zoomLevelBadge = document.getElementById('zoom-level-badge');
        const modalPhotoAuthor = document.getElementById('modal-photo-author');
        const modalPhotoDownload = document.getElementById('modal-photo-download');

        let currentZoom = 1;
        let translateX = 0;
        let translateY = 0;
        let isDragging = false;
        let startX = 0;
        let startY = 0;

        const minZoom = 1.0;
        const maxZoom = 3.5;
        const zoomStep = 0.25;

        function applyTransform() {
            if (!zoomWrapper) return;
            zoomWrapper.style.transform = `translate(${translateX}px, ${translateY}px) scale(${currentZoom})`;
            if (zoomLevelBadge) {
                zoomLevelBadge.textContent = `${Math.round(currentZoom * 100)}%`;
            }
            if (photoContainer) {
                if (currentZoom > 1) {
                    photoContainer.classList.add('cursor-grab');
                    if (isDragging) {
                        photoContainer.classList.add('cursor-grabbing');
                    } else {
                        photoContainer.classList.remove('cursor-grabbing');
                    }
                } else {
                    photoContainer.classList.remove('cursor-grab', 'cursor-grabbing');
                }
            }
        }

        function zoomIn() {
            if (currentZoom < maxZoom) {
                currentZoom = Math.min(maxZoom, Math.round((currentZoom + zoomStep) * 100) / 100);
                applyTransform();
            }
        }

        function zoomOut() {
            if (currentZoom > minZoom) {
                currentZoom = Math.max(minZoom, Math.round((currentZoom - zoomStep) * 100) / 100);
                if (currentZoom <= 1) {
                    translateX = 0;
                    translateY = 0;
                }
                applyTransform();
            }
        }

        function resetZoom() {
            currentZoom = 1;
            translateX = 0;
            translateY = 0;
            applyTransform();
        }

        function toggleZoom() {
            if (currentZoom === 1) {
                currentZoom = 2;
            } else {
                currentZoom = 1;
                translateX = 0;
                translateY = 0;
            }
            applyTransform();
        }

        // Drag / Pan Logic (Mouse)
        photoContainer?.addEventListener('mousedown', (e) => {
            if (e.button !== 0) return;
            if (currentZoom > 1) {
                isDragging = true;
                startX = e.clientX - translateX;
                startY = e.clientY - translateY;
                photoContainer.classList.add('cursor-grabbing');
                e.preventDefault();
            }
        });

        window.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            applyTransform();
        });

        window.addEventListener('mouseup', () => {
            if (isDragging) {
                isDragging = false;
                if (photoContainer) photoContainer.classList.remove('cursor-grabbing');
            }
        });

        // Touch Drag (Mobile / Tablet)
        photoContainer?.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1 && currentZoom > 1) {
                isDragging = true;
                startX = e.touches[0].clientX - translateX;
                startY = e.touches[0].clientY - translateY;
            }
        }, { passive: true });

        window.addEventListener('touchmove', (e) => {
            if (!isDragging || e.touches.length !== 1) return;
            translateX = e.touches[0].clientX - startX;
            translateY = e.touches[0].clientY - startY;
            applyTransform();
        }, { passive: true });

        window.addEventListener('touchend', () => {
            isDragging = false;
        });

        // Mouse Wheel Zoom
        photoContainer?.addEventListener('wheel', (e) => {
            e.preventDefault();
            if (e.deltaY < 0) {
                zoomIn();
            } else {
                zoomOut();
            }
        }, { passive: false });

        function openPhotoModal(imgSrc, authorName) {
            if (!photoModal || !modalPhotoImg || !imgSrc) return;
            resetZoom();
            modalPhotoImg.src = imgSrc;
            if (modalPhotoAuthor) {
                modalPhotoAuthor.textContent = `Pengadu: ${authorName || 'Anonim'}`;
            }
            if (modalPhotoDownload) {
                modalPhotoDownload.href = imgSrc;
            }
            photoModal.classList.remove('hidden');
            photoModal.classList.add('flex');
        }

        function closePhotoModal() {
            if (!photoModal) return;
            photoModal.classList.add('hidden');
            photoModal.classList.remove('flex');
            if (modalPhotoImg) modalPhotoImg.src = '';
            resetZoom();
        }

        photoModal?.addEventListener('click', (e) => {
            if (e.target === photoModal) {
                closePhotoModal();
            }
        });

        function openHomePhotoModal() {
            if (!currentHomePhotoUrl) return;
            openPhotoModal(currentHomePhotoUrl, currentHomePhotoAuthor);
        }

        document.querySelectorAll('.home-aduan-row').forEach((row) => {
            row.addEventListener('click', () => {
                const detail = homeAduanDetails[row.dataset.aduanId];

                if (!detail) {
                    return;
                }

                setHomeAduanText('home-aduan-title', detail.isi_aduan.length > 70 ? detail.isi_aduan.slice(0, 70) + '...' : detail.isi_aduan);
                setHomeAduanText('home-aduan-date', detail.tanggal);
                setHomeAduanText('home-aduan-name', detail.nama_pengadu);
                setHomeAduanText('home-aduan-email', detail.email);
                setHomeAduanText('home-aduan-dinas', detail.dinas_nama);
                updateHomeAduanStatusBadge(detail.status);
                setHomeAduanText('home-aduan-body', detail.isi_aduan);
                setHomeAduanText('home-aduan-reply', detail.balasan_admin);

                currentHomePhotoUrl = detail.foto_url || '';
                currentHomePhotoAuthor = detail.nama_pengadu || 'Anonim';
                currentHomePhotoDate = detail.tanggal || '-';

                if (detail.foto_url && homeAduanPhotoImg && homeAduanPhotoContainer) {
                    homeAduanPhotoImg.src = detail.foto_url;
                    homeAduanPhotoContainer.classList.remove('hidden');
                } else if (homeAduanPhotoContainer) {
                    homeAduanPhotoContainer.classList.add('hidden');
                    if (homeAduanPhotoImg) homeAduanPhotoImg.src = '';
                }

                homeAduanModal.classList.remove('hidden');
                homeAduanModal.classList.add('flex');
            });
        });

        homeAduanModal?.addEventListener('click', (event) => {
            if (event.target === homeAduanModal) {
                closeHomeAduanModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && homeAduanModal && !homeAduanModal.classList.contains('hidden')) {
                closeHomeAduanModal();
            }
            if (e.key === 'Escape' && photoModal && !photoModal.classList.contains('hidden')) {
                closePhotoModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const previewModal = document.getElementById('modal-preview-foto');
                if (previewModal && !previewModal.classList.contains('hidden')) {
                    if (typeof closeImagePreview === 'function') {
                        closeImagePreview();
                    }
                    return;
                }
                if (homeAduanModal && !homeAduanModal.classList.contains('hidden')) {
                    homeAduanModal.classList.add('hidden');
                    homeAduanModal.classList.remove('flex');
                }
                if (weatherModal && !weatherModal.classList.contains('hidden')) {
                    weatherModal.classList.add('hidden');
                    weatherModal.classList.remove('flex');
                }
            }
        });

        function formatWeatherValue(value, suffix = '') {
            return value === null || value === undefined || value === '' ? '-' : `${value}${suffix}`;
        }

        function setText(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        }

        function getWeatherIcon(code, conditionStr) {
            const codeNum = Number(code);
            if (codeNum === 0) return '☀️';
            if (codeNum === 1 || codeNum === 2) return '⛅';
            if (codeNum === 3) return '☁️';
            if ([45, 48].includes(codeNum)) return '🌫️';
            if ([51, 53, 55, 61, 63, 65, 66, 67, 80, 81, 82].includes(codeNum)) return '🌧️';
            if ([95, 96, 99].includes(codeNum)) return '⛈️';

            const cond = (conditionStr || '').toLowerCase();
            if (cond.includes('hujan') || cond.includes('gerimis')) return '🌧️';
            if (cond.includes('badai') || cond.includes('petir')) return '⛈️';
            if (cond.includes('mendung') || cond.includes('berawan')) return '☁️';
            if (cond.includes('terik') || cond.includes('cerah')) return '☀️';
            return '⛅';
        }

        async function loadWeather() {
            if (weatherLoaded) {
                return;
            }

            weatherLoaded = true;
            weatherError.classList.add('hidden');
            weatherDaily.innerHTML = '<div class="md:col-span-3 rounded-2xl bg-gray-50 dark:bg-[#0f1c19] border border-transparent dark:border-[#233a34] p-4 text-xs text-gray-500 dark:text-gray-400">Memuat data cuaca dari API...</div>';

            try {
                const response = await fetch('{{ route('publik.cuaca.api') }}', { headers: { 'Accept': 'application/json' } });
                const payload = await response.json();
                const current = payload.current || {};

                if (!payload.success && payload.message) {
                    weatherError.textContent = payload.message;
                    weatherError.classList.remove('hidden');
                }

                const icon = getWeatherIcon(current.weather_code, current.condition);

                setText('weather-location', payload.location || 'Cibinong, Kabupaten Bogor');
                setText('weather-updated', `Diperbarui: ${payload.updated_at || '-'}`);
                setText('weather-temp', formatWeatherValue(current.temperature, '°C'));
                setText('weather-condition', current.condition || '-');
                setText('weather-humidity', formatWeatherValue(current.humidity, '%'));
                setText('home-weather-location', payload.location || 'Cibinong, Kabupaten Bogor');
                setText('home-weather-temp', formatWeatherValue(current.temperature, '°C'));
                setText('home-weather-condition', current.condition || 'Data API belum tersedia');
                setText('home-weather-humidity', `Kelembapan ${formatWeatherValue(current.humidity, '%')} • Klik untuk detail `);
                setText('home-weather-icon', icon);
                setText('weather-cloud', `Awan ${formatWeatherValue(current.cloud_cover, '%')}`);
                setText('weather-wind', formatWeatherValue(current.wind_speed, ' km/jam'));
                setText('weather-rain', `Hujan ${formatWeatherValue(current.precipitation, ' mm')}`);
                setText('weather-source', `Sumber: ${payload.source || 'Open-Meteo'}${payload.attribution ? ' - ' + payload.attribution : ''}`);

                const daily = payload.daily || [];
                weatherDaily.innerHTML = daily.length
                    ? daily.map((item) => `
                        <div class="rounded-2xl bg-gray-50 dark:bg-[#0f1c19] border border-transparent dark:border-[#233a34] p-4">
                            <p class="text-[10px] font-bold uppercase text-gray-400 dark:text-gray-400">${item.date || '-'}</p>
                            <h4 class="mt-1 text-sm font-extrabold text-gray-900 dark:text-white">${getWeatherIcon(item.weather_code, item.condition)} ${item.condition || '-'}</h4>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-300">${formatWeatherValue(item.temperature_min, '°C')} - ${formatWeatherValue(item.temperature_max, '°C')}</p>
                            <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-400">Hujan ${formatWeatherValue(item.precipitation_sum, ' mm')}</p>
                        </div>
                    `).join('')
                    : '<div class="md:col-span-3 rounded-2xl bg-gray-50 dark:bg-[#0f1c19] border border-transparent dark:border-[#233a34] p-4 text-xs text-gray-500 dark:text-gray-400">Prakiraan harian belum tersedia.</div>';
            } catch (error) {
                weatherLoaded = false;
                weatherDaily.innerHTML = '<div class="md:col-span-3 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-transparent dark:border-red-900/40 p-4 text-xs text-red-600 dark:text-red-400">Gagal memuat API cuaca.</div>';
            }
        }

        weatherOpen?.addEventListener('click', () => {
            weatherModal.classList.remove('hidden');
            weatherModal.classList.add('flex');
            loadWeather();
        });

        loadWeather();

        weatherClose?.addEventListener('click', () => {
            weatherModal.classList.add('hidden');
            weatherModal.classList.remove('flex');
        });

        weatherModal?.addEventListener('click', (event) => {
            if (event.target === weatherModal) {
                weatherModal.classList.add('hidden');
                weatherModal.classList.remove('flex');
            }
        });

        // Initializing Map Sebaran Agenda & Kunjungan Kerja (Kabupaten Bogor)
        document.addEventListener('DOMContentLoaded', function initKunkerMap() {
            const mapContainer = document.getElementById('beranda-map');
            if (!mapContainer || typeof L === 'undefined') return;

            const centerLat = -6.55;
            const centerLng = 106.82;
            const defaultZoom = 10;

            const map = L.map('beranda-map', {
                center: [centerLat, centerLng],
                zoom: defaultZoom,
                zoomControl: true,
                scrollWheelZoom: false
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',
                maxNativeZoom: 19,
                maxZoom: 19
            }).addTo(map);

            let geojsonLayer;

            // Fetch Administrative GeoJSON of Kabupaten Bogor (40 Kecamatan)
            fetch("{{ asset('admin_kec.json') }}")
                .then(res => {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                })
                .then(data => {
                    geojsonLayer = L.geoJSON(data, {
                        style: function(feature) {
                            return {
                                fillColor: '#35635b',
                                weight: 1.5,
                                opacity: 0.7,
                                color: '#2b4f49',
                                dashArray: '2',
                                fillOpacity: 0.18
                            };
                        },
                        onEachFeature: function(feature, layer) {
                            const kecName = feature.properties?.NKEC || 'Kecamatan';
                            layer.bindTooltip(`
                                <div class="px-1.5 py-0.5 font-sans">
                                    <p class="font-extrabold text-xs text-gray-900">Kec. ${kecName}</p>
                                    <p class="text-[10px] text-gray-500">Kabupaten Bogor</p>
                                </div>
                            `, { sticky: true });

                            layer.on({
                                mouseover: function(e) {
                                    const l = e.target;
                                    l.setStyle({
                                        weight: 2.5,
                                        color: '#D89B3C',
                                        dashArray: '',
                                        fillOpacity: 0.5,
                                        fillColor: '#D89B3C'
                                    });
                                    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                                        l.bringToFront();
                                    }
                                },
                                mouseout: function(e) {
                                    if (geojsonLayer) geojsonLayer.resetStyle(e.target);
                                },
                                click: function(e) {
                                    map.fitBounds(e.target.getBounds());
                                }
                            });
                        }
                    }).addTo(map);
                })
                .catch(err => console.log('GeoJSON Map Notice:', err));

            let allMarkers = [];
            let searchableLocations = [];

            // Dynamic Pushpin Icon Sizer based on Zoom Level
            const getIconConfigForZoom = (zoom) => {
                if (zoom <= 8) return { headSize: 'w-[18px] h-[18px]', stickHeight: 'h-[10px]', width: 18, totalHeight: 28 };
                if (zoom === 9) return { headSize: 'w-[22px] h-[22px]', stickHeight: 'h-[12px]', width: 22, totalHeight: 34 };
                if (zoom === 10) return { headSize: 'w-[28px] h-[28px]', stickHeight: 'h-[15px]', width: 28, totalHeight: 43 };
                if (zoom === 11) return { headSize: 'w-[34px] h-[34px]', stickHeight: 'h-[18px]', width: 34, totalHeight: 52 };
                if (zoom === 12) return { headSize: 'w-[40px] h-[40px]', stickHeight: 'h-[21px]', width: 40, totalHeight: 61 };
                if (zoom === 13) return { headSize: 'w-[46px] h-[46px]', stickHeight: 'h-[24px]', width: 46, totalHeight: 70 };
                return { headSize: 'w-[52px] h-[52px]', stickHeight: 'h-[27px]', width: 52, totalHeight: 79 };
            };

            // Custom Leaflet Pushpin Marker Builder (Clean Glossy Sphere Ball + Needle Stick)
            const createCustomIcon = (bgClass = 'bg-gradient-to-br from-red-500 via-red-600 to-red-800', zoom = defaultZoom) => {
                const cfg = getIconConfigForZoom(zoom);
                return L.divIcon({
                    className: 'custom-pushpin-marker',
                    html: `
                        <div class="relative flex flex-col items-center justify-start transition-all duration-200 hover:scale-125 cursor-pointer drop-shadow-md group">
                            <!-- Round Clean Glossy Pushpin Ball -->
                            <div class="${cfg.headSize} rounded-full ${bgClass} shadow-lg border-2 border-white dark:border-[#152420] relative overflow-hidden shrink-0">
                                <!-- Glossy White Crescent Glare Arc -->
                                <span class="absolute top-[8%] left-[10%] w-[50%] h-[32%] bg-gradient-to-br from-white/90 via-white/40 to-transparent rounded-full rotate-[-35deg] pointer-events-none z-20"></span>
                            </div>
                            <!-- Pushpin Needle Stick Pointing Down to Coordinate -->
                            <div class="w-[3px] ${cfg.stickHeight} bg-gradient-to-b from-gray-900 via-gray-800 to-black rounded-b-full shadow-xs -mt-[1px]"></div>
                        </div>
                    `,
                    iconSize: [cfg.width, cfg.totalHeight],
                    iconAnchor: [cfg.width / 2, cfg.totalHeight],
                    popupAnchor: [0, -cfg.totalHeight]
                });
            };

            const updateAllMarkerSizes = () => {
                const currentZoom = map.getZoom();
                allMarkers.forEach(m => {
                    m.marker.setIcon(createCustomIcon(m.bgClass, currentZoom));
                });
            };

            map.on('zoomend', updateAllMarkerSizes);

            // Dynamic Agenda Markers from Database (Pre-resolved Coordinates)
            const agendaLocations = [
                @foreach ($agendaItems as $agenda)
                    {
                        nama: @json($agenda->nama_agenda),
                        lokasi: @json($agenda->lokasi_display ?? 'Cibinong, Kab. Bogor'),
                        waktu: @json(substr((string) $agenda->waktu, 0, 5) . ' WIB'),
                        kategori: @json(strtolower((string)($agenda->kategori_surat ?? 'internal'))),
                        detailUrl: @json(route('publik.agenda.detail', $agenda->id_agenda)),
                        lat: {{ (float)($agenda->gps_lat ?? -6.478846) }},
                        lng: {{ (float)($agenda->gps_long ?? 106.824738) }}
                    },
                @endforeach
            ];

            const attachedAgendas = [];
            let totalMapPoints = 0;
            const existingLocationKeys = new Set();

            function getCanonicalKey(name, lat, lng) {
                if (!name) return `coord:${lat.toFixed(3)},${lng.toFixed(3)}`;
                let str = name.toLowerCase()
                    .replace(/kantor|dinas|badan|satuan|kecamatan|kabupaten|bogor/g, '')
                    .replace(/[^a-z0-9]/g, '').trim();
                
                if (str.length >= 3) {
                    return 'name:' + str;
                }
                return `coord:${lat.toFixed(3)},${lng.toFixed(3)}`;
            }

            function sanitizeLat(raw) {
                if (!raw) return NaN;
                const str = String(raw).trim();
                const num = parseFloat(str);
                if (!isNaN(num) && num < 0 && num > -10) return num;
                const digits = str.replace(/[^0-9]/g, '');
                if (!digits) return NaN;
                return parseFloat(digits.replace(/^6/, '-6.'));
            }

            function sanitizeLong(raw) {
                if (!raw) return NaN;
                const str = String(raw).trim();
                const num = parseFloat(str);
                if (!isNaN(num) && num > 100 && num < 115) return num;
                const digits = str.replace(/[^0-9]/g, '');
                if (!digits) return NaN;
                if (digits.startsWith('106')) return parseFloat(digits.replace(/^106/, '106.'));
                if (digits.startsWith('107')) return parseFloat(digits.replace(/^107/, '107.'));
                return parseFloat('106.' + digits.replace(/^10/, ''));
            }

            function addMapMarker(name, addr, lat, lng, typeBadge, badgeClass, defaultBgClass, idDinas = null, idKecamatan = null) {
                if (isNaN(lat) || isNaN(lng) || lat === 0 || lng === 0) return;

                const locKey = getCanonicalKey(name, lat, lng);
                const coordKey = `coord:${lat.toFixed(3)},${lng.toFixed(3)}`;

                if (existingLocationKeys.has(locKey) || existingLocationKeys.has(coordKey)) {
                    return;
                }
                existingLocationKeys.add(locKey);
                existingLocationKeys.add(coordKey);

                totalMapPoints++;
                const nameLower = name.toLowerCase();
                const cleanKeyword = nameLower.replace('kantor', '').replace('camat', '').replace('bupati', '').replace('dinas', '').trim();

                const matchedAgendas = agendaLocations.filter(ag => {
                    if (idDinas && Number(ag.id_dinas) === Number(idDinas)) return true;
                    if (idKecamatan && Number(ag.id_kecamatan) === Number(idKecamatan)) return true;
                    const agLoc = ag.lokasi.toLowerCase();
                    const byName = cleanKeyword.length >= 3 && agLoc.includes(cleanKeyword);
                    const byCoord = Math.abs(ag.lat - lat) < 0.008 && Math.abs(ag.lng - lng) < 0.008;
                    return byName || byCoord;
                });

                const hasAgenda = matchedAgendas.length > 0;
                if (hasAgenda) {
                    matchedAgendas.forEach(ag => attachedAgendas.push(ag));
                }

                let bgClass = defaultBgClass;

                const currentZoom = map.getZoom();
                const marker = L.marker([lat, lng], {
                    icon: createCustomIcon(bgClass, currentZoom)
                }).addTo(map);

                allMarkers.push({ marker: marker, bgClass: bgClass });
                searchableLocations.push({
                    name: name,
                    addr: addr || 'Kabupaten Bogor',
                    lat: lat,
                    lng: lng,
                    typeBadge: typeBadge,
                    marker: marker
                });

                const safeName = name.replace(/'/g, "\\'");
                const safeAddr = (addr || '').replace(/'/g, "\\'");
                const safeBadge = (typeBadge || '').replace(/'/g, "\\'");

                let popupHtml = `
                    <div class="p-2 font-sans max-w-[250px]">
                        <span class="${badgeClass} text-[10px] font-extrabold px-2 py-0.5 rounded-full">${typeBadge}</span>
                        <h4 class="font-bold text-xs text-gray-900 mt-1.5 leading-snug" style="color: #111827 !important;">${name}</h4>
                        <p class="text-[11px] text-gray-700 font-medium mt-1 leading-relaxed" style="color: #374151 !important;">${addr}</p>
                `;

                if (hasAgenda) {
                    const topAg = matchedAgendas[0];
                    popupHtml += `
                        <div class="mt-2.5 pt-2 border-t border-gray-200">
                            <span class="bg-red-100 text-red-800 text-[9.5px] font-black px-2 py-0.5 rounded-full">Ada Agenda Hari Ini</span>
                            <h5 class="font-bold text-xs text-gray-900 mt-1 leading-tight" style="color: #111827 !important;">${topAg.nama}</h5>
                            <p class="text-[10px] text-gray-600 font-medium mt-0.5" style="color: #4b5563 !important;">${topAg.waktu}</p>
                        </div>
                    `;
                }

                popupHtml += `
                    <div class="flex items-center gap-2 mt-3 pt-2.5 border-t border-gray-200">
                        <a href="{{ route('publik.form-kunjungan') }}" style="color: #ffffff !important; text-decoration: none !important;" class="leaflet-popup-btn flex-1 text-center text-[10px] sm:text-[11px] font-bold text-white bg-[#35635b] hover:bg-[#2b4f49] px-2 py-1.5 rounded-lg transition-all shadow-xs">Form Kunjungan</a>
                        <button type="button" onclick="openLocationAgendasModal('${safeName}', '${safeAddr}', ${idDinas || 'null'}, ${idKecamatan || 'null'}, '${safeBadge}')" style="color: #ffffff !important;" class="leaflet-popup-btn flex-1 text-center text-[10px] sm:text-[11px] font-bold text-white bg-emerald-700 hover:bg-emerald-800 px-2 py-1.5 rounded-lg transition-all shadow-xs cursor-pointer">Agenda &rarr;</button>
                    </div>
                </div>`;

                marker.bindPopup(popupHtml);
            }

            // 1. Render Database Dinas Points
            const dbDinasList = @json($dinasListMap ?? []);
            dbDinasList.forEach(d => {
                const lat = sanitizeLat(d.gps_lat);
                const lng = sanitizeLong(d.gps_long);
                addMapMarker(
                    d.nama_dinas,
                    d.alamat || 'Kabupaten Bogor',
                    lat,
                    lng,
                    'Kantor Dinas / SKPD',
                    'bg-red-100 text-red-800',
                    'bg-gradient-to-br from-red-500 via-red-600 to-red-800',
                    d.id_dinas,
                    null
                );
            });

            // 2. Render Database Kecamatan Points
            const dbKecList = @json($kecamatanListMap ?? []);
            dbKecList.forEach(k => {
                const lat = sanitizeLat(k.gps_lat);
                const lng = sanitizeLong(k.gps_long);
                addMapMarker(
                    k.nama_kecamatan,
                    k.alamat_kantor || 'Kabupaten Bogor',
                    lat,
                    lng,
                    'Kantor Kecamatan',
                    'bg-orange-100 text-orange-800',
                    'bg-gradient-to-br from-amber-500 via-orange-500 to-orange-700',
                    null,
                    k.id_kecamatan
                );
            });

            // 3. Load app_md_lokasidinas.csv for full CSV Dinas coverage
            fetch("{{ asset('app_md_lokasidinas.csv') }}")
                .then(res => res.ok ? res.text() : '')
                .then(csvText => {
                    if (!csvText) return;
                    const lines = csvText.split('\n');
                    lines.slice(1).forEach(line => {
                        if (!line.trim()) return;
                        const parts = line.split(/,(?=(?:[^\"]*\"[^\"]*\")*[^\"]*$)/).map(p => p.replace(/^"|"$/g, '').trim());
                        if (parts.length >= 4) {
                            const lat = sanitizeLat(parts[2]);
                            const lng = sanitizeLong(parts[3]);
                            addMapMarker(
                                parts[0],
                                parts[1] || 'Kabupaten Bogor',
                                lat,
                                lng,
                                'Kantor Dinas / SKPD',
                                'bg-red-100 text-red-800',
                                'bg-gradient-to-br from-red-500 via-red-600 to-red-800'
                            );
                        }
                    });
                })
                .catch(err => console.log('CSV Dinas Notice:', err));

            // 4. Load app_md_mapgovpoint.csv for 41 Gov Points
            fetch("{{ asset('app_md_mapgovpoint.csv') }}")
                .then(res => res.ok ? res.text() : '')
                .then(csvText => {
                    if (csvText) {
                        const lines = csvText.split('\n');
                        lines.slice(1).forEach(line => {
                            if (!line.trim()) return;
                            const parts = line.split(';').map(p => p.replace(/"/g, '').trim());
                            if (parts.length >= 4) {
                                const name = parts[0];
                                const isCamat = name.toLowerCase().includes('camat');
                                const lat = sanitizeLat(parts[2]);
                                const lng = sanitizeLong(parts[3]);
                                addMapMarker(
                                    name,
                                    parts[1] || 'Kabupaten Bogor',
                                    lat,
                                    lng,
                                    isCamat ? 'Kantor Kecamatan' : 'Kantor Pemkab',
                                    isCamat ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800',
                                    isCamat
                                        ? 'bg-gradient-to-br from-amber-500 via-orange-500 to-orange-700'
                                        : 'bg-gradient-to-br from-red-500 via-red-600 to-red-800'
                                );
                            }
                        });
                    }

                    // Render remaining standalone agendas
                    agendaLocations.forEach(item => {
                        if (attachedAgendas.includes(item)) return;
                        const bgClass = 'bg-gradient-to-br from-red-500 via-red-600 to-red-800';
                        const currentZoom = map.getZoom();

                        const marker = L.marker([item.lat, item.lng], {
                            icon: createCustomIcon(bgClass, currentZoom)
                        }).addTo(map);

                        allMarkers.push({ marker: marker, bgClass: bgClass });

                        const safeItemName = (item.nama || '').replace(/'/g, "\\'");
                        const safeItemLoc = (item.lokasi || '').replace(/'/g, "\\'");

                        marker.bindPopup(`
                            <div class="p-2 font-sans max-w-[250px]">
                                <span class="bg-red-100 text-red-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">Agenda Kegiatan</span>
                                <h4 class="font-bold text-xs text-gray-900 mt-1 leading-snug" style="color: #111827 !important;">${item.nama}</h4>
                                <p class="text-[11px] text-gray-700 font-medium mt-1 leading-relaxed" style="color: #374151 !important;">${item.lokasi}</p>
                                <p class="text-[10px] text-gray-600 font-medium mt-0.5" style="color: #4b5563 !important;">${item.waktu}</p>
                                <div class="flex items-center gap-2 mt-3 pt-2.5 border-t border-gray-200">
                                    <a href="{{ route('publik.form-kunjungan') }}" style="color: #ffffff !important; text-decoration: none !important;" class="leaflet-popup-btn flex-1 text-center text-[10px] sm:text-[11px] font-bold text-white bg-[#35635b] hover:bg-[#2b4f49] px-2 py-1.5 rounded-lg transition-all shadow-xs">Form Kunjungan</a>
                                    <button type="button" onclick="openLocationAgendasModal('${safeItemLoc}', '${safeItemLoc}', null, null, 'Agenda Kegiatan')" style="color: #ffffff !important;" class="leaflet-popup-btn flex-1 text-center text-[10px] sm:text-[11px] font-bold text-white bg-emerald-700 hover:bg-emerald-800 px-2 py-1.5 rounded-lg transition-all shadow-xs cursor-pointer">Agenda &rarr;</button>
                                </div>
                            </div>
                        `);
                    });

                    const govCountEl = document.getElementById('stat-gov-points-count');
                    if (govCountEl && totalMapPoints > 0) {
                        govCountEl.textContent = `${totalMapPoints} Lokasi Dinas, Kecamatan & Pemkab`;
                    }
                })
                .catch(err => console.log('CSV Gov Points Notice:', err));


            // Map Search Input & Suggestion Dropdown
            const searchInput = document.getElementById('map-search-input');
            const searchResults = document.getElementById('map-search-results');
            const searchClear = document.getElementById('map-search-clear');

            if (searchInput && searchResults) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim().toLowerCase();
                    if (searchClear) searchClear.classList.toggle('hidden', query === '');

                    if (query.length < 2) {
                        searchResults.classList.add('hidden');
                        searchResults.innerHTML = '';
                        return;
                    }

                    const matches = searchableLocations.filter(item => 
                        item.name.toLowerCase().includes(query) || 
                        item.addr.toLowerCase().includes(query)
                    ).slice(0, 8);

                    if (matches.length === 0) {
                        searchResults.innerHTML = `
                            <div class="px-3.5 py-3 text-xs text-gray-500 dark:text-gray-400 text-center">
                                Dinas / Kecamatan tidak ditemukan.
                            </div>
                        `;
                    } else {
                        searchResults.innerHTML = matches.map(item => `
                            <button type="button" onclick="focusOnMapLocation(${item.lat}, ${item.lng})" 
                                    class="w-full text-left px-3.5 py-2.5 hover:bg-gray-100 dark:hover:bg-[#1f352f] transition-colors flex items-center justify-between gap-2 cursor-pointer border-b border-gray-100 dark:border-[#233a34] last:border-0">
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-xs text-gray-900 dark:text-white truncate">${item.name}</p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate mt-0.5">${item.addr}</p>
                                </div>
                                <span class="text-[9.5px] font-extrabold px-2 py-0.5 rounded-full shrink-0 ${item.typeBadge.includes('Kecamatan') ? 'bg-orange-100 dark:bg-orange-950/70 text-orange-800 dark:text-orange-300' : 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300'}">
                                    ${item.typeBadge}
                                </span>
                            </button>
                        `).join('');
                    }

                    searchResults.classList.remove('hidden');
                });

                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.add('hidden');
                    }
                });
            }

            window.clearMapSearch = function() {
                if (searchInput) {
                    searchInput.value = '';
                    if (searchClear) searchClear.classList.add('hidden');
                }
                if (searchResults) {
                    searchResults.classList.add('hidden');
                    searchResults.innerHTML = '';
                }
            };

            window.focusOnMapLocation = function(lat, lng) {
                map.flyTo([lat, lng], 15, { animate: true, duration: 1.2 });
                const found = searchableLocations.find(loc => Math.abs(loc.lat - lat) < 0.0001 && Math.abs(loc.lng - lng) < 0.0001);
                if (found && found.marker) {
                    setTimeout(() => {
                        found.marker.openPopup();
                    }, 1200);
                }
                if (searchResults) searchResults.classList.add('hidden');
            };

            // Reset Zoom Button
            const btnReset = document.getElementById('btn-reset-map-view');
            if (btnReset) {
                btnReset.addEventListener('click', () => {
                    map.setView([centerLat, centerLng], defaultZoom);
                });
            }

            setTimeout(() => {
                map.invalidateSize();
            }, 300);
        });

        window.allAgendasDataset = @json($allMapAgendas ?? []);

        window.openLocationAgendasModal = function(locationName, locationAddr, idDinas, idKecamatan, typeBadge) {
            const modal = document.getElementById('modal-agenda-lokasi');
            const modalCard = document.getElementById('modal-agenda-lokasi-card');
            const titleEl = document.getElementById('modal-location-title');
            const subTitleEl = document.getElementById('modal-location-subtitle');
            const badgeEl = document.getElementById('modal-location-badge');
            const countEl = document.getElementById('modal-agenda-count');
            const container = document.getElementById('modal-agenda-list-container');

            if (!modal) return;

            titleEl.textContent = locationName;
            subTitleEl.textContent = locationAddr || 'Kabupaten Bogor';
            badgeEl.textContent = typeBadge || 'Titik Lokasi';

            const cleanKeyword = locationName.toLowerCase().replace('kantor', '').replace('camat', '').replace('bupati', '').replace('dinas', '').trim();

            // Filter agendas for this location
            const matched = window.allAgendasDataset.filter(ag => {
                if (idDinas && Number(ag.id_dinas) === Number(idDinas)) return true;
                if (idKecamatan && Number(ag.id_kecamatan) === Number(idKecamatan)) return true;
                const agLoc = (ag.lokasi || '').toLowerCase();
                if (cleanKeyword.length >= 3 && agLoc.includes(cleanKeyword)) return true;
                if (agLoc.length >= 3 && locationName.toLowerCase().includes(agLoc)) return true;
                return false;
            });

            countEl.textContent = `${matched.length} Agenda`;

            if (matched.length > 0) {
                let html = '';
                matched.forEach(item => {
                    let catBg = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
                    if (item.kategori === 'masuk') catBg = 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300';
                    if (item.kategori === 'keluar') catBg = 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300';

                    html += `
                        <div class="rounded-2xl border border-gray-100 dark:border-[#233a34] bg-white dark:bg-[#0f1c19] p-4 shadow-sm hover:shadow-md transition-all">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="${catBg} text-[10px] font-extrabold px-2.5 py-0.5 rounded-full">${item.kategori_label}</span>
                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">🕒 ${item.waktu}</span>
                            </div>
                            <h4 class="font-bold text-sm text-gray-900 dark:text-white leading-snug">${item.nama_agenda}</h4>
                            <div class="flex items-center justify-between gap-2 mt-3 pt-2.5 border-t border-gray-50 dark:border-white/5 text-xs">
                                <span class="text-gray-500 dark:text-gray-400 font-medium">📅 ${item.tanggal}</span>
                                <a href="${item.detailUrl}" class="inline-flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 transition">
                                    Detail Agenda &rarr;
                                </a>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div class="text-center py-8 px-4">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 mb-3">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm text-gray-900 dark:text-white">Belum Ada Agenda Rapat Terdaftar</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-xs mx-auto">Saat ini belum ada jadwal agenda rapat khusus yang terdaftar di instansi/lokasi ini.</p>
                        <a href="{{ route('publik.agenda') }}" class="inline-block mt-4 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-xl transition shadow-sm">
                            Lihat Semua Agenda Publik &rarr;
                        </a>
                    </div>
                `;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modalCard.classList.remove('scale-95', 'opacity-0');
                modalCard.classList.add('scale-100', 'opacity-100');
            }, 10);
        };

        window.closeLocationAgendasModal = function() {
            const modal = document.getElementById('modal-agenda-lokasi');
            const modalCard = document.getElementById('modal-agenda-lokasi-card');
            if (!modal || !modalCard) return;

            modalCard.classList.remove('scale-100', 'opacity-100');
            modalCard.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 200);
        };
    </script>

    <!-- Modal Daftar Rapat Per Lokasi / Dinas -->
    <div id="modal-agenda-lokasi" class="fixed inset-0 z-[2500] hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 transition-all duration-300" onclick="if(event.target === this) closeLocationAgendasModal()">
        <div class="relative flex max-h-[85vh] w-full max-w-lg flex-col overflow-hidden rounded-3xl bg-white dark:bg-[#152420] shadow-2xl border border-gray-100 dark:border-[#284c43] transform transition-all scale-95 opacity-0" id="modal-agenda-lokasi-card">
            
            <!-- Header Modal -->
            <div class="flex items-start justify-between border-b border-gray-100 dark:border-[#233a34] p-5 sm:p-6 bg-gradient-to-r from-emerald-50/50 via-teal-50/30 to-transparent dark:from-emerald-950/20 dark:to-transparent">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-md shadow-emerald-600/20">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span id="modal-location-badge" class="bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full">Dinas / SKPD</span>
                            <span id="modal-agenda-count" class="bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full">0 Agenda</span>
                        </div>
                        <h3 id="modal-location-title" class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mt-1 leading-snug">Nama Lokasi</h3>
                        <p id="modal-location-subtitle" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Alamat Instansi</p>
                    </div>
                </div>
                <button type="button" onclick="closeLocationAgendasModal()" class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-white/10 dark:hover:text-white transition cursor-pointer">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content Area / Agenda List -->
            <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-3" id="modal-agenda-list-container">
            </div>

            <!-- Footer Modal -->
            <div class="flex items-center justify-between border-t border-gray-100 dark:border-[#233a34] p-4 bg-gray-50 dark:bg-[#0f1c19]">
                <a href="{{ route('publik.form-kunjungan') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:underline">
                    <span>Isi Form Kunjungan</span> &rarr;
                </a>
                <button type="button" onclick="closeLocationAgendasModal()" class="rounded-xl bg-gray-200 dark:bg-white/10 px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-300 transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @include('publik.layout.image-preview-modal')

    <!-- Scroll Reveal Observer -->
    <script>
    (function() {
        const revealEls = document.querySelectorAll('.scroll-reveal');
        if (!revealEls.length) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');

                    // Stagger children (cards inside grids)
                    const children = entry.target.querySelectorAll('.scroll-reveal-child');
                    children.forEach((child, i) => {
                        setTimeout(() => {
                            child.classList.add('is-visible');
                        }, i * 140);
                    });

                    observer.unobserve(entry.target); // Only animate once
                }
            });
        }, {
            threshold: 0.08,
            rootMargin: '0px 0px -40px 0px'
        });

        revealEls.forEach(el => observer.observe(el));
    })();
    </script>
</body>
</html>
