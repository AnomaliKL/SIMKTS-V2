<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        // --------------------------------------------------------------------------
        // LOGIKA PENANGANAN NOMOR HP DI BACKEND (FALLBACK AUTOMATIC FORMATTING)
        // --------------------------------------------------------------------------
        $rawHp = $request->input('no_hp') ?: $request->input('profile_phone_input');

        if ($rawHp) {
            // Bersihkan dari huruf/spasi
            $cleanHp = preg_replace('/\D/', '', $rawHp);

            // Jika diawali angka 0 (misal 0812...), potong angka 0-nya
            if (str_starts_with($cleanHp, '0')) {
                $cleanHp = substr($cleanHp, 1);
            }

            // Pasang kode negara 62 jika belum ada
            if (!str_starts_with($cleanHp, '62')) {
                $cleanHp = '62' . $cleanHp;
            }

            $request->merge([
                'no_hp' => $cleanHp
            ]);
        }
        // --------------------------------------------------------------------------

        $request->validate([
            'name'    => 'required|string|max:100',
            'no_hp'   => 'required|numeric|digits_between:9,15',
            'nik_ktp' => 'required|numeric',
            'foto'    => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // Maksimal 10MB
            'password'=> 'nullable|min:6',
        ], [
            'no_hp.required'     => 'Nomor WhatsApp wajib diisi.',
            'no_hp.digits_between' => 'Format nomor WhatsApp tidak valid.',
            'nik_ktp.required'   => 'NIK KTP wajib diisi.',
            'nik_ktp.numeric'    => 'NIK KTP harus berupa angka.',
        ]);

        $user = Auth::user();

        $user->name  = $request->name;
        $user->no_hp = $request->no_hp;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Update data relasi ke tabel 'penghunis' (NIK, Nama, dan No HP agar selalu sinkron)
        if ($user->penghuni) {
            $user->penghuni->update([
                'nama_lengkap' => $request->name,
                'no_hp'        => $request->no_hp,
                'nik_ktp'      => $request->nik_ktp,
            ]);
        }

        // Penanganan Upload Foto Profil
        if ($request->hasFile('foto')) {
            $nik = $user->penghuni ? $user->penghuni->nik_ktp : 'user_' . $user->id;
            $extension = $request->file('foto')->getClientOriginalExtension();
            $fileName = $nik . '.' . $extension;

            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $path = $request->file('foto')->storeAs('foto_profile', $fileName, 'public');
            $user->foto = $path; 
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}