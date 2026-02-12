<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Admins (including Chairman, Treasurer, Finance Admin, Store Admin, etc.)
            // should always land on the admin console first, even if they are also partners.
            // Use admin roles (chairman, finance_admin, etc.) in addition to the raw is_admin flag,
            // because some admin users (e.g. Chairman) may have roles assigned even if is_admin
            // was not explicitly set to true.
            $hasAdminAccess = ($user->is_admin ?? false) || $user->adminRoles()->exists();

            if ($hasAdminAccess) {
                return redirect()->intended(route('admin.dashboard'));
            }

            // Partners (non-admin) → Partner Console
            if ($user->is_partner ?? false) {
                return redirect()->intended(route('partner.dashboard'));
            }

            // Regular customers → Customer account dashboard (with intended support)
            return redirect()->intended(route('account.profile'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // After logout always send user back to the login page
        return redirect()->route('login');
    }
}

