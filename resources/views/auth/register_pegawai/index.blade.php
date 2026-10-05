<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Daftar Pegawai - RAPID</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @include('publik.layout.theme_script')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        sirapi: {
                            green: '#35635b',
                            greenSoft: '#2b4f49',
                            ink: '#14211F',
                        }
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        /* Sembunyikan ikon mata bawaan browser Edge / IE */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }

        /* Autofill Styling (Mencegah background input berubah putih saat autofill di dark mode) */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active,
        select:-webkit-autofill,
        select:-webkit-autofill:hover,
        select:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #ffffff inset !important;
            -webkit-text-fill-color: #1f2937 !important;
            caret-color: #1f2937 !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        html.dark input:-webkit-autofill,
        html.dark input:-webkit-autofill:hover,
        html.dark input:-webkit-autofill:focus,
        html.dark input:-webkit-autofill:active,
        html.dark select:-webkit-autofill,
        html.dark select:-webkit-autofill:hover,
        html.dark select:-webkit-autofill:focus,
        .dark input:-webkit-autofill,
        .dark input:-webkit-autofill:hover,
        .dark input:-webkit-autofill:focus,
        .dark input:-webkit-autofill:active,
        .dark select:-webkit-autofill,
        .dark select:-webkit-autofill:hover,
        .dark select:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #0f1c19 inset !important;
            -webkit-text-fill-color: #ffffff !important;
            caret-color: #ffffff !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        html.dark {
            color-scheme: dark;
        }
        html:not(.dark) {
            color-scheme: light;
        }

        @keyframes biometricScan {
            0% {
                top: 5%;
                opacity: 0.2;
            }
            20% {
                opacity: 1;
            }
            80% {
                opacity: 1;
            }
            100% {
                top: 90%;
                opacity: 0.2;
            }
        }
        .biometric-laser-line {
            position: absolute;
            left: 4%;
            right: 4%;
            height: 2.5px;
            background: linear-gradient(90deg, transparent 0%, rgba(52, 211, 153, 0.7) 15%, #10b981 50%, rgba(52, 211, 153, 0.7) 85%, transparent 100%);
            box-shadow: 0 0 12px 2.5px rgba(16, 185, 129, 0.85), 0 0 4px rgba(255, 255, 255, 0.95);
            animation: biometricScan 2s ease-in-out infinite alternate;
            pointer-events: none;
            z-index: 25;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50/70 dark:bg-[#0d1614] font-sans text-sirapi-ink dark:text-slate-100 antialiased transition-colors duration-200 flex flex-col">
    <!-- Header -->
    <header class="relative flex min-h-[170px] sm:min-h-[220px] py-6 sm:py-8 items-center justify-center overflow-hidden bg-sirapi-green dark:bg-[#0f1c19] dark:border-b dark:border-[#233a34] text-white shrink-0">
        <div class="absolute -left-20 -top-24 h-72 w-72 rounded-full bg-white/10 dark:bg-white/5"></div>
        <div class="absolute -right-20 -bottom-24 h-72 w-72 rounded-full bg-white/5 dark:bg-white/5"></div>
        
        <!-- Back Button Top-Left -->
        <a href="{{ route('publik.beranda') }}" 
           class="absolute top-4 left-4 sm:top-5 sm:left-5 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs sm:text-sm font-semibold text-white/90 hover:text-white bg-white/10 hover:bg-white/20 dark:bg-[#152420] dark:border dark:border-[#284c43] transition cursor-pointer z-20"
           title="Kembali ke Beranda">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Beranda</span>
        </a>

        <!-- Theme Toggle Top-Right -->
        <button type="button" 
                onclick="toggleSirapiTheme()" 
                class="absolute top-4 right-4 sm:top-5 sm:right-5 w-10 h-10 rounded-full flex items-center justify-center text-white/90 hover:text-white dark:text-amber-400 dark:hover:text-amber-300 bg-white/10 hover:bg-white/20 dark:bg-[#152420] dark:border dark:border-[#284c43] transition-colors cursor-pointer z-20"
                title="Ganti Mode Gelap / Terang">
            <svg data-theme-icon-light class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg data-theme-icon-dark class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <div class="relative flex flex-col items-center text-center px-4 z-10">
            <img src="{{ asset('assets/foto/logo-bappenda.png') }}" alt="Logo Kabupaten Bogor" class="h-14 sm:h-20 w-auto object-contain drop-shadow">
            <h1 class="mt-2.5 sm:mt-3 text-xl sm:text-2xl font-extrabold tracking-wide">RAPID</h1>
            <p class="mt-0.5 sm:mt-1 text-xs sm:text-sm font-semibold text-emerald-100 dark:text-gray-300">Pendaftaran Akun Pegawai</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex flex-1 justify-center px-3.5 sm:px-6 py-6 sm:py-10">
        <section class="w-full max-w-xl md:max-w-2xl lg:max-w-3xl">
            @if (session('status'))
                <div class="mb-5 rounded-2xl border border-emerald-200 dark:border-emerald-800/80 bg-emerald-50 dark:bg-emerald-950/40 p-4 text-xs sm:text-sm font-medium text-emerald-800 dark:text-emerald-200 shadow-xs flex items-start gap-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5"></i>
                    <div class="leading-relaxed">
                        <strong class="font-bold text-emerald-900 dark:text-emerald-100">Pendaftaran Berhasil!</strong>
                        <p class="mt-0.5">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-red-200 dark:border-red-800/80 bg-red-50 dark:bg-red-950/40 p-4 text-xs sm:text-sm font-medium text-red-700 dark:text-red-300 shadow-xs">
                    <div class="flex items-center gap-2 font-bold mb-1.5 text-red-800 dark:text-red-200">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                        <span>Mohon periksa kesalahan berikut:</span>
                    </div>
                    <ul class="list-inside list-disc space-y-0.5 pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-5 sm:mb-6 rounded-2xl border border-blue-200 dark:border-blue-800/60 bg-blue-50/80 dark:bg-blue-950/40 p-4 text-xs sm:text-sm text-blue-800 dark:text-blue-200 flex items-start sm:items-center gap-3 shadow-xs">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5 sm:mt-0"></i>
                <div class="leading-relaxed">
                    <strong class="font-bold text-blue-900 dark:text-blue-100">Informasi Pendaftaran:</strong>
                    Akun dan data rekaman biometrik wajah Anda akan diverifikasi Administrator. Setelah diverifikasi, Anda dapat langsung melakukan presensi rapat menggunakan Scan Wajah (Face Recognition) di lokasi rapat.
                </div>
            </div>

            <form action="{{ route('pegawai.register.submit') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 rounded-xl border border-[#DDE3DF] dark:border-[#233a34] bg-white dark:bg-[#152420] p-4.5 sm:p-7 md:p-8 shadow-xs transition-colors">
                @csrf

                <!-- Foto Profil (Otomatis dari Perekaman Wajah / Face Recognition) -->
                <div class="sm:col-span-2 flex flex-col items-center justify-center py-2.5 border-b border-gray-100 dark:border-[#233a34] mb-2">
                    <div class="relative flex h-24 w-24 items-center justify-center rounded-full border-2 border-dashed border-gray-200 dark:border-[#284c43] bg-gray-50 dark:bg-[#0f1c19] shadow-xs">
                        <img id="foto-preview" src="{{ asset('assets/foto/profile.png') }}" alt="Preview Foto Profil" class="h-full w-full rounded-full object-cover">
                    </div>
                    <div class="flex flex-col items-center mt-2.5 text-center">
                        <span class="text-xs font-bold text-gray-800 dark:text-gray-200">Foto Profil</span>
                        <span id="foto-profil-desc" class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                            Otomatis diambil dari hasil scan kamera Face Recognition
                        </span>
                    </div>
                </div>

                <!-- Nama Lengkap -->
                <div class="sm:col-span-2">
                    <label for="nama_pegawai" class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 sm:pl-4 text-gray-400 dark:text-gray-500">
                            <i data-lucide="user" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </span>
                        <input id="nama_pegawai" type="text" name="nama_pegawai" value="{{ old('nama_pegawai') }}" required autofocus
                            class="h-11 sm:h-12 w-full rounded-xl border border-[#DDE3DF] dark:border-[#284c43] bg-gray-50/50 dark:bg-[#0f1c19] pl-10 sm:pl-11 pr-4 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-sirapi-green/15"
                            placeholder="Masukkan nama lengkap beserta gelar jika ada">
                    </div>
                </div>

                <!-- Instansi (Dinas / Kecamatan) -->
                <div class="sm:col-span-2">
                    <label for="instansi" class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 mb-1.5">
                        Instansi (Dinas / Kecamatan) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 sm:pl-4 text-gray-400 dark:text-gray-500">
                            <i data-lucide="landmark" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </span>
                        <select id="instansi" name="instansi" required
                            class="h-11 sm:h-12 w-full rounded-xl border border-[#DDE3DF] dark:border-[#284c43] bg-gray-50/50 dark:bg-[#0f1c19] pl-10 sm:pl-11 pr-8 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-sirapi-green/15 cursor-pointer appearance-none">
                            <option value="">Pilih Instansi (Dinas / Kecamatan)</option>
                            @if (!empty($dinasList) && $dinasList->isNotEmpty())
                                <optgroup label="Dinas / Perangkat Daerah">
                                    @foreach ($dinasList as $dinas)
                                        <option value="dinas_{{ $dinas->id_dinas }}" @selected(old('instansi') === 'dinas_' . $dinas->id_dinas)>
                                            {{ $dinas->nama_lengkap ?? $dinas->nama_dinas }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if (!empty($kecamatanList) && $kecamatanList->isNotEmpty())
                                <optgroup label="Kecamatan">
                                    @foreach ($kecamatanList as $kecamatan)
                                        <option value="kecamatan_{{ $kecamatan->id_kecamatan }}" @selected(old('instansi') === 'kecamatan_' . $kecamatan->id_kecamatan)>
                                            {{ $kecamatan->nama_kecamatan }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 dark:text-gray-500">
                            <i data-lucide="chevron-down" class="h-4 w-4"></i>
                        </span>
                    </div>
                    <p class="mt-1.5 text-[11px] text-gray-500 dark:text-gray-400">Pilih instansi tempat Anda bertugas agar akun terhubung ke Admin instansi yang bersangkutan.</p>
                </div>

                <!-- NIP -->
                <div>
                    <label for="nip" class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 mb-1.5">
                        NIP <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 sm:pl-4 text-gray-400 dark:text-gray-500">
                            <i data-lucide="badge-check" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </span>
                        <input id="nip" type="text" inputmode="numeric" name="nip" value="{{ old('nip') }}" required pattern="[0-9]+" maxlength="18" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="h-11 sm:h-12 w-full rounded-xl border border-[#DDE3DF] dark:border-[#284c43] bg-gray-50/50 dark:bg-[#0f1c19] pl-10 sm:pl-11 pr-4 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-sirapi-green/15"
                            placeholder="18 digit Nomor Induk Pegawai">
                    </div>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="tanggal_lahir" class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 mb-1.5">
                        Tanggal Lahir
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 sm:pl-4 text-gray-400 dark:text-gray-500">
                            <i data-lucide="calendar" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </span>
                        <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                            class="h-11 sm:h-12 w-full rounded-xl border border-[#DDE3DF] dark:border-[#284c43] bg-gray-50/50 dark:bg-[#0f1c19] pl-10 sm:pl-11 pr-4 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-sirapi-green/15">
                    </div>
                </div>

                <!-- Jabatan -->
                <div>
                    <label for="jabatan" class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 mb-1.5">
                        Jabatan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 sm:pl-4 text-gray-400 dark:text-gray-500">
                            <i data-lucide="briefcase" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </span>
                        <select id="jabatan" name="jabatan" required
                            class="h-11 sm:h-12 w-full rounded-xl border border-[#DDE3DF] dark:border-[#284c43] bg-gray-50/50 dark:bg-[#0f1c19] pl-10 sm:pl-11 pr-8 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-sirapi-green/15 cursor-pointer appearance-none disabled:opacity-60 disabled:cursor-not-allowed">
                            <option value="">{{ old('instansi') ? 'Pilih jabatan' : '-- Pilih Instansi Terlebih Dahulu --' }}</option>
                            @foreach ($jabatanOptions as $jabatan)
                                <option value="{{ $jabatan }}" @selected(old('jabatan') === $jabatan)>{{ $jabatan }}</option>
                            @endforeach
                            @if (old('instansi'))
                                <option value="__lainnya__" @selected(old('jabatan') && !in_array(old('jabatan'), $jabatanOptions))>Lainnya / Tulis Manual</option>
                            @endif
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 dark:text-gray-500">
                            <i data-lucide="chevron-down" class="h-4 w-4"></i>
                        </span>
                    </div>
                    <div id="jabatan_manual_container" class="{{ old('jabatan') && !in_array(old('jabatan'), $jabatanOptions) ? '' : 'hidden' }} mt-2">
                        <input type="text" id="jabatan_manual" placeholder="Ketik nama jabatan Anda..."
                            value="{{ old('jabatan') && !in_array(old('jabatan'), $jabatanOptions) ? old('jabatan') : '' }}"
                            class="h-10 sm:h-11 w-full rounded-xl border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50/40 dark:bg-[#12221e] px-3.5 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:ring-2 focus:ring-sirapi-green/20">
                    </div>
                </div>

                <!-- Bidang -->
                <div>
                    <label for="bidang" class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 mb-1.5">
                        Bidang / Bagian
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 sm:pl-4 text-gray-400 dark:text-gray-500">
                            <i data-lucide="building" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </span>
                        <select id="bidang" name="bidang"
                            class="h-11 sm:h-12 w-full rounded-xl border border-[#DDE3DF] dark:border-[#284c43] bg-gray-50/50 dark:bg-[#0f1c19] pl-10 sm:pl-11 pr-8 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-sirapi-green/15 cursor-pointer appearance-none disabled:opacity-60 disabled:cursor-not-allowed">
                            <option value="">{{ old('instansi') ? 'Pilih bidang (opsional)' : '-- Pilih Instansi Terlebih Dahulu --' }}</option>
                            @foreach ($bidangOptions as $bidang)
                                <option value="{{ $bidang }}" @selected(old('bidang') === $bidang)>{{ $bidang }}</option>
                            @endforeach
                            @if (old('instansi'))
                                <option value="__lainnya__" @selected(old('bidang') && !in_array(old('bidang'), $bidangOptions))>Lainnya / Tulis Manual</option>
                            @endif
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 dark:text-gray-500">
                            <i data-lucide="chevron-down" class="h-4 w-4"></i>
                        </span>
                    </div>
                    <div id="bidang_manual_container" class="{{ old('bidang') && !in_array(old('bidang'), $bidangOptions) ? '' : 'hidden' }} mt-2">
                        <input type="text" id="bidang_manual" placeholder="Ketik nama bidang/bagian Anda..."
                            value="{{ old('bidang') && !in_array(old('bidang'), $bidangOptions) ? old('bidang') : '' }}"
                            class="h-10 sm:h-11 w-full rounded-xl border border-emerald-300 dark:border-emerald-700/60 bg-emerald-50/40 dark:bg-[#12221e] px-3.5 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:ring-2 focus:ring-sirapi-green/20">
                    </div>
                </div>

                <!-- No. HP -->
                <div>
                    <label for="nomor_hp" class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 mb-1.5">
                        No. HP / WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 sm:pl-4 text-gray-400 dark:text-gray-500">
                            <i data-lucide="phone" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </span>
                        <input id="nomor_hp" type="tel" inputmode="numeric" name="nomor_hp" value="{{ old('nomor_hp') }}" required pattern="[0-9]+" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="h-11 sm:h-12 w-full rounded-xl border border-[#DDE3DF] dark:border-[#284c43] bg-gray-50/50 dark:bg-[#0f1c19] pl-10 sm:pl-11 pr-4 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-sirapi-green/15"
                            placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 mb-1.5">
                        Email Resmi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 sm:pl-4 text-gray-400 dark:text-gray-500">
                            <i data-lucide="mail" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="h-11 sm:h-12 w-full rounded-xl border border-[#DDE3DF] dark:border-[#284c43] bg-gray-50/50 dark:bg-[#0f1c19] pl-10 sm:pl-11 pr-4 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-sirapi-green/15"
                            placeholder="nama@domain.go.id">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 mb-1.5">
                        Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 sm:pl-4 text-gray-400 dark:text-gray-500">
                            <i data-lucide="lock" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </span>
                        <input id="password" type="password" name="password" required minlength="8"
                            class="h-11 sm:h-12 w-full rounded-xl border border-[#DDE3DF] dark:border-[#284c43] bg-gray-50/50 dark:bg-[#0f1c19] pl-10 sm:pl-11 pr-11 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-sirapi-green/15"
                            placeholder="Minimal 8 karakter">
                        <button type="button" onclick="togglePasswordVisibility('password', 'eye-icon-pass')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition cursor-pointer" title="Lihat/Sembunyikan Sandi">
                            <i id="eye-icon-pass" data-lucide="eye" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200 mb-1.5">
                        Konfirmasi Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 sm:pl-4 text-gray-400 dark:text-gray-500">
                            <i data-lucide="lock-keyhole" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </span>
                        <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                            class="h-11 sm:h-12 w-full rounded-xl border border-[#DDE3DF] dark:border-[#284c43] bg-gray-50/50 dark:bg-[#0f1c19] pl-10 sm:pl-11 pr-11 text-xs sm:text-sm font-medium text-gray-800 dark:text-white shadow-xs outline-none transition focus:border-sirapi-green focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-sirapi-green/15"
                            placeholder="Ulangi kata sandi">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eye-icon-confirm')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition cursor-pointer" title="Lihat/Sembunyikan Sandi">
                            <i id="eye-icon-confirm" data-lucide="eye" class="h-4 sm:h-5 w-4 sm:w-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Seksi Face Recognition (Perekaman Wajah) -->
                <div class="sm:col-span-2 rounded-2xl border border-emerald-200/80 dark:border-[#233a34] bg-emerald-50/40 dark:bg-[#0f1c19]/60 p-4 sm:p-5 transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-emerald-100 dark:border-[#233a34]">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-sirapi-green/10 dark:bg-emerald-500/15 text-sirapi-green dark:text-emerald-400">
                                <i data-lucide="scan-face" class="h-5 w-5"></i>
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-white flex items-center gap-1.5">
                                    <span>Perekaman Wajah (Face Recognition)</span>
                                    <span class="text-red-500 font-bold">*</span>
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-950/70 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800/60">Wajib</span>
                                </h3>
                                <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400">Wajib merekam wajah untuk validasi akun & presensi rapat biometrik</p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div id="face-status-badge">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800/60 px-3 py-1 text-[11px] font-bold text-amber-700 dark:text-amber-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                Belum Direkam
                            </span>
                        </div>
                    </div>

                    <!-- Hidden Inputs for Form Submission -->
                    <input type="hidden" name="face_descriptor" id="face_descriptor" value="{{ old('face_descriptor') }}">
                    <input type="hidden" name="foto_wajah" id="foto_wajah" value="{{ old('foto_wajah') }}">

                    <!-- State 1: Belum Direkam -->
                    <div id="face-unrecorded-view" class="pt-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed max-w-md">
                            Wajah yang didaftarkan akan tersimpan dan siap digunakan untuk presensi wajah secara otomatis setelah akun disetujui Administrator.
                        </p>
                        <button type="button" onclick="openFaceModal()" class="inline-flex items-center justify-center gap-2 h-10 px-4 rounded-xl bg-sirapi-green hover:bg-sirapi-greenSoft dark:bg-[#107050] dark:hover:bg-[#0c5940] text-white text-xs font-bold shadow-xs transition cursor-pointer shrink-0">
                            <i data-lucide="camera" class="h-4 w-4"></i>
                            <span>Buka Kamera & Rekam Wajah</span>
                        </button>
                    </div>

                    <!-- State 2: Sudah Direkam -->
                    <div id="face-recorded-view" class="pt-3.5 hidden items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="relative h-12 w-12 rounded-xl overflow-hidden border-2 border-emerald-500 shadow-xs shrink-0">
                                <img id="face-preview-thumb" src="" alt="Thumbnail Wajah" class="h-full w-full object-cover">
                                <span class="absolute bottom-0 right-0 bg-emerald-500 text-white p-0.5 rounded-tl">
                                    <i data-lucide="check" class="h-2.5 w-2.5"></i>
                                </span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800 dark:text-white flex items-center gap-1">
                                    <span>Wajah Berhasil Direkam</span>
                                    <i data-lucide="check-circle" class="h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400"></i>
                                </p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">Data biometrik 128-vektor siap didaftarkan ke sistem.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openFaceModal()" class="inline-flex items-center gap-1.5 h-9 px-3 rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#152420] text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition cursor-pointer">
                                <i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i>
                                <span>Rekam Ulang</span>
                            </button>
                            <button type="button" onclick="resetFaceRecord()" class="inline-flex items-center gap-1.5 h-9 px-2.5 rounded-xl border border-red-200 dark:border-red-900/50 bg-red-50/50 dark:bg-red-950/40 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-100/70 transition cursor-pointer" title="Hapus rekaman wajah">
                                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex justify-end pt-4 sm:col-span-2 border-t border-gray-100 dark:border-[#233a34] mt-2">
                    <button type="submit" class="w-full sm:w-auto h-11 sm:h-12 rounded-xl bg-sirapi-green hover:bg-sirapi-greenSoft dark:bg-[#107050] dark:hover:bg-[#0c5940] dark:border dark:border-[#10b981]/30 px-7 sm:px-8 text-xs sm:text-sm font-extrabold text-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-sirapi-green/25 cursor-pointer flex items-center justify-center gap-2">
                        <span>Daftar Akun Pegawai</span>
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>
        </section>
    </main>

    <!-- Face Registration Modal -->
    <div id="face-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-xs p-2.5 sm:p-4 overflow-y-auto">
        <div class="my-auto flex max-h-[calc(100dvh-1.5rem)] w-full max-w-md sm:max-w-lg flex-col overflow-hidden rounded-2xl bg-white dark:bg-[#152420] text-sirapi-ink dark:text-slate-100 shadow-2xl border border-gray-100 dark:border-[#233a34]">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#233a34] bg-white dark:bg-[#0f1c19] px-4 py-3 sm:px-6 sm:py-4 shrink-0">
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-gray-900 dark:text-white">Perekaman Wajah Pegawai</h3>
                    <p class="text-[11px] sm:text-xs font-medium text-gray-500 dark:text-emerald-400">Kamera Pendaftaran Face Recognition</p>
                </div>
                <button type="button" onclick="closeFaceModal()" class="rounded-xl p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-white/5 dark:hover:text-gray-200 cursor-pointer" title="Tutup">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <div class="p-3.5 sm:p-5 text-center space-y-3 overflow-y-auto">
                <p class="text-[11px] sm:text-xs text-gray-500 dark:text-gray-400 font-medium">Posisikan wajah Anda dengan jelas di tengah lingkaran kamera, lalu klik tombol Ambil Wajah.</p>
                
                <div class="relative w-full aspect-[4/3] bg-gray-950 rounded-2xl overflow-hidden shadow-inner border border-gray-200 dark:border-[#284c43]">
                    <div id="face-status-container" class="absolute inset-0 flex flex-col items-center justify-center text-white text-xs font-medium z-10 p-4 gap-2">
                        <div class="w-8 h-8 border-2 border-white/20 border-t-emerald-400 rounded-full animate-spin"></div>
                        <p id="face-status" class="animate-pulse text-center">Memuat kamera dan sistem pendeteksi...</p>
                    </div>
                    <video id="face-video" class="absolute top-0 left-0 w-full h-full object-cover hidden" style="transform: scaleX(-1);" autoplay muted playsinline></video>
                    <canvas id="face-overlay" class="absolute top-0 left-0 w-full h-full z-20 pointer-events-none" style="transform: scaleX(-1);"></canvas>

                    <!-- Target Face Guide Frame -->
                    <div id="register-face-guide-frame" class="absolute inset-0 z-20 pointer-events-none flex items-center justify-center hidden">
                        <div id="register-face-guide-box" class="relative w-[56%] h-[74%] rounded-[36px] border-2 border-dashed border-white/70 shadow-[0_0_0_9999px_rgba(0,0,0,0.38)] transition-all duration-300 flex flex-col justify-between items-center p-2.5 overflow-hidden">
                            <!-- Laser scan bar -->
                            <div id="register-biometric-laser" class="biometric-laser-line hidden"></div>

                            <div class="w-full flex justify-between z-10">
                                <span class="register-guide-corner w-4 h-4 border-t-3 border-l-3 border-white rounded-tl-xl transition-colors"></span>
                                <span class="register-guide-corner w-4 h-4 border-t-3 border-r-3 border-white rounded-tr-xl transition-colors"></span>
                            </div>
                            <div class="w-full flex justify-between z-10">
                                <span class="register-guide-corner w-4 h-4 border-b-3 border-l-3 border-white rounded-bl-xl transition-colors"></span>
                                <span class="register-guide-corner w-4 h-4 border-b-3 border-r-3 border-white rounded-br-xl transition-colors"></span>
                            </div>
                        </div>
                        <!-- Teks Petunjuk di Bawah (Di Luar Border/Frame Wajah) -->
                        <div class="absolute bottom-2.5 inset-x-0 flex justify-center z-30 pointer-events-none">
                            <span id="register-face-guide-hint" class="bg-black/75 backdrop-blur-xs text-white text-[10.5px] sm:text-xs font-bold px-3.5 py-1 rounded-full text-center tracking-wide transition-colors shadow-md border border-white/10">Arahkan Wajah ke Bingkai</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5 pt-1">
                    <button type="button" onclick="closeFaceModal()" class="h-9.5 sm:h-10 rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] text-xs font-bold text-gray-700 dark:text-gray-300 transition hover:bg-gray-100 dark:hover:bg-white/5 cursor-pointer flex items-center justify-center">Batal</button>
                    <button type="button" id="btn-capture-face" class="inline-flex h-9.5 sm:h-10 items-center justify-center gap-1.5 rounded-xl bg-sirapi-green hover:bg-sirapi-greenSoft dark:bg-[#107050] dark:hover:bg-[#0c5940] dark:border dark:border-[#10b981]/30 text-xs font-bold text-white transition hidden shadow-xs cursor-pointer">
                        <i data-lucide="scan" class="h-4 w-4"></i>
                        <span>Ambil Wajah</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 text-center text-xs text-gray-400 dark:text-gray-500 shrink-0">
        &copy; {{ date('Y') }} RAPID - Pemerintah Kabupaten Bogor. Hak cipta dilindungi.
    </footer>

    <script src="{{ asset('js/face-api.min.js') }}"></script>
    <script>
        lucide.createIcons();

        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        const fotoPreview = document.getElementById('foto-preview');
        const fotoProfilDesc = document.getElementById('foto-profil-desc');

        // Face Recognition Variables & Logic
        let faceModelsLoaded = false;
        let faceStream = null;
        const faceModal = document.getElementById('face-modal');
        const faceVideo = document.getElementById('face-video');
        const faceOverlay = document.getElementById('face-overlay');
        const faceStatusContainer = document.getElementById('face-status-container');
        const faceStatus = document.getElementById('face-status');
        const btnCaptureFace = document.getElementById('btn-capture-face');
        const registerFaceGuideFrame = document.getElementById('register-face-guide-frame');
        const registerFaceGuideBox = document.getElementById('register-face-guide-box');

        const faceDescriptorInput = document.getElementById('face_descriptor');
        const fotoWajahInput = document.getElementById('foto_wajah');
        const faceStatusBadge = document.getElementById('face-status-badge');
        const faceUnrecordedView = document.getElementById('face-unrecorded-view');
        const faceRecordedView = document.getElementById('face-recorded-view');
        const facePreviewThumb = document.getElementById('face-preview-thumb');

        function setFaceRecordedState(imageUrl) {
            if (!imageUrl) return;
            if (faceStatusBadge) {
                faceStatusBadge.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/60 px-3 py-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Wajah Terdaftar ✓
                    </span>
                `;
            }
            if (facePreviewThumb) {
                facePreviewThumb.src = imageUrl;
            }
            if (faceUnrecordedView) faceUnrecordedView.classList.add('hidden');
            if (faceRecordedView) {
                faceRecordedView.classList.remove('hidden');
                faceRecordedView.classList.add('flex');
            }

            // Sinkronkan foto avatar utama secara otomatis dari hasil scan wajah
            if (fotoPreview) {
                fotoPreview.src = imageUrl;
            }
            if (fotoProfilDesc) {
                fotoProfilDesc.innerHTML = '<span class="text-emerald-600 dark:text-emerald-400 font-bold">✓ Foto profil berhasil tersinkron</span>';
            }
            lucide.createIcons();
        }

        function resetFaceRecord() {
            if (faceDescriptorInput) faceDescriptorInput.value = '';
            if (fotoWajahInput) fotoWajahInput.value = '';
            if (faceStatusBadge) {
                faceStatusBadge.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800/60 px-3 py-1 text-[11px] font-bold text-amber-700 dark:text-amber-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        Belum Direkam
                    </span>
                `;
            }
            if (faceRecordedView) {
                faceRecordedView.classList.add('hidden');
                faceRecordedView.classList.remove('flex');
            }
            if (faceUnrecordedView) faceUnrecordedView.classList.remove('hidden');

            if (fotoPreview) {
                fotoPreview.src = "{{ asset('assets/foto/profile.png') }}";
            }
            if (fotoProfilDesc) {
                fotoProfilDesc.innerText = 'Otomatis diambil dari hasil scan kamera Face Recognition';
            }
            lucide.createIcons();
        }

        function drawBiometricLandmarks(ctx, targetFace) {
            if (!targetFace || !targetFace.landmarks) return;
            const points = targetFace.landmarks.positions;
            ctx.save();
            
            // Gambar 68 cyber dots biometrik
            ctx.fillStyle = '#34d399';
            ctx.shadowColor = '#10b981';
            ctx.shadowBlur = 6;
            for (let i = 0; i < points.length; i++) {
                ctx.beginPath();
                ctx.arc(points[i].x, points[i].y, 2, 0, 2 * Math.PI);
                ctx.fill();
            }

            // Gambar garis kontur biometrik halus
            ctx.strokeStyle = 'rgba(52, 211, 153, 0.45)';
            ctx.lineWidth = 1.2;

            const segments = [
                targetFace.landmarks.getJawOutline(),
                targetFace.landmarks.getLeftEyeBrow(),
                targetFace.landmarks.getRightEyeBrow(),
                targetFace.landmarks.getNose(),
                targetFace.landmarks.getLeftEye(),
                targetFace.landmarks.getRightEye(),
                targetFace.landmarks.getMouth()
            ];

            for (const segment of segments) {
                if (segment && segment.length > 0) {
                    ctx.beginPath();
                    ctx.moveTo(segment[0].x, segment[0].y);
                    for (let i = 1; i < segment.length; i++) {
                        ctx.lineTo(segment[i].x, segment[i].y);
                    }
                    if (segment === targetFace.landmarks.getLeftEye() || 
                        segment === targetFace.landmarks.getRightEye() || 
                        segment === targetFace.landmarks.getMouth()) {
                        ctx.closePath();
                    }
                    ctx.stroke();
                }
            }
            ctx.restore();
        }

        function getGuideBoxRoi(guideBoxEl, videoEl, displaySize) {
            if (!guideBoxEl || !videoEl) {
                return {
                    x: displaySize.width * 0.22,
                    y: displaySize.height * 0.13,
                    width: displaySize.width * 0.56,
                    height: displaySize.height * 0.74
                };
            }
            const boxRect = guideBoxEl.getBoundingClientRect();
            const videoRect = videoEl.getBoundingClientRect();

            if (videoRect.width <= 0 || videoRect.height <= 0) {
                return {
                    x: displaySize.width * 0.22,
                    y: displaySize.height * 0.13,
                    width: displaySize.width * 0.56,
                    height: displaySize.height * 0.74
                };
            }

            const leftPercent = Math.max(0, (boxRect.left - videoRect.left) / videoRect.width);
            const topPercent = Math.max(0, (boxRect.top - videoRect.top) / videoRect.height);
            const widthPercent = Math.min(1, boxRect.width / videoRect.width);
            const heightPercent = Math.min(1, boxRect.height / videoRect.height);

            return {
                x: leftPercent * displaySize.width,
                y: topPercent * displaySize.height,
                width: widthPercent * displaySize.width,
                height: heightPercent * displaySize.height
            };
        }

        function checkFaceInRoi(detection, roi) {
            const b = detection.detection.box;
            const landmarks = detection.landmarks ? detection.landmarks.positions : null;

            // Abaikan jika wajah terlalu kecil (orang di belakang)
            if (b.width < roi.width * 0.28 || b.height < roi.height * 0.28) {
                return { isFull: false, isCutting: false, reason: 'too_small' };
            }

            // Toleransi batas tepi border agar wajah harus benar-benar di dalam
            const pad = 4;
            const roiLeft = roi.x + pad;
            const roiRight = roi.x + roi.width - pad;
            const roiTop = roi.y + pad;
            const roiBottom = roi.y + roi.height - pad;

            // 1. Seluruh kotak wajah harus berada di dalam batas bingkai
            const isBoxInside = (
                b.x >= roiLeft &&
                (b.x + b.width) <= roiRight &&
                b.y >= roiTop &&
                (b.y + b.height) <= roiBottom
            );

            // 2. Seluruh 68 titik biometrik (dagu, rahang, alis, mata) harus berada di dalam bingkai
            let areLandmarksInside = true;
            if (landmarks && landmarks.length > 0) {
                for (let i = 0; i < landmarks.length; i++) {
                    const p = landmarks[i];
                    if (p.x < roiLeft || p.x > roiRight || p.y < roiTop || p.y > roiBottom) {
                        areLandmarksInside = false;
                        break;
                    }
                }
            }

            if (isBoxInside && areLandmarksInside) {
                return { isFull: true, isCutting: false };
            }

            // Cek apakah sebagian wajah memotong/mengenai batas bingkai
            const isOverlapping = (
                (b.x + b.width) > roi.x &&
                b.x < (roi.x + roi.width) &&
                (b.y + b.height) > roi.y &&
                b.y < (roi.y + roi.height)
            );

            if (isOverlapping) {
                return { isFull: false, isCutting: true };
            }

            return { isFull: false, isCutting: false };
        }

        async function openFaceModal() {
            if (!faceModal) return;
            faceModal.classList.remove('hidden');
            faceModal.classList.add('flex');
            faceVideo.classList.add('hidden');
            btnCaptureFace.classList.add('hidden');
            faceStatusContainer.classList.remove('hidden');
            faceStatus.innerText = "Memuat kamera dan sistem pendeteksi...";

            try {
                if (!faceModelsLoaded) {
                    await Promise.all([
                        faceapi.nets.ssdMobilenetv1.loadFromUri('{{ asset('models') }}'),
                        faceapi.nets.faceLandmark68Net.loadFromUri('{{ asset('models') }}'),
                        faceapi.nets.faceRecognitionNet.loadFromUri('{{ asset('models') }}')
                    ]);
                    faceModelsLoaded = true;
                }

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    faceStatus.innerText = "Kamera tidak didukung pada browser/koneksi ini (perlu HTTPS atau localhost).";
                    return;
                }

                faceStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "user",
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    }
                });

                faceVideo.srcObject = faceStream;
                faceVideo.onloadedmetadata = () => {
                    faceVideo.play();
                    faceVideo.classList.remove('hidden');
                    if (registerFaceGuideFrame) registerFaceGuideFrame.classList.remove('hidden');
                    const registerLaser = document.getElementById('register-biometric-laser');
                    if (registerLaser) registerLaser.classList.remove('hidden');
                    faceStatusContainer.classList.add('hidden');
                    btnCaptureFace.classList.remove('hidden');
                };
            } catch (err) {
                console.error("Error starting face camera:", err);
                faceStatus.innerText = "Gagal mengakses kamera. Pastikan izin kamera telah diberikan.";
            }
        }

        function closeFaceModal() {
            if (!faceModal) return;
            faceModal.classList.add('hidden');
            faceModal.classList.remove('flex');
            if (registerFaceGuideFrame) registerFaceGuideFrame.classList.add('hidden');
            const registerLaser = document.getElementById('register-biometric-laser');
            if (registerLaser) registerLaser.classList.add('hidden');
            if (faceStream) {
                faceStream.getTracks().forEach(track => track.stop());
                faceStream = null;
            }
            faceVideo.classList.add('hidden');
            btnCaptureFace.classList.add('hidden');
            faceStatusContainer.classList.remove('hidden');
            faceStatus.innerText = "Memuat kamera dan sistem pendeteksi...";
            if (faceOverlay) {
                const ctx = faceOverlay.getContext('2d');
                ctx.clearRect(0, 0, faceOverlay.width, faceOverlay.height);
            }
        }

        btnCaptureFace?.addEventListener('click', async () => {
            btnCaptureFace.disabled = true;
            btnCaptureFace.innerHTML = '<span>Memproses...</span>';

            try {
                const displaySize = { width: faceVideo.videoWidth || 640, height: faceVideo.videoHeight || 480 };
                faceapi.matchDimensions(faceOverlay, displaySize);

                const detections = await faceapi.detectAllFaces(faceVideo, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.5 }))
                                               .withFaceLandmarks()
                                               .withFaceDescriptors();

                const resizedDetections = faceapi.resizeResults(detections, displaySize);

                const roi = getGuideBoxRoi(registerFaceGuideBox, faceVideo, displaySize);

                let validFace = null;
                let hasCuttingFace = false;

                for (const d of resizedDetections) {
                    const status = checkFaceInRoi(d, roi);
                    if (status.isFull) {
                        if (!validFace || (d.detection.box.width * d.detection.box.height) > (validFace.detection.box.width * validFace.detection.box.height)) {
                            validFace = d;
                        }
                    } else if (status.isCutting) {
                        hasCuttingFace = true;
                    }
                }

                if (!validFace) {
                    if (hasCuttingFace) {
                        alert("Wajah Anda masih terpotong bingkai. Pastikan seluruh bagian wajah (termasuk dagu, dahi, dan pipi) berada penuh di dalam bingkai panduan.");
                    } else {
                        alert("Wajah tidak terdeteksi di dalam bingkai panduan. Pastikan posisi wajah Anda tepat di dalam bingkai kamera.");
                    }
                    btnCaptureFace.disabled = false;
                    btnCaptureFace.innerHTML = '<i data-lucide="scan" class="h-4 w-4"></i><span>Ambil Wajah</span>';
                    lucide.createIcons();
                    return;
                }

                const targetFace = validFace;

                // Gambar deteksi wajah & titik biometrik pada overlay
                const ctx = faceOverlay.getContext('2d');
                ctx.clearRect(0, 0, faceOverlay.width, faceOverlay.height);
                drawBiometricLandmarks(ctx, targetFace);
                faceapi.draw.drawDetections(faceOverlay, targetFace);

                // Ekstrak array 128 float descriptor
                const descriptorArray = Array.from(targetFace.descriptor);
                faceDescriptorInput.value = JSON.stringify(descriptorArray);

                // Tangkap frame foto dari video stream
                const captureCanvas = document.createElement('canvas');
                captureCanvas.width = faceVideo.videoWidth || 640;
                captureCanvas.height = faceVideo.videoHeight || 480;
                const captureCtx = captureCanvas.getContext('2d');
                captureCtx.drawImage(faceVideo, 0, 0, captureCanvas.width, captureCanvas.height);
                const dataUrl = captureCanvas.toDataURL('image/jpeg', 0.9);
                fotoWajahInput.value = dataUrl;

                // Terapkan state wajah terdaftar ke tampilan form
                setFaceRecordedState(dataUrl);

                // Tutup modal
                closeFaceModal();
            } catch (err) {
                console.error("Error capturing face:", err);
                alert("Terjadi kesalahan saat memproses data wajah.");
            } finally {
                btnCaptureFace.disabled = false;
                btnCaptureFace.innerHTML = '<i data-lucide="scan" class="h-4 w-4"></i><span>Ambil Wajah</span>';
                lucide.createIcons();
            }
        });

        // ==========================================
        // DYNAMIC JABATAN & BIDANG SESUAI INSTANSI
        // ==========================================
        const instansiMasterData = @json($masterInstansiData ?? []);
        const instansiSelect = document.getElementById('instansi');
        const jabatanSelect = document.getElementById('jabatan');
        const bidangSelect = document.getElementById('bidang');
        const jabatanManualContainer = document.getElementById('jabatan_manual_container');
        const jabatanManualInput = document.getElementById('jabatan_manual');
        const bidangManualContainer = document.getElementById('bidang_manual_container');
        const bidangManualInput = document.getElementById('bidang_manual');

        let initialOldJabatan = @json(old('jabatan') ?? '');
        let initialOldBidang = @json(old('bidang') ?? '');

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function populateJabatanDanBidang(instansiKey, preserveOld = false) {
            if (!jabatanSelect || !bidangSelect) return;

            if (!instansiKey || !instansiMasterData[instansiKey]) {
                jabatanSelect.innerHTML = '<option value="">-- Pilih Instansi Terlebih Dahulu --</option>';
                jabatanSelect.disabled = true;
                if (jabatanManualContainer) jabatanManualContainer.classList.add('hidden');
                if (jabatanManualInput) jabatanManualInput.value = '';

                bidangSelect.innerHTML = '<option value="">-- Pilih Instansi Terlebih Dahulu --</option>';
                bidangSelect.disabled = true;
                if (bidangManualContainer) bidangManualContainer.classList.add('hidden');
                if (bidangManualInput) bidangManualInput.value = '';
                return;
            }

            const data = instansiMasterData[instansiKey] || { jabatan: [], bidang: [] };
            jabatanSelect.disabled = false;
            bidangSelect.disabled = false;

            // 1. Populate Jabatan
            let jabatanHtml = '<option value="">Pilih jabatan</option>';
            const targetJabatan = preserveOld ? initialOldJabatan : jabatanSelect.value;
            let jabatanMatched = false;

            (data.jabatan || []).forEach(j => {
                const isSelected = targetJabatan && targetJabatan === j;
                if (isSelected) jabatanMatched = true;
                jabatanHtml += `<option value="${escapeHtml(j)}" ${isSelected ? 'selected' : ''}>${escapeHtml(j)}</option>`;
            });

            const isManualJabatan = targetJabatan && !jabatanMatched;
            jabatanHtml += `<option value="__lainnya__" ${isManualJabatan ? 'selected' : ''}>Lainnya / Tulis Manual</option>`;
            jabatanSelect.innerHTML = jabatanHtml;

            if (isManualJabatan) {
                if (jabatanManualContainer) jabatanManualContainer.classList.remove('hidden');
                if (jabatanManualInput) jabatanManualInput.value = targetJabatan;
            } else {
                if (jabatanManualContainer) jabatanManualContainer.classList.add('hidden');
                if (jabatanManualInput) jabatanManualInput.value = '';
            }

            // 2. Populate Bidang
            let bidangHtml = '<option value="">Pilih bidang (opsional)</option>';
            const targetBidang = preserveOld ? initialOldBidang : bidangSelect.value;
            let bidangMatched = false;

            (data.bidang || []).forEach(b => {
                const isSelected = targetBidang && targetBidang === b;
                if (isSelected) bidangMatched = true;
                bidangHtml += `<option value="${escapeHtml(b)}" ${isSelected ? 'selected' : ''}>${escapeHtml(b)}</option>`;
            });

            const isManualBidang = targetBidang && !bidangMatched;
            bidangHtml += `<option value="__lainnya__" ${isManualBidang ? 'selected' : ''}>Lainnya / Tulis Manual</option>`;
            bidangSelect.innerHTML = bidangHtml;

            if (isManualBidang) {
                if (bidangManualContainer) bidangManualContainer.classList.remove('hidden');
                if (bidangManualInput) bidangManualInput.value = targetBidang;
            } else {
                if (bidangManualContainer) bidangManualContainer.classList.add('hidden');
                if (bidangManualInput) bidangManualInput.value = '';
            }
        }

        instansiSelect?.addEventListener('change', function () {
            initialOldJabatan = '';
            initialOldBidang = '';
            populateJabatanDanBidang(this.value, false);
        });

        jabatanSelect?.addEventListener('change', function () {
            if (this.value === '__lainnya__') {
                if (jabatanManualContainer) jabatanManualContainer.classList.remove('hidden');
                jabatanManualInput?.focus();
            } else {
                if (jabatanManualContainer) jabatanManualContainer.classList.add('hidden');
                if (jabatanManualInput) jabatanManualInput.value = '';
            }
        });

        bidangSelect?.addEventListener('change', function () {
            if (this.value === '__lainnya__') {
                if (bidangManualContainer) bidangManualContainer.classList.remove('hidden');
                bidangManualInput?.focus();
            } else {
                if (bidangManualContainer) bidangManualContainer.classList.add('hidden');
                if (bidangManualInput) bidangManualInput.value = '';
            }
        });

        const registerForm = document.querySelector('form[action="{{ route("pegawai.register.submit") }}"]') || document.querySelector('form');
        registerForm?.addEventListener('submit', function (e) {
            // 1. Validasi Perekaman Wajah (Face Recognition) Wajib (otomatis menjadi foto profil)
            if (!faceDescriptorInput?.value || !fotoWajahInput?.value) {
                e.preventDefault();
                alert('Perekaman Wajah (Face Recognition) wajib dilakukan sebelum mendaftar. Foto profil akan otomatis diambil dari hasil perekaman wajah.');
                openFaceModal();
                return false;
            }

            // 3. Validasi Jabatan Manual jika memilih Lainnya
            if (jabatanSelect && jabatanSelect.value === '__lainnya__') {
                const val = jabatanManualInput ? jabatanManualInput.value.trim() : '';
                if (!val) {
                    e.preventDefault();
                    alert('Mohon ketikkan nama jabatan Anda pada kotak input manual.');
                    jabatanManualInput?.focus();
                    return false;
                }
                jabatanSelect.options[jabatanSelect.selectedIndex].value = val;
            }

            // 4. Validasi Bidang Manual jika memilih Lainnya
            if (bidangSelect && bidangSelect.value === '__lainnya__') {
                const val = bidangManualInput ? bidangManualInput.value.trim() : '';
                bidangSelect.options[bidangSelect.selectedIndex].value = val;
            }
        });

        // Cek apakah ada old data foto_wajah dan face_descriptor (misal jika ada error validasi field lain)
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi dropdown Jabatan & Bidang
            if (instansiSelect && instansiSelect.value) {
                populateJabatanDanBidang(instansiSelect.value, true);
            } else {
                populateJabatanDanBidang('', false);
            }

            const existingFotoWajah = fotoWajahInput?.value;
            const existingDescriptor = faceDescriptorInput?.value;
            if (existingFotoWajah && existingDescriptor) {
                setFaceRecordedState(existingFotoWajah);
            }
        });
    </script>
</body>
</html>
