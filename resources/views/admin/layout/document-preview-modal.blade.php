<!-- MODAL PREVIEW DOKUMEN / LAMPIRAN / NOTULEN / DOKUMENTASI -->
<div id="modal-preview-dokumen" style="z-index: 999999 !important;" class="fixed inset-0 hidden items-center justify-center bg-black/85 backdrop-blur-sm p-2 sm:p-4 md:p-6 transition-all duration-200" onclick="handleDocModalClick(event)">
    <div class="relative flex flex-col w-full max-w-5xl max-h-[calc(100dvh-1rem)] sm:max-h-[92vh] rounded-2xl bg-white dark:bg-[#152420] shadow-2xl dark:border dark:border-[#284c43] overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Header Modal -->
        <div class="rounded-t-2xl bg-[#3f8078] dark:bg-[#163830] text-white px-3.5 py-3 sm:px-5 sm:py-3.5 flex items-center justify-between gap-2.5 sm:gap-3 shrink-0 border-b border-white/10">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <div class="min-w-0">
                    <h3 id="doc-preview-title" class="text-xs sm:text-base font-bold text-white truncate leading-tight">Preview Dokumen</h3>
                    <p id="doc-preview-filename" class="text-[10px] sm:text-[11px] text-white/75 truncate mt-0.5">-</p>
                </div>
            </div>

            <!-- Tombol Aksi Header -->
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <!-- Image Zoom Controls (ditampilkan jika file berupa gambar) -->
                <div id="doc-preview-zoom-controls" class="hidden items-center gap-1 sm:gap-1.5 shrink-0">
                    <button id="doc-btn-zoom-out" type="button" onclick="zoomDocImage(-0.25)" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15 hover:bg-white/25 text-white transition backdrop-blur-xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed" title="Perkecil">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                    </button>
                    <button type="button" onclick="resetDocImageZoom()" class="flex h-8 px-2 sm:px-2.5 items-center justify-center rounded-lg bg-white/15 hover:bg-white/25 text-white text-[10px] sm:text-xs font-bold transition backdrop-blur-xs cursor-pointer" title="Reset (100%)">
                        <span id="doc-preview-scale">100%</span>
                    </button>
                    <button id="doc-btn-zoom-in" type="button" onclick="zoomDocImage(0.25)" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15 hover:bg-white/25 text-white transition backdrop-blur-xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed" title="Perbesar">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>

                <!-- Tombol Unduh / Buka Asli -->
                <a id="doc-preview-download" href="#" target="_blank" download class="inline-flex items-center gap-1.5 rounded-lg bg-white/15 hover:bg-white/25 px-2.5 sm:px-3 py-1.5 text-xs font-bold text-white transition backdrop-blur-xs" title="Unduh file">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3M7 10l5 5m0 0l5-5m-5 5V3"></path>
                    </svg>
                    <span class="hidden sm:inline">Unduh</span>
                </a>

                <!-- Tombol Tutup -->
                <button type="button" onclick="closeDocumentPreview()" class="flex h-8 w-8 items-center justify-center rounded-full text-white/80 transition hover:bg-white/20 hover:text-white cursor-pointer" aria-label="Tutup preview">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body Konten Preview -->
        <div class="relative flex-1 overflow-auto min-h-[260px] sm:min-h-[350px] max-h-[calc(100dvh-70px)] sm:max-h-[calc(92vh-60px)] bg-gray-50 dark:bg-[#0f1c19] flex items-center justify-center">
            
            <!-- 1. Preview PDF (iframe) -->
            <iframe id="doc-preview-pdf" src="" class="hidden w-full h-[65dvh] sm:h-[72vh] border-0 bg-white dark:bg-[#0f1c19]" title="Preview PDF"></iframe>

            <!-- 2. Preview Gambar (img) -->
            <div id="doc-preview-image-container" class="hidden w-full h-full p-2 sm:p-4 flex items-center justify-center overflow-hidden max-h-[65dvh] sm:max-h-[72vh] select-none touch-none cursor-default">
                <img id="doc-preview-img" src="" alt="Preview Gambar" ondragstart="return false;" class="max-h-[60dvh] sm:max-h-[68vh] max-w-full rounded-lg object-contain shadow-md origin-center will-change-transform select-none pointer-events-auto">
            </div>

            <!-- 3. Fallback Dokumen Lain (Word/Excel/dll) -->
            <div id="doc-preview-unsupported" class="hidden flex flex-col items-center justify-center py-10 sm:py-16 px-4 sm:px-6 text-center space-y-3 sm:space-y-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-emerald-100 dark:bg-[#1b3832] text-[#04733f] dark:text-emerald-300 flex items-center justify-center text-3xl sm:text-4xl shadow-inner">
                    📄
                </div>
                <div>
                    <h4 id="doc-unsupported-name" class="text-sm sm:text-base font-bold text-gray-800 dark:text-white">Dokumen Dokumen</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-md">Format file ini (.docx/.doc) tidak dapat dipratinjau langsung di browser. Silakan unduh dokumen untuk membukanya.</p>
                </div>
                <a id="doc-unsupported-btn" href="#" download class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 bg-[#04733f] hover:bg-[#035f35] dark:bg-[#107050] dark:hover:bg-[#0c5940] text-white font-bold rounded-xl text-xs shadow-md transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3M7 10l5 5m0 0l5-5m-5 5V3"></path>
                    </svg>
                    <span>Unduh Dokumen Sekarang</span>
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    let docPhotoScale = 1.0;
    const docPhotoMinScale = 1.0;
    const docPhotoMaxScale = 3.5;
    let docPhotoTranslateX = 0;
    let docPhotoTranslateY = 0;
    let isDraggingDocPhoto = false;
    let docPhotoStartX = 0;
    let docPhotoStartY = 0;
    let docPhotoLastTranslateX = 0;
    let docPhotoLastTranslateY = 0;

    function zoomDocImage(delta) {
        const newScale = Math.min(Math.max(Number((docPhotoScale + delta).toFixed(2)), docPhotoMinScale), docPhotoMaxScale);
        if (newScale === docPhotoScale) return;
        docPhotoScale = newScale;
        if (docPhotoScale <= docPhotoMinScale) {
            docPhotoTranslateX = 0;
            docPhotoTranslateY = 0;
        }
        applyDocPhotoTransform(true);
    }

    function resetDocImageZoom() {
        docPhotoScale = 1.0;
        docPhotoTranslateX = 0;
        docPhotoTranslateY = 0;
        applyDocPhotoTransform(true);
    }

    function applyDocPhotoTransform(smooth = true) {
        const img = document.getElementById('doc-preview-img');
        const container = document.getElementById('doc-preview-image-container');
        const scaleEl = document.getElementById('doc-preview-scale');
        const btnOut = document.getElementById('doc-btn-zoom-out');
        const btnIn = document.getElementById('doc-btn-zoom-in');

        if (!img) return;

        if (docPhotoScale <= docPhotoMinScale) {
            docPhotoScale = docPhotoMinScale;
            docPhotoTranslateX = 0;
            docPhotoTranslateY = 0;
            if (container) container.style.cursor = 'default';
        } else {
            if (container) container.style.cursor = isDraggingDocPhoto ? 'grabbing' : 'grab';
        }

        img.style.transition = smooth ? 'transform 0.18s cubic-bezier(0.16, 1, 0.3, 1)' : 'none';
        img.style.transform = `translate(${docPhotoTranslateX}px, ${docPhotoTranslateY}px) scale(${docPhotoScale})`;

        const percentText = `${Math.round(docPhotoScale * 100)}%`;
        if (scaleEl) scaleEl.textContent = percentText;
        if (btnOut) btnOut.disabled = (docPhotoScale <= docPhotoMinScale);
        if (btnIn) btnIn.disabled = (docPhotoScale >= docPhotoMaxScale);
    }

    function openDocumentPreview(fileUrl, title = 'Preview Dokumen', fileName = '') {
        const modal = document.getElementById('modal-preview-dokumen');
        const titleEl = document.getElementById('doc-preview-title');
        const filenameEl = document.getElementById('doc-preview-filename');
        const downloadBtn = document.getElementById('doc-preview-download');
        const zoomControls = document.getElementById('doc-preview-zoom-controls');

        const pdfFrame = document.getElementById('doc-preview-pdf');
        const imgContainer = document.getElementById('doc-preview-image-container');
        const imgEl = document.getElementById('doc-preview-img');
        const unsupportedContainer = document.getElementById('doc-preview-unsupported');
        const unsupportedName = document.getElementById('doc-unsupported-name');
        const unsupportedBtn = document.getElementById('doc-unsupported-btn');

        if (!modal || !fileUrl) return;

        // Pastikan modal berada di body teratas dengan z-index tertinggi
        if (modal.parentElement && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
        modal.style.zIndex = '999999';

        // Ekstrak nama file jika belum disediakan
        const cleanFileName = fileName || fileUrl.split('/').pop().split('?')[0] || 'Dokumen';
        const fileExt = cleanFileName.split('.').pop().toLowerCase();

        // Set metadata header
        if (titleEl) titleEl.textContent = title;
        if (filenameEl) filenameEl.textContent = cleanFileName;
        if (downloadBtn) {
            downloadBtn.href = fileUrl;
            downloadBtn.setAttribute('download', cleanFileName);
        }

        // Reset display
        pdfFrame.classList.add('hidden');
        pdfFrame.src = '';
        imgContainer.classList.add('hidden');
        imgEl.src = '';
        unsupportedContainer.classList.add('hidden');
        if (zoomControls) {
            zoomControls.classList.remove('flex');
            zoomControls.classList.add('hidden');
        }

        // Deteksi Tipe File
        const imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        const isImage = imageExtensions.includes(fileExt);
        const isPdf = fileExt === 'pdf';

        if (isPdf) {
            pdfFrame.src = fileUrl;
            pdfFrame.classList.remove('hidden');
        } else if (isImage) {
            imgEl.src = fileUrl;
            imgContainer.classList.remove('hidden');
            if (zoomControls) {
                zoomControls.classList.remove('hidden');
                zoomControls.classList.add('flex');
            }
            resetDocImageZoom();
        } else {
            if (unsupportedName) unsupportedName.textContent = cleanFileName;
            if (unsupportedBtn) {
                unsupportedBtn.href = fileUrl;
                unsupportedBtn.setAttribute('download', cleanFileName);
            }
            unsupportedContainer.classList.remove('hidden');
        }

        modal.classList.replace('hidden', 'flex');
        document.body.style.overflow = 'hidden';
    }

    function closeDocumentPreview() {
        const modal = document.getElementById('modal-preview-dokumen');
        if (!modal) return;

        const pdfFrame = document.getElementById('doc-preview-pdf');
        if (pdfFrame) pdfFrame.src = '';

        modal.classList.replace('flex', 'hidden');
        document.body.style.overflow = '';

        // Beritahu parent modal jika ada yang perlu direstore
        window.dispatchEvent(new CustomEvent('documentPreviewClosed'));
    }

    function handleDocModalClick(event) {
        if (event.target.id === 'modal-preview-dokumen') {
            closeDocumentPreview();
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('modal-preview-dokumen');
            if (modal && !modal.classList.contains('hidden')) {
                closeDocumentPreview();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('doc-preview-image-container');
        if (container) {
            container.addEventListener('wheel', (e) => {
                const modal = document.getElementById('modal-preview-dokumen');
                if (modal && !modal.classList.contains('hidden')) {
                    e.preventDefault();
                    zoomDocImage(e.deltaY < 0 ? 0.25 : -0.25);
                }
            }, { passive: false });

            const startDrag = (clientX, clientY) => {
                if (docPhotoScale <= docPhotoMinScale) return;
                isDraggingDocPhoto = true;
                docPhotoStartX = clientX;
                docPhotoStartY = clientY;
                docPhotoLastTranslateX = docPhotoTranslateX;
                docPhotoLastTranslateY = docPhotoTranslateY;
                container.style.cursor = 'grabbing';
            };

            const moveDrag = (clientX, clientY) => {
                if (!isDraggingDocPhoto || docPhotoScale <= docPhotoMinScale) return;
                const deltaX = clientX - docPhotoStartX;
                const deltaY = clientY - docPhotoStartY;
                const boundX = (container.clientWidth * (docPhotoScale - 1)) / 1.5 + 50;
                const boundY = (container.clientHeight * (docPhotoScale - 1)) / 1.5 + 50;
                docPhotoTranslateX = Math.max(-boundX, Math.min(boundX, docPhotoLastTranslateX + deltaX));
                docPhotoTranslateY = Math.max(-boundY, Math.min(boundY, docPhotoLastTranslateY + deltaY));
                applyDocPhotoTransform(false);
            };

            const endDrag = () => {
                if (isDraggingDocPhoto) {
                    isDraggingDocPhoto = false;
                    container.style.cursor = docPhotoScale > docPhotoMinScale ? 'grab' : 'default';
                    applyDocPhotoTransform(true);
                }
            };

            container.addEventListener('mousedown', (e) => startDrag(e.clientX, e.clientY));
            window.addEventListener('mousemove', (e) => moveDrag(e.clientX, e.clientY));
            window.addEventListener('mouseup', endDrag);

            container.addEventListener('touchstart', (e) => {
                if (e.touches.length === 1) startDrag(e.touches[0].clientX, e.touches[0].clientY);
            }, { passive: true });
            container.addEventListener('touchmove', (e) => {
                if (e.touches.length === 1) moveDrag(e.touches[0].clientX, e.touches[0].clientY);
            }, { passive: true });
            container.addEventListener('touchend', endDrag);

            container.addEventListener('dblclick', () => {
                if (docPhotoScale > 1.0) resetDocImageZoom();
                else zoomDocImage(1.0);
            });
        }
    });
</script>
