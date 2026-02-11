<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlanInstallment;
use App\Models\Partner;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentReminderController extends Controller
{
    /**
     * Get overdue and upcoming payment reminders.
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin') && !$user->hasAdminRole('chairman') && !$user->hasAdminRole('treasurer')) {
            abort(403, 'You do not have permission to view payment reminders.');
        }

        // Get overdue installments
        $overdueInstallments = PaymentPlanInstallment::with(['paymentPlan.entryContribution.partner'])
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->orderBy('due_date')
            ->get()
            ->map(function ($installment) {
                $installment->days_overdue = now()->diffInDays($installment->due_date);
                $installment->updateStatus(); // This will calculate penalties
                return $installment;
            });

        // Get upcoming installments (next 30 days)
        $upcomingInstallments = PaymentPlanInstallment::with(['paymentPlan.entryContribution.partner'])
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(30))
            ->orderBy('due_date')
            ->get();

        // Get partners with entry contributions
        $partnersWithContributions = Partner::whereHas('entryContribution')
            ->with(['entryContribution.paymentPlan.installments'])
            ->get()
            ->map(function ($partner) {
                $entryContribution = $partner->entryContribution;
                $overdue = $entryContribution->paymentPlan?->installments
                    ->where('status', '!=', 'paid')
                    ->where('due_date', '<', now())
                    ->count() ?? 0;
                $upcoming = $entryContribution->paymentPlan?->installments
                    ->where('status', 'pending')
                    ->where('due_date', '>=', now())
                    ->where('due_date', '<=', now()->addDays(30))
                    ->count() ?? 0;
                
                return [
                    'partner' => $partner,
                    'entry_contribution' => $entryContribution,
                    'overdue_count' => $overdue,
                    'upcoming_count' => $upcoming,
                    'outstanding_balance' => $entryContribution->outstanding_balance,
                ];
            })
            ->filter(function ($item) {
                return $item['outstanding_balance'] > 0;
            });

        return view('admin.payment-reminders.index', compact(
            'overdueInstallments',
            'upcomingInstallments',
            'partnersWithContributions'
        ));
    }
}
