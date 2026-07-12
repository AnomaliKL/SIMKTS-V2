<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|min:6',
        ]);

        $user = Auth::user();

        $user->name = $request->name;
        $user->no_hp = $request->no_hp;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {

            $path = $request
                ->file('foto')
                ->store('foto_user','public');

            $user->foto_user = $path;
        }

        $user->save();

        return back()->with(
            'success',
            'Profil berhasil diperbarui.'
        );
    }
}