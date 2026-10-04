@php
    $appName = config('sirapi.name', 'RAPID');
    $regionName = config('sirapi.region', 'Pemerintah Kabupaten Bogor');

    $routeName = request()->route() ? request()->route()->getName() : '';

    $defaultTitle = match(true) {
        str_contains($routeName, 'dashboard') => 'Dashboard',
        str_contains($routeName, 'agenda.detail') => 'Detail Agenda Rapat',
        str_contains($routeName, 'agenda.riwayat') => 'Riwayat Agenda Rapat',
        str_contains($routeName, 'agenda') => 'Daftar Agenda',
        str_contains($routeName, 'ruang') => 'Daftar Ruangan',
        str_contains($routeName, 'pegawai') => 'Data Pegawai',
        str_contains($routeName, 'tamu') => 'Data Tamu Rapat',
        str_contains($routeName, 'kunjungan') => 'Daftar Kunjungan',
        str_contains($routeName, 'instansi') => 'Master Data Instansi',
        str_contains($routeName, 'dinas') && !str_contains($routeName, 'akun') => 'Master Data Instansi',
        str_contains($routeName, 'kecamatan') && !str_contains($routeName, 'akun') => 'Master Data Instansi',
        str_contains($routeName, 'akun.dinas') => 'Manajemen Akun Admin',
        str_contains($routeName, 'akun.kecamatan') => 'Manajemen Akun Kecamatan',
        str_contains($routeName, 'masukkan') => 'Pengaduan Masyarakat',
        str_contains($routeName, 'publik') => 'Konten Publik',
        default => 'Portal Admin',
    };

    $defaultSubtitle = match(true) {
        str_contains($routeName, 'dashboard') => 'Ringkasan aktivitas agenda dan kunjungan.',
        str_contains($routeName, 'agenda.detail') => 'Detail informasi dan daftar presensi kegiatan rapat.',
        str_contains($routeName, 'agenda.riwayat') => 'Arsip dan riwayat seluruh agenda rapat yang telah selesai.',
        str_contains($routeName, 'agenda') => 'Kelola agenda kegiatan rapat dan acara instansi.',
        str_contains($routeName, 'ruang') => 'Kelola daftar dan kapasitas fasilitas ruang rapat.',
        str_contains($routeName, 'pegawai') => 'Kelola data dan verifikasi akun pegawai.',
        str_contains($routeName, 'tamu') => 'Kelola riwayat presensi tamu rapat.',
        str_contains($routeName, 'kunjungan') => 'Kelola dan pantau seluruh riwayat kunjungan di sini.',
        str_contains($routeName, 'instansi') => 'Kelola master data dinas dan kecamatan dalam satu tempat.',
        str_contains($routeName, 'dinas') && !str_contains($routeName, 'akun') => 'Kelola master data dinas dan kecamatan dalam satu tempat.',
        str_contains($routeName, 'kecamatan') && !str_contains($routeName, 'akun') => 'Kelola master data dinas dan kecamatan dalam satu tempat.',
        str_contains($routeName, 'akun.dinas') => 'Kelola akun admin untuk masing-masing instansi dinas Kabupaten Bogor',
        str_contains($routeName, 'akun.kecamatan') => 'Kelola data login dan kredensial akun perwakilan kecamatan.',
        str_contains($routeName, 'masukkan') => 'Kelola dan balasan pengaduan masyarakat.',
        str_contains($routeName, 'publik') => 'Kelola berita, galeri foto, video, dan sitemap.',
        default => 'Pemerintah Kabupaten Bogor',
    };
@endphp

