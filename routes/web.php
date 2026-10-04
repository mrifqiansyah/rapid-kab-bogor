<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAgendaController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminAkunDinasController;
use App\Http\Controllers\AdminAkunKecamatanController;
use App\Http\Controllers\AdminDinasController;
use App\Http\Controllers\AdminInstansiController;
use App\Http\Controllers\AdminKecamatanDataController;
use App\Http\Controllers\AdminKehadiranController;
use App\Http\Controllers\AdminKunjunganController;
use App\Http\Controllers\AdminLaporanController;
use App\Http\Controllers\AdminMasukkanController;
use App\Http\Controllers\AdminPegawaiController;
use App\Http\Controllers\AdminPengaturanController;
use App\Http\Controllers\AdminPublikController;
use App\Http\Controllers\AdminRuangController;
use App\Http\Controllers\AdminTamuController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\PegawaiAuthController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\UserController;

Route::get('/', [PublicPageController::class, 'index'])->name('publik.beranda');
Route::redirect('/publik', '/');

Route::redirect('/login', '/admin/login')->name('login');
Route::post('/login/proses', [AdminAuthController::class, 'login'])->name('login.proses');

// ROUTE GRUP ADMIN
Route::get('/admin', function () {
    if (\Illuminate\Support\Facades\Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
})->name('admin');

Route::prefix('admin')->group(function () {
    // Show login form
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    // Protected admin routes (require admin authentication)
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/layout', [AdminDashboardController::class, 'layout'])->name('admin.layout');

        // Agenda & Kehadiran Internal
        Route::get('/agenda/tambah', fn () => redirect()->route('admin.agenda.lihat'));
        Route::post('/agenda/tambah', [AdminAgendaController::class, 'kelola_Agenda'])->name('admin.agenda.store');
        Route::get('/agenda/lihat', [AdminAgendaController::class, 'lihat_Agenda'])->name('admin.agenda.lihat');
        Route::get('/agenda/riwayat', [AdminAgendaController::class, 'riwayat_Agenda'])->name('admin.agenda.riwayat');
        
        Route::get('/agenda/detail', [AdminAgendaController::class, 'detail_Agenda']);
        Route::get('/agenda/{id}', [AdminAgendaController::class, 'detail_Agenda']);
        Route::get('/agenda/{id}/detail', [AdminAgendaController::class, 'detail_Agenda'])->name('admin.agenda.detail');

        Route::redirect('/agenda', '/admin/agenda/lihat')->name('admin.agenda');
        Route::get('/agenda/cari', [AdminAgendaController::class, 'cari_Agenda']);
        // Redirect bekas pengajuan agenda ke daftar agenda
        Route::redirect('/pengajuan-agenda', '/admin/agenda/lihat');

        // Halaman admin
        Route::get('/ruang', [AdminRuangController::class, 'daftarRuang'])->name('admin.ruang.lihat');

        Route::get('/pengguna', [AdminPegawaiController::class, 'dataPegawai'])->name('admin.pengguna.lihat');
        Route::get('/datapegawai', [AdminPegawaiController::class, 'dataPegawai'])->name('admin.datapegawai');
        Route::get('/pegawai', [AdminPegawaiController::class, 'dataPegawai'])->name('admin.pegawai.lihat');
        Route::get('/datatamu', [AdminTamuController::class, 'dataTamu'])->name('admin.datatamu');
        Route::get('/tamu', [AdminTamuController::class, 'dataTamu'])->name('admin.tamu.lihat');
        Route::get('/umpanbalik', [AdminMasukkanController::class, 'umpanBalik'])->name('admin.umpanbalik');
        Route::get('/masukkan', [AdminMasukkanController::class, 'umpanBalik'])->name('admin.masukkan.lihat');
        Route::middleware('superadmin')->group(function () {
            Route::get('/konten-publik', [AdminPublikController::class, 'index'])->name('admin.publik.index');
            Route::post('/konten-publik/berita/refresh', [AdminPublikController::class, 'refreshBerita'])->name('admin.publik.berita.refresh');
            Route::post('/konten-publik/galeri', [AdminPublikController::class, 'storeGaleri'])->name('admin.publik.galeri.store');
            Route::put('/konten-publik/galeri/{id}', [AdminPublikController::class, 'updateGaleri'])->name('admin.publik.galeri.update');
            Route::delete('/konten-publik/galeri/{id}', [AdminPublikController::class, 'destroyGaleri'])->name('admin.publik.galeri.destroy');
            Route::post('/konten-publik/youtube', [AdminPublikController::class, 'updateYoutube'])->name('admin.publik.youtube.update');
        });

        // Filter agenda by kategori surat
        Route::get('/agenda/kategori/internal-to-internal', [AdminAgendaController::class, 'lihat_AgendaInternalToInternal']);
        Route::get('/agenda/kategori/external-to-internal', [AdminAgendaController::class, 'lihat_AgendaExternalToInternal']);
        Route::get('/agenda/kategori/internal-to-external', [AdminAgendaController::class, 'lihat_AgendaInternalToExternal']);
        Route::get('/agenda/kategori/{kategori}', [AdminAgendaController::class, 'lihat_AgendaByCategory']);
        
        Route::put('/agenda/{id}/konfigurasi-fr', [AdminAgendaController::class, 'konfigurasi_FaceRecognition']);
        Route::get('/agenda/{id}/generate-qr', [AdminAgendaController::class, 'generate_QR']);
        Route::post('/agenda/{id}/dokumen', [AdminAgendaController::class, 'upload_DokumenAgenda'])->name('admin.agenda.dokumen.store');
        Route::delete('/agenda/{id}/dokumen/{dokumenId}', [AdminAgendaController::class, 'hapus_DokumenAgenda'])->name('admin.agenda.dokumen.destroy');
        Route::put('/agenda/{id}', [AdminAgendaController::class, 'update_Agenda'])->name('admin.agenda.update');
        Route::delete('/agenda/{id}', [AdminAgendaController::class, 'hapus_Agenda'])->name('admin.agenda.destroy');
        Route::post('/kehadiran/verifikasi', [AdminKehadiranController::class, 'verifikasi_Kehadiran']);

        // Kunjungan
        Route::get('/kunjungan/kelola', fn () => redirect()->route('admin.kunjungan.lihat'));
        Route::post('/kunjungan/kelola', [AdminKunjunganController::class, 'kelola_Kunjungan'])->name('admin.kunjungan.store');
        Route::put('/kunjungan/{id}', [AdminKunjunganController::class, 'update_Kunjungan'])->name('admin.kunjungan.update');
        Route::delete('/kunjungan/{id}', [AdminKunjunganController::class, 'hapus_Kunjungan'])->name('admin.kunjungan.destroy');
        Route::get('/kunjungan', [AdminKunjunganController::class, 'daftarKunjungan'])->name('admin.kunjungan.lihat');
        Route::get('/daftarkunjungan', [AdminKunjunganController::class, 'daftarKunjungan'])->name('admin.daftarkunjungan');
        
        Route::get('/laporan/cetak', [AdminLaporanController::class, 'cetak_Laporan']);
        Route::get('/kehadiran/download', [AdminLaporanController::class, 'cetak_Laporan'])->name('admin.kehadiran.download');
        Route::get('/laporan', [AdminLaporanController::class, 'laporan'])->name('admin.laporan.lihat');

        Route::post('/ruang', [AdminRuangController::class, 'store_Ruang'])->name('admin.ruang.store');
        Route::put('/ruang/{id}', [AdminRuangController::class, 'update_Ruang'])->name('admin.ruang.update');
        Route::delete('/ruang/{id}', [AdminRuangController::class, 'hapus_Ruang'])->name('admin.ruang.destroy');

        Route::post('/pegawai/bidang', [AdminPegawaiController::class, 'storeBidang'])->name('admin.pegawai.bidang.store');
        Route::delete('/pegawai/bidang/{id}', [AdminPegawaiController::class, 'destroyBidang'])->name('admin.pegawai.bidang.destroy');
        Route::post('/pegawai/jabatan', [AdminPegawaiController::class, 'storeJabatan'])->name('admin.pegawai.jabatan.store');
        Route::delete('/pegawai/jabatan/{id}', [AdminPegawaiController::class, 'destroyJabatan'])->name('admin.pegawai.jabatan.destroy');
        Route::post('/pegawai', [AdminPegawaiController::class, 'store_Pegawai'])->name('admin.pegawai.store');
        Route::put('/pegawai/{id}', [AdminPegawaiController::class, 'update_Pegawai'])->name('admin.pegawai.update');
        Route::put('/pegawai/{id}/verifikasi', [AdminPegawaiController::class, 'verifikasi_Pegawai'])->name('admin.pegawai.verifikasi');
        Route::post('/pegawai/{id}/reset-wajah', [AdminPegawaiController::class, 'resetWajah'])->name('admin.pegawai.reset-wajah');
        Route::delete('/pegawai/{id}', [AdminPegawaiController::class, 'hapus_Pegawai'])->name('admin.pegawai.destroy');

        Route::post('/tamu', [AdminTamuController::class, 'store_Tamu'])->name('admin.tamu.store');
        Route::put('/tamu/{id}', [AdminTamuController::class, 'update_Tamu'])->name('admin.tamu.update');
        Route::delete('/tamu/{id}', [AdminTamuController::class, 'hapus_Tamu'])->name('admin.tamu.destroy');

        Route::put('/masukkan/{id}/reply', [AdminMasukkanController::class, 'reply_Masukan'])->name('admin.masukkan.reply');
        Route::put('/masukkan/{id}', [AdminMasukkanController::class, 'update_Masukan'])->name('admin.masukkan.update');
        Route::delete('/masukkan/{id}', [AdminMasukkanController::class, 'hapus_Masukan'])->name('admin.masukkan.destroy');

        // Master Data Instansi (Dinas & Kecamatan Combined)
        Route::get('/instansi', [AdminInstansiController::class, 'index'])->name('admin.instansi.index');
        Route::post('/instansi/dinas', [AdminInstansiController::class, 'storeDinas'])->name('admin.instansi.dinas.store');
        Route::put('/instansi/dinas/{id}', [AdminInstansiController::class, 'updateDinas'])->name('admin.instansi.dinas.update');
        Route::delete('/instansi/dinas/{id}', [AdminInstansiController::class, 'destroyDinas'])->name('admin.instansi.dinas.destroy');
        Route::post('/instansi/kecamatan', [AdminInstansiController::class, 'storeKecamatan'])->name('admin.instansi.kecamatan.store');
        Route::put('/instansi/kecamatan/{id}', [AdminInstansiController::class, 'updateKecamatan'])->name('admin.instansi.kecamatan.update');
        Route::delete('/instansi/kecamatan/{id}', [AdminInstansiController::class, 'destroyKecamatan'])->name('admin.instansi.kecamatan.destroy');

        // Master Data Dinas (Redirect / Legacy compatibility)
        Route::get('/dinas', fn () => redirect()->route('admin.instansi.index', ['tab' => 'dinas']))->name('admin.dinas.index');
        Route::get('/dinas/lihat', fn () => redirect()->route('admin.instansi.index', ['tab' => 'dinas']))->name('admin.dinas.lihat');
        Route::post('/dinas', [AdminInstansiController::class, 'storeDinas'])->name('admin.dinas.store');
        Route::put('/dinas/{id}', [AdminInstansiController::class, 'updateDinas'])->name('admin.dinas.update');
        Route::delete('/dinas/{id}', [AdminInstansiController::class, 'destroyDinas'])->name('admin.dinas.destroy');

        // Master Data Kecamatan (Redirect / Legacy compatibility)
        Route::get('/kecamatan', fn () => redirect()->route('admin.instansi.index', ['tab' => 'kecamatan']))->name('admin.kecamatan.index');
        Route::get('/kecamatan/lihat', fn () => redirect()->route('admin.instansi.index', ['tab' => 'kecamatan']))->name('admin.kecamatan.lihat');
        Route::post('/kecamatan', [AdminInstansiController::class, 'storeKecamatan'])->name('admin.kecamatan.store');
        Route::put('/kecamatan/{id}', [AdminInstansiController::class, 'updateKecamatan'])->name('admin.kecamatan.update');
        Route::delete('/kecamatan/{id}', [AdminInstansiController::class, 'destroyKecamatan'])->name('admin.kecamatan.destroy');

        // Manajemen Akun Admin / Dinas
        Route::redirect('/akun', '/admin/akun/dinas')->name('admin.akun.index');
        Route::get('/akun/dinas', [AdminAkunDinasController::class, 'index'])->name('admin.akun.dinas.index');
        Route::post('/akun/dinas', [AdminAkunDinasController::class, 'store'])->name('admin.akun.dinas.store');
        Route::put('/akun/dinas/{id}', [AdminAkunDinasController::class, 'update'])->name('admin.akun.dinas.update');
        Route::post('/akun/dinas/{id}/reset-password', [AdminAkunDinasController::class, 'resetPassword'])->name('admin.akun.dinas.reset-password');
        Route::delete('/akun/dinas/{id}', [AdminAkunDinasController::class, 'destroy'])->name('admin.akun.dinas.destroy');

        // Manajemen Akun Kecamatan
        Route::get('/akun/kecamatan', [AdminAkunKecamatanController::class, 'index'])->name('admin.akun.kecamatan.index');
        Route::post('/akun/kecamatan', [AdminAkunKecamatanController::class, 'store'])->name('admin.akun.kecamatan.store');
        Route::put('/akun/kecamatan/{id}', [AdminAkunKecamatanController::class, 'update'])->name('admin.akun.kecamatan.update');
        Route::post('/akun/kecamatan/{id}/reset-password', [AdminAkunKecamatanController::class, 'resetPassword'])->name('admin.akun.kecamatan.reset-password');
        Route::delete('/akun/kecamatan/{id}', [AdminAkunKecamatanController::class, 'destroy'])->name('admin.akun.kecamatan.destroy');

        // Pengaturan (Setting)
        Route::get('/pengaturan', [AdminPengaturanController::class, 'index'])->name('admin.pengaturan.index');
        Route::post('/pengaturan/publik', [AdminPengaturanController::class, 'updatePublik'])->name('admin.pengaturan.publik.update');
        Route::post('/pengaturan/bidang', [AdminPengaturanController::class, 'storeBidang'])->name('admin.pengaturan.bidang.store');
        Route::delete('/pengaturan/bidang/{id}', [AdminPengaturanController::class, 'destroyBidang'])->name('admin.pengaturan.bidang.destroy');
        Route::post('/pengaturan/jabatan', [AdminPengaturanController::class, 'storeJabatan'])->name('admin.pengaturan.jabatan.store');
        Route::delete('/pengaturan/jabatan/{id}', [AdminPengaturanController::class, 'destroyJabatan'])->name('admin.pengaturan.jabatan.destroy');
    });
});

