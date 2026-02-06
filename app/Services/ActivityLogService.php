<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log an activity.
     */
    public static function log(string $action, $subject = null, array $details = []): ActivityLog
    {
        $request = request();

        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => $details,
        ]);
    }

    /**
     * Log partner-related activity.
     */
    public static function logPartner(string $action, $partner, array $details = []): ActivityLog
    {
        return self::log("partner_{$action}", $partner, $details);
    }

    /**
     * Log financial record activity.
     */
    public static function logFinancial(string $action, $financialRecord, array $details = []): ActivityLog
    {
        return self::log("financial_{$action}", $financialRecord, $details);
    }

    /**
     * Log admin activity.
     */
    public static function logAdmin(string $action, $admin = null, array $details = []): ActivityLog
    {
        return self::log("admin_{$action}", $admin, $details);
    }

    /**
     * Log document activity.
     */
    public static function logDocument(string $action, $document, array $details = []): ActivityLog
    {
        return self::log("document_{$action}", $document, $details);
    }
}
