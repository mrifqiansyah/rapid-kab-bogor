@extends('admin.layout.app')

@section('title', 'Pengaduan Masyarakat')

@section('content')
@php
    $statusFilter = request('status', 'semua');
    $statusCategory = function ($status) {
        $normalized = strtolower(trim((string) $status));

        return match ($normalized) {
            'pending', 'menunggu' => 'menunggu',
            'diproses', 'proses', 'di baca' => 'diproses',
            'selesai' => 'selesai',
            default => $normalized ?: 'menunggu',
        };
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

    $filteredMasukan = $statusFilter === 'semua'
        ? $masukan
        : $masukan->filter(fn ($item) => $statusCategory($item->status) === $statusFilter);

    $totalAduan = $masukan->count();
    $totalMenunggu = $masukan->filter(fn ($item) => $statusCategory($item->status) === 'menunggu')->count();
    $totalDiproses = $masukan->filter(fn ($item) => $statusCategory($item->status) === 'diproses')->count();
    $totalSelesai = $masukan->filter(fn ($item) => $statusCategory($item->status) === 'selesai')->count();
@endphp

<div class="max-w-[1400px] mx-auto space-y-6">

    <!-- Stat Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] rounded-2xl p-5 shadow-xs transition-colors flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-gray-400 dark:text-gray-300 uppercase tracking-wider">Total Pengaduan</p>
                <p class="mt-2 text-3xl font-black text-[#35635b] dark:text-emerald-400">{{ number_format($totalAduan) }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] rounded-2xl p-5 shadow-xs transition-colors flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-gray-400 dark:text-gray-300 uppercase tracking-wider">Menunggu</p>
                <p class="mt-2 text-3xl font-black text-rose-600 dark:text-rose-400">{{ number_format($totalMenunggu) }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] rounded-2xl p-5 shadow-xs transition-colors flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-gray-400 dark:text-gray-300 uppercase tracking-wider">Diproses</p>
                <p class="mt-2 text-3xl font-black text-amber-600 dark:text-amber-400">{{ number_format($totalDiproses) }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] rounded-2xl p-5 shadow-xs transition-colors flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-gray-400 dark:text-gray-300 uppercase tracking-wider">Selesai</p>
                <p class="mt-2 text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($totalSelesai) }}</p>
            </div>
        </div>
    </div>

    <!-- Filter Pills (Kunker Style) & Search Card -->
    <div class="bg-white dark:bg-[#152420] rounded-2xl shadow-xs border border-gray-100 dark:border-[#233a34] p-5 sm:p-6 space-y-4 transition-colors">
        <!-- Status Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <a href="{{ route('admin.masukkan.lihat', ['status' => 'semua']) }}"
               class="px-5 py-2 rounded-full text-xs font-extrabold transition-all whitespace-nowrap {{ $statusFilter === 'semua' ? 'bg-[#35635b] text-white shadow-sm' : 'bg-gray-100 dark:bg-[#0f1c19] text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 border border-gray-200 dark:border-[#284c43]' }}">
                Semua Aduan 
            </a>
            <a href="{{ route('admin.masukkan.lihat', ['status' => 'menunggu']) }}"
               class="px-5 py-2 rounded-full text-xs font-extrabold transition-all whitespace-nowrap {{ $statusFilter === 'menunggu' ? 'bg-rose-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-[#0f1c19] text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 border border-gray-200 dark:border-[#284c43]' }}">
                Menunggu 
            </a>
            <a href="{{ route('admin.masukkan.lihat', ['status' => 'diproses']) }}"
               class="px-5 py-2 rounded-full text-xs font-extrabold transition-all whitespace-nowrap {{ $statusFilter === 'diproses' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-[#0f1c19] text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 border border-gray-200 dark:border-[#284c43]' }}">
                Diproses 
            </a>
            <a href="{{ route('admin.masukkan.lihat', ['status' => 'selesai']) }}"
               class="px-5 py-2 rounded-full text-xs font-extrabold transition-all whitespace-nowrap {{ $statusFilter === 'selesai' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-[#0f1c19] text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/10 border border-gray-200 dark:border-[#284c43]' }}">
                Selesai 
            </a>
        </div>
    </div>

    <!-- Table Container Section -->
    <div class="bg-white dark:bg-[#152420] rounded-2xl shadow-xs border border-gray-100 dark:border-[#233a34] overflow-hidden transition-colors">
        <div class="border-b border-gray-100 dark:border-[#233a34] px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-gray-800 dark:text-white">Daftar Pengaduan Masyarakat</h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-300">Menampilkan {{ $filteredMasukan->count() }} dari {{ $totalAduan }} pengaduan.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[1200px]">
                <thead>
                    <tr class="bg-[#35635b] dark:bg-[#1b3832] text-white text-xs font-extrabold uppercase tracking-wider">
                        <th class="px-6 py-4">Pengadu</th>
                        <th class="px-6 py-4">Foto Lampiran</th>
                        <th class="px-6 py-4">Dinas Tujuan</th>
                        <th class="px-6 py-4">Email & Kontak</th>
                        <th class="px-6 py-4">Isi Aduan</th>
                        <th class="px-6 py-4">Balasan Admin</th>
                        <th class="px-6 py-4">Waktu & Tanggal</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#233a34] text-sm">
                    @forelse ($filteredMasukan as $item)
                        @php $fotoUrl = $getFotoUrl($item->foto); @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-[#1b332d] transition">
                            <td class="px-6 py-4 font-extrabold text-gray-900 dark:text-white">
                                {{ $item->nama_pengadu }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($fotoUrl)
                                    <img src="{{ $fotoUrl }}"
                                         onclick="openDocumentPreview('{{ $fotoUrl }}', 'Foto Pengaduan - {{ e(addslashes($item->nama_pengadu)) }}', '{{ addslashes(basename($item->foto)) }}')"
                                         alt="Foto Lampiran"
                                         class="w-12 h-12 object-cover rounded-xl border border-gray-200 dark:border-[#284c43] shadow-2xs hover:scale-105 transition cursor-pointer mx-auto"
                                         title="Klik untuk memperbesar foto">
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">Tidak ada foto</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-[#35635b] dark:text-emerald-400">
                                {{ $item->dinas?->nama_dinas ?? 'Umum / Diskominfo' }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-200 text-xs">
                                <p class="font-medium">{{ $item->email }}</p>
                                @if ($item->nomor_hp)
                                    <p class="text-gray-500 dark:text-gray-400">{{ $item->nomor_hp }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-200 max-w-xs leading-relaxed text-xs">
                                {{ \Illuminate\Support\Str::limit($item->isi_aduan, 90) }}
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-200 max-w-xs leading-relaxed text-xs">
                                @if ($item->balasan_admin)
                                    <span class="text-emerald-700 dark:text-emerald-300 font-medium">{{ \Illuminate\Support\Str::limit($item->balasan_admin, 80) }}</span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 italic">Belum dibalas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-slate-200 text-xs whitespace-nowrap">
                                <p class="font-bold">{{ optional($item->created_at)->format('H:i') ?? '-' }} WIB</p>
                                <p class="text-gray-500 dark:text-gray-400">{{ optional($item->created_at)->translatedFormat('d M Y') ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('admin.masukkan.update', $item->id_dataaduan) }}">
                                    @csrf
                                    @method('PUT')
                                    @php $cat = $statusCategory($item->status); @endphp
                                    <select
                                        name="status"
                                        onchange="this.form.submit()"
                                        class="rounded-full px-3 py-1 text-xs font-bold outline-none cursor-pointer border transition {{ $cat === 'selesai' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-200' : ($cat === 'diproses' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border-amber-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300 border-rose-200') }}">
                                        <option value="Menunggu" @selected($cat === 'menunggu')>Menunggu</option>
                                        <option value="Diproses" @selected($cat === 'diproses')>Diproses</option>
                                        <option value="Selesai" @selected($cat === 'selesai')>Selesai</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2 whitespace-nowrap">
                                    <button
                                        type="button"
                                        onclick="openReplyModal(this)"
                                        data-id="{{ $item->id_dataaduan }}"
                                        data-action="{{ route('admin.masukkan.reply', $item->id_dataaduan) }}"
                                        data-pengadu="{{ $item->nama_pengadu }}"
                                        data-aduan="{{ $item->isi_aduan }}"
                                        data-balasan="{{ $item->balasan_admin }}"
                                        data-foto="{{ $fotoUrl }}"
                                        class="inline-flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-[#0f513f] dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 px-3 py-1.5 text-xs font-bold transition hover:bg-emerald-100 dark:hover:bg-emerald-900/60 cursor-pointer shadow-2xs">
                                        Balas
                                    </button>
                                    <button
                                        type="button"
                                        onclick="openDeleteModal('{{ route('admin.masukkan.destroy', $item->id_dataaduan) }}', 'Hapus Pengaduan?', 'Apakah Anda yakin ingin menghapus pengaduan ini?')"
                                        class="inline-flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-950/50 text-red-700 dark:text-red-300 border border-red-200/80 dark:border-red-800/60 px-3 py-1.5 text-xs font-bold transition hover:bg-red-100 dark:hover:bg-red-900/60 cursor-pointer shadow-2xs">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada pengaduan masyarakat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Balas Aduan -->
<div id="modal-reply-aduan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white dark:bg-[#152420] border border-gray-100 dark:border-[#233a34] rounded-2xl shadow-2xl max-w-lg w-full p-6 text-left relative max-h-[92vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-100 dark:border-[#233a34]">
            <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Balas Pengaduan Masyarakat</h3>
            <button type="button" onclick="closeModal('modal-reply-aduan')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-lg leading-none cursor-pointer">✕</button>
        </div>
        <form id="form-reply-aduan" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="bg-gray-50 dark:bg-[#0f1c19] p-4 rounded-xl border border-gray-200 dark:border-[#284c43] space-y-3">
                <div>
                    <p class="text-[11px] font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pengadu: <span id="reply-pengadu-nama" class="text-gray-900 dark:text-white font-bold"></span></p>
                    <p id="reply-aduan-teks" class="text-xs text-gray-700 dark:text-gray-300 italic mt-1 leading-relaxed"></p>
                </div>
                <div id="reply-foto-container" class="hidden pt-3 border-t border-gray-200 dark:border-[#284c43]">
                    <p class="text-[11px] font-extrabold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Foto Bukti Aduan</p>
                    <div onclick="viewReplyFotoFull()" 
                         class="group relative overflow-hidden rounded-xl border border-gray-200 dark:border-[#284c43] bg-black/5 dark:bg-black/30 cursor-pointer transition hover:border-emerald-500/50 hover:shadow-md"
                         title="Klik foto untuk melihat ukuran penuh">
                        <img id="reply-foto-img" 
                             src="" 
                             alt="Foto Bukti Aduan" 
                             class="w-full h-44 sm:h-52 object-contain rounded-xl transition duration-300 group-hover:scale-[1.02]">
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-black/75 text-white text-xs font-bold backdrop-blur-xs shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                <span>Lihat Ukuran Penuh</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Respon / Balasan Admin *</label>
                <textarea id="reply-balasan-text" name="balasan_admin" rows="4" required placeholder="Tuliskan respon resmi tindak lanjut pengaduan ini..." class="w-full rounded-xl border border-gray-200 dark:border-[#284c43] bg-white dark:bg-[#0f1c19] px-4 py-2.5 text-sm text-gray-800 dark:text-white outline-none focus:border-[#35635b]"></textarea>
            </div>
            <div class="pt-4 flex justify-end gap-2 border-t border-gray-100 dark:border-[#233a34]">
                <button type="button" onclick="closeModal('modal-reply-aduan')" class="px-4 py-2 text-xs font-bold rounded-xl border border-gray-300 dark:border-[#284c43] text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#1b332d] transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl bg-[#35635b] dark:bg-[#107050] text-white hover:bg-[#2b4f49] dark:hover:bg-[#0c5940] transition cursor-pointer shadow-xs">Kirim Balasan</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentReplyFotoUrl = '';
    let currentReplyPengadu = '';

    function viewReplyFotoFull() {
        if (currentReplyFotoUrl && currentReplyFotoUrl.trim() !== '') {
            const replyModal = document.getElementById('modal-reply-aduan');
            if (replyModal && !replyModal.classList.contains('hidden')) {
                replyModal.classList.add('hidden');
                replyModal.classList.remove('flex');
                replyModal.dataset.wasOpen = 'true';
            }
            if (typeof openDocumentPreview === 'function') {
                openDocumentPreview(currentReplyFotoUrl, 'Foto Bukti Aduan - ' + (currentReplyPengadu || 'Pengaduan'), 'foto-bukti-aduan.jpg');
            } else if (typeof openDocumentPreviewModal === 'function') {
                openDocumentPreviewModal(currentReplyFotoUrl, 'Foto Bukti Aduan - ' + (currentReplyPengadu || 'Pengaduan'));
            } else {
                window.open(currentReplyFotoUrl, '_blank');
            }
        }
    }

    window.addEventListener('documentPreviewClosed', function() {
        const replyModal = document.getElementById('modal-reply-aduan');
        if (replyModal && replyModal.dataset.wasOpen === 'true') {
            replyModal.classList.remove('hidden');
            replyModal.classList.add('flex');
            delete replyModal.dataset.wasOpen;
        }
    });

    // Alias fallback untuk kompatibilitas
    window.openDocumentPreviewModal = function(url, title, filename) {
        if (typeof openDocumentPreview === 'function') {
            openDocumentPreview(url, title, filename);
        } else {
            window.open(url, '_blank');
        }
    };

    function openReplyModal(btn) {
        const form = document.getElementById('form-reply-aduan');
        form.action = btn.dataset.action;
        const pengadu = btn.dataset.pengadu || '';
        currentReplyPengadu = pengadu;
        document.getElementById('reply-pengadu-nama').textContent = pengadu;
        document.getElementById('reply-aduan-teks').textContent = '"' + (btn.dataset.aduan || '') + '"';
        document.getElementById('reply-balasan-text').value = btn.dataset.balasan || '';

        const fotoUrl = btn.dataset.foto;
        currentReplyFotoUrl = fotoUrl || '';
        const fotoContainer = document.getElementById('reply-foto-container');
        const fotoImg = document.getElementById('reply-foto-img');

        if (fotoUrl && fotoUrl.trim() !== '') {
            fotoImg.src = fotoUrl;
            fotoContainer.classList.remove('hidden');
        } else {
            fotoContainer.classList.add('hidden');
        }

        openModal('modal-reply-aduan');
    }
</script>
@endsection
