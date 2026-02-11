<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
}

