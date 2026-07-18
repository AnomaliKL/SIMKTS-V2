<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun - SIMKTS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Panggil CSS intl-tel-input secara lokal hanya di halaman register -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intl-tel-input.css">

    <style>
        .iti {
            width: 100% !important;
            display: block !important;
        }

        .iti input[type="tel"] {
            padding-left: 90px !important;
        }

        .iti__selected-flag {
            background-color: transparent !important;
            padding-left: 12px !important;
        }

        .iti__selected-dial-code {
            color: #cbd5e1 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
        }

        /* --- TAMPILAN DROPDOWN NEGARA STANDAR (DARK MODE) --- */
        .iti__dropdown-content,
        div.iti__dropdown-content,
        .iti__country-list {
            background-color: #0f172a !important;
            color: #cbd5e1 !important;
            border: none !important;
        }

        .iti__dropdown-content {
            border: 1px solid #1e293b !important;
            border-radius: 8px !important;
            padding: 0 !important;
            overflow: hidden !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;

            /* Memaksa posisi dropdown jatuh ke bawah input secara konsisten */
            top: 100% !important;
            bottom: auto !important;
            margin-top: 4px !important;
        }

        .iti__country-list {
            z-index: 9999 !important;
            width: 100% !important;
            min-width: 300px !important;
            max-height: 250px !important;
            margin: 0 !important;
        }

        .iti__country {
            padding: 8px 12px !important;
        }

        .iti__country:hover {
            background-color: #1e293b !important;
            color: #ffffff !important;
        }

        .iti__country-name,
        .iti__dial-code {
            color: #cbd5e1 !important;
        }

        /* KUNCI 2: Memastikan sisa container search box disembunyikan total */
        .iti__search-container {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-100 antialiased h-full font-sans">

    <div class="container-fluid h-full">
        <div class="flex h-full">

            <div class="hidden lg:block lg:w-7/12 relative bg-slate-900 overflow-hidden select-none">
                <div
                    class="absolute inset-0 bg-[url('/public/assets/img/BG-LOGIN.avif')] center center no-repeat bg-cover transform scale-105 hover:scale-100 transition duration-10000">
                </div>
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950/40 to-slate-950/90"></div>

                <div class="absolute bottom-12 left-12 z-10 text-white space-y-2">
                    <h1 class="text-5xl font-black tracking-tighter">Bergabunglah</h1>
                    <p class="text-lg font-light leading-snug text-slate-300">
                        Dapatkan hunian nyaman di<br>Kontrakan Tiga Saudara
                    </p>
                    <div class="flex items-center space-x-2 pt-3">
                        <span
                            class="px-3 py-1.5 bg-white/10 backdrop-blur-md text-white text-[10px] font-bold rounded-full tracking-wider uppercase border border-white/10">🛡️
                            Aman</span>
                        <span
                            class="px-3 py-1.5 bg-white/10 backdrop-blur-md text-white text-[10px] font-bold rounded-full tracking-wider uppercase border border-white/10">⚡
                            Proses Cepat</span>
                    </div>
                </div>
            </div>

            <div
                class="w-full lg:w-5/12 bg-slate-950 flex items-center justify-center p-6 sm:p-12 shadow-2xl relative z-10">
                <div class="w-full max-w-md space-y-6" x-data="{ showPassword: false }">


                    <div class="space-y-1 pt-2">
                        <div class="flex items-center space-x-2">
                            <h3 class="text-xl font-black text-white tracking-tight">Buat Akun Baru</h3>
                        </div>
                        <p class="text-slate-400 text-xs font-medium">Isi data diri Anda secara valid untuk mulai
                            mengajukan sewa kamar.</p>
                    </div>

                    @if (session('success'))
                        <div
                            class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs p-4 rounded-xl">
                            <div class="font-bold mb-2">✅ {{ session('success') }}</div>
                            <p class="text-emerald-300">Silakan <a href="{{ route('login') }}"
                                    class="font-bold underline hover:text-white transition">Login di sini</a> untuk
                                masuk.</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs p-3 rounded-xl space-y-1 animate-shake"
                            role="alert">
                            <div class="font-bold flex items-center space-x-1">
                                <span>⚠️</span>
                                <span>Gagal mendaftarkan akun:</span>
                            </div>
                            <ul class="list-disc list-inside text-[11px] text-slate-400 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (!session('success'))
                        <form action="{{ route('register.process') }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- NAMA LENGKAP -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nama
                                    Lengkap</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <img src="{{ asset('assets/icon/user.png') }}"
                                            class="w-3.5 h-3.5 object-contain opacity-60">
                                    </span>
                                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                        placeholder="Nama Lengkap Anda"
                                        class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                                </div>
                            </div>

                            <!-- EMAIL ADDRESS -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Email
                                    Address</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <img src="{{ asset('assets/icon/envelope.png') }}"
                                            class="w-3.5 h-3.5 object-contain opacity-60">
                                    </span>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                        placeholder="nama@email.com"
                                        class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                                </div>
                            </div>

                            <!-- INPUT NO WHATSAPP -->
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">No.
                                    WhatsApp</label>
                                <div
                                    class="relative rounded-xl bg-slate-900 border border-slate-800 focus-within:border-blue-500 transition">
                                    <input type="tel" id="phone" name="no_hp_input" value="{{ old('no_hp') }}"
                                        required
                                        class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pr-4 py-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                                    <input type="hidden" id="full_phone" name="no_hp">
                                </div>
                            </div>

                            <!-- PASSWORD -->
                            <div class="space-y-1.5">
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <img src="{{ asset('assets/icon/access-control.png') }}"
                                            class="w-3.5 h-3.5 object-contain opacity-60">
                                    </span>
                                    <input :type="showPassword ? 'text' : 'password'" name="password" required
                                        placeholder="Buat password baru..."
                                        class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-12 py-3 text-slate-200 focus:outline-none focus:border-blue-500 tracking-wide transition">
                                    <button type="button" @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-white transition focus:outline-none text-xs">
                                        <img :src="showPassword ? '{{ asset('assets/icon/eye.png') }}' :
                                            '{{ asset('assets/icon/eye-crossed.png') }}'"
                                            alt="Toggle Password"
                                            class="w-4 h-4 object-contain opacity-60 hover:opacity-100 transition duration-150">
                                    </button>
                                </div>
                            </div>

                            <!-- KONFIRMASI PASSWORD -->
                            <div class="space-y-1.5">
                                <label
                                    class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Konfirmasi
                                    Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <img src="{{ asset('assets/icon/access-control.png') }}"
                                            class="w-3.5 h-3.5 object-contain opacity-60">
                                    </span>
                                    <input :type="showPassword ? 'text' : 'password'" name="password_confirmation"
                                        required placeholder="Tulis ulang password..."
                                        class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-3 text-slate-200 focus:outline-none focus:border-blue-500 tracking-wide transition">
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full tracking-wide shadow-xl shadow-blue-600/10 transition duration-200 cursor-pointer uppercase flex items-center justify-center space-x-2">
                                    <span>Daftar Akun</span>
                                </button>
                                <div class="text-center mt-4 mb-6">
                                    <span class="text-xs text-slate-500 font-medium">Sudah memiliki akun? </span>
                                    <a href="{{ route('login') }}"
                                        class="text-xs text-blue-500 hover:text-blue-400 font-bold transition duration-150 underline">Login
                                        di sini</a>
                                </div>
                            </div>
                        </form>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneInputField = document.querySelector("#phone");
            const fullPhoneInput = document.querySelector("#full_phone");

            if (phoneInputField && window.intlTelInput) {
                const phoneInput = window.intlTelInput(phoneInputField, {
                    initialCountry: "id",
                    allowDropdown: true,
                    separateDialCode: true,
                    // KUNCI UTAMA: Matikan search box bawaan v18+ dan aktifkan gulir keyboard
                    countrySearch: false,
                    dropdownContainer: document.body,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
                });

                // Otomatis hapus angka 0 di depan saat mulai mengetik
                phoneInputField.addEventListener("input", function() {
                    let value = phoneInputField.value;
                    if (value.startsWith("0")) {
                        phoneInputField.value = value.substring(1);
                    }
                });

                const form = document.querySelector("form");
                if (form) {
                    form.addEventListener("submit", function(e) {
                        let value = phoneInputField.value;
                        if (value.startsWith("0")) {
                            phoneInputField.value = value.substring(1);
                        }

                        // Ambil nomor gabungan bersih tanpa karakter '+' untuk disubmit ke backend
                        const fullNumber = phoneInput.getNumber().replace('+', '');
                        fullPhoneInput.value = fullNumber;
                    });
                }
            }
        });
    </script>
</body>

</html>
