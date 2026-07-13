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
        $request->validate([
            'name' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
            'nik_ktp' => 'required|string|max:20', // 🛠️ Tambahkan validasi ini agar tidak lolos liar
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // Maksimal 10MB
            'password' => 'nullable|min:6',
        ]);

        $user = Auth::user();

        $user->name = $request->name;
        $user->no_hp = $request->no_hp;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Simpan perubahan NIK ke tabel penghuni jika berelasi
        if ($user->penghuni) {
            $user->penghuni->update([
                'nik_ktp' => $request->nik_ktp
            ]);
        }

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