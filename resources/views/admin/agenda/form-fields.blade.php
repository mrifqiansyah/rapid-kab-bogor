@php
    $prefix = $prefix ?? '';
    $kategori = old('kategori_surat', $kategoriSurat ?? 'internal');
    $isMasuk = $kategori === 'masuk';
    $isKeluar = $kategori === 'keluar';
    $isInternal = $kategori === 'internal';
    $currentAdmin = Auth::guard('admin')->user();
    $instansiInfo = $instansiInfo ?? ($currentAdmin ? $currentAdmin->getInstansiInfo() : [
        'nama' => 'Dinas Komunikasi & Informatika (Diskominfo)',
        'singkatan' => 'Diskominfo',
        'alamat' => 'Jl. Tegar Beriman, Cibinong, Kabupaten Bogor (Pusat Pemkab Bogor)',
        'tipe' => 'superadmin',
    ]);
@endphp

<input id="{{ $prefix }}kategori_surat" name="kategori_surat" type="hidden" value="{{ $kategori }}">
<input id="{{ $prefix }}status_qr" name="status_qr" type="hidden" value="nonaktif">
<input id="{{ $prefix }}status_fr" name="status_fr" type="hidden" value="0">

@if(! $isMasuk)
    <input id="{{ $prefix }}ditugaskan" name="ditugaskan" type="hidden" value="">
@endif

{{-- 1. Nama Agenda --}}
<div>
    <label class="mb-1.5 block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">Nama Agenda</label>
    <input id="{{ $prefix }}nama_agenda" name="nama_agenda" type="text" required placeholder="Masukkan nama agenda" class="h-10 sm:h-11 w-full rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] px-3.5 sm:px-4 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
</div>

{{-- 1.5. Dinas / OPD Penyelenggara --}}
<div>
    <label class="mb-1.5 block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">
        Dinas / Perangkat Daerah Penyelenggara
    </label>
    @if (auth('admin')->check() && auth('admin')->user()->role === 'admin_dinas')
        <input type="hidden" id="{{ $prefix }}id_dinas" name="id_dinas" value="{{ auth('admin')->user()->id_dinas }}">
        <div class="h-10 sm:h-11 w-full rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#eef7f3] dark:bg-[#132722] px-3.5 sm:px-4 text-xs sm:text-sm font-bold text-[#35635b] dark:text-emerald-400 flex items-center gap-2">
            <span>{{ auth('admin')->user()->dinas?->nama_lengkap ?? 'Dinas Terdaftar' }}</span>
        </div>
    @else
        <div class="relative">
            <select id="{{ $prefix }}id_dinas" name="id_dinas" class="h-10 sm:h-11 w-full appearance-none rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] px-3.5 sm:px-4 pr-10 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10 cursor-pointer">
                <option value="">-- Pilih Dinas / Instansi Penyelenggara --</option>
                @foreach ($dinasList ?? \App\Models\Dinas::orderBy('nama_dinas')->get() as $d)
                    <option value="{{ $d->id_dinas }}">{{ $d->nama_lengkap }}</option>
                @endforeach
            </select>
            <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    @endif
</div>

