<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireFullAdmin
{
    /**
     * Gates routes that are never delegable to sub-admins (Settings, and
     * sub-admin management itself — a sub-admin must never be able to grant
     * itself or anyone else more access).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        return $next($request);
    }
}
