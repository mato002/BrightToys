<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    /**
     * Check if user has permission to access financial management.
     */
    protected function checkFinanceAdminPermission()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    /**
     * Display a listing of activity logs.
     */
    public function index()
    {
        $this->checkFinanceAdminPermission();
        
        $query = ActivityLog::with(['user']);

        if ($action = request('action')) {
            $query->where('action', 'like', "%{$action}%");
        }

        if ($user_id = request('user_id')) {
            $query->where('user_id', $user_id);
        }

        if ($from = request('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = request('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $logs = $query->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.activity-logs.index', compact('logs'));
    }

    /**
     * Display the specified activity log.
     */
    public function show(ActivityLog $activityLog)
    {
        $this->checkFinanceAdminPermission();
        $activityLog->load(['user', 'subject']);
        return view('admin.activity-logs.show', compact('activityLog'));
    }
}
