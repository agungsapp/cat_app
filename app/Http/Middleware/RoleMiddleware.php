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

        $userRole = Auth::user()->role; // ini enum

        // Ambil value dari enum (string)
        $userRoleValue = $userRole->value; // atau $userRole->value()

        // Cek apakah value enum ada di array $roles
        if (!in_array($userRoleValue, $roles, true)) { // strict = true biar tipe data juga dicek
            abort(403, "Akses ditolak. Role kamu: {$userRoleValue}");
        }

        return $next($request);
    }
}
