<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Optionally filter to financial actions only in future.
        if ($action = $request->get('action')) {
            $query->where('action', 'like', "%{$action}%");
        }

        $logs = $query->latest()->paginate(50)->withQueryString();

        return view('partner.activity.index', compact('logs'));
    }
}

