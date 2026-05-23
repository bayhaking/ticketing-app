<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
{
    // Jika user belum login atau role-nya tidak ada dalam daftar yang diizinkan
    if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
        abort(403, 'Area ini terlarang buat staff!');
    }

    return $next($request);
}
}
