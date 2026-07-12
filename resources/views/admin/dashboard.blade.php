@extends('layouts.app')

@section('title', 'Dashboard Admin - SIMKTS')

@section('content')
<div class="space-y-6">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
        <div class="md:col-span-2">
            <h2 class="text-2xl font-black text-white tracking-tight">Dashboard</h2>
            <p class="text-slate-400 text-xs font-medium mt-1">
                Selamat datang kembali, <span class="text-blue-400 font-bold">{{ Auth::user()->name }}</span>!
            </p>
        </div>
        
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-4 shadow-xl relative overflow-hidden group select-none"
             x-data="{ time: '',
                 init() {
                     const update = () => {
                         const now = new Date();
                         this.time = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                     };
                     update();
                     setInterval(update, 1000);
                 }
             }">
            <div class="flex justify-between items-center relative z-10">
                <div>
                    <span class="text-[9px] uppercase tracking-wider text-blue-200/80 font-black">Hari Ini</span>
                    <h5 class="font-extrabold text-sm text-white mt-0.5">{{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</h5>
                </div>
                <div class="text-right leading-none">
                    <div class="text-2xl font-black text-white tracking-mono" x-text="time">00:00</div>
                    <span class="text-[9px] text-blue-200 font-bold tracking-widest">WIB</span>
                </div>
            </div>
            <span class="absolute -right-3 -bottom-5 text-7xl opacity-15 transform -rotate-12 group-hover:scale-110 group-hover:rotate-0 transition duration-500">📅</span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:border-slate-700 transition">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Kamar</span>
                {{-- <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center text-sm">🏢</div> --}}
            </div>
            <h3 class="text-2xl font-black text-white mt-4">{{ $totalKamar }}</h3>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:border-slate-700 transition">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tersedia</span>
                {{-- <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">✅</div> --}}
            </div>
            <h3 class="text-2xl font-black text-emerald-400 mt-4">{{ $kamarKosong }}</h3>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:border-slate-700 transition">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Terisi</span>
                {{-- <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center text-sm">🚪</div> --}}
            </div>
            <h3 class="text-2xl font-black text-rose-400 mt-4">{{ $kamarIsi }}</h3>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:border-slate-700 transition">
            <div class="flex justify-between items-start">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Penghuni Active</span>
                {{-- <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center text-sm">👥</div> --}}
            </div>
            <h3 class="text-2xl font-black text-amber-400 mt-4">{{ $totalPenghuni }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden flex flex-col shadow-sm">
            <div class="p-4 border-b border-slate-800 flex items-center space-x-2">
                <span class="text-blue-500">⏳</span>
                <h5 class="text-xs font-extrabold text-white uppercase tracking-wider">Penghuni Terbaru</h5>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-950/40 border-b border-slate-800/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="px-5 py-3">Nama</th>
                            <th class="px-5 py-3">Kamar</th>
                            <th class="px-5 py-3">Tgl Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-xs font-medium">
                        @forelse($penghuniTerbaru as $row)
                            <tr class="hover:bg-slate-800/20 text-slate-200 transition">
                                <td class="px-5 py-3.5 flex items-center space-x-3">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-[10px] uppercase shadow-inner">
                                        {{ strtoupper(substr($row->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <span class="font-bold tracking-tight text-slate-200">{{ $row->nama_lengkap }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-0.5 bg-blue-500/10 text-blue-400 border border-blue-500/10 text-[10px] font-bold rounded-full">
                                        Kamar {{ $row->nomor_kamar }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 text-[11px]">
                                    {{ \Carbon\Carbon::parse($row->tgl_masuk)->isoFormat('DD MMM YYYY') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-slate-500 italic text-xs border-0">
                                    Belum ada data transaksi penghuni kos baru terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 shadow-sm space-y-4">
            <h5 class="text-xs font-extrabold text-white uppercase tracking-wider mb-2">Aksi Cepat</h5>
            
            <div class="flex flex-col space-y-2.5">
                <a href="/admin/penghuni" class="group flex items-center space-x-4 p-3 rounded-xl border border-slate-800 hover:border-slate-700 bg-slate-950/20 hover:bg-slate-950/60 transition duration-200">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm transition group-hover:scale-105">👤</div>
                    <div class="leading-tight">
                        <div class="text-xs font-bold text-slate-200 group-hover:text-blue-400 transition">Input Penghuni</div>
                        <span class="text-[10px] text-slate-500 font-medium">Tambah data penyewa baru</span>
                    </div>
                </a>

                <a href="/admin/laporan" class="group flex items-center space-x-4 p-3 rounded-xl border border-slate-800 hover:border-slate-700 bg-slate-950/20 hover:bg-slate-950/60 transition duration-200">
                    <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center text-sm transition group-hover:scale-105">🖨️</div>
                    <div class="leading-tight">
                        <div class="text-xs font-bold text-slate-200 group-hover:text-blue-400 transition">Cetak Laporan</div>
                        <span class="text-[10px] text-slate-500 font-medium">Rekap data kamar & keuangan</span>
                    </div>
                </a>

                <a href="https://wa.me/6289608520151" target="_blank" class="group flex items-center space-x-4 p-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-600/10 transition duration-200 cursor-pointer">
                    <div class="w-9 h-9 rounded-lg bg-white/20 text-white flex items-center justify-center text-sm transition group-hover:scale-105">💬</div>
                    <div class="leading-tight">
                        <div class="text-xs font-extrabold text-white">Hubungi Pengembang</div>
                        <span class="text-[10px] text-blue-200 font-medium">Bantuan teknis aplikasi</span>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection