<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403);
        }

        // role user
        $userRole = Auth::user()->role;

        // cek apakah role user ada di daftar role yang dibolehkan
        if (!in_array($userRole, $roles)) {
            abort(403, 'Tidak punya akses.');
        }

        return $next($request);
    }
}
