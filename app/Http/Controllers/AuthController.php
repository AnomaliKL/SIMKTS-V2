<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $request->session()->regenerate();
            switch (Auth::user()->role) {

                case 'admin':
                    return redirect()->route('admin.dashboard');

                case 'pengunjung':
                    return redirect()->route('home');

                case 'penghuni':
                    return redirect()->route('penghuni.dashboard');

                default:

                    Auth::logout();

                    return redirect()
                        ->route('login')
                        ->withErrors([
                            'email' => 'Role akun tidak dikenali.'
                        ]);
            }
        }

        return back()
            ->withErrors([
                'email' => 'Email atau password salah.'
            ])
            ->onlyInput('email');
    }

    /**
     * Halaman Register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses Register
     */
    public function register(Request $request)
    {
        // --------------------------------------------------------------------------
        // LOGIKA PENANGANAN NOMOR HP DI BACKEND (FALLBACK AUTOMATIC FORMATTING)
        // --------------------------------------------------------------------------
        // Ambil nilai dari 'no_hp' (hidden JS) atau 'no_hp_input' (input visual)
        $rawHp = $request->input('no_hp') ?: $request->input('no_hp_input');

        if ($rawHp) {
            // 1. Bersihkan semua karakter selain angka
            $cleanHp = preg_replace('/\D/', '', $rawHp);

            // 2. Jika diawali angka 0 (misal: 0812...), potong angka 0 di depan
            if (str_starts_with($cleanHp, '0')) {
                $cleanHp = substr($cleanHp, 1);
            }

            // 3. Jika belum memiliki kode negara 62 di depan, tambahkan 62
            if (!str_starts_with($cleanHp, '62')) {
                $cleanHp = '62' . $cleanHp;
            }

            // Gabungkan/Replace nilai 'no_hp' ke dalam $request agar lolos validasi
            $request->merge([
                'no_hp' => $cleanHp
            ]);
        }
        // --------------------------------------------------------------------------

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'no_hp'    => 'required|numeric|digits_between:9,15|unique:users,no_hp',
            'password' => 'required|min:6|confirmed',
        ], [
            'no_hp.required'     => 'Nomor WhatsApp wajib diisi.',
            'no_hp.unique'       => 'Nomor WhatsApp sudah terdaftar.',
            'no_hp.digits_between' => 'Nomor WhatsApp tidak valid.',
            'email.unique'       => 'Email sudah digunakan.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'no_hp'    => $request->no_hp, // Tersimpan rapi: 628xxx
            'password' => Hash::make($request->password),
            'role'     => 'pengunjung',
        ]);

        return redirect()
            ->route('register')
            ->with('success', 'Akun Berhasil Dibuat!');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}