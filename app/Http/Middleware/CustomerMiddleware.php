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

        // If user is an admin, redirect them to admin dashboard
        if (Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admins cannot access customer account pages. Please use the admin panel.');
        }

        return $next($request);
    }
}
