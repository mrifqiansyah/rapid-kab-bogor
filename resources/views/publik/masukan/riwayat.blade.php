<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aduan - RAPID</title>
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
                        'ijo-sangatmuda': '#e3eeea',
                        'oren-muda': '#FBEBD1',
                        'oren-tua': '#B87A1E',
                        'biru-muda': '#DCEEF5',
                        'biru-tua': '#1E6E8C',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#F8F7F4] dark:bg-[#0d1614] font-sans antialiased text-gray-800 dark:text-slate-100 flex flex-col min-h-screen transition-colors duration-200">
    @include('publik.layout.navbarpublik')

    <main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @php
            $masukanItems = ($masukan ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator
                ? $masukan->getCollection()
                : collect($masukan ?? []);
            $statusClass = fn ($status) => match (strtolower((string) $status)) {
                'selesai' => 'bg-ijo-sangatmuda text-ijo-tua dark:bg-emerald-950/60 dark:text-emerald-300 dark:border dark:border-emerald-800/40',
                'diproses', 'proses' => 'bg-biru-muda text-biru-tua dark:bg-sky-950/60 dark:text-sky-300 dark:border dark:border-sky-800/40',
                default => 'bg-oren-muda text-oren-tua dark:bg-amber-950/60 dark:text-amber-300 dark:border dark:border-amber-700/40',
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

        <div class="space-y-3">

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">Daftar Aduan</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">Transparansi riwayat aspirasi, saran, dan tindak lanjut laporan pengaduan masyarakat seputar layanan Diskominfo Kabupaten Bogor.</p>
                </div>
            </div>
        </div>

        <section class="bg-white dark:bg-[#152420] rounded-xl p-6 border border-gray-100 dark:border-[#233a34] shadow-xl space-y-4 transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-[#0f1c19] text-gray-500 dark:text-gray-300 uppercase text-[10px] tracking-wider">
                            <th class="p-3 rounded-l-xl">Nama Pengadu</th>
                            <th class="p-3">Isi Aduan</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Balasan Admin</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-right rounded-r-xl">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#233a34] font-medium text-gray-700 dark:text-gray-200">
                        @forelse ($masukanItems as $aduan)
                            <tr class="aduan-row cursor-pointer hover:bg-gray-50/80 dark:hover:bg-white/5 transition" data-aduan-id="{{ $aduan->id_dataaduan }}" title="Klik untuk melihat detail aduan">
                                <td class="p-3 font-bold text-gray-900 dark:text-white whitespace-nowrap">{{ $aduan->nama_pengadu }}</td>
                                <td class="p-3 text-gray-500 dark:text-gray-300 min-w-[260px]">{{ $aduan->isi_aduan }}</td>
                                <td class="p-3 text-gray-500 dark:text-gray-300 min-w-[180px]">{{ $maskEmail($aduan->email) }}</td>
                                <td class="p-3 text-gray-500 dark:text-gray-300 min-w-[220px]">
                                    {{ $aduan->balasan_admin ? \Illuminate\Support\Str::limit($aduan->balasan_admin, 90) : 'Belum ada balasan' }}
                                </td>
                                <td class="p-3 text-center">
                                    <span class="{{ $statusClass($aduan->status) }} font-bold px-3 py-1 rounded-full text-[10px]">{{ (strtolower((string) ($aduan->status ?? '')) === 'pending' || empty($aduan->status)) ? 'Menunggu' : $aduan->status }}</span>
                                </td>
                                <td class="p-3 text-right text-gray-400 dark:text-gray-400 whitespace-nowrap">{{ $aduan->created_at ? \Carbon\Carbon::parse($aduan->created_at)->translatedFormat('d M Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500 dark:text-gray-400">Belum ada aduan di database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (($masukan ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="pt-4">
                    {{ $masukan->links() }}
                </div>
            @endif
        </section>
    </main>

    <!-- Modal Detail Aduan Publik (Z-Index 2000 untuk menutupi sticky navbar z-1001) -->
    <div id="aduan-modal" class="fixed inset-0 z-[2000] hidden items-center justify-center bg-slate-950/70 backdrop-blur-md p-3 sm:p-6 overflow-y-auto transition-all duration-300">
        <div class="relative my-auto flex max-h-[92vh] sm:max-h-[85vh] w-full max-w-2xl flex-col rounded-3xl bg-white dark:bg-[#152420] text-gray-800 dark:text-slate-100 shadow-2xl overflow-hidden border border-gray-100 dark:border-[#233a34] transform transition-all animate-in fade-in zoom-in-95 duration-200">
            <!-- Header Modal -->
            <div class="bg-gradient-to-r from-ijo-tua to-ijo-semitua dark:from-[#0f1c19] dark:to-[#152420] text-white p-5 sm:p-6 flex items-start justify-between gap-4 border-b border-white/10 dark:border-[#233a34] shrink-0">
                <div class="space-y-1 min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white/15 dark:bg-emerald-950/80 text-emerald-100 dark:text-emerald-300 border border-white/20 dark:border-emerald-800/40">
                            Detail Aduan Publik
                        </span>
                    </div>
                    <h2 id="aduan-modal-title" class="text-base sm:text-lg font-bold text-white leading-snug line-clamp-2 mt-1.5">-</h2>
                    <p id="aduan-modal-date" class="text-xs text-emerald-100/80 dark:text-gray-300 flex items-center gap-1.5 pt-0.5">
                        <span>-</span>
                    </p>
                </div>
                <button type="button" id="aduan-modal-close" onclick="closeAduanModal()" class="shrink-0 w-9 h-9 rounded-2xl bg-white/10 hover:bg-white/20 dark:bg-white/5 dark:hover:bg-white/15 flex items-center justify-center text-white text-base font-bold transition cursor-pointer shadow-xs" title="Tutup Modal">
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
                        <p id="aduan-modal-name" class="font-bold text-gray-900 dark:text-white truncate text-xs sm:text-sm">-</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50/80 dark:bg-[#0f1c19] border border-gray-100 dark:border-[#233a34] p-4 space-y-1">
                        <p class="text-[10px] uppercase font-extrabold tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1">
                            <span>Email</span>
                        </p>
                        <p id="aduan-modal-email" class="font-bold text-gray-900 dark:text-white truncate text-xs sm:text-sm font-mono">-</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50/80 dark:bg-[#0f1c19] border border-gray-100 dark:border-[#233a34] p-4 space-y-1">
                        <p class="text-[10px] uppercase font-extrabold tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1">
                            <span>Dinas Tujuan</span>
                        </p>
                        <p id="aduan-modal-dinas" class="font-bold text-ijo-tua dark:text-emerald-400 truncate text-xs sm:text-sm">-</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50/80 dark:bg-[#0f1c19] border border-gray-100 dark:border-[#233a34] p-4 space-y-1">
                        <p class="text-[10px] uppercase font-extrabold tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1">
                            <span>Status</span>
                        </p>
                        <div id="aduan-modal-status-badge" class="mt-0.5">
                            <p id="aduan-modal-status" class="font-bold text-gray-900 dark:text-white text-xs sm:text-sm">-</p>
                        </div>
                    </div>
                </div>

                <!-- Card Isi Aduan -->
                <div class="rounded-2xl border border-gray-100 dark:border-[#233a34] bg-white dark:bg-[#0f1c19] p-5 space-y-2 shadow-2xs">
                    <p class="text-[10px] uppercase font-extrabold tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1.5">
                        <span>Isi Aduan / Masukan</span>
                    </p>
                    <p id="aduan-modal-body" class="text-xs sm:text-sm leading-relaxed text-gray-700 dark:text-gray-200 whitespace-pre-line font-medium">-</p>
                </div>

                <!-- Card Lampiran Foto -->
                <div id="aduan-modal-photo-container" class="hidden rounded-2xl border border-gray-100 dark:border-[#233a34] bg-white dark:bg-[#0f1c19] p-4 sm:p-5 space-y-2.5 shadow-2xs">
                    <p class="text-[10px] uppercase font-extrabold tracking-wider text-gray-400 dark:text-gray-400 flex items-center gap-1.5">
                        <span>Lampiran Foto Aduan</span>
                    </p>
                    <button type="button" 
                            onclick="openPhotoModal(currentPhotoUrl, currentPhotoAuthor)" 
                            class="group relative w-full overflow-hidden rounded-xl bg-gray-50 dark:bg-[#0a1210] border border-gray-200/80 dark:border-[#284c43] flex items-center justify-center cursor-pointer transition hover:border-[#35635b] dark:hover:border-emerald-500/60 focus:outline-none"
                            title="Klik untuk melihat foto dalam ukuran penuh">
                        <img id="aduan-modal-photo-img" src="" alt="Lampiran Foto Aduan" class="w-full max-h-72 sm:max-h-80 object-contain rounded-xl transition duration-300 group-hover:scale-[1.01]">
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
                    <p id="aduan-modal-reply" class="text-xs sm:text-sm leading-relaxed text-gray-800 dark:text-gray-100 whitespace-pre-line font-medium">-</p>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="px-5 sm:px-6 py-3 bg-gray-50/80 dark:bg-[#0f1c19] border-t border-gray-100 dark:border-[#233a34] flex items-center justify-between shrink-0">
                <span class="text-[11px] text-gray-400 dark:text-gray-400 flex items-center gap-1">
                    <span>Klik di luar area atau tekan ESC untuk menutup</span>
                </span>
                <button type="button" onclick="closeAduanModal()" class="px-5 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 dark:bg-white/10 dark:hover:bg-white/20 text-gray-700 dark:text-gray-200 text-xs font-bold transition cursor-pointer">
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

    @include('publik.layout.footer')
    <script>
        const aduanDetails = @json($aduanDetailItems);
        const aduanModal = document.getElementById('aduan-modal');
        const aduanModalPhotoContainer = document.getElementById('aduan-modal-photo-container');
        const aduanModalPhotoImg = document.getElementById('aduan-modal-photo-img');

        let currentPhotoUrl = '';
        let currentPhotoAuthor = '';

        function setAduanText(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value || '-';
            }
        }

        function updateAduanStatusBadge(statusText) {
            const badgeEl = document.getElementById('aduan-modal-status-badge');
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

        function closeAduanModal() {
            if (aduanModal) {
                aduanModal.classList.add('hidden');
                aduanModal.classList.remove('flex');
            }
        }

        document.querySelectorAll('.aduan-row').forEach((row) => {
            row.addEventListener('click', () => {
                const detail = aduanDetails[row.dataset.aduanId];

                if (!detail) {
                    return;
                }

                setAduanText('aduan-modal-title', detail.isi_aduan.length > 70 ? detail.isi_aduan.slice(0, 70) + '...' : detail.isi_aduan);
                setAduanText('aduan-modal-date', detail.tanggal);
                setAduanText('aduan-modal-name', detail.nama_pengadu);
                setAduanText('aduan-modal-email', detail.email);
                setAduanText('aduan-modal-dinas', detail.dinas_nama);
                updateAduanStatusBadge(detail.status);
                setAduanText('aduan-modal-body', detail.isi_aduan);
                setAduanText('aduan-modal-reply', detail.balasan_admin);

                currentPhotoUrl = detail.foto_url || '';
                currentPhotoAuthor = detail.nama_pengadu || 'Anonim';

                if (detail.foto_url) {
                    aduanModalPhotoImg.src = detail.foto_url;
                    aduanModalPhotoContainer.classList.remove('hidden');
                } else {
                    aduanModalPhotoContainer.classList.add('hidden');
                    aduanModalPhotoImg.src = '';
                }

                aduanModal.classList.remove('hidden');
                aduanModal.classList.add('flex');
            });
        });

        aduanModal?.addEventListener('click', (event) => {
            if (event.target === aduanModal) {
                closeAduanModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && aduanModal && !aduanModal.classList.contains('hidden')) {
                closeAduanModal();
            }
        });

        // ==========================================
        // PHOTO LIGHTBOX POP-UP & ZOOM / PAN LOGIC
        // ==========================================
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

        // Tutup jika klik backdrop / latar belakang hitam
        photoModal?.addEventListener('click', (e) => {
            if (e.target === photoModal) {
                closePhotoModal();
            }
        });

        // Tutup jika tombol ESC ditekan
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !photoModal?.classList.contains('hidden')) {
                closePhotoModal();
            }
        });
    </script>
</body>
</html>
