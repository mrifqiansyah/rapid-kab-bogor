<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sampaikan Masukan Anda - RAPID</title>
    @include('publik.layout.theme_script')
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'ijo-tua': '#35635b',
                        'ijo-semitua': '#2b4f49',
                        'ijo-muda': '#4e857b',
                        'ijo-sangatmuda': '#e3eeea',
                        'oren-utama': '#D89B3C',
                        'oren-muda': '#FBEBD1',
                        'oren-tua': '#B87A1E',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#F8F7F4] dark:bg-[#0d1614] font-sans antialiased text-gray-800 dark:text-slate-100 flex flex-col min-h-screen transition-colors duration-200">

    <!-- Memanggil Navbar Publik -->
    @include('publik.layout.navbarpublik') 

    <main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Header Section -->
        <div class="space-y-2">

            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">Sampaikan Masukan / Aduan Anda</h1>
                <p class="text-xs text-gray-500 dark:text-gray-300 mt-1">Laporkan kendala pelaksanaan rapat atau masalah teknis aplikasi</p>
            </div>
        </div>

        <!-- MAIN LAYOUT (Form Left 8 Cols, Sidebar Right 4 Cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT FORM COLUMN (8 Cols) -->
            <div class="lg:col-span-8 bg-white dark:bg-[#152420] rounded-xl p-6 md:p-8 border border-gray-100 dark:border-[#233a34] shadow-xl space-y-6 transition-colors">
                
                <div class="border-b border-gray-100 dark:border-[#233a34] pb-4">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">Ajukan Aduan Baru</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mt-0.5">Semua kolom wajib diisi</p>
                </div>

                @if (session('success'))
                    <div class="rounded-2xl bg-ijo-sangatmuda dark:bg-[#0f1c19] border border-ijo-muda/40 dark:border-[#284c43] px-4 py-3 text-xs font-bold text-ijo-tua dark:text-emerald-400">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 px-4 py-3 text-xs font-bold text-red-600 dark:text-red-300">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('publik.aduan.kirim') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Nama Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-200">Nama *</label>
                        <input type="text" name="nama_pengadu" value="{{ old('nama_pengadu') }}" required placeholder="Nama lengkap"
                               class="w-full bg-[#EAE8E1]/60 dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] focus:border-ijo-semitua focus:bg-white dark:focus:bg-[#152420] text-xs rounded-2xl px-4 py-3 text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none transition-all">
                    </div>

                    <!-- Email Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-200">Email *</label>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <input id="aduan-email" type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                                   class="w-full bg-[#EAE8E1]/60 dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] focus:border-ijo-semitua focus:bg-white dark:focus:bg-[#152420] text-xs rounded-2xl px-4 py-3 text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none transition-all">
                            <button id="send-otp-button" type="button" class="shrink-0 rounded-2xl bg-ijo-tua hover:bg-ijo-semitua dark:bg-[#107050] dark:hover:bg-[#0c5940] dark:border dark:border-[#10b981]/30 px-5 py-3 text-xs font-bold text-white transition-colors cursor-pointer">
                                Kirim OTP
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-400 dark:text-gray-400 flex items-center space-x-1 pt-0.5">
                            <span>Email akan disamarkan otomatis saat ditampilkan ke publik</span>
                        </p>
                    </div>

                    <p id="otp-status" class="hidden text-[10px] font-bold"></p>

                    <!-- OTP Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-200">Masukkan OTP *</label>
                        <input id="aduan-otp" type="text" name="otp" value="{{ old('otp') }}" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="6 digit kode OTP"
                               class="w-full bg-[#EAE8E1]/60 dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] focus:border-ijo-semitua focus:bg-white dark:focus:bg-[#152420] text-xs rounded-2xl px-4 py-3 text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none transition-all">
                        <p class="text-[10px] text-gray-400 dark:text-gray-400 pt-0.5">Kode OTP dikirim ke email dan berlaku 10 menit.</p>
                    </div>

                    <!-- No HP Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-200">No. HP *</label>
                        <input type="tel" name="nomor_pengadu" id="aduan-nomor-pengadu" value="{{ old('nomor_pengadu') }}" required inputmode="numeric" pattern="[0-9]+" maxlength="13" placeholder="Contoh: 081234567890" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                               onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                               class="w-full bg-[#EAE8E1]/60 dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] focus:border-ijo-semitua focus:bg-white dark:focus:bg-[#152420] text-xs rounded-2xl px-4 py-3 text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none transition-all">
                        <p class="text-[10px] text-gray-400 dark:text-gray-400 pt-0.5">Maksimal 13 digit angka (hanya angka tanpa spasi/huruf/simbol).</p>
                    </div>

                    <!-- Perangkat Daerah / Dinas Tujuan Dropdown -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-200">Perangkat Daerah / Dinas Tujuan *</label>
                        <div class="relative">
                            <select name="id_dinas" required class="w-full bg-[#EAE8E1]/60 dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] focus:border-ijo-semitua focus:bg-white dark:focus:bg-[#152420] text-xs rounded-2xl px-4 py-3 text-gray-700 dark:text-gray-200 appearance-none focus:outline-none transition-all cursor-pointer">
                                <option value="" disabled selected class="bg-white dark:bg-[#152420] text-gray-700 dark:text-gray-300">Pilih Perangkat Daerah / Dinas yang dituju</option>
                                @foreach ($dinasList ?? [] as $dinasItem)
                                    <option value="{{ $dinasItem->id_dinas }}" @selected(old('id_dinas') == $dinasItem->id_dinas) class="bg-white dark:bg-[#152420] text-gray-900 dark:text-white py-1">
                                        {{ $dinasItem->nama_lengkap ?? $dinasItem->nama_dinas }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500 text-xs">
                                ▼
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 dark:text-gray-400 pt-0.5">Pilih Dinas tujuan agar aduan diteruskan dan ditangani langsung oleh instansi yang sesuai.</p>
                    </div>

                    <!-- Kategori Masalah Dropdown -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-200">Kategori Masalah *</label>
                        <div class="relative">
                            <select name="kategori" required class="w-full bg-[#EAE8E1]/60 dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] focus:border-ijo-semitua focus:bg-white dark:focus:bg-[#152420] text-xs rounded-2xl px-4 py-3 text-gray-700 dark:text-gray-200 appearance-none focus:outline-none transition-all cursor-pointer">
                                <option value="" disabled selected class="bg-white dark:bg-[#152420] text-gray-700 dark:text-gray-300">Pilih kategori masalah</option>
                                <option value="rapat" class="bg-white dark:bg-[#152420] text-gray-900 dark:text-white py-1">Kendala Pelaksanaan Rapat</option>
                                <option value="aplikasi" class="bg-white dark:bg-[#152420] text-gray-900 dark:text-white py-1">Masalah Teknis Aplikasi</option>
                                <option value="infrastruktur" class="bg-white dark:bg-[#152420] text-gray-900 dark:text-white py-1">Jaringan / WiFi / Jaringan TI</option>
                                <option value="lainnya" class="bg-white dark:bg-[#152420] text-gray-900 dark:text-white py-1">Lainnya</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500 text-xs">
                                ▼
                            </div>
                        </div>
                    </div>

                    <!-- Isi Masukan Textarea -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-200">Isi Masukan / Aduan *</label>
                        <textarea rows="4" name="isi_aduan" required placeholder="Jelaskan detail masalah yang Anda alami, contoh: ruang rapat bentrok jadwal, atau aplikasi SIAP Bogor gagal login sejak pagi ini..." 
                                  class="w-full bg-[#EAE8E1]/60 dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] focus:border-ijo-semitua focus:bg-white dark:focus:bg-[#152420] text-xs rounded-2xl p-4 text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none transition-all resize-none">{{ old('isi_aduan') }}</textarea>
                    </div>

                    <!-- Lampiran File Upload -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-200">Lampiran</label>
                            <button type="button" id="aduan-btn-hapus-foto" onclick="clearAduanFoto()" class="text-[11px] font-bold text-red-500 hover:text-red-700 dark:text-red-400 hidden cursor-pointer">
                                ✕ Hapus Foto
                            </button>
                        </div>
                        <input type="file" name="foto" id="aduan-foto-input" accept="image/*" class="hidden">
                        
                        <label for="aduan-foto-input" id="aduan-dropzone" class="flex flex-col items-center justify-center bg-[#EAE8E1]/60 dark:bg-[#0f1c19] hover:bg-[#EAE8E1] dark:hover:bg-[#152420] border-2 border-dashed border-sky-400 dark:border-[#284c43] rounded-2xl p-4 cursor-pointer transition-colors relative overflow-hidden group">
                            
                            <!-- Placeholder -->
                            <div id="aduan-foto-placeholder" class="flex items-center space-x-3 w-full">
                                <div class="w-9 h-9 rounded-xl bg-white/80 dark:bg-white/10 flex items-center justify-center shrink-0 text-base shadow-xs group-hover:scale-105 transition-transform">
                                    🖼️
                                </div>
                                <div class="text-xs text-left">
                                    <p class="font-bold text-gray-800 dark:text-white">Klik untuk unggah gambar</p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400">PNG, JPG, atau WEBP - maks 5MB per gambar</p>
                                </div>
                            </div>

                            <!-- Preview Container -->
                            <div id="aduan-foto-preview-container" class="hidden flex flex-col items-center justify-center w-full py-1">
                                <img id="aduan-foto-preview-img" src="" alt="Preview Lampiran Aduan" class="max-h-36 w-auto rounded-xl object-contain shadow-xs border border-gray-200 dark:border-[#284c43]">
                                <p id="aduan-foto-preview-name" class="mt-2 text-xs font-semibold text-gray-700 dark:text-gray-300 truncate max-w-xs"></p>
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold mt-0.5">Klik untuk mengganti gambar</span>
                            </div>

                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-ijo-tua hover:bg-ijo-semitua dark:bg-[#107050] dark:hover:bg-[#0c5940] dark:border dark:border-[#10b981]/30 text-white font-bold text-xs py-3.5 rounded-2xl transition-colors flex items-center justify-center space-x-2 shadow-xs cursor-pointer">
                            <span>Kirim</span>
                        </button>
                    </div>

                </form>
            </div>

            <!-- RIGHT SIDEBAR COLUMN (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- CARD 1: Tips Mengisi Feedback -->
                <div class="bg-white dark:bg-[#152420] rounded-xl p-6 border border-gray-100 dark:border-[#233a34] shadow-lg hover:shadow-xl transition-all duration-300 space-y-4">
                    <h3 class="font-bold text-sm text-gray-900 dark:text-white">Tips Mengisi Feedback yang Baik</h3>
                    
                    <ul class="space-y-3 text-xs text-gray-600 dark:text-gray-300">
                        <li class="flex items-start space-x-2.5">
                            <span class="w-5 h-5 rounded-full bg-ijo-sangatmuda dark:bg-[#0f1c19] text-ijo-tua dark:text-emerald-400 border border-transparent dark:border-[#284c43] flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
                            <span>Jelaskan kronologi kejadian secara singkat & jelas</span>
                        </li>
                        <li class="flex items-start space-x-2.5">
                            <span class="w-5 h-5 rounded-full bg-ijo-sangatmuda dark:bg-[#0f1c19] text-ijo-tua dark:text-emerald-400 border border-transparent dark:border-[#284c43] flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
                            <span>Gunakan bahasa yang sopan dan mudah dipahami</span>
                        </li>
                        <li class="flex items-start space-x-2.5">
                            <span class="w-5 h-5 rounded-full bg-ijo-sangatmuda dark:bg-[#0f1c19] text-ijo-tua dark:text-emerald-400 border border-transparent dark:border-[#284c43] flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
                            <span>Satu formulir untuk satu masalah agar mudah ditelusuri</span>
                        </li>
                    </ul>
                </div>

                <!-- CARD 2: Butuh Bantuan Cepat? -->
                <div class="bg-white dark:bg-[#152420] rounded-xl p-6 border border-gray-100 dark:border-[#233a34] shadow-lg hover:shadow-xl transition-all duration-300 space-y-5">
                    <h3 class="font-bold text-sm text-gray-900 dark:text-white">Butuh Bantuan Cepat?</h3>

                    <div class="space-y-4 text-xs">
                        <!-- Call Center -->
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-2xl bg-gray-100 dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] flex items-center justify-center shrink-0 text-sm">
                                📞
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">Call Center</p>
                                <p class="text-gray-500 dark:text-gray-400 font-mono text-[11px]">(0251) 8750-000</p>
                            </div>
                        </div>

                        <!-- WhatsApp -->
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-2xl bg-gray-100 dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] flex items-center justify-center shrink-0 text-sm">
                                💬
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">WhatsApp</p>
                                <p class="text-gray-500 dark:text-gray-400 font-mono text-[11px]">0812-9876-5432</p>
                            </div>
                        </div>

                        <!-- Email Resmi -->
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-2xl bg-gray-100 dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] flex items-center justify-center shrink-0 text-sm">
                                ✉️
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">Email Resmi</p>
                                <p class="text-gray-500 dark:text-gray-400 font-mono text-[11px]">feedback@bogorkab.go.id</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pemberitahuan Estimasi Penanganan -->
                    <div class="pt-1">
                        <div class="p-3 bg-ijo-sangatmuda dark:bg-emerald-950/40 border border-ijo-tua/20 dark:border-emerald-800/40 rounded-xl text-xs text-ijo-tua dark:text-emerald-300">
                            <p class="font-bold">Estimasi Penanganan:</p>
                            <p class="text-[11px] mt-0.5 text-gray-600 dark:text-gray-300">Rata-rata aduan diproses dalam 1×24 Jam.</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- ========================================================= -->
        <!-- SEKSI BARU: DAFTAR ADUAN & REPLIES / TANGGAPAN ADMIN      -->
        <!-- ========================================================= -->
        <div class="bg-white dark:bg-[#152420] rounded-xl p-6 md:p-8 border border-gray-100 dark:border-[#233a34] shadow-xs space-y-6">
            <div class="border-b border-gray-100 dark:border-[#233a34] pb-4 flex flex-col md:flex-row md:items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Masukan & Aduan Publik</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar riwayat aduan publik beserta status tindak lanjut resmi dari tim Diskominfo Kabupaten Bogor</p>
                </div>
            </div>

            <div class="space-y-4">
                @if(isset($aduans) && count($aduans) > 0)
                    @foreach($aduans as $aduan)
                        <div class="border border-gray-100 dark:border-[#233a34] rounded-2xl p-5 bg-[#FBFBFA] dark:bg-[#0f1c19] space-y-3 transition-all hover:border-ijo-muda/40">
                            
                            <!-- Header Aduan: Nama, Email Samaran, Tanggal, & Badge Status -->
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200/60 dark:border-[#233a34] pb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-ijo-sangatmuda dark:bg-[#1b3832] text-ijo-tua dark:text-emerald-400 border border-transparent dark:border-emerald-500/30 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($aduan->nama_pengadu ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $aduan->nama_pengadu ?? 'Anonim' }}</p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-400 font-mono">
                                            {{ isset($aduan->email) ? Str::mask($aduan->email, '*', 2, 5) : '***@email.com' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <span class="text-[10px] text-gray-400 dark:text-gray-400">
                                        {{ isset($aduan->created_at) ? $aduan->created_at->format('d M Y, H:i') : 'Baru saja' }}
                                    </span>
                                    
                                    <!-- Badge Status Balasan -->
                                    @php
                                        $st = strtolower(trim((string) ($aduan->status ?? 'menunggu')));
                                        $hasReply = !empty($aduan->balasan_admin);
                                    @endphp
                                    @if($st === 'selesai' || ($hasReply && $st !== 'diproses'))
                                        <span class="bg-ijo-sangatmuda dark:bg-emerald-950/60 text-ijo-tua dark:text-emerald-300 text-[10px] font-bold px-2.5 py-1 rounded-full border border-ijo-muda/30 dark:border-emerald-800/40">
                                            ✓ Selesai
                                        </span>
                                    @elseif($st === 'diproses' || $st === 'proses' || $st === 'di baca')
                                        <span class="bg-blue-50 dark:bg-sky-950/60 text-blue-700 dark:text-sky-300 text-[10px] font-bold px-2.5 py-1 rounded-full border border-blue-200 dark:border-sky-800/40">
                                            🔄 Diproses
                                        </span>
                                    @else
                                        <span class="bg-oren-muda dark:bg-amber-950/60 text-oren-tua dark:text-amber-300 text-[10px] font-bold px-2.5 py-1 rounded-full border border-oren-utama/30 dark:border-amber-700/40">
                                            ⏳ Menunggu
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Isi Aduan -->
                            <div>
                                <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ $aduan->isi_aduan ?? 'Tidak ada pesan' }}
                                </p>
                                @php
                                    $hasPhoto = !empty($aduan->foto)
                                        && $aduan->foto !== 'aduan/default.jpg'
                                        && (file_exists(public_path('storage/' . $aduan->foto)) || \Illuminate\Support\Facades\Storage::disk('public')->exists($aduan->foto));
                                @endphp
                                @if($hasPhoto)
                                    <div class="mt-2.5">
                                        <button type="button" 
                                                onclick="openPhotoModal('{{ asset('storage/' . $aduan->foto) }}', '{{ e($aduan->nama_pengadu ?? 'Aduan Publik') }}')"
                                                class="inline-flex items-center space-x-1.5 text-[11px] text-ijo-semitua dark:text-emerald-400 hover:text-ijo-tua dark:hover:text-emerald-300 font-semibold bg-ijo-sangatmuda/60 dark:bg-[#1a332d] hover:bg-ijo-sangatmuda px-3 py-1.5 rounded-xl border border-ijo-muda/30 dark:border-[#284c43] transition-all shadow-2xs cursor-pointer">
                                            <span>🖼️</span>
                                            <span>Lihat Lampiran Foto</span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- BOX TANGGAPAN / REPLY ADMIN -->
                            @if(!empty($aduan->balasan_admin))
                                <div class="mt-3 pt-3 border-t border-dashed border-gray-200 dark:border-[#233a34]">
                                    <div class="bg-ijo-sangatmuda/40 dark:bg-[#1a332d] border border-ijo-muda/30 dark:border-[#284c43] rounded-xl p-4 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs">🛡️</span>
                                                <span class="text-xs font-bold text-ijo-tua dark:text-emerald-400">Admin</span>
                                            </div>
                                            <span class="text-[10px] text-gray-400 dark:text-gray-400">
                                                {{ isset($aduan->updated_at) ? \Carbon\Carbon::parse($aduan->updated_at)->format('d M Y, H:i') : '' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed">
                                            {{ $aduan->balasan_admin }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endforeach
                @else
                    <div class="border border-dashed border-gray-200 dark:border-[#233a34] rounded-2xl p-8 text-center bg-[#FBFBFA] dark:bg-[#0f1c19]">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">Belum ada aduan atau masukan publik yang dikirimkan.</p>
                    </div>
                @endif
            </div>
        </div>

    </main>

    <!-- Modal Preview Foto Lampiran Pop-Up (Larger size, Zoom Controls, Sleek Rounded Corners) -->
    <div id="photo-preview-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-xs p-3 sm:p-6 transition-all duration-300">
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
                    <span>Gunakan tombol <span class="font-bold text-gray-700 dark:text-gray-200">Zoom</span> / Scroll mouse, lalu <span class="font-bold text-gray-700 dark:text-gray-200">drag (geser mouse)</span> bebas ke kiri, kanan, atas, bawah.</span>
                </p>
                <div class="flex items-center space-x-2.5">
                    <a id="modal-photo-download" href="#" target="_blank" download class="text-xs text-ijo-semitua dark:text-emerald-400 hover:text-ijo-tua dark:hover:text-emerald-300 font-bold px-3.5 py-2 rounded-lg hover:bg-ijo-sangatmuda/50 dark:hover:bg-white/5 border border-ijo-muda/30 dark:border-[#284c43] transition-colors flex items-center space-x-1.5">
                        <span>⬇️</span>
                        <span>Unduh Gambar</span>
                    </a>
                    <button type="button" onclick="closePhotoModal()" class="bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 text-gray-700 dark:text-gray-200 text-xs font-bold px-4 py-2 rounded-lg transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Memanggil Footer Publik -->
    @include('publik.layout.footer') 

    <script>
        // Pop-up Lightbox Foto & Zoom / Pan Controls
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

        // Touch Drag / Pan Logic (Mobile)
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
            if (!photoModal || !modalPhotoImg) return;
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
            document.body.classList.add('overflow-hidden');
        }

        function closePhotoModal() {
            if (!photoModal) return;
            photoModal.classList.add('hidden');
            photoModal.classList.remove('flex');
            if (modalPhotoImg) modalPhotoImg.src = '';
            resetZoom();
            document.body.classList.remove('overflow-hidden');
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

        const otpButton = document.getElementById('send-otp-button');
        const otpEmail = document.getElementById('aduan-email');
        const otpStatus = document.getElementById('otp-status');
        const csrfToken = document.querySelector('input[name="_token"]')?.value;

        function showOtpStatus(message, isSuccess = false) {
            if (!otpStatus) return;
            otpStatus.textContent = message;
            otpStatus.classList.remove('hidden', 'text-red-600', 'text-ijo-tua');
            otpStatus.classList.add(isSuccess ? 'text-ijo-tua' : 'text-red-600');
        }

        otpButton?.addEventListener('click', async () => {
            const email = otpEmail?.value.trim();

            if (!email) {
                showOtpStatus('Isi email terlebih dahulu.');
                otpEmail?.focus();
                return;
            }

            otpButton.disabled = true;
            otpButton.textContent = 'Mengirim...';
            showOtpStatus('Mengirim kode OTP...', true);

            try {
                const response = await fetch('{{ route('publik.aduan.otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ email }),
                });
                const payload = await response.json();

                showOtpStatus(payload.message || 'Gagal mengirim OTP.', response.ok && payload.success);
            } catch (error) {
                showOtpStatus('Gagal mengirim OTP. Periksa koneksi atau konfigurasi email.');
            } finally {
                otpButton.disabled = false;
                otpButton.textContent = 'Kirim OTP';
            }
        });

        const aduanFotoInput = document.getElementById('aduan-foto-input');
        const aduanPlaceholder = document.getElementById('aduan-foto-placeholder');
        const aduanPreviewContainer = document.getElementById('aduan-foto-preview-container');
        const aduanPreviewImg = document.getElementById('aduan-foto-preview-img');
        const aduanPreviewName = document.getElementById('aduan-foto-preview-name');
        const aduanBtnHapus = document.getElementById('aduan-btn-hapus-foto');

        if (aduanFotoInput) {
            aduanFotoInput.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        if (aduanPreviewImg) aduanPreviewImg.src = e.target.result;
                        if (aduanPreviewName) aduanPreviewName.textContent = file.name;
                        if (aduanPreviewContainer) aduanPreviewContainer.classList.remove('hidden');
                        if (aduanPlaceholder) aduanPlaceholder.classList.add('hidden');
                        if (aduanBtnHapus) aduanBtnHapus.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    clearAduanFoto();
                }
            });
        }

        function clearAduanFoto() {
            if (aduanFotoInput) aduanFotoInput.value = '';
            if (aduanPreviewImg) aduanPreviewImg.src = '';
            if (aduanPreviewName) aduanPreviewName.textContent = '';
            if (aduanPreviewContainer) aduanPreviewContainer.classList.add('hidden');
            if (aduanPlaceholder) aduanPlaceholder.classList.remove('hidden');
            if (aduanBtnHapus) aduanBtnHapus.classList.add('hidden');
        }
    </script>

</body>
</html>