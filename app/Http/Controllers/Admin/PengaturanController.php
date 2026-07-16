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
        // 1. Tambahkan validasi untuk field email dan app password baru
        $request->validate([
            'nama_bank'         => 'required|string',
            'no_rekening'       => 'required',
            'atas_nama'         => 'required|string',
            'smtp_email'        => 'required|email', // Wajib diisi & harus berformat email
            'smtp_app_password' => 'required|string', // Wajib diisi untuk password aplikasinya
        ]);

        // 2. Simpan atau perbarui datanya ke database (id_bank = 1)
        Bank::updateOrCreate(
            ['id_bank' => 1],
            [
                'nama_bank'         => $request->nama_bank,
                'no_rekening'       => $request->no_rekening,
                'atas_nama'         => $request->atas_nama,
                'smtp_email'        => $request->smtp_email,        // 🛠️ Simpan Email Baru
                'smtp_app_password' => $request->smtp_app_password, // 🛠️ Simpan App Password Baru
            ]
        );

        return back()->with(
            'success',
            'Pengaturan sistem dan kredensial Gmail berhasil diperbarui.'
        );
    }
}