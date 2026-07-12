<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $bank = Bank::first();

        return view('admin.pengaturan', compact('bank'));
    }

    public function update(Request $request)
    {
        $request->validate([

            'nama_bank'   => 'required',
            'no_rekening' => 'required',
            'atas_nama'   => 'required',

        ]);

        Bank::updateOrCreate(

            ['id_bank' => 1],

            [

                'nama_bank'   => $request->nama_bank,
                'no_rekening' => $request->no_rekening,
                'atas_nama'   => $request->atas_nama,

            ]

        );

        return back()->with(
            'success',
            'Pengaturan rekening berhasil diperbarui.'
        );
    }
}