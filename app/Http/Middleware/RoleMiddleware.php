<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Belum login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Role tidak sesuai
        if ($user->role !== $role) {

            switch ($user->role) {

                case 'admin':
                    return redirect()->route('admin.dashboard');

                case 'pengunjung':
                    return redirect()->route('pengunjung.dashboard');

                case 'penghuni':
                    return redirect()->route('penghuni.dashboard');

                default:
                    Auth::logout();

                    return redirect()->route('login');
            }
        }

        return $next($request);
    }
}