// Routes Kehadiran
Route::post('/kehadiran/scan-qr', [KehadiranController::class, 'scan_QR']);
Route::post('/kehadiran/verifikasi-fr', [KehadiranController::class, 'verifikasi_FaceRecognition']);

// ROUTE GRUP PEGAWAI (PENDAFTARAN & REGISTRASI AKUN PEGAWAI)
Route::prefix('pegawai')->group(function () {
    Route::redirect('/login', '/pegawai/daftar')->name('pegawai.login');
    Route::post('/login', [PegawaiAuthController::class, 'login'])->name('pegawai.login.submit');
    Route::post('/password/otp', [PegawaiAuthController::class, 'kirimOtpLupaPassword'])->name('pegawai.password.otp');
    Route::post('/password/reset', [PegawaiAuthController::class, 'resetPassword'])->name('pegawai.password.reset');
    Route::get('/daftar', [PegawaiAuthController::class, 'showRegisterForm'])->name('pegawai.register');
    Route::post('/daftar', [PegawaiAuthController::class, 'register'])->name('pegawai.register.submit');
    // Halaman presensi pegawai diarahkan langsung ke presensi publik (Scan Wajah / QR)
    Route::get('/presensi', function (\Illuminate\Http\Request $request) {
        return redirect()->route('publik.presensi.pegawai', array_filter(['agenda_id' => $request->query('agenda_id')]));
    })->name('pegawai.presensi.index');

    // Redirect bekas pengajuan agenda pegawai
    Route::redirect('/pengajuan-agenda', '/pegawai/dashboard');

    Route::middleware('auth:pegawai')->group(function () {
        // Portal Pegawai
        Route::get('/dashboard', [PegawaiAuthController::class, 'dashboard'])->name('pegawai.dashboard');
        Route::get('/booking-ruang', [PegawaiAuthController::class, 'bookingRuang'])->name('pegawai.booking.index');
        Route::get('/history-rapat', [PegawaiAuthController::class, 'historyRapat'])->name('pegawai.history.index');
        Route::get('/profil', [PegawaiAuthController::class, 'profilPegawai'])->name('pegawai.profil.index');

        // Presensi & Profil existing
        Route::post('/presensi', [PegawaiAuthController::class, 'simpanPresensi'])->name('pegawai.presensi.submit');
        Route::post('/profil/password-otp', [PegawaiAuthController::class, 'kirimOtpPassword'])->name('pegawai.profil.password-otp');
        Route::put('/profil/update', [PegawaiAuthController::class, 'updateProfil'])->name('pegawai.profil.update');
        Route::post('/profil/face', [PegawaiAuthController::class, 'updateFace'])->name('pegawai.profil.face');
        Route::post('/logout', [PegawaiAuthController::class, 'logout'])->name('pegawai.logout');
    });
});

