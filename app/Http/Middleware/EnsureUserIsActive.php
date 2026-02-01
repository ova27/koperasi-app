<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // user belum login → lanjut
        if (! $user) {
            return $next($request);
        }

        // user tidak punya anggota (admin murni)
        if (! $user->anggota) {
            return $next($request);
        }

        // anggota tidak aktif → logout
        if ($user->anggota->status === 'tidak_aktif') {
            Auth::logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun Anda sudah tidak aktif'
                ]);
        }

        return $next($request);
    }
}
