<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\SystemNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = SystemNotification::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('partner.notifications.index', compact('notifications'));
    }
}

