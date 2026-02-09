<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PartnerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Refresh user from database to get latest is_partner flag
        $user = auth()->user()->fresh();
        
        // Ensure is_partner flag is checked
        if (!$user->is_partner) {
            abort(403, 'You must be a partner to access this area. Your account is not marked as a partner.');
        }
        
        // Load the partner relationship if not already loaded
        if (!$user->relationLoaded('partner')) {
            $user->load('partner');
        }
        
        // Check if partner record exists
        if (!$user->partner) {
            abort(403, 'You must be a partner to access this area. No partner record found for your account.');
        }

        return $next($request);
    }
}
