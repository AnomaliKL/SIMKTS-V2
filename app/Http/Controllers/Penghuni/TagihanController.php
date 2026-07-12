<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TagihanController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'id_tagihan'   => 'required|exists:tagihans,id_tagihan',
            'bukti_bayar'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $tagihan = Tagihan::findOrFail($request->id_tagihan);

        if ($request->hasFile('bukti_bayar')) {

            $path = $request->file('bukti_bayar')
                ->store('bukti_pembayaran', 'public');

            $tagihan->update([
                'bukti_bayar' => $path,
                'status' => 'menunggu_validasi',
                'tgl_bayar' => now(),
            ]);
        }

        return back()->with('success', 'Bukti pembayaran berhasil dikirim.');
    }                   
}