<header class="bg-white dark:bg-[#152420] px-3 sm:px-8 py-3 flex items-center justify-between shadow-xs z-30 transition-colors duration-200 min-h-[64px] sm:min-h-[72px]">
    <!-- Left Side: Hamburger Menu & Title + Subtitle -->
    <div class="flex items-center space-x-2 sm:space-x-3.5 min-w-0 pr-2">
        <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 focus:outline-none shrink-0 transition-colors cursor-pointer" title="Menu Navigasi">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <div class="flex flex-col min-w-0 justify-center">
            <h1 class="text-base sm:text-2xl font-black text-[#1F2937] dark:text-white tracking-tight leading-tight truncate">
                @yield('header_title', $defaultTitle)
            </h1>
            <p class="hidden xs:block text-[11px] sm:text-xs text-gray-500 dark:text-gray-300 font-medium leading-snug truncate mt-0.5">
                @yield('header_subtitle', $defaultSubtitle)
            </p>
        </div>
    </div>

    <!-- Right Side: Actions, Mode Gelap/Terang, Tanggal, dan Akun Profile Pill -->
    <div class="flex items-center space-x-1.5 sm:space-x-3 shrink-0">
        @yield('header_actions')

        <!-- Setting / Pengaturan Button (Pojok Atas Dashboard - Modal Popup) -->
        <button type="button" onclick="openModal('modal-setting-popup')" title="Pengaturan Aplikasi & Master Data" class="p-2 sm:p-2.5 rounded-xl bg-gray-100 dark:bg-[#0f1c19] border border-gray-200/80 dark:border-[#284c43] text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-[#1b3832] hover:text-[#35635b] dark:hover:text-emerald-400 transition-all focus:outline-none shadow-2xs cursor-pointer flex items-center justify-center">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </button>

        <!-- Dark / Light Mode Switcher Button -->
        <button type="button" onclick="toggleSirapiTheme()" title="Ubah Mode Gelap / Terang" class="p-2 sm:p-2.5 rounded-xl bg-gray-100 dark:bg-[#0f1c19] border border-gray-200/80 dark:border-[#284c43] text-gray-600 dark:text-amber-400 hover:bg-gray-200 dark:hover:bg-[#1b3832] transition-all focus:outline-none shadow-2xs cursor-pointer">
            <!-- Ikon Matahari (Muncul saat Dark Mode) -->
            <svg data-theme-icon-light class="w-4 h-4 sm:w-5 sm:h-5 text-amber-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <!-- Ikon Bulan (Muncul saat Light Mode) -->
            <svg data-theme-icon-dark class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
        </button>

        <!-- Date / Time Info -->
        <div class="hidden sm:flex flex-col text-right">
            <span class="text-xs font-bold text-gray-700 dark:text-slate-200">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </div>

        <!-- User Profile Pill -->
        <div class="bg-[#35635b] dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] text-white py-1 px-2.5 sm:py-1.5 sm:px-4 rounded-full flex items-center space-x-2 sm:space-x-3 shadow-md hover:opacity-95 dark:hover:border-emerald-500/50 transition cursor-pointer">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/20 dark:bg-[#1b3832] flex items-center justify-center text-white dark:text-emerald-400 font-bold text-xs sm:text-sm overflow-hidden border border-white/30 dark:border-emerald-500/30 shrink-0">
                @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->foto)
                    <img src="{{ asset('storage/' . Auth::guard('admin')->user()->foto) }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                @endif
            </div>
            <div class="hidden sm:flex flex-col text-left">
                <span class="text-xs font-bold leading-tight text-white truncate max-w-[90px] sm:max-w-[140px]">{{ Auth::guard('admin')->user()->nama ?? 'Admin' }}</span>
                @php
                    $u = Auth::guard('admin')->user();
                    $roleLabel = match(true) {
                        $u?->isAdminKecamatan() => 'Admin Kecamatan',
                        $u?->isAdminDinas() => 'Admin Dinas',
                        default => 'Super Admin',
                    };
                @endphp
                <span class="text-[9.5px] sm:text-[10px] text-white/80 dark:text-emerald-400 font-semibold leading-tight">{{ $roleLabel }}</span>
            </div>
        </div>
    </div>
</header>
