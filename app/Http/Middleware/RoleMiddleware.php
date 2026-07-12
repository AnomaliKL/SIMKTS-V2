<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Belum login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Role tidak sesuai
        if (Auth::user()->role != $role) {

            switch (Auth::user()->role) {

                case 'admin':
                    return redirect()->route('admin.dashboard');

                case 'pengunjung':
                    return redirect()->route('pengunjung.dashboard');

                case 'penghuni':
                    return redirect()->route('penghuni.dashboard');

                default:
                    abort(403);
            }
        }

        return $next($request);
    }
}