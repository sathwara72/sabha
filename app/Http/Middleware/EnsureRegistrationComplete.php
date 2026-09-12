<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationComplete
{
    /**
     * Guards member-only actions (profile, reviews, event reservations) behind
     * full registration completion. Guests pass through untouched — the
     * underlying route/controller already handles its own auth requirement.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->isRegistrationComplete()) {
            if ($request->expectsJson()) {
                abort(403, 'Please complete your registration before continuing.');
            }

            return redirect('/register');
        }

        return $next($request);
    }
}