// Fitur pengaduan masyarakat
Route::post('/aduan/otp', [UserController::class, 'kirimOtpAduan'])->name('publik.aduan.otp');
Route::post('/aduan/kirim', [UserController::class, 'kirimAduan'])->name('publik.aduan.kirim');
Route::get('/aduan/cek/{id}', [UserController::class, 'cekStatusAduan']);

// Fitur cari jadwal agenda rapat publik
Route::get('/agenda/cari', [UserController::class, 'CariAgenda']);

// ROUTE MILIK USER
Route::prefix('user')->group(function () {
    // Halaman Beranda Informasi Publik
    Route::get('/beranda/pengumuman', [UserController::class, 'TampilkanPengumuman']);
    Route::get('/beranda/ringkasan', [UserController::class, 'TampilkanRingkasan']);

    // Informasi Agenda Rapat Terbuka Publik
    Route::get('/agenda/list', [UserController::class, 'listAgenda']);
    Route::get('/agenda/cari', [UserController::class, 'CariAgenda']);
    Route::get('/agenda/qr/{id}', [UserController::class, 'tampilkanQrKode']);

    // Manajemen Layanan Pengaduan (Aduan)
    Route::post('/aduan/kirim', [UserController::class, 'kirimAduan']);
    Route::get('/aduan/status/{id}', [UserController::class, 'cekStatusAduan']);

    // Pendaftaran Kehadiran Tamu (Non-Pegawai)
    Route::post('/tamu/hadir', [UserController::class, 'inputDataTamu'])->name('publik.tamu.hadir');
});

