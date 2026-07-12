<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SIMKTS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 text-slate-100 antialiased h-full font-sans">

    <div class="container-fluid h-full">
        <div class="flex h-full">

            <div class="hidden lg:block lg:w-7/12 relative bg-slate-900 overflow-hidden select-none">
                <div style="background-image:url('{{ asset('assets/img/BG-LOGIN.avif') }}')" class="absolute inset-0 bg-cover bg-center"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950/40 to-slate-950/90"></div>
                
                <div class="absolute bottom-12 left-12 z-10 text-white space-y-2">
                    <h1 class="text-5xl font-black tracking-tighter">SIMKTS</h1>
                    <p class="text-lg font-light leading-snug text-slate-300">
                        Sistem Informasi Manajemen<br>Kontrakan Tiga Saudara
                    </p>
                    <div class="flex items-center space-x-2 pt-3">
                        <span class="px-3 py-1.5 bg-white/10 backdrop-blur-md text-white text-[10px] font-bold rounded-full tracking-wider uppercase border border-white/10">🛡️ Aman</span>
                        <span class="px-3 py-1.5 bg-white/10 backdrop-blur-md text-white text-[10px] font-bold rounded-full tracking-wider uppercase border border-white/10">⚡ Cepat</span>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-5/12 bg-slate-950 flex items-center justify-center p-6 sm:p-12 shadow-2xl relative z-10">
                <div class="w-full max-w-md space-y-6" x-data="{ showPassword: false }">

                    <a href="{{ route('home') }}" class="text-slate-500 hover:text-blue-400 text-xs font-bold inline-flex items-center space-x-2 transition duration-150 group">
                        <img src="{{ asset('assets/icon/angle-circle-left.png') }}" class="w-3.5 h-3.5 object-contain opacity-60">
                        <span>Kembali ke Beranda</span>
                    </a>

                    <div class="space-y-1 pt-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl">🏢</span>
                            <h3 class="text-xl font-black text-white tracking-tight">Selamat Datang</h3>
                        </div>
                        <p class="text-slate-400 text-xs font-medium pb-10">Silakan login untuk melanjutkan proses booking atau mengakses akun Anda.</p>
                    </div>

                    @if ($errors->any())
                        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs p-3 rounded-xl flex items-center space-x-2 animate-shake" role="alert">
                            <span>⚠️</span>
                            <span class="font-semibold">Email atau Password yang Anda masukkan tidak cocok!</span>
                        </div>
                    @endif

                    <form action="{{ route('login.process') }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Email Address</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <img src="{{ asset('assets/icon/envelope.png') }}" alt="Email Icon" class="w-3.5 h-3.5 object-contain opacity-60">
                                </span>
                                <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan Email"
                                       class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Password</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <img src="{{ asset('assets/icon/access-control.png') }}" alt="Email Icon" class="w-3.5 h-3.5 object-contain opacity-60">
                                </span>
                                <input :type="showPassword ? 'text' : 'password'" name="password" required placeholder="Masukkan Password"
                                       class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-12 py-3 text-slate-200 focus:outline-none focus:border-blue-500 tracking-wide transition">
                                <button type="button" @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-white transition focus:outline-none text-xs">
                                         <img :src="showPassword ? '{{ asset('assets/icon/eye.png') }}' : '{{ asset('assets/icon/eye-crossed.png') }}'" 
                                             alt="Toggle Password" 
                                             class="w-4 h-4 object-contain opacity-60 hover:opacity-100 transition duration-150">
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center space-x-2 cursor-pointer select-none">
                                <input type="checkbox" name="remember" id="remember_me"
                                       class="w-4 h-4 rounded bg-slate-900 border-slate-800 text-blue-600 focus:ring-0 focus:ring-offset-0 cursor-pointer transition">
                                <span class="text-xs text-slate-400 font-medium">Ingat Saya di Perangkat Ini</span>
                            </label>
                        </div>

                        <div class="space-y-3 pt-3">
                            <button type="submit" 
                                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full tracking-wide shadow-xl shadow-blue-600/10 transition duration-200 cursor-pointer uppercase flex items-center justify-center space-x-2">
                                <span>Masuk Aplikasi</span>
                            </button>
                            
                            {{-- <div class="text-center">
                                <span class="text-xs text-slate-500 font-medium">Belum punya akun?</span>
                                <a href="{{ route('register') }}" class="text-xs text-slate-500 hover:text-blue-400 font-medium transition duration-150">
                                   Daftar sekarang di sini
                                </a>
                            </div> --}}
                            <div class="text-center mt-4">
                                    <span class="text-xs text-slate-500 font-medium">Belum punya akun? </span>
                                    <a href="{{ route('register') }}" class="text-xs text-blue-500 hover:text-blue-400 font-bold transition duration-150 underline">
                                        Daftar sekarang di sini
                                    </a>
                                </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

</body>
</html>