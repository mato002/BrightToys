<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // In this system, partners are also admins (investors operating the business).
        // Allow access if the user is either an admin or a partner.
        if (! $user || (!($user->is_admin ?? false) && !($user->is_partner ?? false))) {
            abort(403);
        }

        return $next($request);
    }
}

