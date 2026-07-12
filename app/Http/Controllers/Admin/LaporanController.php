<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan_ini = $request->bulan ?? now()->format('Y-m');

        $tahun = Carbon::parse($bulan_ini)->year;
        $bulan = Carbon::parse($bulan_ini)->month;

        $tagihans = Tagihan::with([
            'penghuni.user',
            'penghuni.kamar'
        ])
        ->whereYear('bulan_tagihan', $tahun)
        ->whereMonth('bulan_tagihan', $bulan)
        ->orderBy('tgl_jatuh_tempo')
        ->get();

        $summary = [

            'total_potensi' => $tagihans->sum('jumlah_tagihan'),

            'total_transaksi' => $tagihans->count(),

            'total_masuk' => $tagihans
                ->where('status','lunas')
                ->sum('jumlah_tagihan'),

            'total_tunggakan' => $tagihans
                ->where('status','!=','lunas')
                ->sum('jumlah_tagihan'),

            'jml_penunggak' => $tagihans
                ->where('status','!=','lunas')
                ->count(),

        ];

        return view('admin.laporan', compact(
            'bulan_ini',
            'summary',
            'tagihans'
        ));
    }

    public function cetak(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');

        $tahun = Carbon::parse($bulan)->year;
        $bln = Carbon::parse($bulan)->month;

        $tagihans = Tagihan::with([
            'penghuni.kamar'
        ])
        ->whereYear('bulan_tagihan',$tahun)
        ->whereMonth('bulan_tagihan',$bln)
        ->get();

        $totalMasuk = $tagihans
            ->where('status','lunas')
            ->sum('jumlah_tagihan');

        $totalTunggakan = $tagihans
            ->where('status','!=','lunas')
            ->sum('jumlah_tagihan');

        return view(
            'admin.laporan_print',
            compact(
                'bulan',
                'tagihans',
                'totalMasuk',
                'totalTunggakan'
            )
        );
    }
}