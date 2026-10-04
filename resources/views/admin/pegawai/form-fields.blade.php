@php
    $prefix = $prefix ?? '';
    $bidangMaster = collect($bidangMaster ?? []);
    $jabatanMaster = collect($jabatanMaster ?? []);
@endphp

<div class="col-span-full flex flex-col items-center py-1 sm:py-2">
    <input id="{{ $prefix }}foto" name="foto" type="file" accept="image/*" class="hidden" data-photo-input="{{ $prefix }}">
    <input type="hidden" id="{{ $prefix }}hapus_foto" name="hapus_foto" value="0">
    <label for="{{ $prefix }}foto" class="relative flex h-20 w-20 sm:h-24 sm:w-24 cursor-pointer items-center justify-center rounded-full border-2 border-dashed border-[#7b8d86] dark:border-[#284c43] bg-white dark:bg-[#0f1c19] text-[#6f7d78] dark:text-gray-400 transition hover:border-[#35635b] hover:text-[#35635b]">
        <img id="{{ $prefix }}foto-preview" src="" alt="Preview foto" class="hidden h-full w-full rounded-full object-cover">
        <svg id="{{ $prefix }}foto-icon" class="h-7 w-7 sm:h-8 sm:w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 8a3 3 0 11-6 0 3 3 0 016 0zM16 19H8a4 4 0 018 0zM19 7v4m2-2h-4"></path>
        </svg>
        <span class="absolute -bottom-1 -right-1 flex h-6 w-6 sm:h-7 sm:w-7 items-center justify-center rounded-full border-2 border-white dark:border-[#152420] bg-[#0f5d3f] text-white shadow-sm">
            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3M8 12l4-4m0 0l4 4m-4-4v9"></path>
            </svg>
        </span>
    </label>
    <div class="flex gap-2 mt-2 sm:mt-2.5 items-center">
        <label for="{{ $prefix }}foto" class="cursor-pointer text-xs font-semibold text-gray-600 dark:text-gray-300 hover:text-[#35635b] dark:hover:text-emerald-400">Unggah Foto</label>
        <button type="button" class="text-xs font-semibold text-red-500 hover:text-red-700 hidden cursor-pointer" id="{{ $prefix }}btn-hapus-foto" onclick="removePhoto('{{ $prefix }}')">Hapus</button>
    </div>
</div>

@if (auth('admin')->check() && !auth('admin')->user()->isSuperAdmin())
    @php
        $instansiInfo = auth('admin')->user()->getInstansiInfo();
    @endphp
    <div class="col-span-full bg-gray-50 dark:bg-[#0f1c19] border border-gray-200 dark:border-[#284c43] rounded-xl p-3 flex items-center gap-2.5">
        <div class="text-xs">
            <p class="font-bold text-gray-700 dark:text-gray-200">{{ $instansiInfo['nama'] }}</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400">Pegawai otomatis terdaftar pada instansi ini.</p>
        </div>
    </div>
@endif

<div class="col-span-full sm:col-span-1">
    <label class="mb-1.5 block text-xs font-bold text-gray-900 dark:text-gray-200">Nama Lengkap <span class="text-red-500">*</span></label>
    <div class="relative">
        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zM17 20H7a5 5 0 0110 0z"></path>
        </svg>
        <input id="{{ $prefix }}nama_pegawai" name="nama_pegawai" type="text" required placeholder="Masukkan nama lengkap" class="h-10 sm:h-11 w-full rounded-xl border border-[#b9c9c1] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] pl-10 pr-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
    </div>
</div>

@if (auth('admin')->check() && auth('admin')->user()->isSuperAdmin())
<div class="col-span-full sm:col-span-1">
    <label class="mb-1.5 block text-xs font-bold text-gray-900 dark:text-gray-200">Instansi <span class="text-red-500">*</span></label>
    <div class="relative">
        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        <select id="{{ $prefix }}instansi" name="instansi" class="h-10 sm:h-11 w-full rounded-xl border border-[#b9c9c1] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] pl-10 pr-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
            <option value="">Pilih Instansi</option>
            <optgroup label="Dinas / Perangkat Daerah">
                @foreach (($dinasList ?? collect()) as $dinas)
                    <option value="dinas_{{ $dinas->id_dinas }}">{{ $dinas->nama_lengkap ?? $dinas->nama_dinas }}</option>
                @endforeach
            </optgroup>
            <optgroup label="Kecamatan">
                @foreach (($kecamatanList ?? collect()) as $kecamatan)
                    <option value="kecamatan_{{ $kecamatan->id_kecamatan }}">{{ $kecamatan->nama_kecamatan }}</option>
                @endforeach
            </optgroup>
        </select>
    </div>
