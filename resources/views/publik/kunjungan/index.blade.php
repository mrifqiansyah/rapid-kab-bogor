<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Kunjungan - RAPID</title>
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
    @include('publik.layout.navbarpublik')

    <main class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 flex items-center justify-center">
        @php
            $initial = fn ($name) => collect(explode(' ', trim((string) $name)))->filter()->take(2)->map(fn ($word) => strtoupper(substr($word, 0, 1)))->join('') ?: 'P';
            $colors = ['bg-ijo-tua text-white', 'bg-ijo-muda text-white', 'bg-oren-utama text-white', 'bg-ijo-semitua text-white'];

            $allPejabat = collect($pegawaiList ?? [])->map(function($p, $idx) use ($colors) {
                return [
                    'nama' => $p->nama_pegawai,
                    'jabatan' => $p->jabatan ?? $p->bidang ?? 'Pegawai Diskominfo',
                    'color' => $colors[$idx % count($colors)],
                ];
            })->values();

            $featuredPejabat = $allPejabat->take(3);
            $extraPejabat = $allPejabat->slice(3);
        @endphp

        <div class="w-full max-w-xl bg-white dark:bg-[#152420] border border-gray-200/80 dark:border-[#233a34] rounded-xl p-6 md:p-10 shadow-xl space-y-6 my-4 transition-colors">
            
            <!-- Success Alert -->
            @if (session('success'))
                <div class="rounded-xl bg-ijo-sangatmuda dark:bg-[#0f1c19] text-ijo-tua dark:text-emerald-400 p-4 flex items-start space-x-3 text-xs md:text-sm font-bold shadow-xs border border-transparent dark:border-[#284c43]">
                    <span class="text-base">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Validation Errors Alert -->
            @if ($errors->any())
                <div class="rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 p-4 text-xs md:text-sm space-y-1">
                    <p class="font-bold">Mohon periksa kembali isian form Anda:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Header Bar -->
            <div class="flex items-center justify-between">
                <a href="{{ route('publik.beranda') }}" class="inline-flex items-center space-x-1.5 text-xs md:text-sm font-bold text-ijo-semitua dark:text-emerald-400 hover:underline">
                    <span>Kembali</span>
                </a>
            </div>

            <!-- Title & Subtitle -->
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">Form Kunjungan</h1>
                <p class="text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 mt-1">Pemerintah Kabupaten Bogor</p>
                <hr class="border-gray-100 dark:border-[#233a34] mt-4">
            </div>

            <!-- Form -->
            <form action="{{ route('publik.form-kunjungan.simpan') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Perangkat Daerah / Dinas / Kecamatan Dituju -->
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-900 dark:text-gray-200 mb-1.5">Dinas / Kecamatan Tujuan *</label>
                    
                    <!-- Type Selector Buttons -->
                    <div class="grid grid-cols-2 gap-2 mb-2.5">
                        <button type="button" id="btn-tujuan-dinas" onclick="switchTujuanType('dinas')"
                                class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all text-center border cursor-pointer bg-ijo-tua text-white border-transparent shadow-xs">
                            Dinas / SKPD
                        </button>
                        <button type="button" id="btn-tujuan-kecamatan" onclick="switchTujuanType('kecamatan')"
                                class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all text-center border cursor-pointer bg-[#F3F2ED] dark:bg-[#0f1c19] text-gray-700 dark:text-gray-300 border-transparent hover:bg-gray-200 dark:hover:bg-[#1a2d28]">
                            Kecamatan
                        </button>
                    </div>

                    <!-- Hidden Input for Tipe -->
                    <input type="hidden" name="tujuan_tipe" id="tujuan_tipe" value="dinas">

                    <!-- Select Dinas -->
                    <div id="container-dinas" class="relative">
                        <select name="id_dinas" id="id_dinas" class="w-full bg-[#F3F2ED] dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] rounded-2xl p-4 text-xs md:text-sm text-gray-900 dark:text-white appearance-none focus:ring-2 focus:ring-ijo-tua focus:bg-white dark:focus:bg-[#152420] transition-all cursor-pointer">
                            <option value="" disabled @selected(!old('id_dinas')) class="bg-white dark:bg-[#152420] text-gray-700 dark:text-gray-300">-- Pilih Dinas / SKPD yang Dituju --</option>
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

                    <!-- Select Kecamatan -->
                    <div id="container-kecamatan" class="relative hidden">
                        <select name="id_kecamatan" id="id_kecamatan" disabled class="w-full bg-[#F3F2ED] dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] rounded-2xl p-4 text-xs md:text-sm text-gray-900 dark:text-white appearance-none focus:ring-2 focus:ring-ijo-tua focus:bg-white dark:focus:bg-[#152420] transition-all cursor-pointer">
                            <option value="" disabled @selected(!old('id_kecamatan')) class="bg-white dark:bg-[#152420] text-gray-700 dark:text-gray-300">-- Pilih Kecamatan yang Dituju --</option>
                            @foreach ($kecamatanList ?? [] as $kecItem)
                                <option value="{{ $kecItem->id_kecamatan }}" @selected(old('id_kecamatan') == $kecItem->id_kecamatan) class="bg-white dark:bg-[#152420] text-gray-900 dark:text-white py-1">
                                    {{ $kecItem->nama_kecamatan }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500 text-xs">
                            ▼
                        </div>
                    </div>
                </div>

                <!-- Pihak yang Dituju -->
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-900 dark:text-gray-200 mb-1.5">Pihak / Pejabat yang Dituju *</label>
                    <input type="text" name="nama_pegawai" value="{{ old('nama_pegawai', old('nama_pejabat')) }}" placeholder="Contoh: Kepala Bidang / Ibu Anita / Sekdis" required
                           class="w-full bg-[#F3F2ED] dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] rounded-2xl p-4 text-xs md:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-ijo-tua focus:bg-white dark:focus:bg-[#152420] transition-all">
                </div>

                <!-- Nama Lengkap Tamu -->
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-900 dark:text-gray-200 mb-1.5">Nama Lengkap Tamu *</label>
                    <input type="text" name="nama_pengunjung" value="{{ old('nama_pengunjung') }}" placeholder="Masukkan nama lengkap" required
                           class="w-full bg-[#F3F2ED] dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] rounded-2xl p-4 text-xs md:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-ijo-tua focus:bg-white dark:focus:bg-[#152420] transition-all">
                </div>

                <!-- Instansi / Asal -->
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-900 dark:text-gray-200 mb-1.5">Instansi / Asal *</label>
                    <input type="text" name="asal_instansi" value="{{ old('asal_instansi') }}" placeholder="Contoh: PT Teknologi Nusantara" required
                           class="w-full bg-[#F3F2ED] dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] rounded-2xl p-4 text-xs md:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-ijo-tua focus:bg-white dark:focus:bg-[#152420] transition-all">
                </div>

                <!-- No. HP / WhatsApp -->
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-900 dark:text-gray-200 mb-1.5">No. HP / WhatsApp *</label>
                    <input type="text" name="nomorhp_pengunjung" value="{{ old('nomorhp_pengunjung') }}" placeholder="08xx-xxxx-xxxx" required pattern="[0-9]+" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           class="w-full bg-[#F3F2ED] dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] rounded-2xl p-4 text-xs md:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-ijo-tua focus:bg-white dark:focus:bg-[#152420] transition-all">
                </div>

                <!-- Email & Kirim OTP -->
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-900 dark:text-gray-200 mb-1.5">Email *</label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="email" id="email_pengunjung" name="email_pengunjung" value="{{ old('email_pengunjung') }}" placeholder="Masukkan alamat email anda" required
                               class="w-full bg-[#F3F2ED] dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] rounded-2xl p-4 text-xs md:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-ijo-tua focus:bg-white dark:focus:bg-[#152420] transition-all">
                        <button type="button" id="btn-send-kunjungan-otp"
                                class="shrink-0 rounded-2xl bg-ijo-tua hover:bg-ijo-semitua dark:bg-[#107050] dark:hover:bg-[#0c5940] dark:border dark:border-[#10b981]/30 px-6 py-3.5 text-xs font-bold text-white transition-all shadow-xs cursor-pointer inline-flex items-center justify-center gap-1.5">
                            <span id="btn-otp-text">Kirim OTP</span>
                        </button>
                    </div>
                    <p id="kunjungan-otp-status" class="hidden text-[11px] font-bold mt-1.5"></p>
                </div>

                <!-- Input OTP -->
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-900 dark:text-gray-200 mb-1.5">Kode OTP *</label>
                    <input type="text" id="kunjungan-otp" name="otp" value="{{ old('otp') }}" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="6 digit kode OTP"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)"
                           class="w-full bg-[#F3F2ED] dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] rounded-2xl p-4 text-xs md:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-ijo-tua focus:bg-white dark:focus:bg-[#152420] transition-all tracking-wider font-semibold">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Kode OTP 6 digit dikirimkan ke email Anda dan berlaku selama 10 menit.</p>
                </div>

                <!-- Keperluan Kunjungan -->
                <div>
                    <label class="block text-xs md:text-sm font-bold text-gray-900 dark:text-gray-200 mb-1.5">Keperluan Kunjungan *</label>
                    <textarea name="keperluan" rows="3" placeholder="Contoh: Audiensi kerja sama pengembangan aplikasi layanan publik..." required
                              class="w-full bg-[#F3F2ED] dark:bg-[#0f1c19] border border-transparent dark:border-[#284c43] rounded-2xl p-4 text-xs md:text-sm text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:ring-2 focus:ring-ijo-tua focus:bg-white dark:focus:bg-[#152420] transition-all">{{ old('keperluan') }}</textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-ijo-tua hover:bg-ijo-semitua dark:bg-[#107050] dark:hover:bg-[#0c5940] dark:border dark:border-[#10b981]/30 text-white font-bold text-base py-4 rounded-full transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 mt-4 cursor-pointer">
                    Kirim
                </button>
            </form>

        </div>
    </main>

    @include('publik.layout.footer')

    <script>
        function switchTujuanType(type) {
            const btnDinas = document.getElementById('btn-tujuan-dinas');
            const btnKecamatan = document.getElementById('btn-tujuan-kecamatan');
            const containerDinas = document.getElementById('container-dinas');
            const containerKecamatan = document.getElementById('container-kecamatan');
            const selectDinas = document.getElementById('id_dinas');
            const selectKecamatan = document.getElementById('id_kecamatan');
            const tipeInput = document.getElementById('tujuan_tipe');

            if (type === 'kecamatan') {
                tipeInput.value = 'kecamatan';
                btnDinas.className = 'py-2.5 px-3 rounded-xl text-xs font-bold transition-all text-center border cursor-pointer bg-[#F3F2ED] dark:bg-[#0f1c19] text-gray-700 dark:text-gray-300 border-transparent hover:bg-gray-200 dark:hover:bg-[#1a2d28]';
                btnKecamatan.className = 'py-2.5 px-3 rounded-xl text-xs font-bold transition-all text-center border cursor-pointer bg-ijo-tua text-white border-transparent shadow-xs';
                containerDinas.classList.add('hidden');
                containerKecamatan.classList.remove('hidden');
                selectDinas.disabled = true;
                selectKecamatan.disabled = false;
            } else {
                tipeInput.value = 'dinas';
                btnDinas.className = 'py-2.5 px-3 rounded-xl text-xs font-bold transition-all text-center border cursor-pointer bg-ijo-tua text-white border-transparent shadow-xs';
                btnKecamatan.className = 'py-2.5 px-3 rounded-xl text-xs font-bold transition-all text-center border cursor-pointer bg-[#F3F2ED] dark:bg-[#0f1c19] text-gray-700 dark:text-gray-300 border-transparent hover:bg-gray-200 dark:hover:bg-[#1a2d28]';
                containerDinas.classList.remove('hidden');
                containerKecamatan.classList.add('hidden');
                selectDinas.disabled = false;
                selectKecamatan.disabled = true;
            }
        }

        // --- OTP KUNJUNGAN HANDLER ---
        const btnSendOtp = document.getElementById('btn-send-kunjungan-otp');
        const btnOtpText = document.getElementById('btn-otp-text');
        const inputEmail = document.getElementById('email_pengunjung');
        const statusOtp = document.getElementById('kunjungan-otp-status');
        let otpCooldownInterval = null;

        function showOtpStatus(message, isSuccess = false) {
            if (!statusOtp) return;
            statusOtp.textContent = message;
            statusOtp.classList.remove('hidden', 'text-red-600', 'text-emerald-600', 'dark:text-emerald-400');
            statusOtp.classList.add(isSuccess ? 'text-emerald-600' : 'text-red-600');
            if (isSuccess) statusOtp.classList.add('dark:text-emerald-400');
        }

        function startOtpCooldown(seconds = 60) {
            let count = seconds;
            if (btnSendOtp) btnSendOtp.disabled = true;
            if (btnOtpText) btnOtpText.textContent = `Tunggu (${count}s)`;

            if (otpCooldownInterval) clearInterval(otpCooldownInterval);
            otpCooldownInterval = setInterval(() => {
                count--;
                if (count <= 0) {
                    clearInterval(otpCooldownInterval);
                    if (btnSendOtp) btnSendOtp.disabled = false;
                    if (btnOtpText) btnOtpText.textContent = 'Kirim Ulang OTP';
                } else {
                    if (btnOtpText) btnOtpText.textContent = `Tunggu (${count}s)`;
                }
            }, 1000);
        }

        btnSendOtp?.addEventListener('click', async () => {
            const email = inputEmail?.value.trim();
            if (!email) {
                showOtpStatus('Silakan isi alamat email Anda terlebih dahulu.', false);
                inputEmail?.focus();
                return;
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                showOtpStatus('Format email tidak valid. Masukkan email yang benar.', false);
                inputEmail?.focus();
                return;
            }

            btnSendOtp.disabled = true;
            if (btnOtpText) btnOtpText.textContent = 'Mengirim...';
            showOtpStatus('Sedang mengirim kode OTP ke email...', true);

            try {
                const response = await fetch('{{ route('publik.form-kunjungan.otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email_pengunjung: email })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showOtpStatus(data.message || 'Kode OTP berhasil dikirim. Silakan periksa kotak masuk atau spam email Anda.', true);
                    startOtpCooldown(60);
                    document.getElementById('kunjungan-otp')?.focus();
                } else {
                    showOtpStatus(data.message || 'Gagal mengirim kode OTP.', false);
                    btnSendOtp.disabled = false;
                    if (btnOtpText) btnOtpText.textContent = 'Kirim OTP';
                }
            } catch (err) {
                showOtpStatus('Terjadi kendala saat mengirim OTP. Periksa koneksi internet Anda.', false);
                btnSendOtp.disabled = false;
                if (btnOtpText) btnOtpText.textContent = 'Kirim OTP';
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            @if(old('id_kecamatan'))
                switchTujuanType('kecamatan');
            @endif
        });
    </script>

</body>
</html>
