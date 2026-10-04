@extends('admin.layout.app')

@section('title', 'Data Pegawai')

@section('header_actions')
<button onclick="openModal('modal-tambah-pegawai')" class="bg-[#35635b] hover:bg-[#2b4f49] dark:bg-[#107050] dark:hover:bg-[#0c5940] text-white font-bold py-2 px-4 rounded-xl flex items-center justify-center gap-1.5 transition shadow-xs text-xs border border-transparent dark:border-[#10b981]/30 cursor-pointer">
    <span class="text-base leading-none">+</span>
    <span>Tambah Pegawai</span>
</button>
@endsection

@section('content')
<div class="max-w-[1400px] mx-auto space-y-6">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] rounded-2xl p-5 shadow-xs transition-colors">
            <p class="text-[11px] font-bold text-gray-400 dark:text-gray-300 uppercase tracking-wider">Total Pegawai</p>
            <p class="mt-2 text-3xl font-black text-[#35635b] dark:text-emerald-400">{{ $totalPegawai ?? $pegawai->count() }}</p>
        </div>
        <div class="bg-white dark:bg-[#152420] border border-emerald-100 dark:border-emerald-900/30 rounded-2xl p-5 shadow-xs transition-colors">
            <p class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Pegawai Aktif</p>
            <p class="mt-2 text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $totalAktif ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-[#152420] border border-amber-100 dark:border-amber-900/30 rounded-2xl p-5 shadow-xs transition-colors relative overflow-hidden">
            @if (($totalPending ?? 0) > 0)
                <span class="absolute top-4 right-4 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                </span>
            @endif
            <p class="text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Menunggu Verifikasi</p>
            <p class="mt-2 text-3xl font-black text-amber-600 dark:text-amber-400">{{ $totalPending ?? 0 }}</p>
        </div>
        <div class="bg-white dark:bg-[#152420] border border-red-100 dark:border-red-900/30 rounded-2xl p-5 shadow-xs transition-colors">
            <p class="text-[11px] font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">Ditolak</p>
            <p class="mt-2 text-3xl font-black text-red-600 dark:text-red-400">{{ $totalDitolak ?? 0 }}</p>
        </div>
    </div>

    <!-- Status Filter Pills (Outside Card, Left) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        <a href="{{ route('admin.pegawai.lihat', array_filter(['status' => 'semua', 'keyword' => request('keyword'), 'bidang' => request('bidang'), 'jabatan' => request('jabatan'), 'instansi' => request('instansi')])) }}"
           class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap {{ ($statusFilter ?? 'semua') === 'semua' ? 'bg-[#35635b] text-white shadow-sm' : 'bg-white dark:bg-[#152420] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 border border-gray-200 dark:border-[#284c43]' }}">
            Semua Pegawai 
        </a>
        <a href="{{ route('admin.pegawai.lihat', array_filter(['status' => 'aktif', 'keyword' => request('keyword'), 'bidang' => request('bidang'), 'jabatan' => request('jabatan'), 'instansi' => request('instansi')])) }}"
           class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap {{ ($statusFilter ?? '') === 'aktif' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-[#152420] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 border border-gray-200 dark:border-[#284c43]' }}">
            Aktif 
        </a>
        <a href="{{ route('admin.pegawai.lihat', array_filter(['status' => 'pending', 'keyword' => request('keyword'), 'bidang' => request('bidang'), 'jabatan' => request('jabatan'), 'instansi' => request('instansi')])) }}"
           class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap {{ ($statusFilter ?? '') === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white dark:bg-[#152420] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 border border-gray-200 dark:border-[#284c43]' }}">
            Menunggu Verifikasi 
        </a>
        <a href="{{ route('admin.pegawai.lihat', array_filter(['status' => 'ditolak', 'keyword' => request('keyword'), 'bidang' => request('bidang'), 'jabatan' => request('jabatan'), 'instansi' => request('instansi')])) }}"
           class="px-4 py-2.5 rounded-xl text-xs font-extrabold transition-all whitespace-nowrap {{ ($statusFilter ?? '') === 'ditolak' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white dark:bg-[#152420] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 border border-gray-200 dark:border-[#284c43]' }}">
            Ditolak 
        </a>
    </div>

    <div class="bg-white dark:bg-[#152420] rounded-2xl shadow-xs border border-gray-100 dark:border-[#233a34] overflow-hidden transition-colors">
        <!-- Card Header: Title (Left), Search & Filters (Middle), Button (Right) -->
        <div class="border-b border-gray-100 dark:border-[#233a34] px-5 sm:px-6 py-4 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <!-- Left: Title & Subtitle -->
            <div class="shrink-0">
                <h2 class="text-base font-extrabold text-gray-800 dark:text-white">Daftar Pegawai</h2>
                <p id="text-count-pegawai" class="mt-0.5 text-xs text-gray-500 dark:text-gray-300">Menampilkan {{ $pegawai->count() }} dari {{ $totalPegawai ?? $pegawai->count() }} pegawai.</p>
            </div>

            <!-- Middle: Search Bar & Filters -->
            <form id="form-search-pegawai" method="GET" action="{{ route('admin.pegawai.lihat') }}" class="flex-1 max-w-3xl w-full">
                <input type="hidden" name="status" value="{{ request('status', 'semua') }}">
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input
                            id="keyword"
                            name="keyword"
                            value="{{ $keyword ?? request('keyword') }}"
                            type="search"
                            class="h-10 w-full pl-10 pr-4 rounded-xl border border-gray-200 dark:border-[#284c43] bg-gray-50 dark:bg-[#0f1c19] text-xs text-gray-700 dark:text-white outline-none transition focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20 placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="Cari nama, NIP, jabatan, bidang, HP...">
                    </div>
                    @if ($admin->isSuperAdmin())
                        <select
                            id="instansi-filter"
                            name="instansi"
                            onchange="document.getElementById('form-search-pegawai').submit()"
                            class="h-10 rounded-xl border border-gray-200 dark:border-[#284c43] bg-gray-50 dark:bg-[#0f1c19] px-3 text-xs font-medium text-gray-700 dark:text-white outline-none transition focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20">
                            <option value="semua" @selected(($instansiFilter ?? 'semua') === 'semua')>Semua Instansi</option>
                            <optgroup label="Dinas / Perangkat Daerah">
                                @foreach (($dinasList ?? collect()) as $dinas)
                                    <option value="dinas_{{ $dinas->id_dinas }}" @selected(($instansiFilter ?? 'semua') === 'dinas_' . $dinas->id_dinas)>{{ $dinas->nama_lengkap ?? $dinas->nama_dinas }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Kecamatan">
                                @foreach (($kecamatanList ?? collect()) as $kecamatan)
                                    <option value="kecamatan_{{ $kecamatan->id_kecamatan }}" @selected(($instansiFilter ?? 'semua') === 'kecamatan_' . $kecamatan->id_kecamatan)>{{ $kecamatan->nama_kecamatan }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    @endif
                    <select
                        id="bidang-filter"
                        name="bidang"
                        onchange="document.getElementById('form-search-pegawai').submit()"
                        class="h-10 rounded-xl border border-gray-200 dark:border-[#284c43] bg-gray-50 dark:bg-[#0f1c19] px-3 text-xs font-medium text-gray-700 dark:text-white outline-none transition focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20">
                        <option value="semua" @selected(($bidangFilter ?? 'semua') === 'semua')>Semua Bidang</option>
                        @foreach (($bidangOptions ?? collect()) as $bidang)
                            <option value="{{ $bidang }}" @selected(($bidangFilter ?? 'semua') === $bidang)>{{ $bidang }}</option>
                        @endforeach
                    </select>
                    <select
                        id="jabatan-filter"
                        name="jabatan"
                        onchange="document.getElementById('form-search-pegawai').submit()"
                        class="h-10 rounded-xl border border-gray-200 dark:border-[#284c43] bg-gray-50 dark:bg-[#0f1c19] px-3 text-xs font-medium text-gray-700 dark:text-white outline-none transition focus:border-[#35635b] focus:ring-2 focus:ring-[#35635b]/20">
                        <option value="semua" @selected(($jabatanFilter ?? 'semua') === 'semua')>Semua Jabatan</option>
                        @foreach (($jabatanOptions ?? collect()) as $jabatan)
                            <option value="{{ $jabatan }}" @selected(($jabatanFilter ?? 'semua') === $jabatan)>{{ $jabatan }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Right: Action Button -->
            <button onclick="openModal('modal-tambah-pegawai')" class="bg-[#35635b] hover:bg-[#2b4f49] dark:bg-[#107050] dark:hover:bg-[#0c5940] text-white font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-1.5 transition shadow-xs text-xs border border-transparent dark:border-[#10b981]/30 cursor-pointer shrink-0">
                <span class="text-base leading-none">+</span>
                <span>Tambah Pegawai</span>
            </button>
        </div>
        <div class="overflow-x-auto overflow-y-auto max-h-[450px] custom-scrollbar">
            <table class="w-full text-left min-w-[1380px]">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-[#35635b] dark:bg-[#1b3832] text-white text-xs font-bold uppercase tracking-wider outline outline-1 outline-[#35635b] dark:outline-[#1b3832]">
                        <th class="px-6 py-4">Foto</th>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">NIP</th>
                        <th class="px-6 py-4">Instansi</th>
                        <th class="px-6 py-4">Tanggal Lahir</th>
                        <th class="px-6 py-4">Jabatan</th>
                        <th class="px-6 py-4">Bidang</th>
                        <th class="px-6 py-4">No HP</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Status Akun</th>
                        <th class="px-6 py-4">Data Wajah</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#233a34] text-sm">
                    @forelse ($pegawai as $item)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-[#1b332d] transition">
                            <td class="px-6 py-4">
                                @if (!empty($item->foto))
                                    <img src="{{ asset('storage/' . $item->foto) }}" 
                                         alt="{{ $item->nama_pegawai }}" 
                                         onerror="this.onerror=null;this.src='{{ asset('assets/foto/profile.png') }}';"
                                         class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-[#233a34]">
                                @else
                                    <img src="{{ asset('assets/foto/profile.png') }}" alt="{{ $item->nama_pegawai }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-[#233a34]">
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-[#35635b] dark:text-emerald-400">{{ $item->nama_pegawai }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-700 dark:text-slate-200">{{ $item->nip }}</td>
                            <td class="px-6 py-4">
                                @if ($item->dinas)
                                    <span class="inline-flex items-center rounded-lg bg-blue-50 dark:bg-blue-950/60 px-2.5 py-1 text-xs font-bold text-blue-700 dark:text-blue-300 border border-blue-200/80 dark:border-blue-800/50 whitespace-nowrap">
                                        <span>{{ $item->dinas->nama_lengkap ?? $item->dinas->nama_dinas }}</span>
                                    </span>
                                @elseif ($item->kecamatan)
                                    <span class="inline-flex items-center rounded-lg bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/50 whitespace-nowrap">
                                        <span>{{ $item->kecamatan->nama_kecamatan }}</span>
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">Pusat / Belum Diatur</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-200">{{ $item->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-200">{{ $item->jabatan }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-200">{{ $item->bidang ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-200">{{ $item->nomor_hp }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-200">{{ $item->email }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-lg border px-2.5 py-1 text-xs font-bold whitespace-nowrap {{ $item->status_badge_class }}">
                                    {{ $item->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if (!is_null($item->face_descriptor))
                                    @if ($item->foto_wajah)
                                        <div class="flex items-center gap-2">
                                            <img src="{{ asset('storage/' . $item->foto_wajah) }}" 
                                                 alt="Bukti Wajah" 
                                                 onerror="this.onerror=null;this.src='{{ asset('assets/foto/profile.png') }}';"
                                                 class="w-8 h-8 rounded-lg object-cover border border-green-200 dark:border-emerald-800 shadow-xs cursor-pointer hover:scale-150 transition-transform origin-left">
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-green-100 dark:bg-emerald-950/60 text-green-700 dark:text-emerald-300 border border-transparent dark:border-emerald-800/50">Terdaftar</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-green-100 dark:bg-emerald-950/60 text-green-700 dark:text-emerald-300 border border-transparent dark:border-emerald-800/50">Terdaftar</span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400">Belum</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center gap-1.5 whitespace-nowrap">
                                    @if ($item->isPending() || $item->isDitolak())
                                        <form method="POST" action="{{ route('admin.pegawai.verifikasi', $item->id_pegawai) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status_verifikasi" value="aktif">
                                            <button
                                                type="submit"
                                                onclick="return confirm('Setujui dan aktifkan akun pegawai ini?')"
                                                class="inline-flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-[#0f513f] dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 px-2.5 py-1.5 text-xs font-bold transition hover:bg-emerald-100 dark:hover:bg-emerald-900/60 cursor-pointer shadow-2xs"
                                                title="Setujui / Aktifkan Akun">
                                                <span>Setujui</span>
                                            </button>
                                        </form>
                                    @endif

                                    @if ($item->isPending() || $item->isAktif())
                                        <form method="POST" action="{{ route('admin.pegawai.verifikasi', $item->id_pegawai) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status_verifikasi" value="ditolak">
                                            <button
                                                type="submit"
                                                onclick="return confirm('Tolak/nonaktifkan akun pegawai ini? Pegawai tidak akan bisa login atau presensi.')"
                                                class="inline-flex items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 px-2.5 py-1.5 text-xs font-bold transition hover:bg-amber-100 dark:hover:bg-amber-900/60 cursor-pointer shadow-2xs"
                                                title="{{ $item->isAktif() ? 'Nonaktifkan Akun' : 'Tolak Pendaftaran' }}">
                                                <span>{{ $item->isAktif() ? 'Nonaktifkan' : 'Tolak' }}</span>
                                            </button>
                                        </form>
                                    @endif

                                    <button
                                        type="button"
                                        onclick="openEditPegawai(this)"
                                        data-id="{{ $item->id_pegawai }}"
                                        data-action="{{ route('admin.pegawai.update', $item->id_pegawai) }}"
                                        data-foto-url="{{ $item->foto ? asset('storage/' . $item->foto) : '' }}"
                                        data-nama="{{ $item->nama_pegawai }}"
                                        data-nip="{{ $item->nip }}"
                                        data-instansi="{{ $item->id_dinas ? 'dinas_' . $item->id_dinas : ($item->id_kecamatan ? 'kecamatan_' . $item->id_kecamatan : '') }}"
                                        data-tanggal-lahir="{{ $item->tanggal_lahir?->format('Y-m-d') }}"
                                        data-jabatan="{{ $item->jabatan }}"
                                        data-bidang="{{ $item->bidang }}"
                                        data-nomor="{{ $item->nomor_hp }}"
                                        data-email="{{ $item->email }}"
                                        class="inline-flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-[#0f513f] dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 px-2.5 py-1.5 text-xs font-bold transition hover:bg-emerald-100 dark:hover:bg-emerald-900/60 cursor-pointer shadow-2xs"
                                        title="Edit Pegawai">
                                        <span>Edit</span>
                                    </button>

                                    @if (!is_null($item->face_descriptor))
                                    <form method="POST" action="{{ route('admin.pegawai.reset-wajah', $item->id_pegawai) }}" onsubmit="return confirm('Apakah Anda yakin ingin mereset data wajah pegawai ini? Pegawai harus mendaftarkan ulang wajahnya saat presensi berikutnya.');" class="inline">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800/60 px-2.5 py-1.5 text-xs font-bold transition hover:bg-purple-100 dark:hover:bg-purple-900/60 cursor-pointer shadow-2xs"
                                            title="Reset Data Wajah">
                                            <span>Reset Wajah</span>
                                        </button>
                                    </form>
                                    @endif

                                    <button
                                        type="button"
                                        onclick="openDeleteModal('{{ route('admin.pegawai.destroy', $item->id_pegawai) }}', 'Hapus Pegawai?', 'Apakah Anda yakin ingin menghapus pegawai ini?')"
                                        class="inline-flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-950/50 text-red-700 dark:text-red-300 border border-red-200/80 dark:border-red-800/60 px-2.5 py-1.5 text-xs font-bold transition hover:bg-red-100 dark:hover:bg-red-900/60 cursor-pointer shadow-2xs"
                                        title="Hapus Pegawai">
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada data pegawai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


</div>

<div id="modal-tambah-pegawai" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3 sm:p-4">
    <div class="relative flex max-h-[calc(100dvh-1.5rem)] sm:max-h-[calc(100vh-2rem)] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white dark:bg-[#152420] shadow-2xl dark:border dark:border-[#284c43]">
        <div class="flex items-start justify-between border-b border-gray-100 dark:border-[#233a34] px-5 py-4 sm:px-6 sm:py-5 bg-white dark:bg-[#152420] shrink-0">
            <div class="flex items-center gap-3.5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-[#0f513f] dark:text-emerald-400 shrink-0">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Tambah Data Pegawai</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Input data pegawai baru dan akun login sistem</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modal-tambah-pegawai')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 cursor-pointer" aria-label="Tutup modal">
                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="form-tambah-pegawai" method="POST" action="{{ route('admin.pegawai.store') }}" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col">
            @csrf
            @if ($errors->any() && !old('_method'))
                <div class="px-4 pt-3 sm:px-6 sm:pt-4">
                    <div class="rounded-xl border border-red-200 bg-red-50 dark:bg-red-950/50 px-4 py-3 text-sm font-semibold text-red-700 dark:text-red-300">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif
            <div class="flex-1 min-h-0 grid grid-cols-1 gap-3 sm:gap-4 overflow-y-auto p-4 sm:p-6 sm:grid-cols-2">
                @include('admin.pegawai.form-fields')
            </div>
            <div class="flex justify-end items-center gap-3 border-t border-gray-100 dark:border-[#233a34] bg-gray-50/50 dark:bg-[#0f1c19] px-5 py-4 sm:px-6 rounded-b-2xl shrink-0">
                <button type="button" onclick="closeModal('modal-tambah-pegawai')" class="h-10 rounded-xl px-5 text-xs sm:text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-[#152420] border border-gray-200 dark:border-[#284c43] hover:bg-gray-100 dark:hover:bg-white/5 transition cursor-pointer flex items-center justify-center">Batal</button>
                <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#0f513f] hover:bg-[#0b3f31] dark:bg-[#107050] dark:hover:bg-[#0c5940] px-6 text-xs sm:text-sm font-bold text-white transition cursor-pointer shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h12l2 2v12H5zM8 5v6h8V5M9 18h6"></path>
                    </svg>
                    <span>Simpan Pegawai</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modal-edit-pegawai" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3 sm:p-4">
    <div class="relative flex max-h-[calc(100dvh-1.5rem)] sm:max-h-[calc(100vh-2rem)] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white dark:bg-[#152420] shadow-2xl dark:border dark:border-[#284c43]">
        <div class="flex items-start justify-between border-b border-gray-100 dark:border-[#233a34] px-5 py-4 sm:px-6 sm:py-5 bg-white dark:bg-[#152420] shrink-0">
            <div class="flex items-center gap-3.5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-[#0f513f] dark:text-emerald-400 shrink-0">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Edit Data Pegawai</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui rincian profil dan informasi akun pegawai terpilih</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modal-edit-pegawai')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 cursor-pointer" aria-label="Tutup modal">
                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="form-edit-pegawai" method="POST" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-id_pegawai" name="id_pegawai">
            @if ($errors->any() && old('_method') === 'PUT')
                <div class="px-4 pt-3 sm:px-6 sm:pt-4">
                    <div class="rounded-xl border border-red-200 bg-red-50 dark:bg-red-950/50 px-4 py-3 text-sm font-semibold text-red-700 dark:text-red-300">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif
            <div class="flex-1 min-h-0 grid grid-cols-1 gap-3 sm:gap-4 overflow-y-auto p-4 sm:p-6 sm:grid-cols-2">
                @include('admin.pegawai.form-fields', ['prefix' => 'edit-'])
            </div>
            <div class="flex justify-end items-center gap-3 border-t border-gray-100 dark:border-[#233a34] bg-gray-50/50 dark:bg-[#0f1c19] px-5 py-4 sm:px-6 rounded-b-2xl shrink-0">
                <button type="button" onclick="closeModal('modal-edit-pegawai')" class="h-10 rounded-xl px-5 text-xs sm:text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-[#152420] border border-gray-200 dark:border-[#284c43] hover:bg-gray-100 dark:hover:bg-white/5 transition cursor-pointer flex items-center justify-center">Batal</button>
                <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-[#0f513f] hover:bg-[#0b3f31] dark:bg-[#107050] dark:hover:bg-[#0c5940] px-6 text-xs sm:text-sm font-bold text-white transition cursor-pointer shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5h12l2 2v12H5zM8 5v6h8V5M9 18h6"></path>
                    </svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (id === 'modal-tambah-pegawai') {
            const form = document.getElementById('form-tambah-pegawai');
            if (form) form.reset();
            setPegawaiPhotoPreview('', '');
            if (document.getElementById('instansi')) {
                document.getElementById('instansi').value = '';
            }
        }
        if (modal) {
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function openEditPegawai(button) {
        document.getElementById('form-edit-pegawai').action = button.dataset.action;
        document.getElementById('edit-id_pegawai').value = button.dataset.id;
        document.getElementById('edit-foto').value = '';
        document.getElementById('edit-nama_pegawai').value = button.dataset.nama;
        document.getElementById('edit-nip').value = button.dataset.nip;
        document.getElementById('edit-tanggal_lahir').value = button.dataset.tanggalLahir || '';
        document.getElementById('edit-jabatan').value = button.dataset.jabatan;
        document.getElementById('edit-bidang').value = button.dataset.bidang || '';
        document.getElementById('edit-nomor_hp').value = button.dataset.nomor;
        document.getElementById('edit-email').value = button.dataset.email;
        if (document.getElementById('edit-instansi')) {
            document.getElementById('edit-instansi').value = button.dataset.instansi || '';
        }
        setPegawaiPhotoPreview('edit-', button.dataset.fotoUrl || '');
        openModal('modal-edit-pegawai');
    }

    function setPegawaiPhotoPreview(prefix, url) {
        const preview = document.getElementById(prefix + 'foto-preview');
        const icon = document.getElementById(prefix + 'foto-icon');
        const hapusBtn = document.getElementById(prefix + 'btn-hapus-foto');

        if (url) {
            preview.src = url;
            preview.classList.remove('hidden');
            if (icon) icon.classList.add('hidden');
            if (hapusBtn) hapusBtn.classList.remove('hidden');
        } else {
            preview.src = '{{ asset("assets/foto/profile.png") }}';
            preview.classList.remove('hidden');
            if (icon) icon.classList.add('hidden');
            if (hapusBtn) hapusBtn.classList.add('hidden');
        }
    }

    function removePhoto(prefix) {
        const input = document.getElementById(prefix + 'foto');
        const hapusInput = document.getElementById(prefix + 'hapus_foto');
        
        if (input) input.value = '';
        
        setPegawaiPhotoPreview(prefix, '');
        
        if (hapusInput) hapusInput.value = '1';
    }

    document.querySelectorAll('input[type="file"][data-photo-input]').forEach(input => {
        input.addEventListener('change', function(e) {
            const prefix = this.dataset.photoInput;
            const file = this.files[0];
            const hapusInput = document.getElementById(prefix + 'hapus_foto');
            if (hapusInput) hapusInput.value = '0';
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    setPegawaiPhotoPreview(prefix, e.target.result);
                };
                reader.readAsDataURL(file);
            } else {
                setPegawaiPhotoPreview(prefix, '');
            }
        });
    });

    const masterState = {
        bidang: { expanded: false },
        jabatan: { expanded: false }
    };

    function initMasterList(type) {
        const searchInput = document.getElementById(`search-${type}`);
        if (searchInput) {
            searchInput.addEventListener('input', () => renderMasterList(type));
        }
        renderMasterList(type);
    }

    function renderMasterList(type) {
        const searchInput = document.getElementById(`search-${type}`);
        const container = document.getElementById(`container-${type}`);
        
        if (!container) return;
        
        const items = container.querySelectorAll(`.item-${type}`);
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        
        items.forEach((item) => {
            const name = item.dataset.name || '';
            if (name.includes(searchTerm)) {
                item.style.display = 'table-row';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function initLiveSearch() {
        const form = document.getElementById('form-search-pegawai');
        const searchInput = document.getElementById('keyword');
        const bidangFilter = document.getElementById('bidang-filter');
        const jabatanFilter = document.getElementById('jabatan-filter');
        const tableBody = document.querySelector('table tbody');
        const countText = document.getElementById('text-count-pegawai');
        
        if (!form || !searchInput || !tableBody) return;

        let debounceTimer;

        function fetchResults() {
            const url = new URL(form.action);
            const params = new URLSearchParams(new FormData(form));
            url.search = params.toString();

            // Update URL in browser without reload
            window.history.replaceState({}, '', url);

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

                const newCount = doc.getElementById('text-count-pegawai');
                if (countText && newCount) {
                    countText.innerHTML = newCount.innerHTML;
                }
            });
        }

        searchInput.addEventListener('input', function(e) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchResults, 300);
        });

        // Prevent form submission on enter
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        if(bidangFilter) {
            bidangFilter.addEventListener('change', fetchResults);
        }
        if(jabatanFilter) {
            jabatanFilter.addEventListener('change', fetchResults);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initMasterList('bidang');
        initMasterList('jabatan');
        initLiveSearch();

        @if($errors->any())
            @if(old('_method') === 'PUT')
                const editId = "{{ old('id_pegawai') }}";
                const editBtn = document.querySelector(`button[data-id="${editId}"]`);
                if (editBtn) {
                    openEditPegawai(editBtn);
                    document.getElementById('edit-nama_pegawai').value = @json(old('nama_pegawai'));
                    document.getElementById('edit-nip').value = @json(old('nip'));
                    document.getElementById('edit-tanggal_lahir').value = @json(old('tanggal_lahir'));
                    document.getElementById('edit-jabatan').value = @json(old('jabatan'));
                    document.getElementById('edit-bidang').value = @json(old('bidang'));
                    document.getElementById('edit-nomor_hp').value = @json(old('nomor_hp'));
                    document.getElementById('edit-email').value = @json(old('email'));
                }
            @else
                openModal('modal-tambah-pegawai');
                document.getElementById('nama_pegawai').value = @json(old('nama_pegawai'));
                document.getElementById('nip').value = @json(old('nip'));
                document.getElementById('tanggal_lahir').value = @json(old('tanggal_lahir'));
                document.getElementById('jabatan').value = @json(old('jabatan'));
                document.getElementById('bidang').value = @json(old('bidang'));
                document.getElementById('nomor_hp').value = @json(old('nomor_hp'));
                document.getElementById('email').value = @json(old('email'));
            @endif
        @endif
    });
</script>
@endpush
@endsection