</div>
@endif

<div>
    <label class="mb-1.5 block text-xs font-bold text-gray-900 dark:text-gray-200">NIP <span class="text-red-500">*</span></label>
    <div class="relative">
        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6M9 11h6M9 15h3M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
        </svg>
        <input id="{{ $prefix }}nip" name="nip" type="text" required pattern="[0-9]+" maxlength="18" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Nomor Induk Pegawai" class="h-10 sm:h-11 w-full rounded-xl border border-[#b9c9c1] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] pl-10 pr-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
    </div>
</div>

<div>
    <label class="mb-1.5 block text-xs font-bold text-gray-900 dark:text-gray-200">Tanggal Lahir</label>
    <div class="relative">
        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V4m8 3V4M5 10h14M7 20h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v11a2 2 0 002 2z"></path>
        </svg>
        <input id="{{ $prefix }}tanggal_lahir" name="tanggal_lahir" type="date" class="h-10 sm:h-11 w-full rounded-xl border border-[#b9c9c1] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] pl-10 pr-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
    </div>
</div>

<div>
    <label class="mb-1.5 block text-xs font-bold text-gray-900 dark:text-gray-200">Jabatan</label>
    <div class="relative">
        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6h4m-7 4h10m-9 9h8a3 3 0 003-3v-5a2 2 0 00-2-2H7a2 2 0 00-2 2v5a3 3 0 003 3zM9 9V7a3 3 0 016 0v2"></path>
        </svg>
        <select id="{{ $prefix }}jabatan" name="jabatan" required class="h-10 sm:h-11 w-full rounded-xl border border-[#b9c9c1] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] pl-10 pr-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
            <option value="">Pilih Jabatan</option>
            @foreach ($jabatanMaster->groupBy(fn ($jabatan) => $jabatan->kategori ?: 'Lainnya') as $kategori => $jabatanItems)
                <optgroup label="{{ $kategori }}">
                    @foreach ($jabatanItems as $jabatan)
                        <option value="{{ $jabatan->nama_jabatan }}">{{ $jabatan->nama_jabatan }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
    </div>
</div>

<div>
    <label class="mb-1.5 block text-xs font-bold text-gray-900 dark:text-gray-200">Bidang</label>
    <div class="relative">
        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6h4m-7 4h10m-9 9h8a3 3 0 003-3v-5a2 2 0 00-2-2H7a2 2 0 00-2 2v5a3 3 0 003 3zM9 9V7a3 3 0 016 0v2"></path>
        </svg>
        <select id="{{ $prefix }}bidang" name="bidang" class="h-10 sm:h-11 w-full rounded-xl border border-[#b9c9c1] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] pl-10 pr-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
            <option value="">Pilih Bidang</option>
            @foreach ($bidangMaster as $bidang)
                <option value="{{ $bidang->nama_bidang }}">{{ $bidang->nama_bidang }}</option>
            @endforeach
        </select>
    </div>
</div>

<div>
    <label class="mb-1.5 block text-xs font-bold text-gray-900 dark:text-gray-200">Email <span class="text-red-500">*</span></label>
    <div class="relative">
        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16v12H4zM4 7l8 6 8-6"></path>
        </svg>
        <input id="{{ $prefix }}email" name="email" type="email" required placeholder="Masukkan Email" class="h-10 sm:h-11 w-full rounded-xl border border-[#b9c9c1] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] pl-10 pr-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
    </div>
</div>

<div>
    <label class="mb-1.5 block text-xs font-bold text-gray-900 dark:text-gray-200">Kontak</label>
    <div class="relative">
        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[#61706a] dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 4h10a1 1 0 011 1v14a1 1 0 01-1 1H7a1 1 0 01-1-1V5a1 1 0 011-1zM10 18h4M9 7h6v8H9z"></path>
        </svg>
        <input id="{{ $prefix }}nomor_hp" name="nomor_hp" type="text" required pattern="[0-9]+" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Nomor Telepon/WA" class="h-10 sm:h-11 w-full rounded-xl border border-[#b9c9c1] dark:border-[#284c43] bg-[#f4faf7] dark:bg-[#0f1c19] pl-10 pr-3 text-xs sm:text-sm text-gray-800 dark:text-white outline-none transition placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-[#35635b] focus:bg-white dark:focus:bg-[#0f1c19] focus:ring-2 focus:ring-[#35635b]/10">
    </div>
</div>
