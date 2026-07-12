@extends('layouts.app')

@section('title', 'Laporan Keuangan - SIMKTS')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-black text-white tracking-tight">Laporan Keuangan Bulanan</h2>
        <p class="text-slate-400 text-xs font-medium mt-1">Pantau performa pendapatan, uang masuk, dan status tunggakan kontrakan.</p>
    </div>

    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-4 shadow-sm">
       <form action="{{ route('admin.laporan') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div class="w-full sm:max-w-xs">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Pilih Periode Bulan</label>
                <input type="month" name="bulan" value="{{ $bulan_ini }}" onchange="this.form.submit()"
                       class="w-full text-xs bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-200 focus:outline-none focus:border-blue-500 [color-scheme:dark] transition">
            </div>
            
            <div>
                <a href="{{ route('admin.laporan.cetak', ['bulan' => $bulan_ini]) }}" target="_blank"  class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black rounded-xl transition shadow-md shadow-amber-500/10 cursor-pointer">
                    🖨️ Cetak Laporan PDF
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:border-slate-800 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Potensi Pendapatan</span>
                <h3 class="text-2xl font-black text-white">Rp {{ number_format($summary['total_potensi'] ?? 0, 0, ',', '.') }}</h3>
            </div>
            <span class="text-[10px] text-slate-500 font-medium mt-3 block">
                Dari <strong class="text-slate-400">{{ $summary['total_transaksi'] ?? 0 }}</strong> Kamar Terisi
            </span>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:border-slate-800 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">Uang Masuk (Lunas)</span>
                <h3 class="text-2xl font-black text-emerald-400">Rp {{ number_format($summary['total_masuk'] ?? 0, 0, ',', '.') }}</h3>
            </div>
            <span class="text-[10px] text-emerald-500/80 font-bold mt-3 block flex items-center space-x-1">
                <span>🛡️</span> <span>Aliran Kas Aman</span>
            </span>
        </div>

        <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 flex flex-col justify-between shadow-sm hover:border-slate-800 transition">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-rose-400 uppercase tracking-wider">Tunggakan (Belum Bayar)</span>
                <h3 class="text-2xl font-black text-rose-400">Rp {{ number_format($summary['total_tunggakan'] ?? 0, 0, ',', '.') }}</h3>
            </div>
            <span class="text-[10px] text-rose-500 font-bold mt-3 block">
                ⚠️ <span class="underline">{{ $summary['jml_penunggak'] ?? 0 }}</span> Orang Belum Membayar
            </span>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800/60 rounded-2xl overflow-hidden flex flex-col shadow-sm">
        <div class="p-4 border-b border-slate-800 flex items-center space-x-2 bg-slate-900">
            <span class="text-xs">📋</span>
            <h5 class="text-xs font-extrabold text-white uppercase tracking-wider">
                Rincian Transaksi — {{ \Carbon\Carbon::parse($bulan_ini)->isoFormat('MMMM YYYY') }}
            </h5>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-950/40 border-b border-slate-800/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3">Kamar</th>
                        <th class="px-5 py-3">Nama Penghuni</th>
                        <th class="px-5 py-3">Jatuh Tempo</th>
                        <th class="px-5 py-3">Tagihan</th>
                        <th class="px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-xs font-medium">
                    @forelse($tagihans as $row)
                        <tr class="hover:bg-slate-800/20 text-slate-200 transition">
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 bg-slate-800 border border-slate-700 text-slate-300 text-[10px] font-bold rounded-md shadow-inner">
                                    Kamar {{ $row->penghuni->kamar->no_kamar }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-bold tracking-tight text-slate-200">
                                {{ $row->penghuni->nama_lengkap }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 text-[11px]">
                                {{ \Carbon\Carbon::parse($row->tgl_jatuh_tempo)->isoFormat('DD MMM YYYY') }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-200 font-extrabold">
                                Rp {{ number_format($row->jumlah_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if(strtolower($row->status) === 'lunas')
                                    <span class="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide">
                                        Lunas
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-bold rounded-full uppercase tracking-wide animate-pulse">
                                        Nunggak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-500 italic text-xs border-0">
                                📭 Belum ada data tagihan untuk periode bulan ini.<br>
                                <span class="text-[10px] font-normal not-italic text-slate-600 block mt-1">Silakan lakukan sinkronisasi massal terlebih dahulu di menu Data Tagihan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection