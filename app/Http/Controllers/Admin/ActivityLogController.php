<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Check if user has permission to access activity logs (view only for partners).
     */
    protected function checkPermission($allowPartners = false)
    {
        $user = auth()->user();
        if ($allowPartners && $user->is_partner) {
            return; // Partners can view
        }
        // Restrict to Super Admin and Finance Admin only
        if (! $user->isSuperAdmin() && ! $user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public function index()
    {
        $this->checkPermission(true); // Allow partners to view

        $query = ActivityLog::with(['user']);

        // Filter by action
        if ($action = request('action')) {
            $query->where('action', 'like', "%{$action}%");
        }

        // Filter by user
        if ($userId = request('user_id')) {
            $query->where('user_id', $userId);
        }

        // Search
        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->latest()->paginate(50)->withQueryString();

        return view('admin.activity-logs.index', compact('logs'));
    }

    public function show(ActivityLog $activityLog)
    {
        $this->checkPermission(true); // Allow partners to view
        $activityLog->load(['user']);
        return view('admin.activity-logs.show', compact('activityLog'));
    }
}
