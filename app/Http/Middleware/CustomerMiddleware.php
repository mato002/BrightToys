<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     * Prevent admins from accessing customer account routes
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // If user is a partner, redirect them to the Partner Console
        if ($user->is_partner ?? false) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Partners should use the Partner Console, not the customer account area.');
        }

        // If user is an admin (non-partner), redirect to admin dashboard
        if ($user->is_admin ?? false) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admins cannot access customer account pages. Please use the admin panel.');
        }

        return $next($request);
    }
}
