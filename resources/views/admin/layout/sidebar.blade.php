@php
    $appName = config('sirapi.name', 'RAPID');
    if (Auth::guard('admin')->check() && in_array(Auth::guard('admin')->user()->role, ['admin_dinas', 'dinas']) && Auth::guard('admin')->user()->dinas) {
        $organizationName = Auth::guard('admin')->user()->dinas->nama_dinas;
    } elseif (Auth::guard('admin')->check() && in_array(Auth::guard('admin')->user()->role, ['admin_kecamatan', 'kecamatan']) && Auth::guard('admin')->user()->kecamatan) {
        $organizationName = Auth::guard('admin')->user()->kecamatan->nama_kecamatan;
    } else {
        $organizationName = config('sirapi.organization', 'Dinas Komunikasi & Informatika');
    }
    $regionName = config('sirapi.region', 'Pemerintah Kabupaten Bogor');
@endphp

<!-- Mobile Overlay Backdrop -->
<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity"></div>

<!-- Sidebar Container (White Theme with Green Text & Icons) -->
<aside id="sidebar-menu" class="fixed md:static inset-y-0 left-0 z-50 w-72 md:w-[270px] h-screen bg-white dark:bg-[#0f1c19] border-r border-gray-200/80 dark:border-[#233a34] text-gray-800 dark:text-white flex flex-col justify-between font-sans shadow-xl md:shadow-[6px_0_30px_rgba(0,0,0,0.06)] select-none transform -translate-x-full md:translate-x-0 transition-all duration-300 ease-in-out">
    
    <div>
        <!-- Logo & Header (Solid Green RAPID Card) -->
        <div class="px-5 py-4 min-h-[64px] sm:min-h-[72px] flex items-center gap-3 bg-[#35635b] dark:bg-[#16352e] text-white border-b border-[#2a5049] dark:border-[#233a34] shadow-sm">
            <div class="w-9 h-10 rounded-xl bg-white/15 dark:bg-white/10 border border-white/25 dark:border-white/20 p-1.5 flex items-center justify-center shrink-0 shadow-xs">
                <img src="{{ asset('assets/foto/logo-bappenda.png') }}" alt="Logo Kab. Bogor" class="w-full h-full object-contain drop-shadow-xs">
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="font-black text-xl leading-none tracking-wide text-white drop-shadow-xs">RAPID</h1>
                <p class="text-[8px] font-bold text-white/85 dark:text-emerald-200 tracking-wider uppercase mt-1 leading-none whitespace-nowrap">RAPAT DAN PRESENSI INTEGRASI DIGITAL</p>
            </div>
            <!-- Mobile Close Button -->
            <button onclick="toggleSidebar()" class="md:hidden ml-auto w-8 h-8 rounded-lg bg-white/15 hover:bg-white/25 flex items-center justify-center text-white focus:outline-none shrink-0 transition-colors cursor-pointer" title="Tutup Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="mt-4 px-3 space-y-1.5 text-sm font-medium">
            
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#35635b] dark:bg-[#1a332d] text-white dark:text-emerald-400 font-bold shadow-md dark:border dark:border-[#284c43]' : 'hover:bg-[#35635b]/10 dark:hover:bg-[#152420] text-[#35635b] dark:text-gray-300 dark:hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white dark:text-emerald-400' : 'text-[#35635b] dark:text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span>Dashboard</span>
            </a>

            <!-- Agenda Submenu -->
            @php
                $isAgendaActive = request()->routeIs('admin.agenda.*') || request()->routeIs('admin.ruang.*');
            @endphp
            <div class="space-y-1">
                <button type="button" onclick="toggleSidebarSubmenu(this)" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all {{ $isAgendaActive ? 'bg-[#35635b] dark:bg-[#1a332d] text-white dark:text-emerald-400 font-bold shadow-md dark:border dark:border-[#284c43]' : 'hover:bg-[#35635b]/10 dark:hover:bg-[#152420] text-[#35635b] dark:text-gray-300 dark:hover:text-white' }} focus:outline-none cursor-pointer">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 {{ $isAgendaActive ? 'text-white dark:text-emerald-400' : 'text-[#35635b] dark:text-emerald-400/80' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="17" rx="3.5" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-width="2.5" d="M8 2v4M16 2v4M3 9h18"/>
                            <path stroke-linecap="round" stroke-width="2.5" d="M7 13h2M11 13h2M15 13h2M7 17h2M11 17h2M15 17h2"/>
                        </svg>
                        <span>Agenda</span>
                    </div>
                    <svg class="w-4 h-4 arrow-icon transition-transform {{ $isAgendaActive ? 'rotate-180 text-white dark:text-emerald-400' : 'text-[#35635b] dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <div class="{{ $isAgendaActive ? 'flex' : 'hidden' }} flex-col pl-11 pr-4 py-1 space-y-1">
                    <a href="{{ route('admin.agenda.lihat') }}" class="block text-xs font-semibold py-2 px-3 rounded-lg transition {{ request()->routeIs('admin.agenda.lihat') ? 'bg-[#35635b]/15 dark:bg-[#23423b] font-bold text-[#35635b] dark:text-emerald-300' : 'text-[#35635b]/80 dark:text-gray-400 hover:bg-[#35635b]/10 dark:hover:bg-[#152420] dark:hover:text-white' }}">Daftar Agenda</a>
                    <a href="{{ route('admin.ruang.lihat') }}" class="block text-xs font-semibold py-2 px-3 rounded-lg transition {{ request()->routeIs('admin.ruang.lihat') ? 'bg-[#35635b]/15 dark:bg-[#23423b] font-bold text-[#35635b] dark:text-emerald-300' : 'text-[#35635b]/80 dark:text-gray-400 hover:bg-[#35635b]/10 dark:hover:bg-[#152420] dark:hover:text-white' }}">Daftar Ruangan</a>
                </div>
            </div>

            <!-- Data Pengguna Submenu -->
            @php $isUserActive = request()->routeIs('admin.pegawai.*') || request()->routeIs('admin.tamu.*'); @endphp
            <div class="space-y-1">
                <button type="button" onclick="toggleSidebarSubmenu(this)" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all {{ $isUserActive ? 'bg-[#35635b] dark:bg-[#1a332d] text-white dark:text-emerald-400 font-bold shadow-md dark:border dark:border-[#284c43]' : 'hover:bg-[#35635b]/10 dark:hover:bg-[#152420] text-[#35635b] dark:text-gray-300 dark:hover:text-white' }} focus:outline-none cursor-pointer">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 {{ $isUserActive ? 'text-white dark:text-emerald-400' : 'text-[#35635b] dark:text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span>Data Pengguna</span>
                    </div>
                    <svg class="w-4 h-4 arrow-icon transition-transform {{ $isUserActive ? 'rotate-180 text-white dark:text-emerald-400' : 'text-[#35635b] dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div class="{{ $isUserActive ? 'flex' : 'hidden' }} flex-col pl-11 pr-4 py-1 space-y-1">
                    <a href="{{ route('admin.pegawai.lihat') }}" class="flex items-center justify-between text-xs font-semibold py-2 px-3 rounded-lg transition {{ request()->routeIs('admin.pegawai.lihat') ? 'bg-[#35635b]/15 dark:bg-[#23423b] font-bold text-[#35635b] dark:text-emerald-300' : 'text-[#35635b]/80 dark:text-gray-400 hover:bg-[#35635b]/10 dark:hover:bg-[#152420] dark:hover:text-white' }}">
                        <span>Data Pegawai</span>
                        @php
                            $pendingPegawaiCount = \App\Models\Pegawai::where('status_verifikasi', 'pending')->count();
                        @endphp
                        @if ($pendingPegawaiCount > 0)
                            <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-extrabold leading-none text-white bg-amber-500 rounded-full shadow-xs">{{ $pendingPegawaiCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.tamu.lihat') }}" class="block text-xs font-semibold py-2 px-3 rounded-lg transition {{ request()->routeIs('admin.tamu.lihat') ? 'bg-[#35635b]/15 dark:bg-[#23423b] font-bold text-[#35635b] dark:text-emerald-300' : 'text-[#35635b]/80 dark:text-gray-400 hover:bg-[#35635b]/10 dark:hover:bg-[#152420] dark:hover:text-white' }}">Data Tamu</a>
                </div>
            </div>

            <!-- Instansi (Single Page) - Hanya untuk Superadmin -->
            @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->isSuperAdmin())
            @php $isInstansiActive = request()->routeIs('admin.instansi.*') || request()->routeIs('admin.dinas.*') || request()->routeIs('admin.kecamatan.*'); @endphp
            <a href="{{ route('admin.instansi.index') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ $isInstansiActive ? 'bg-[#35635b] dark:bg-[#1a332d] text-white dark:text-emerald-400 font-bold shadow-md dark:border dark:border-[#284c43]' : 'hover:bg-[#35635b]/10 dark:hover:bg-[#152420] text-[#35635b] dark:text-gray-300 dark:hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 {{ $isInstansiActive ? 'text-white dark:text-emerald-400' : 'text-[#35635b] dark:text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0v10"/>
                </svg>
                <span>Instansi</span>
            </a>
            @endif

            <!-- Kunjungan (Kunker) -->
            <a href="{{ route('admin.kunjungan.lihat') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.kunjungan.*') ? 'bg-[#35635b] dark:bg-[#1a332d] text-white dark:text-emerald-400 font-bold shadow-md dark:border dark:border-[#284c43]' : 'hover:bg-[#35635b]/10 dark:hover:bg-[#152420] text-[#35635b] dark:text-gray-300 dark:hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.kunjungan.*') ? 'text-white dark:text-emerald-400' : 'text-[#35635b] dark:text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Kunjungan</span>
            </a>

            <!-- Manajemen Akun Submenu - Hanya untuk Superadmin -->
            @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->isSuperAdmin())
            @php $isAkunActive = request()->routeIs('admin.akun.*'); @endphp
            <div class="space-y-1">
                <button type="button" onclick="toggleSidebarSubmenu(this)" class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all {{ $isAkunActive ? 'bg-[#35635b] dark:bg-[#1a332d] text-white dark:text-emerald-400 font-bold shadow-md dark:border dark:border-[#284c43]' : 'hover:bg-[#35635b]/10 dark:hover:bg-[#152420] text-[#35635b] dark:text-gray-300 dark:hover:text-white' }} focus:outline-none cursor-pointer">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 {{ $isAkunActive ? 'text-white dark:text-emerald-400' : 'text-[#35635b] dark:text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        <span>Manajemen Akun</span>
                    </div>
                    <svg class="w-4 h-4 arrow-icon transition-transform {{ $isAkunActive ? 'rotate-180 text-white dark:text-emerald-400' : 'text-[#35635b] dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div class="{{ $isAkunActive ? 'flex' : 'hidden' }} flex-col pl-11 pr-4 py-1 space-y-1">
                    <a href="{{ route('admin.akun.dinas.index') }}" class="block text-xs font-semibold py-2 px-3 rounded-lg transition {{ request()->routeIs('admin.akun.dinas.*') ? 'bg-[#35635b]/15 dark:bg-[#23423b] font-bold text-[#35635b] dark:text-emerald-300' : 'text-[#35635b]/80 dark:text-gray-400 hover:bg-[#35635b]/10 dark:hover:bg-[#152420] dark:hover:text-white' }}">Akun Dinas</a>
                    <a href="{{ route('admin.akun.kecamatan.index') }}" class="block text-xs font-semibold py-2 px-3 rounded-lg transition {{ request()->routeIs('admin.akun.kecamatan.*') ? 'bg-[#35635b]/15 dark:bg-[#23423b] font-bold text-[#35635b] dark:text-emerald-300' : 'text-[#35635b]/80 dark:text-gray-400 hover:bg-[#35635b]/10 dark:hover:bg-[#152420] dark:hover:text-white' }}">Akun Kecamatan</a>
                </div>
            </div>
            @endif

            @if(!Auth::guard('admin')->check() || !in_array(Auth::guard('admin')->user()->role, ['admin_kecamatan', 'kecamatan']))
            <!-- Masukkan / Aduan -->
            <a href="{{ route('admin.masukkan.lihat') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.masukkan.lihat') ? 'bg-[#35635b] dark:bg-[#1a332d] text-white dark:text-emerald-400 font-bold shadow-md dark:border dark:border-[#284c43]' : 'hover:bg-[#35635b]/10 dark:hover:bg-[#152420] text-[#35635b] dark:text-gray-300 dark:hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.masukkan.lihat') ? 'text-white dark:text-emerald-400' : 'text-[#35635b] dark:text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                </svg>
                <span>Aduan</span>
            </a>
            @endif



        </nav>
    </div>

    <!-- Bottom Logout Button -->
    <div class="p-4 border-t border-gray-100 dark:border-[#233a34]">
        <button type="button" onclick="openAdminLogoutModal()" class="w-full flex items-center justify-center gap-2.5 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-600 dark:bg-white/10 dark:hover:bg-red-600/90 text-red-600 hover:text-white dark:text-white border border-red-200 dark:border-white/15 text-xs font-bold transition-all shadow-xs hover:shadow-md cursor-pointer group" title="Keluar">
            <span>Keluar</span>
        </button>
    </div>

