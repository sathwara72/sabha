<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Page-level gate for a delegable admin module. Runs after the 'admin'
     * middleware (which already confirms role is admin or sub_admin), so
     * this only needs to check the module-specific can_view grant.
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (! Auth::user()->hasModuleAbility($module, 'can_view')) {
            abort(403);
        }

        return $next($request);
    }
}
