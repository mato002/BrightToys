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

            // Partners are admins with extra responsibilities,
            // but their primary home is the Partner Console.
            if ($user->is_partner ?? false) {
                return redirect()->intended(route('partner.dashboard'));
            }

            // Pure admins (non-partner) → Admin dashboard
            if ($user->is_admin ?? false) {
                return redirect()->intended(route('admin.dashboard'));
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

