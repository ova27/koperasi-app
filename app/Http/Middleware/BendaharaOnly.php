<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BendaharaOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        // INI KUNCI UTAMANYA ⬇️
        if (! method_exists($user, 'hasRole') || ! $user->hasRole('bendahara')) {
            abort(403, 'Akses hanya untuk bendahara');
        }

        return $next($request);
    }
}