Route::get('/publik/agenda', [PublicPageController::class, 'agenda'])->name('publik.agenda');

Route::get('/publik/agenda/detail/{id?}', [PublicPageController::class, 'agendaDetail'])->name('publik.agenda.detail');

Route::get('/publik/agenda/{id}/lampiran/file', [PublicPageController::class, 'fileLampiranAgenda'])->name('publik.agenda.lampiran.file');

Route::get('/publik/agenda/{id}/lampiran', [PublicPageController::class, 'lampiranAgenda'])->name('publik.agenda.lampiran');

Route::get('/publik/agenda-detail/{id?}', [PublicPageController::class, 'agendaDetail'])->name('publik.agenda-detail');

Route::get('/publik/berita', [PublicPageController::class, 'berita'])->name('publik.berita');

Route::get('/publik/berita/detail/{id?}', [PublicPageController::class, 'beritaDetail'])->name('publik.berita.detail');

Route::get('/publik/masukan', [PublicPageController::class, 'masukan'])->name('publik.masukan');

Route::get('/publik/riwayat-aduan', [PublicPageController::class, 'riwayatAduan'])->name('publik.riwayat-aduan');

Route::redirect('/publik/feedback', '/publik/masukan');

Route::get('/publik/cuaca/api', [PublicPageController::class, 'cuacaApi'])->name('publik.cuaca.api');