</aside>

<!-- Modal Konfirmasi Logout -->
<div id="logoutModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-xs items-center justify-center p-4 transition-all duration-200" onclick="if(event.target === this) closeAdminLogoutModal()">
    <div class="relative w-full max-w-sm rounded-xl bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] text-center shadow-2xl p-6 sm:p-8 transform scale-95 transition-all">
        <!-- Ikon Peringatan -->
        <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-950/60 text-red-600 dark:text-red-400 mb-4 shadow-sm">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
            </svg>
        </div>
        
        <!-- Teks Konfirmasi -->
        <div class="space-y-1.5 mb-6">
            <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white">Konfirmasi Keluar</h3>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-300 font-medium">Apakah Anda yakin ingin keluar dari sistem?</p>
        </div>
        
        <!-- Tombol Aksi -->
        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-[#233a34]">
            <button type="button" onclick="closeAdminLogoutModal()" class="inline-flex w-full h-10 items-center justify-center rounded-xl bg-white dark:bg-[#0f1c19] px-4 text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 shadow-sm border border-gray-300 dark:border-[#284c43] hover:bg-gray-50 dark:hover:bg-white/5 transition cursor-pointer">
                Batal
            </button>

            <!-- Form Logout -->
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0 w-full">
                @csrf 
                <button type="submit" class="inline-flex w-full h-10 items-center justify-center rounded-xl bg-red-600 hover:bg-red-700 px-4 text-xs sm:text-sm font-bold text-white shadow-md transition cursor-pointer">
                    Ya, Keluar
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSidebarSubmenu(btn) {
        const submenu = btn.nextElementSibling;
        if (!submenu) return;
        const arrow = btn.querySelector('.arrow-icon');
        
        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            submenu.classList.add('flex');
        } else {
            submenu.classList.add('hidden');
            submenu.classList.remove('flex');
        }
        
        if (arrow) {
            arrow.classList.toggle('rotate-180');
        }
    }

    function openAdminLogoutModal() {
        const modal = document.getElementById('logoutModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    function closeAdminLogoutModal() {
        const modal = document.getElementById('logoutModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
