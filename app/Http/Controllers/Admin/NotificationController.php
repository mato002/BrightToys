<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected function ensureCanView(): void
    {
        $user = auth()->user();

        if (! $user->isSuperAdmin()
            && ! $user->hasAdminRole('finance_admin')
            && ! $user->hasAdminRole('chairman')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureCanView();

        $query = SystemNotification::with('user');

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($channel = $request->get('channel')) {
            $query->where('channel', $channel);
        }

        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        // If export is requested, stream a CSV of the current filtered result set.
        if ($request->get('export') === 'csv') {
            $filename = 'notifications_' . now()->format('Y-m-d_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($query) {
                $handle = fopen('php://output', 'w');

                // UTF‑8 BOM for Excel compatibility
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

                fputcsv($handle, ['Timestamp', 'User', 'Email', 'Type', 'Channel', 'Title', 'Message']);

                $query->latest()->chunk(500, function ($chunk) use ($handle) {
                    foreach ($chunk as $notification) {
                        fputcsv($handle, [
                            optional($notification->created_at)->format('Y-m-d H:i:s'),
                            optional($notification->user)->name ?? 'System',
                            optional($notification->user)->email ?? '',
                            $notification->type,
                            $notification->channel,
                            $notification->title,
                            $notification->message,
                        ]);
                    }
                });

                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        $notifications = $query->latest()->paginate(50)->withQueryString();
        $adminUsers = User::where('is_admin', true)->orderBy('name')->get();

        return view('admin.notifications.index', compact('notifications', 'adminUsers'));
    }
}