Route::get('/publik/ulang-tahun', [PublicPageController::class, 'ulangTahun'])->name('publik.ulang-tahun');

Route::redirect('/publik/ulangtahun', '/publik/ulang-tahun')->name('publik.ulangtahun');

Route::get('/publik/galeri', [PublicPageController::class, 'galeri'])->name('publik.galeri');

Route::get('/publik/video', [PublicPageController::class, 'video'])->name('publik.video');

Route::get('/publik/index', [PublicPageController::class, 'index'])->name('publik.index');

Route::get('/publik/berita-detail/{id?}', [PublicPageController::class, 'beritaDetail'])->name('publik.berita-detail');

Route::get('/publik/presensi-pilih', function (\Illuminate\Http\Request $request) {
    if ($agendaId = $request->query('agenda_id')) {
        return redirect()->route('publik.agenda.detail', $agendaId);
    }
    return redirect()->route('publik.agenda');
})->name('publik.presensi.pilih');

Route::get('/publik/presensi-pegawai', [PublicPageController::class, 'presensiPegawaiPilih'])->name('publik.presensi.pegawai');
Route::get('/publik/presensi-pegawai-wajah', [PublicPageController::class, 'presensiPegawaiWajah'])->name('publik.presensi.pegawai.wajah');
Route::get('/api/pegawai/faces', [PegawaiAuthController::class, 'getRegisteredFaces'])->name('api.pegawai.faces');
Route::post('/api/presensi/face', [PegawaiAuthController::class, 'simpanPresensiFace'])->name('api.presensi.face');

