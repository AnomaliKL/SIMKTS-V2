<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun - SIMKTS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 text-slate-100 antialiased h-full font-sans">

    <div class="container-fluid h-full">
        <div class="flex h-full">

            <div class="hidden lg:block lg:w-7/12 relative bg-slate-900 overflow-hidden select-none">
                <div class="absolute inset-0 bg-[url('/public/assets/img/BG-LOGIN.avif')] center center no-repeat bg-cover transform scale-105 hover:scale-100 transition duration-10000"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950/40 to-slate-950/90"></div>
                
                <div class="absolute bottom-12 left-12 z-10 text-white space-y-2">
                    <h1 class="text-5xl font-black tracking-tighter">Bergabunglah</h1>
                    <p class="text-lg font-light leading-snug text-slate-300">
                        Dapatkan hunian nyaman di<br>Kontrakan Tiga Saudara
                    </p>
                    <div class="flex items-center space-x-2 pt-3">
                        <span class="px-3 py-1.5 bg-white/10 backdrop-blur-md text-white text-[10px] font-bold rounded-full tracking-wider uppercase border border-white/10">🛡️ Aman</span>
                        <span class="px-3 py-1.5 bg-white/10 backdrop-blur-md text-white text-[10px] font-bold rounded-full tracking-wider uppercase border border-white/10">⚡ Proses Cepat</span>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-5/12 bg-slate-950 flex items-center justify-center p-6 sm:p-12 shadow-2xl relative z-10">
                <div class="w-full max-w-md space-y-6" x-data="{ showPassword: false }">

                    <a href="/" class="text-slate-500 hover:text-blue-400 text-xs font-bold inline-flex items-center space-x-2 transition duration-150 group">
                        <span class="transform group-hover:-translate-x-1 transition duration-150">⬅️</span>
                        <span>Kembali ke Beranda</span>
                    </a>

                    <div class="space-y-1 pt-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl">👤➕</span>
                            <h3 class="text-xl font-black text-white tracking-tight">Buat Akun Baru</h3>
                        </div>
                        <p class="text-slate-400 text-xs font-medium">Isi data diri Anda secara valid untuk mulai mengajukan sewa kamar.</p>
                    </div>

                    @if (session('status'))
                        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs p-4 rounded-xl space-y-2" role="alert">
                            <h6 class="font-black flex items-center space-x-1">
                                <span>✅</span> <span>Pendaftaran Berhasil!</span>
                            </h6>
                            <p class="text-slate-400">Akun Anda telah terdaftar. Silakan <a href="{{ route('login') }}" class="font-bold text-emerald-400 underline hover:text-emerald-300">Login di sini</a>.</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs p-3 rounded-xl space-y-1 animate-shake" role="alert">
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

                    @if (!session('status'))
                        <form action="{{ route('register.process') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 text-xs">👤</span>
                                    <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Nama Lengkap Anda"
                                           class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Email Address</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 text-xs">✉️</span>
                                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                                           class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">No. WhatsApp</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 text-xs">💬</span>
                                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" required placeholder="08123xxxx"
                                           class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 text-xs">🔒</span>
                                    <input :type="showPassword ? 'text' : 'password'" name="password" required placeholder="Buat password baru..."
                                           class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-12 py-3 text-slate-200 focus:outline-none focus:border-blue-500 tracking-wide transition">
                                    
                                    <button type="button" @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-white transition focus:outline-none text-xs">
                                        <span x-text="showPassword ? '👁️' : '🕶️'"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Konfirmasi Password</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 text-xs">🔒</span>
                                    <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" required placeholder="Tulis ulang password..."
                                           class="w-full text-xs bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-3 text-slate-200 focus:outline-none focus:border-blue-500 tracking-wide transition">
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit" 
                                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full tracking-wide shadow-xl shadow-blue-600/10 transition duration-200 cursor-pointer uppercase flex items-center justify-center space-x-2">
                                    <span>Daftar Akun</span> <span>🚀</span>
                                </button>
                                
                                <div class="text-center mt-4">
                                    <span class="text-xs text-slate-500 font-medium">Sudah memiliki akun? </span>
                                    <a href="{{ route('login') }}" class="text-xs text-blue-500 hover:text-blue-400 font-bold transition duration-150 underline">
                                        Login di sini
                                    </a>
                                </div>
                            </div>
                        </form>
                    @endif

                    <div class="text-center pt-4 border-t border-slate-900/60">
                        <small class="text-[10px] text-slate-600 font-medium">&copy; 2026 SIMKTS — Kontrakan 3 Saudara</small>
                    </div>

                </div>
            </div>

        </div>
    </div>

</body>
</html>