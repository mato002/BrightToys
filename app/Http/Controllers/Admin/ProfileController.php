<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Load all relevant relationships
        $user->load([
            'adminRoles.permissions',
            'partner.ownerships',
            'partner.contributions' => function($q) {
                $q->latest()->limit(5);
            },
            'orders' => function($q) {
                $q->latest()->limit(5);
            },
        ]);

        // Get partner info if user is a partner
        $partner = $user->partner;
        $currentOwnership = null;
        if ($partner) {
            $currentOwnership = $partner->ownerships()
                ->where('effective_from', '<=', now())
                ->where(function($q) {
                    $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
                })
                ->first();
        }

        // Get recent activity logs for this user
        $recentActivity = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        // Calculate stats
        $stats = [
            'total_orders' => $user->orders()->count(),
            'total_contributions' => $partner ? $partner->contributions()->where('status', 'approved')->count() : 0,
            'total_activity' => ActivityLog::where('user_id', $user->id)->count(),
            'account_age_days' => $user->created_at->diffInDays(now()),
        ];

        return view('admin.profile.index', compact('user', 'partner', 'currentOwnership', 'recentActivity', 'stats'));
    }

    public function edit()
    {
        $user = auth()->user();

        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        return redirect()->route('admin.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function showChangePasswordForm()
    {
        return view('admin.profile.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Show active sessions for the authenticated user
     */
    public function sessions()
    {
        $user = auth()->user();
        $currentSessionId = Session::getId();
        
        // Get all sessions for this user
        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => $session->last_activity,
                    'is_current' => $session->id === $currentSessionId,
                    'device' => $this->parseUserAgent($session->user_agent),
                ];
            });

        return view('admin.profile.sessions', compact('sessions', 'currentSessionId'));
    }

    /**
     * Revoke a specific session
     */
    public function revokeSession(Request $request, $sessionId)
    {
        $user = auth()->user();
        $currentSessionId = Session::getId();

        // Prevent revoking current session
        if ($sessionId === $currentSessionId) {
            return redirect()->route('admin.profile.sessions')
                ->with('error', 'You cannot revoke your current session.');
        }

        // Verify the session belongs to the user
        $session = DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->first();

        if (!$session) {
            return redirect()->route('admin.profile.sessions')
                ->with('error', 'Session not found or does not belong to you.');
        }

        // Delete the session
        DB::table('sessions')->where('id', $sessionId)->delete();

        return redirect()->route('admin.profile.sessions')
            ->with('success', 'Session revoked successfully.');
    }

    /**
     * Revoke all other sessions (except current)
     */
    public function revokeAllOtherSessions()
    {
        $user = auth()->user();
        $currentSessionId = Session::getId();

        // Delete all sessions except current
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        return redirect()->route('admin.profile.sessions')
            ->with('success', 'All other sessions have been revoked.');
    }

    /**
     * Parse user agent string to extract device and browser info
     */
    private function parseUserAgent($userAgent)
    {
        if (!$userAgent) {
            return [
                'device' => 'Unknown Device',
                'browser' => 'Unknown Browser',
                'platform' => 'Unknown',
            ];
        }

        $device = 'Desktop';
        $browser = 'Unknown Browser';
        $platform = 'Unknown';

        // Detect device type
        if (preg_match('/mobile|android|iphone|ipad|ipod|blackberry|iemobile|opera mini/i', strtolower($userAgent))) {
            $device = 'Mobile';
            if (preg_match('/tablet|ipad|playbook|silk/i', strtolower($userAgent))) {
                $device = 'Tablet';
            }
        }

        // Detect browser
        if (preg_match('/chrome/i', $userAgent) && !preg_match('/edg/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/opera|opr/i', $userAgent)) {
            $browser = 'Opera';
        }

        // Detect platform
        if (preg_match('/windows/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $platform = 'iOS';
        }

        return [
            'device' => $device,
            'browser' => $browser,
            'platform' => $platform,
            'full' => $userAgent,
        ];
    }
}