Route::get('/publik/presensi-tamu', [PublicPageController::class, 'presensiTamu'])->name('publik.presensi.tamu');

Route::get('/publik/form-kunjungan', [PublicPageController::class, 'formKunjungan'])->name('publik.form-kunjungan');
Route::post('/publik/form-kunjungan/otp', [PublicPageController::class, 'kirimOtpKunjungan'])->name('publik.form-kunjungan.otp');
Route::post('/publik/form-kunjungan/simpan', [PublicPageController::class, 'simpanKunjungan'])->name('publik.form-kunjungan.simpan');
Route::redirect('/publik/kunjungan', '/publik/form-kunjungan');

Route::get('/publik/presensi/qr/{agenda}/hadir', [PublicPageController::class, 'qrHadir'])->name('publik.presensi.qr.hadir');

Route::get('/peta-situs', [PublicPageController::class, 'petaSitus'])->name('peta.situs');

// Route handler media storage publik (Laragon, cPanel, fallback tanpa symlink)
$serveStorageFile = function (string $path) {
    if (str_contains($path, '..')) {
        abort(400);
    }

    $cleanPath = ltrim(str_replace('\\', '/', $path), '/');
    if (str_starts_with($cleanPath, 'storage/')) {
        $cleanPath = substr($cleanPath, 8);
    }

    // Cek di berbagai lokasi penyimpanan server (Laragon, cPanel, public/storage, storage/app/public)
    $candidates = [
        storage_path('app/public/' . $cleanPath),
        storage_path('app/' . $cleanPath),
        public_path('storage/' . $cleanPath),
        public_path('uploads/' . $cleanPath),
        public_path($cleanPath),
        storage_path('app/public/presensi/' . $cleanPath),
        storage_path('app/public/tamu/' . $cleanPath),
        storage_path('app/public/aduan/' . $cleanPath),
        storage_path('app/public/pegawai/' . $cleanPath),
        public_path('storage/presensi/' . $cleanPath),
        public_path('storage/tamu/' . $cleanPath),
        public_path('storage/aduan/' . $cleanPath),
        public_path('storage/pegawai/' . $cleanPath),
    ];

    foreach ($candidates as $filePath) {
        if (file_exists($filePath) && is_file($filePath)) {
            $mime = mime_content_type($filePath) ?: 'image/jpeg';
            return response()->file($filePath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400',
                'Access-Control-Allow-Origin' => '*',
            ]);
        }
    }

    // Jika file fisik belum ter-upload di server, kembalikan gambar SVG pesan informasi (bukan broken image browser)
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="260" viewBox="0 0 400 260" fill="none">
        <rect width="400" height="260" fill="#152420" rx="16"/>
        <circle cx="200" cy="95" r="35" fill="#284c43"/>
        <path d="M190 85L210 105M210 85L190 105" stroke="#a7f3d0" stroke-width="3" stroke-linecap="round"/>
        <text x="200" y="160" text-anchor="middle" fill="#ffffff" font-family="sans-serif" font-size="14" font-weight="bold">Foto Bukti Presensi</text>
        <text x="200" y="185" text-anchor="middle" fill="#9ca3af" font-family="sans-serif" font-size="12">File gambar belum tersimpan di folder storage server</text>
    </svg>';

    return response($svg, 200, [
        'Content-Type' => 'image/svg+xml',
        'Cache-Control' => 'no-cache',
    ]);
};

Route::get('/media-storage/{path}', $serveStorageFile)->where('path', '.*')->name('storage.media');
Route::get('/storage/{path}', $serveStorageFile)->where('path', '.*')->name('storage.fallback');

