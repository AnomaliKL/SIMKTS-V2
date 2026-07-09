<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Kamar - SIMKTS</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 text-slate-100 antialiased min-h-screen selection:bg-blue-600 selection:text-white"
      x-data="{ showModal: false, selectedId: '', selectedNomor: '' }">

    @if(session('success') || session('error') || session('warning'))
        <div x-data="{ open: true }" x-show="open" x-transition class="fixed bottom-5 right-5 z-50 max-w-sm w-full bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-2xl flex justify-between items-start space-x-3">
            <div class="text-sm">
                @if(session('success')) <span class="text-emerald-500 font-bold">✅ Sukses:</span> <span class="text-slate-300">{{ session('success') }}</span> @endif
                @if(session('error')) <span class="text-rose-500 font-bold">⚠️ Gagal:</span> <span class="text-slate-300">{{ session('error') }}</span> @endif
                @if(session('warning')) <span class="text-amber-500 font-bold">🔔 Info:</span> <span class="text-slate-300">{{ session('warning') }}</span> @endif
            </div>
            <button @click="open = false" class="text-slate-500 hover:text-slate-300 font-bold">&times;</button>
        </div>
    @endif

    <nav class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800/60 sticky top-0 z-40 transition duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="#" class="flex items-center space-x-2 text-lg font-extrabold tracking-tight text-white group">
                <span class="text-blue-500 transition group-hover:scale-110">🏢</span>
                <span>SIM<span class="text-blue-500">KTS</span></span>
            </a>
            
            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-xs text-slate-400 font-medium bg-slate-950 px-3 py-1.5 rounded-lg border border-slate-800 hidden sm:inline-block">
                        Halo, <span class="text-slate-200 font-semibold">{{ Auth::user()->name }}</span>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 px-4 py-2 rounded-full hover:bg-rose-500/20 transition duration-200">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="/login" class="text-xs font-semibold text-slate-300 hover:text-white px-4 py-2 transition">
                        Login
                    </a>
                    <a href="/register" class="text-xs font-bold text-white bg-blue-600 px-5 py-2 rounded-full hover:bg-blue-700 shadow-md shadow-blue-600/20 transition duration-200">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <section class="relative bg-gradient-to-b from-slate-900 to-slate-950 border-b border-slate-900 py-20 lg:py-28 overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(59,130,246,0.08),transparent_45%)]"></div>
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 relative z-10 space-y-6">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-none">
                Hunian Nyaman & <span class="bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">Strategis</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-400 max-w-xl mx-auto leading-relaxed font-medium">
                Kost Tiga Saudara — Menyediakan fasilitas lengkap, lingkungan aman, dan harga sewa bersahabat di kelasnya.
            </p>

            <div class="flex flex-wrap justify-center gap-3 pt-4">
                <a href="#listKamar" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-full shadow-lg shadow-blue-600/20 flex items-center space-x-2 transition duration-200">
                    <span>Cari Kamar</span>
                    <span class="text-xs">⬇️</span>
                </a>
                <a href="https://wa.me/{{ $wa_admin ?? '628' }}?text=Halo%20Admin,%20saya%20tertarik%20dengan%20info%20kost" 
                   target="_blank" 
                   class="px-6 py-3 bg-slate-900 hover:bg-slate-850 text-slate-200 border border-slate-800 text-sm font-bold rounded-full flex items-center space-x-2 transition duration-200">
                    <span class="text-emerald-500">💬</span>
                    <span>Hubungi Admin</span>
                </a>
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16" id="listKamar">
        <div class="flex items-center space-x-3 mb-8">
            <span class="w-1.5 h-7 bg-blue-500 rounded-full"></span>
            <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Daftar Pilihan Kamar</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($kamars as $kamar)
                @php
                    $status = strtolower($kamar->status_kamar);
                    $badgeStyle = $status === 'kosong' 
                        ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' 
                        : 'bg-rose-500/10 text-rose-400 border-rose-500/20';
                @endphp
                <div class="bg-slate-900 border border-slate-800/80 rounded-2xl overflow-hidden flex flex-col justify-between transition duration-300 hover:border-slate-700/80 hover:-translate-y-1 hover:shadow-2xl hover:shadow-black/40 group relative">
                    
                    <span class="absolute top-4 right-4 z-10 text-[10px] font-bold tracking-wider uppercase border px-3 py-1 rounded-full backdrop-blur-md {{ $badgeStyle }}">
                        ● {{ $status }}
                    </span>

                    <div>
                        <div class="aspect-video w-full bg-slate-950 overflow-hidden relative border-b border-slate-950 shadow-inner">
                            @if($kamar->foto_kamar)
                                <img src="{{ asset('storage/' . $kamar->foto_kamar) }}" alt="Foto Kamar" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-700">
                                    <span class="text-4xl">🖼️</span>
                                </div>
                            @endif
                        </div>

                        <div class="p-5 space-y-3">
                            <h3 class="text-lg font-bold text-white tracking-tight group-hover:text-blue-400 transition">
                                Kamar {{ $kamar->no_kamar }}
                            </h3>
                            <div class="text-blue-400 font-black text-xl">
                                Rp {{ number_format($kamar->harga_sewa, 0, ',', '.') }}
                                <span class="text-xs text-slate-500 font-medium">/ bulan</span>
                            </div>
                            <p class="text-xs text-slate-400 leading-relaxed line-clamp-2 bg-slate-950/40 p-3 rounded-xl border border-slate-800/40 italic">
                                ✨ {{ $kamar->deskripsi ?? 'Fasilitas Kamar Standar Kamar Tiga Saudara.' }}
                            </p>
                        </div>
                    </div>

                    <div class="p-5 pt-0 mt-auto">
                        @if($status === 'kosong')
                            @auth
                                <button @click="showModal = true; selectedId = '{{ $kamar->id_kamar }}'; selectedNomor = '{{ $kamar->no_kamar }}'"
                                        class="w-full py-2.5 bg-slate-950 hover:bg-blue-600 text-slate-300 hover:text-white border border-slate-800 hover:border-blue-500 text-xs font-bold rounded-xl shadow-sm transition duration-200 cursor-pointer text-center">
                                    Booking Sekarang
                                </button>
                            @else
                                <a href="/login" class="block w-full py-2.5 bg-slate-950 hover:bg-blue-600 text-slate-300 hover:text-white border border-slate-800 hover:border-blue-500 text-xs font-bold rounded-xl shadow-sm transition duration-200 text-center">
                                    Booking Sekarang
                                </a>
                            @endauth
                        @else
                            <button disabled class="w-full py-2.5 bg-slate-950 text-slate-600 border border-slate-900 text-xs font-bold rounded-xl cursor-not-allowed text-center">
                                Sudah Terisi
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-slate-900 border border-dashed border-slate-800 rounded-2xl p-12 text-center text-slate-500 italic text-sm">
                    Belum ada data kamar kontrakan yang terdaftar di sistem SIMKTS.
                </div>
            @endforelse
        </div>
    </main>

    @auth
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak x-transition>
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-2xl overflow-hidden shadow-2xl transform transition-all p-6 space-y-4">
            
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <h5 class="text-base font-extrabold text-white">
                    Ajukan Sewa Kamar <span x-text="selectedNomor" class="text-blue-500"></span>
                </h5>
                <button @click="showModal = false" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <form action="{{ route('member.booking.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="id_kamar" :value={selectedId}>

                <p class="text-xs text-slate-400 leading-relaxed">
                    Halo <strong>{{ Auth::user()->name }}</strong>, silakan lengkapi parameter berkas berikut untuk mengajukan sewa kontrakan.
                </p>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Mulai Ngekos</label>
                    <input type="date" name="tgl_mulai_kos" required class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 [color-scheme:dark] transition">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nomor NIK KTP (16 Digit)</label>
                    <input type="number" name="nik_ktp" required placeholder="Masukan 16 Digit NIK KTP Anda" class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 transition">
                    <span class="text-[10px] text-slate-500 block mt-1">Data NIK sah digunakan petugas untuk memvalidasi kartu identitas.</span>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full shadow-md shadow-blue-600/10 transition">Kirim Pengajuan 🚀</button>
                </div>
            </form>
        </div>
    </div>
    @endauth

</body>
</html>