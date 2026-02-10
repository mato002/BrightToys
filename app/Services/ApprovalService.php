<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\ApprovalDecision;
use App\Models\ApprovalRule;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ApprovalService
{
    /**
     * Ensure there is an approval record for a given subject+action.
     */
    public static function ensureApproval(string $action, Model $subject, User $initiator): Approval
    {
        $approval = Approval::firstOrCreate(
            [
                'action' => $action,
                'subject_type' => get_class($subject),
                'subject_id' => $subject->getKey(),
            ],
            [
                'created_by' => $initiator->getKey(),
                'status' => 'pending',
            ]
        );

        return $approval;
    }

    /**
     * Check if a user is allowed to approve according to the rule.
     */
    public static function canApprove(User $user, Approval $approval, ?ApprovalRule $rule = null): bool
    {
        if ($approval->status !== 'pending') {
            return false;
        }

        $rule ??= ApprovalRule::where('action', $approval->action)
            ->where('enabled', true)
            ->first();

        if (! $rule) {
            // If no rule defined, fallback to super admin only
            return $user->isSuperAdmin();
        }

        // Segregation of duties: prevent initiator from approving unless explicitly allowed
        if (! $rule->allow_initiator_approve && $user->getKey() === $approval->created_by) {
            return false;
        }

        // Required roles check (if configured)
        $requiredRoles = $rule->required_roles ?? [];
        if (! empty($requiredRoles)) {
            $hasRequiredRole = false;
            foreach ($requiredRoles as $role) {
                if ($user->hasAdminRole($role)) {
                    $hasRequiredRole = true;
                    break;
                }
            }

            if (! $hasRequiredRole && ! $user->isSuperAdmin()) {
                return false;
            }
        }

        // One decision per user
        if ($approval->decisions()->where('user_id', $user->getKey())->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Record an approval decision and update overall approval status if thresholds are met.
     */
    public static function approve(User $user, Approval $approval, ?string $comment = null): Approval
    {
        $rule = ApprovalRule::where('action', $approval->action)
            ->where('enabled', true)
            ->first();

        if (! self::canApprove($user, $approval, $rule)) {
            abort(403, 'You are not allowed to approve this action.');
        }

        // Record decision
        ApprovalDecision::create([
            'approval_id' => $approval->getKey(),
            'user_id' => $user->getKey(),
            'role_used' => optional($user->adminRoles->first())->name,
            'decision' => 'approve',
            'comment' => $comment,
        ]);

        // Recalculate aggregated approval status
        $decisions = $approval->decisions()->where('decision', 'approve')->get();
        $uniqueApprovers = $decisions->pluck('user_id')->unique()->count();

        $minApprovals = $rule->min_approvals ?? 1;

        if ($uniqueApprovers >= $minApprovals) {
            $approval->status = 'approved';
            $approval->save();
        }

        return $approval;
    }
}

