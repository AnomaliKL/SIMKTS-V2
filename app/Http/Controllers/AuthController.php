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
        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'no_hp'     => 'required|numeric',
            'password'  => 'required|min:6|confirmed',
        ],[
            'email.unique' => 'Email sudah digunakan.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'no_hp'    => $request->no_hp,
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