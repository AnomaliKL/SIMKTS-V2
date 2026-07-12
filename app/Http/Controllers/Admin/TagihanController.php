<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Penghuni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    /**
     * Menampilkan Data Tagihan
     */
    public function index(Request $request)
    {
        $query = Tagihan::join(
                    'penghunis',
                    'tagihans.id_penghuni',
                    '=',
                    'penghunis.id_penghuni'
                )
                ->join(
                    'kamars',
                    'penghunis.id_kamar',
                    '=',
                    'kamars.id_kamar'
                )
                ->select(
                    'tagihans.*',
                    'penghunis.nama_lengkap',
                    'kamars.no_kamar as nomor_kamar'
                );

        // Filter Status
        if ($request->filled('status')) {

            $query->where('tagihans.status', $request->status);

        }

        // Filter Bulan
        if ($request->filled('bulan')) {

            $query->whereMonth(
                'bulan_tagihan',
                date('m', strtotime($request->bulan))
            );

            $query->whereYear(
                'bulan_tagihan',
                date('Y', strtotime($request->bulan))
            );

        }

        $tagihans = $query
                        ->orderBy('bulan_tagihan', 'desc')
                        ->orderBy('nama_lengkap')
                        ->get();

        return view(
            'admin.data_tagihan',
            compact('tagihans')
        );
    }

    public function validatePayment($id)
    {
        $tagihan = Tagihan::findOrFail($id);

        $tagihan->update([
            'status' => 'lunas'
        ]);

        return back()->with(
            'success',
            'Pembayaran berhasil divalidasi.'
        );
    }

    public function rejectPayment($id)
    {
        $tagihan = Tagihan::findOrFail($id);

        $tagihan->update([
            'status' => 'belum_bayar',
            'bukti_bayar' => null,
            'tgl_bayar' => null,
        ]);

        return back()->with(
            'success',
            'Bukti pembayaran ditolak.'
        );
    }

}