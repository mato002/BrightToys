<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Partners → Partner dashboard
            if ($user->is_partner ?? false) {
                return redirect()->route('partner.dashboard');
            }

            // Pure admins → Admin dashboard
            if ($user->is_admin ?? false) {
                return redirect()->route('admin.dashboard');
            }

            // Customers → customer account
            return redirect()->route('account.profile');
        }

        return $next($request);
    }
}