{{-- 2. Waktu & Tanggal --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
    <div>
        <label class="mb-1.5 block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">Tanggal</label>
        <div class="relative">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#3f4f49] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V4m8 3V4M5 10h14M6 20h12a1 1 0 001-1V7a1 1 0 00-1-1H6a1 1 0 00-1 1v12a1 1 0 001 1z"></path>
            </svg>
            <input id="{{ $prefix }}tanggal" name="tanggal" type="date" required class="h-10 sm:h-11 w-full rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] pl-10 pr-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-2 sm:col-span-2 sm:gap-4">
        <div>
            <label class="mb-1.5 block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">Waktu Mulai</label>
            <div class="relative">
                <svg class="pointer-events-none absolute left-2.5 sm:left-3.5 top-1/2 h-3.5 w-3.5 sm:h-4 sm:w-4 -translate-y-1/2 text-[#3f4f49] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 11 0 0118 0z"></path>
                </svg>
                <input id="{{ $prefix }}waktu" name="waktu" type="time" required class="h-10 sm:h-11 w-full rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] pl-7 sm:pl-10 pr-1.5 sm:pr-2 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">Waktu Selesai</label>
            <div class="relative">
                <svg class="pointer-events-none absolute left-2.5 sm:left-3.5 top-1/2 h-3.5 w-3.5 sm:h-4 sm:w-4 -translate-y-1/2 text-[#3f4f49] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 11 0 0118 0z"></path>
                </svg>
                <input id="{{ $prefix }}waktu_selesai" name="waktu_selesai" type="time" class="h-10 sm:h-11 w-full rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] pl-7 sm:pl-10 pr-1.5 sm:pr-2 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
            </div>
        </div>
    </div>
</div>

{{-- 3. Ditugaskan Kepada (KHUSUS SURAT MASUK - TUGAS LUAR) --}}
@if ($isMasuk)
<div class="relative" data-multi-select-container="{{ $prefix }}">
    <label class="mb-1.5 block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">Ditugaskan Kepada (Pilih Pegawai)</label>
    
    <input id="{{ $prefix }}ditugaskan" name="ditugaskan" type="hidden" value="">

    <div id="{{ $prefix }}ditugaskan-trigger" onclick="togglePegawaiDropdown('{{ $prefix }}')" class="min-h-[40px] sm:min-h-[44px] w-full cursor-pointer rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] px-3 py-2 text-xs sm:text-sm text-gray-800 dark:text-white transition focus-within:border-[#35635b] focus-within:bg-white flex flex-wrap items-center gap-1.5 justify-between">
        <div id="{{ $prefix }}ditugaskan-selected-badges" class="flex flex-wrap items-center gap-1.5">
            <span class="text-gray-400 dark:text-gray-500 text-xs italic">Klik untuk memilih pegawai...</span>
        </div>
        <svg class="h-4 w-4 shrink-0 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>

    <div id="{{ $prefix }}ditugaskan-dropdown" class="absolute left-0 right-0 top-full z-50 mt-1 hidden max-h-56 rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#152420] p-2.5 shadow-xl overflow-hidden flex flex-col">
        <input type="text" id="{{ $prefix }}ditugaskan-search" oninput="filterPegawaiList('{{ $prefix }}')" placeholder="Cari nama atau jabatan..." class="mb-2 h-8 sm:h-9 w-full rounded-lg border border-gray-200 dark:border-[#284c43] bg-gray-50 dark:bg-[#0f1c19] px-3 text-xs text-gray-800 dark:text-white outline-none focus:border-[#35635b] focus:bg-white">

        <div id="{{ $prefix }}ditugaskan-list" class="space-y-2 overflow-y-auto max-h-40 sm:max-h-44 pr-1">
            @php($groupedPegawai = ($pegawaiList ?? collect())->groupBy(fn($p) => !empty(trim($p->bidang)) ? trim($p->bidang) : 'Lainnya / Tanpa Bidang'))
            @forelse ($groupedPegawai as $bidangName => $items)
                <div class="bidang-group space-y-1">
                    <div class="sticky top-0 z-10 bg-[#e8f3ee] dark:bg-[#1b3832] px-2.5 py-1 text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-[#1e4a42] dark:text-emerald-300 rounded-md flex items-center justify-between shadow-2xs">
                        <span>{{ $bidangName }}</span>
                        <span class="text-[9px] sm:text-[10px] bg-white/70 dark:bg-black/30 px-1.5 py-0.5 rounded text-[#35635b] dark:text-emerald-400 font-bold">{{ count($items) }} Pegawai</span>
                    </div>
                    @foreach ($items as $peg)
                        <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-[#f4faf7] dark:hover:bg-[#1b332d] cursor-pointer transition">
                            <input type="checkbox" value="{{ $peg->nama_pegawai }}" onchange="updateDitugaskanSelected('{{ $prefix }}')" class="h-4 w-4 rounded border-gray-300 dark:border-[#284c43] text-[#35635b] focus:ring-[#35635b]">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 dark:text-white leading-tight">{{ $peg->nama_pegawai }}</span>
                                @if($peg->jabatan)
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 leading-tight">{{ $peg->jabatan }}</span>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            @empty
                <p class="p-2 text-center text-xs text-gray-400">Belum ada data pegawai.</p>
            @endforelse
        </div>
    </div>
</div>
@endif

{{-- 4. Asal Surat / Tujuan Surat --}}
<div>
    <label class="mb-1.5 block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">
        @if ($isKeluar)
            Tujuan Surat / Instansi yang Diundang
        @elseif ($isInternal)
            Bidang / Asal Surat Internal
        @else
            Asal Surat
        @endif
    </label>
    <input id="{{ $prefix }}asal_surat" name="asal_surat" type="text" 
        placeholder="{{ $isKeluar ? 'Instansi / pihak luar yang diundang (misal: Seluruh Kecamatan se-Kab. Bogor, Dinas Pendidikan)' : ($isInternal ? 'Bidang / Seksi pengaju rapat di ' . ($instansiInfo['singkatan'] ?? $instansiInfo['nama']) . ' (misal: Bagian Umum / Sekretariat)' : 'Instansi asal surat (misal: Kantor Camat Cariu)') }}" 
        class="h-10 sm:h-11 w-full rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] px-3.5 sm:px-4 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
</div>

{{-- 5. Kuota Rapat / Kuota Tamu (Untuk Surat Internal & Surat Keluar) --}}
@if ($isInternal || $isKeluar)
<div>
    <div class="flex items-center justify-between mb-1.5">
        <label class="block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">
            {{ $isKeluar ? 'Kuota Peserta / Tamu Undangan' : 'Kuota Rapat' }}
        </label>
        <span class="text-[10px] font-semibold text-[#35635b] dark:text-emerald-400">
            {{ $isKeluar ? 'Batas maksimal peserta rapat' : 'Sesuai kapasitas ruangan' }}
        </span>
    </div>
    <input id="{{ $prefix }}kuota" name="kuota" type="number" min="0" 
        placeholder="{{ $isKeluar ? 'Jumlah maksimal peserta/tamu (contoh: 50)' : 'Masukkan kuota agenda' }}" 
        class="h-10 sm:h-11 w-full rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] px-3.5 sm:px-4 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
    <p id="{{ $prefix }}kuota-warning" class="mt-1.5 hidden text-xs font-semibold text-red-600 dark:text-red-400 items-center gap-1.5">
        <svg class="h-4 w-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span id="{{ $prefix }}kuota-warning-text"></span>
    </p>
</div>
@endif

{{-- 6. Tempat / Ruangan / Lokasi --}}
@if ($isInternal || $isKeluar)
<div>
    <label class="mb-1.5 block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">
        Lokasi Instansi Penyelenggara
    </label>
    <div class="rounded-xl border border-emerald-200/70 dark:border-[#284c43] bg-emerald-50/50 dark:bg-[#132420] p-3 flex items-center gap-3">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <span class="text-xs sm:text-sm font-extrabold text-[#0e2f27] dark:text-emerald-300">{{ $instansiInfo['nama'] }}</span>
                @if ($isInternal)
                    <span class="text-[9.5px] font-bold bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 px-2 py-0.5 rounded-full">Internal Instansi</span>
                @else
                    <span class="text-[9.5px] font-bold bg-blue-100 dark:bg-blue-950/80 text-blue-800 dark:text-blue-300 px-2 py-0.5 rounded-full">Tuan Rumah Acara</span>
                @endif
            </div>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $instansiInfo['alamat'] }}</p>
        </div>
    </div>
    <input id="{{ $prefix }}lokasi" name="lokasi" type="hidden" value="{{ $instansiInfo['nama'] }}">
</div>

<div>
    <div class="flex items-center justify-between mb-1.5">
        <label class="block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">
            {{ $isKeluar ? 'Ruangan Pertemuan / Acara di ' . ($instansiInfo['singkatan'] ?? $instansiInfo['nama']) : 'Ruangan Rapat ' . ($instansiInfo['singkatan'] ?? $instansiInfo['nama']) }}
        </label>
        <span class="text-[10px] font-semibold text-[#35635b] dark:text-emerald-400">Pilih ruang gedung {{ $instansiInfo['singkatan'] ?? $instansiInfo['nama'] }}</span>
    </div>
    <div class="relative">
        <select id="{{ $prefix }}id_ruangrapat" name="id_ruangrapat" {{ $ruang->isNotEmpty() ? 'required' : '' }} data-agenda-room-select="{{ $prefix }}" class="h-10 sm:h-11 w-full appearance-none rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] px-3.5 sm:px-4 pr-10 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10 cursor-pointer">
            @if ($ruang->isEmpty())
                <option value="" data-kapasitas="0">Belum ada ruangan terdaftar di {{ $instansiInfo['singkatan'] ?? $instansiInfo['nama'] }} (Kosong)</option>
            @else
                <option value="" data-kapasitas="0">Pilih ruang pertemuan {{ $instansiInfo['singkatan'] ?? $instansiInfo['nama'] }}...</option>
                @foreach ($ruang as $item)
                    <option value="{{ $item->id_ruangrapat }}" data-nama-ruang="{{ $item->nama_ruang }}" data-kapasitas="{{ $item->kapasitas }}">
                        {{ $item->nama_ruang }} (Kapasitas: {{ $item->kapasitas }} org{{ $item->dynamic_status === 'terpakai' ? ' • Ruangan Terpakai' : ' • Tersedia' }})
                    </option>
                @endforeach
            @endif
        </select>
        <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>
    @if ($ruang->isEmpty())
        <div class="mt-2 flex items-start gap-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 p-2.5 border border-amber-200/80 dark:border-amber-800/50">
            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-[11px] text-amber-800 dark:text-amber-300 leading-tight">
                <strong>{{ $instansiInfo['singkatan'] ?? $instansiInfo['nama'] }}</strong> belum mendaftarkan data ruangan. Rapat otomatis berlokasi di kantor instansi, atau tambahkan ruangan di menu <strong>Ruang Rapat</strong>.
            </p>
        </div>
    @endif
</div>
@else
{{-- KHUSUS SURAT MASUK (TUGAS LUAR KE INSTANSI LAIN) --}}
<div>
    <div class="flex items-center justify-between mb-1.5">
        <label class="block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">
            Titik Lokasi Instansi Kegiatan (Peta GIS)
        </label>
        <span class="text-[10.5px] font-semibold text-[#35635b] dark:text-emerald-400">Tersambung dengan Map Pemkab</span>
    </div>
    <div class="relative">
        <select id="{{ $prefix }}lokasi" name="lokasi" required onchange="handleInstansiSelectChange('{{ $prefix }}', this)" class="h-10 sm:h-11 w-full appearance-none rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] px-3.5 sm:px-4 pr-10 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10 cursor-pointer">
            <option value="">Pilih kantor instansi kegiatan (40 Kecamatan & Dinas)...</option>
            <optgroup label="Pusat Pemkab Bogor">
                <option value="Kantor Bupati Bogor">Kantor Bupati Bogor (Cibinong)</option>
            </optgroup>
            @if(isset($listInstansi) && count($listInstansi) > 0)
                <optgroup label="40 Kantor Kecamatan se-Kabupaten Bogor (Peta GIS)">
                    @foreach($listInstansi->where('tipe', 'Kecamatan') as $kec)
                        <option value="{{ $kec['nama'] }}">{{ $kec['nama'] }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Dinas & OPD Pemkab Bogor">
                    @foreach($listInstansi->where('tipe', 'Dinas / OPD') as $dinas)
                        <option value="{{ $dinas['nama'] }}">{{ $dinas['nama'] }}</option>
                    @endforeach
                </optgroup>
            @endif
            <optgroup label="📍 Lokasi Lainnya">
                <option id="{{ $prefix }}lokasi_custom_opt" value="__custom__">-- Ketik Lokasi Lainnya (Manual) --</option>
            </optgroup>
        </select>
        <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>

    <!-- Input manual hanya muncul jika admin memilih '-- Ketik Lokasi Lainnya (Manual) --' -->
    <div id="{{ $prefix }}lokasi_custom_container" class="mt-2 hidden">
        <input type="text" id="{{ $prefix }}lokasi_custom" placeholder="Ketik nama lokasi kegiatan (misal: Hotel Lorin Sentul, Gedung Tegar Beriman)..." oninput="handleCustomLocationInput('{{ $prefix }}', this.value)" class="h-10 sm:h-11 w-full rounded-xl border border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] px-3.5 sm:px-4 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
    </div>
    <p class="mt-1 text-[10.5px] text-gray-500 dark:text-gray-400">Pilih dari 40 Kecamatan / Dinas agar titik agenda tugas luar muncul tepat pada peta GIS.</p>
</div>
<input id="{{ $prefix }}id_ruangrapat" name="id_ruangrapat" type="hidden" value="{{ $ruang->first()?->id_ruangrapat ?? '' }}">
@endif

{{-- 7. Lampiran Surat Undangan --}}
<div>
    <div class="flex items-center justify-between mb-1.5">
        <label class="block text-xs sm:text-sm font-bold text-[#0e2f27] dark:text-gray-200">Lampiran Surat Undangan</label>
        <button type="button" id="{{ $prefix }}btn-hapus-lampiran" onclick="clearAgendaLampiran('{{ $prefix }}')" class="text-[11px] font-bold text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hidden cursor-pointer">
            ✕ Hapus File
        </button>
    </div>
    <input id="{{ $prefix }}lampiran" name="lampiran" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" class="hidden" data-agenda-file-input="{{ $prefix }}">
    <input type="hidden" id="{{ $prefix }}hapus_lampiran" name="hapus_lampiran" value="0">
    <input type="hidden" id="{{ $prefix }}lampiran_existing_url" value="">
    
    <label for="{{ $prefix }}lampiran" id="{{ $prefix }}lampiran-dropzone" class="relative flex min-h-[85px] sm:min-h-[100px] cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-[#c9ddd4] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] p-3 text-center transition hover:border-[#35635b] hover:bg-white dark:hover:bg-[#152420] overflow-hidden group">
        
        <!-- Placeholder awal saat belum ada file -->
        <div id="{{ $prefix }}lampiran-placeholder" class="flex flex-col items-center justify-center">
            <svg class="mb-1.5 h-6 w-6 text-[#35635b] dark:text-emerald-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7zM14 3v5h5M9 15h6M9 18h4"></path>
            </svg>
            <span id="{{ $prefix }}lampiran-label" class="text-xs sm:text-sm font-medium text-[#0e2f27] dark:text-gray-200">Klik atau seret file PDF / Foto ke sini</span>
            <span class="mt-0.5 text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">PDF, DOC, JPG, PNG (Maks. 5MB)</span>
        </div>

        <!-- Preview Image Container (jika file gambar) -->
        <div id="{{ $prefix }}lampiran-img-container" class="hidden flex flex-col items-center justify-center w-full">
            <img id="{{ $prefix }}lampiran-img-preview" src="" alt="Preview Lampiran" class="max-h-28 sm:max-h-36 w-auto rounded-lg object-contain shadow-xs border border-gray-200 dark:border-[#284c43]">
            <p id="{{ $prefix }}lampiran-img-name" class="mt-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300 truncate max-w-xs"></p>
            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold mt-0.5">Klik untuk mengganti berkas</span>
        </div>

        <!-- Preview Document Container (jika file PDF / DOC) -->
        <div id="{{ $prefix }}lampiran-doc-container" class="hidden flex items-center gap-3 bg-emerald-50/80 dark:bg-emerald-950/50 p-2.5 rounded-xl border border-emerald-200/80 dark:border-emerald-800/60 max-w-full">
            <div class="h-9 w-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs uppercase">
                <span id="{{ $prefix }}lampiran-doc-ext">PDF</span>
            </div>
            <div class="text-left min-w-0 flex-1 pr-2">
                <p id="{{ $prefix }}lampiran-doc-name" class="text-xs font-bold text-gray-800 dark:text-white truncate"></p>
                <p class="text-[10px] text-emerald-700 dark:text-emerald-400">File dokumen terpilih • Klik untuk mengganti</p>
            </div>
        </div>

    </label>
</div>
