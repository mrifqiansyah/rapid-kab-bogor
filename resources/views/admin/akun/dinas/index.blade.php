@extends('admin.layout.app')

@section('title', 'Manajemen Akun Admin')
@section('header_title', 'Manajemen Akun Admin')
@section('header_subtitle', 'Kelola akun admin untuk masing-masing instansi dinas Kabupaten Bogor')

@section('content')
<div class="max-w-[1400px] mx-auto space-y-6">

    <!-- Stat Cards (Clickable Filter) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('admin.akun.dinas.index', array_merge(request()->except('status', 'page'), ['status' => 'semua'])) }}" 
           class="bg-white dark:bg-[#152420] border rounded-xl p-5 shadow-xs transition-all hover:border-[#35635b] block {{ ($statusFilter ?? 'semua') === 'semua' ? 'border-[#35635b] ring-2 ring-[#35635b]/20 dark:border-emerald-500' : 'border-gray-100 dark:border-[#233a34]' }}">
            <p class="text-[11px] font-bold text-gray-400 dark:text-gray-300 uppercase tracking-wider">Total Akun Admin</p>
            <p class="mt-2 text-3xl font-black text-[#35635b] dark:text-emerald-400">{{ $totalAkun ?? $akunList->count() }}</p>
        </a>
        <a href="{{ route('admin.akun.dinas.index', array_merge(request()->except('status', 'page'), ['status' => 'aktif'])) }}" 
           class="bg-white dark:bg-[#152420] border rounded-xl p-5 shadow-xs transition-all hover:border-emerald-500 block {{ ($statusFilter ?? '') === 'aktif' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-gray-100 dark:border-[#233a34]' }}">
            <p class="text-[11px] font-bold text-gray-400 dark:text-gray-300 uppercase tracking-wider">Akun Aktif</p>
            <p class="mt-2 text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $totalAktif ?? 0 }}</p>
        </a>
        <a href="{{ route('admin.akun.dinas.index', array_merge(request()->except('status', 'page'), ['status' => 'nonaktif'])) }}" 
           class="bg-white dark:bg-[#152420] border rounded-xl p-5 shadow-xs transition-all hover:border-amber-500 block {{ ($statusFilter ?? '') === 'nonaktif' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-gray-100 dark:border-[#233a34]' }}">
            <p class="text-[11px] font-bold text-gray-400 dark:text-gray-300 uppercase tracking-wider">Akun Nonaktif</p>
            <p class="mt-2 text-3xl font-black text-amber-600 dark:text-amber-400">{{ $totalNonaktif ?? 0 }}</p>
        </a>
    </div>

    <!-- Table Section -->
    <div class="bg-white dark:bg-[#152420] rounded-2xl shadow-xs border border-gray-100 dark:border-[#233a34] overflow-hidden transition-colors">
        <!-- Card Header: Title (Left), Search & Filters (Middle), Button (Right) -->
        <div class="border-b border-gray-100 dark:border-[#233a34] px-5 sm:px-6 py-4 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <!-- Left: Title & Subtitle -->
            <div class="shrink-0">
                <h2 class="text-base font-extrabold text-gray-800 dark:text-white">Daftar Akun Admin</h2>
                <p id="text-count-akun-admin" class="mt-0.5 text-xs text-gray-500 dark:text-gray-300">Menampilkan {{ $akunList->count() }} dari {{ $totalAkun ?? $akunList->count() }} akun admin.</p>
            </div>

            <!-- Right Controls: Search, Instansi Filter, and Buat Akun Baru Button -->
            <div class="flex flex-col sm:flex-row items-center gap-3 flex-1 justify-end w-full xl:w-auto">
                <form id="form-search-akun-admin" method="GET" action="{{ route('admin.akun.dinas.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-2 w-full max-w-xl">
                    <input type="hidden" name="status" value="{{ $statusFilter ?? 'semua' }}">
                    <div class="relative flex-1 w-full min-w-[200px]">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input
                            id="keyword"
                            name="keyword"
                            value="{{ $keyword ?? request('keyword') }}"
                            type="search"
                            autocomplete="off"
                            class="h-10 w-full pl-10 pr-4 rounded-xl border border-gray-200 dark:border-[#284c43] bg-gray-50 dark:bg-[#0f1c19] text-xs text-gray-700 dark:text-white outline-none transition focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="Cari akun berdasarkan...">
                    </div>
                    <select
                        id="dinas-filter"
                        name="dinas"
                        class="h-10 w-full sm:w-60 shrink-0 rounded-xl border border-gray-200 dark:border-[#284c43] bg-gray-50 dark:bg-[#0f1c19] px-3 text-xs font-medium text-gray-700 dark:text-white outline-none transition focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 cursor-pointer truncate">
                        <option value="semua" @selected(($dinasFilter ?? 'semua') === 'semua')>Semua Instansi</option>
                        <option value="superadmin" @selected(($dinasFilter ?? 'semua') === 'superadmin')>Super Admin (Akses Semua)</option>
                        @foreach ($masterDinas as $d)
                            <option value="{{ $d->id_dinas }}" @selected(($dinasFilter ?? 'semua') == $d->id_dinas)>{{ $d->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </form>

                <!-- Action Button -->
                <button onclick="openModal('modal-tambah-akun-admin')" class="w-full sm:w-auto bg-[#35635b] hover:bg-[#2b4f49] dark:bg-[#107050] dark:hover:bg-[#0c5940] text-white font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-1.5 transition shadow-xs text-xs border border-transparent dark:border-[#10b981]/30 cursor-pointer shrink-0 whitespace-nowrap">
                    <span class="text-base leading-none font-bold">+</span>
                    <span>Buat Akun Baru</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[850px]">
                <thead>
                    <tr class="bg-[#35635b] dark:bg-[#1b3832] text-white text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">NAMA ADMIN</th>
                        <th class="px-6 py-4">USERNAME</th>
                        <th class="px-6 py-4">INSTANSI DINAS</th>
                        <th class="px-6 py-4">STATUS AKUN</th>
                        <th class="px-6 py-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#233a34] text-sm">
                    @forelse ($akunList as $item)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-[#1b332d] transition">
                            <td class="px-6 py-4">
                                <span class="font-extrabold text-gray-800 dark:text-slate-100 tracking-tight">{{ $item->nama }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-gray-600 dark:text-gray-300 font-mono">{{ $item->username }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-700 dark:text-slate-200">
                                @if ($item->isSuperAdmin())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                        Super Admin (Akses Semua Instansi)
                                    </span>
                                @else
                                    {{ $item->dinas->nama_lengkap ?? '-' }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->status === 'aktif')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                        NONAKTIF
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2 whitespace-nowrap">
                                    <button
                                        type="button"
                                        onclick="openEditAkunAdmin(this)"
                                        data-id="{{ $item->id_admin }}"
                                        data-action="{{ route('admin.akun.dinas.update', $item->id_admin) }}"
                                        data-nama="{{ $item->nama }}"
                                        data-username="{{ $item->username }}"
                                        data-dinas="{{ $item->isSuperAdmin() ? 'superadmin' : ($item->id_dinas ?? '') }}"
                                        data-status="{{ $item->status }}"
                                        class="inline-flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-[#0f513f] dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 px-3.5 py-1.5 text-xs font-bold transition hover:bg-emerald-100 dark:hover:bg-emerald-900/60 cursor-pointer shadow-2xs"
                                        title="Edit Akun">
                                        <span>Edit</span>
                                    </button>
                                    <button
                                        type="button"
                                        onclick="openResetPasswordAkun('{{ route('admin.akun.dinas.reset-password', $item->id_admin) }}', '{{ $item->nama }}')"
                                        class="inline-flex items-center justify-center rounded-lg bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/50 dark:hover:bg-amber-900/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 px-2.5 py-1.5 text-xs font-bold transition cursor-pointer shadow-2xs"
                                        title="Reset Password">
                                        <span>Reset</span>
                                    </button>
                                    <button
                                        type="button"
                                        onclick="openDeleteModal('{{ route('admin.akun.dinas.destroy', $item->id_admin) }}', 'Hapus Akun Admin?', 'Apakah Anda yakin ingin menghapus akun {{ $item->nama }}?')"
                                        class="inline-flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/60 text-red-600 dark:text-red-300 border border-red-200/80 dark:border-red-800/60 px-2.5 py-1.5 text-xs font-bold transition cursor-pointer shadow-2xs"
                                        title="Hapus Akun">
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada akun admin yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Buat Akun Admin Baru -->
<div id="modal-tambah-akun-admin" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden text-left relative transform transition-all">
        <!-- RAPID Green Header -->
        <div class="bg-[#35635b] dark:bg-[#1b3832] px-6 py-4 flex items-center justify-between border-b border-[#284c43]/40">
            <h3 class="text-base sm:text-lg font-bold text-white tracking-wide">Buat Akun Admin Baru</h3>
            <button type="button" onclick="closeModal('modal-tambah-akun-admin')" class="w-8 h-8 rounded-lg bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('admin.akun.dinas.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama" 
                    required 
                    placeholder="Nama lengkap admin..." 
                    class="w-full rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-gray-800 dark:text-white placeholder-gray-400 outline-none focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 transition">
            </div>

            <!-- Username -->
            <div>
                <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                    Username <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="username" 
                    required 
                    placeholder="Username untuk login..." 
                    class="w-full rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-gray-800 dark:text-white placeholder-gray-400 outline-none focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 transition">
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                    Password <span class="text-red-500">*</span>
                </label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    minlength="6"
                    placeholder="Min. 6 karakter..." 
                    class="w-full rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-gray-800 dark:text-white placeholder-gray-400 outline-none focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 transition">
            </div>

            <!-- Hak Akses Instansi (Dinas) -->
            <div>
                <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                    Hak Akses Instansi (Dinas)
                </label>
                <select 
                    name="id_dinas" 
                    required 
                    class="w-full rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 transition">
                    <option value="superadmin">Super Admin (Akses Semua Instansi)</option>
                    @foreach ($masterDinas as $d)
                        <option value="{{ $d->id_dinas }}">{{ $d->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-3 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-[#233a34]">
                <button 
                    type="button" 
                    onclick="closeModal('modal-tambah-akun-admin')" 
                    class="px-5 py-2.5 text-xs font-bold rounded-xl border border-gray-300 dark:border-[#284c43] bg-white dark:bg-[#152420] text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition cursor-pointer">
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 text-xs font-bold rounded-xl bg-[#35635b] hover:bg-[#2b4f49] dark:bg-[#107050] dark:hover:bg-[#0c5940] text-white shadow-md hover:shadow-lg transition cursor-pointer border border-transparent dark:border-[#10b981]/30">
                    Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Akun Admin -->
<div id="modal-edit-akun-admin" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden text-left relative transform transition-all">
        <!-- RAPID Green Header -->
        <div class="bg-[#35635b] dark:bg-[#1b3832] px-6 py-4 flex items-center justify-between border-b border-[#284c43]/40">
            <h3 class="text-base sm:text-lg font-bold text-white tracking-wide">Edit Akun Admin</h3>
            <button type="button" onclick="closeModal('modal-edit-akun-admin')" class="w-8 h-8 rounded-lg bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="form-edit-akun-admin" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="edit-nama-admin"
                    name="nama" 
                    required 
                    placeholder="Nama lengkap admin..." 
                    class="w-full rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-gray-800 dark:text-white placeholder-gray-400 outline-none focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 transition">
            </div>

            <!-- Username -->
            <div>
                <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                    Username <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="edit-username-admin"
                    name="username" 
                    required 
                    placeholder="Username untuk login..." 
                    class="w-full rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-gray-800 dark:text-white placeholder-gray-400 outline-none focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 transition">
            </div>

            <!-- Password (Opsional) -->
            <div>
                <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                    Password Baru <span class="text-xs text-gray-400 font-normal">(Kosongkan jika tidak diubah)</span>
                </label>
                <input 
                    type="password" 
                    name="password" 
                    minlength="6"
                    placeholder="Min. 6 karakter jika ingin mengganti..." 
                    class="w-full rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-gray-800 dark:text-white placeholder-gray-400 outline-none focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 transition">
            </div>

            <!-- Hak Akses Instansi (Dinas) -->
            <div>
                <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                    Hak Akses Instansi (Dinas)
                </label>
                <select 
                    id="edit-dinas-id"
                    name="id_dinas" 
                    required 
                    class="w-full rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 transition">
                    <option value="superadmin">Super Admin (Akses Semua Instansi)</option>
                    @foreach ($masterDinas as $d)
                        <option value="{{ $d->id_dinas }}">{{ $d->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Akun -->
            <div>
                <label class="block text-xs font-bold text-gray-800 dark:text-gray-200 mb-1.5">
                    Status Akun
                </label>
                <select 
                    id="edit-status-admin"
                    name="status" 
                    required 
                    class="w-full rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] px-4 py-2.5 sm:py-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 transition">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-3 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-[#233a34]">
                <button 
                    type="button" 
                    onclick="closeModal('modal-edit-akun-admin')" 
                    class="px-5 py-2.5 text-xs font-bold rounded-xl border border-gray-300 dark:border-[#284c43] bg-white dark:bg-[#152420] text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition cursor-pointer">
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 text-xs font-bold rounded-xl bg-[#35635b] hover:bg-[#2b4f49] dark:bg-[#107050] dark:hover:bg-[#0c5940] text-white shadow-md hover:shadow-lg transition cursor-pointer border border-transparent dark:border-[#10b981]/30">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Password -->
<div id="modal-reset-password" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] rounded-2xl shadow-2xl max-w-sm w-full p-6 text-left relative">
        <div class="flex justify-between items-center pb-3 mb-3 border-b border-gray-100 dark:border-[#233a34]">
            <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Reset Password</h3>
            <button onclick="closeModal('modal-reset-password')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
        </div>
        <form id="form-reset-password" method="POST" class="space-y-4">
            @csrf
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-300 mb-2">Reset password untuk: <strong id="reset-target-nama" class="text-gray-800 dark:text-white"></strong></p>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Password Baru *</label>
                <input type="password" name="new_password" required minlength="6" placeholder="Masukkan password baru..." class="w-full rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] px-4 py-2.5 text-sm text-gray-800 dark:text-white outline-none focus:border-[#35635b]">
            </div>
            <div class="pt-3 flex justify-end gap-2 border-t border-gray-100 dark:border-[#233a34]">
                <button type="button" onclick="closeModal('modal-reset-password')" class="px-4 py-2 text-xs font-bold rounded-xl border border-gray-300 dark:border-[#284c43] text-gray-700 dark:text-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl bg-amber-600 text-white hover:bg-amber-700">Simpan Password</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditAkunAdmin(btn) {
        const form = document.getElementById('form-edit-akun-admin');
        form.action = btn.dataset.action;
        document.getElementById('edit-nama-admin').value = btn.dataset.nama || '';
        document.getElementById('edit-username-admin').value = btn.dataset.username || '';
        document.getElementById('edit-dinas-id').value = btn.dataset.dinas || '';
        document.getElementById('edit-status-admin').value = btn.dataset.status || 'aktif';
        openModal('modal-edit-akun-admin');
    }

    function openResetPasswordAkun(actionUrl, targetNama) {
        const form = document.getElementById('form-reset-password');
        form.action = actionUrl;
        document.getElementById('reset-target-nama').textContent = targetNama;
        openModal('modal-reset-password');
    }

    // Live Auto-Search & Instant Filter (tanpa perlu tekan Enter)
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('form-search-akun-admin');
        const searchInput = document.getElementById('keyword');
        const dinasFilter = document.getElementById('dinas-filter');
        const tableBody = document.querySelector('table tbody');
        const countText = document.getElementById('text-count-akun-admin');

        if (!form || !searchInput || !tableBody) return;

        let debounceTimer;

        function fetchResults() {
            const url = new URL(form.action);
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            url.search = params.toString();

            // Update URL di browser tanpa reload
            window.history.replaceState({}, '', url);

            // Efek loading transparan halus pada tabel
            tableBody.style.opacity = '0.5';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newTbody = doc.querySelector('table tbody');
                if (newTbody) {
                    tableBody.innerHTML = newTbody.innerHTML;
                }

                const newCount = doc.getElementById('text-count-akun-admin');
                if (countText && newCount) {
                    countText.innerHTML = newCount.innerHTML;
                }
            })
            .catch(err => console.error('Live search error:', err))
            .finally(() => {
                tableBody.style.opacity = '1';
            });
        }

        // Live input saat diketik langsung otomatis search
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchResults, 250);
        });

        // Mencegah reload halaman saat tekan Enter, langsung cari
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(debounceTimer);
                fetchResults();
            }
        });

        // Event saat tombol silang (x) di input search diklik
        searchInput.addEventListener('search', function () {
            clearTimeout(debounceTimer);
            fetchResults();
        });

        // Live filter instansi dinas
        if (dinasFilter) {
            dinasFilter.addEventListener('change', function () {
                clearTimeout(debounceTimer);
                fetchResults();
            });
        }
    });
</script>
@endsection
