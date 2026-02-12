<?php

namespace App\Services;

use App\Models\SystemNotification;
use App\Models\User;
use App\Mail\GenericNotificationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Create a notification for a single user. Always stored in-app; optionally
     * also delivered via email when $channel is 'email' and the user has an address.
     */
    public static function notify(User $user, string $type, string $title, string $message, array $data = [], string $channel = 'in_app'): SystemNotification
    {
        $notification = SystemNotification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'channel' => $channel,
        ]);

        // Primary channel: email
        if ($channel === 'email' && $user->email) {
            try {
                Mail::to($user->email)->send(new GenericNotificationMail($title, $message));
            } catch (\Throwable $e) {
                Log::warning('Failed to send notification email', [
                    'user_id' => $user->id,
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $notification;
    }

    /**
     * Mark a notification as read.
     */
    public static function markAsRead(SystemNotification $notification): void
    {
        if (! $notification->read_at) {
            $notification->update(['read_at' => now()]);
        }
    }
}

