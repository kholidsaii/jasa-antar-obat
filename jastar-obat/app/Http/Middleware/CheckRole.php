<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized. Silakan login terlebih dahulu.'], 401);
        }

        // Cek apakah role user ada di dalam parameter middleware
        if (!$request->user()->hasRole($roles)) {
            return response()->json(['message' => 'Forbidden. Anda tidak memiliki akses ke modul ini.'], 403);
        }

        return $next($request);
    }
}