<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentPlanInstallment;
use App\Models\Partner;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentReminderController extends Controller
{
    /**
     * Get overdue and upcoming payment reminders.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin') && !$user->hasAdminRole('chairman') && !$user->hasAdminRole('treasurer')) {
            abort(403, 'You do not have permission to view payment reminders.');
        }

        $query = PaymentPlanInstallment::with(['paymentPlan.entryContribution.partner.user']);

        // Filter by status
        $statusFilter = $request->get('status', 'all');
        if ($statusFilter === 'overdue') {
            $query->where('status', '!=', 'paid')
                ->where('due_date', '<', now());
        } elseif ($statusFilter === 'upcoming') {
            $query->where('status', 'pending')
                ->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(30));
        } elseif ($statusFilter === 'paid') {
            $query->where('status', 'paid');
        }

        // Filter by partner search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('paymentPlan.entryContribution.partner', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Quick date filter presets
        $datePreset = $request->get('date_preset');
        if ($datePreset) {
            switch ($datePreset) {
                case 'today':
                    $query->whereDate('due_date', today());
                    break;
                case 'this_week':
                    $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('due_date', now()->month)
                          ->whereYear('due_date', now()->year);
                    break;
                case 'next_30_days':
                    $query->whereBetween('due_date', [now(), now()->addDays(30)]);
                    break;
                case 'last_30_days':
                    $query->whereBetween('due_date', [now()->subDays(30), now()]);
                    break;
            }
        } else {
            // Filter by date range
            if ($request->has('date_from') && $request->date_from) {
                $query->where('due_date', '>=', Carbon::parse($request->date_from));
            }
            if ($request->has('date_to') && $request->date_to) {
                $query->where('due_date', '<=', Carbon::parse($request->date_to));
            }
        }

        // Get overdue installments (for display)
        $overdueInstallments = PaymentPlanInstallment::with(['paymentPlan.entryContribution.partner.user'])
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
        $upcomingInstallments = PaymentPlanInstallment::with(['paymentPlan.entryContribution.partner.user'])
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

        // Enhanced statistics
        $totalOverdueAmount = $overdueInstallments->sum('amount');
        $totalPenalties = $overdueInstallments->sum('penalty_amount');
        $totalUpcomingAmount = $upcomingInstallments->sum('amount');
        $averageDaysOverdue = $overdueInstallments->count() > 0 
            ? round($overdueInstallments->avg('days_overdue'), 1) 
            : 0;
        $totalOutstanding = $totalOverdueAmount + $totalPenalties;

        $stats = [
            'overdue_count' => $overdueInstallments->count(),
            'upcoming_count' => $upcomingInstallments->count(),
            'partners_with_balance' => $partnersWithContributions->count(),
            'total_overdue_amount' => $totalOverdueAmount,
            'total_penalties' => $totalPenalties,
            'total_upcoming_amount' => $totalUpcomingAmount,
            'average_days_overdue' => $averageDaysOverdue,
            'total_outstanding' => $totalOutstanding,
        ];

        return view('admin.payment-reminders.index', compact(
            'overdueInstallments',
            'upcomingInstallments',
            'partnersWithContributions',
            'statusFilter',
            'stats',
            'datePreset'
        ));
    }

    /**
     * Send reminder email for a single installment.
     */
    public function sendReminder(Request $request, PaymentPlanInstallment $installment)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin') && !$user->hasAdminRole('chairman') && !$user->hasAdminRole('treasurer')) {
            abort(403, 'You do not have permission to send payment reminders.');
        }

        $installment->load(['paymentPlan.entryContribution.partner.user']);
        $partner = $installment->paymentPlan->entryContribution->partner;
        
        if (!$partner->user || !$partner->user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Partner does not have a valid email address.'
            ], 400);
        }

        // Reminder frequency control: Don't send if sent within last 7 days
        if ($installment->last_reminder_sent_at && $installment->last_reminder_sent_at->isAfter(now()->subDays(7))) {
            $daysSinceLastReminder = now()->diffInDays($installment->last_reminder_sent_at);
            return response()->json([
                'success' => false,
                'message' => "Reminder was sent {$daysSinceLastReminder} day(s) ago. Please wait at least 7 days between reminders."
            ], 400);
        }

        $daysOverdue = $installment->due_date < now() ? now()->diffInDays($installment->due_date) : 0;
        $totalDue = $installment->amount + ($installment->penalty_amount ?? 0);

        // Generate payment link
        $paymentLink = route('partner.dashboard') . '#contributions';

        $title = $daysOverdue > 0 
            ? "Payment Reminder: Overdue Installment #{$installment->installment_number}"
            : "Payment Reminder: Upcoming Installment #{$installment->installment_number}";

        $message = "Dear {$partner->name},\n\n";
        $message .= "This is a reminder regarding your entry contribution payment plan.\n\n";
        $message .= "Installment Details:\n";
        $message .= "- Installment #: {$installment->installment_number}\n";
        $message .= "- Due Date: " . $installment->due_date->format('F d, Y') . "\n";
        $message .= "- Amount Due: KES " . number_format($installment->amount, 2) . "\n";
        
        if ($daysOverdue > 0) {
            $message .= "- Days Overdue: {$daysOverdue}\n";
            if ($installment->penalty_amount > 0) {
                $message .= "- Penalty: KES " . number_format($installment->penalty_amount, 2) . "\n";
            }
            $message .= "- Total Due: KES " . number_format($totalDue, 2) . "\n";
        } else {
            $daysUntilDue = now()->diffInDays($installment->due_date);
            $message .= "- Days Until Due: {$daysUntilDue}\n";
        }
        
        $message .= "\nPlease make payment as soon as possible to avoid additional penalties.\n\n";
        $message .= "You can make payment through your partner dashboard:\n";
        $message .= $paymentLink . "\n\n";
        $message .= "Thank you for your attention to this matter.\n\n";
        $message .= "Otto Investments Finance Team";

        try {
            NotificationService::notify(
                $partner->user,
                'payment_reminder',
                $title,
                $message,
                [
                    'installment_id' => $installment->id,
                    'due_date' => $installment->due_date->toDateString(),
                    'amount' => $installment->amount,
                    'penalty_amount' => $installment->penalty_amount ?? 0,
                    'days_overdue' => $daysOverdue,
                    'payment_link' => $paymentLink,
                ],
                'email'
            );

            // Track reminder sent properly
            $installment->update([
                'reminder_sent_at' => now(),
                'last_reminder_sent_at' => now(),
                'reminder_count' => ($installment->reminder_count ?? 0) + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reminder email sent successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send bulk reminders for multiple installments.
     */
    public function sendBulkReminders(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin') && !$user->hasAdminRole('chairman') && !$user->hasAdminRole('treasurer')) {
            abort(403, 'You do not have permission to send payment reminders.');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:payment_plan_installments,id',
        ]);

        $installments = PaymentPlanInstallment::with(['paymentPlan.entryContribution.partner.user'])
            ->whereIn('id', $request->ids)
            ->get();

        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($installments as $installment) {
            $partner = $installment->paymentPlan->entryContribution->partner;
            
            if (!$partner->user || !$partner->user->email) {
                $failed++;
                $errors[] = "Partner {$partner->name} does not have a valid email address.";
                continue;
            }

            // Reminder frequency control: Skip if sent within last 7 days
            if ($installment->last_reminder_sent_at && $installment->last_reminder_sent_at->isAfter(now()->subDays(7))) {
                $failed++;
                $daysSinceLastReminder = now()->diffInDays($installment->last_reminder_sent_at);
                $errors[] = "Partner {$partner->name}: Reminder sent {$daysSinceLastReminder} day(s) ago. Skipping to respect 7-day cooldown.";
                continue;
            }

            $daysOverdue = $installment->due_date < now() ? now()->diffInDays($installment->due_date) : 0;
            $totalDue = $installment->amount + ($installment->penalty_amount ?? 0);
            $paymentLink = route('partner.dashboard') . '#contributions';

            $title = $daysOverdue > 0 
                ? "Payment Reminder: Overdue Installment #{$installment->installment_number}"
                : "Payment Reminder: Upcoming Installment #{$installment->installment_number}";

            $message = "Dear {$partner->name},\n\n";
            $message .= "This is a reminder regarding your entry contribution payment plan.\n\n";
            $message .= "Installment Details:\n";
            $message .= "- Installment #: {$installment->installment_number}\n";
            $message .= "- Due Date: " . $installment->due_date->format('F d, Y') . "\n";
            $message .= "- Amount Due: KES " . number_format($installment->amount, 2) . "\n";
            
            if ($daysOverdue > 0) {
                $message .= "- Days Overdue: {$daysOverdue}\n";
                if ($installment->penalty_amount > 0) {
                    $message .= "- Penalty: KES " . number_format($installment->penalty_amount, 2) . "\n";
                }
                $message .= "- Total Due: KES " . number_format($totalDue, 2) . "\n";
            } else {
                $daysUntilDue = now()->diffInDays($installment->due_date);
                $message .= "- Days Until Due: {$daysUntilDue}\n";
            }
            
            $message .= "\nPlease make payment as soon as possible to avoid additional penalties.\n\n";
            $message .= "You can make payment through your partner dashboard:\n";
            $message .= $paymentLink . "\n\n";
            $message .= "Thank you for your attention to this matter.\n\n";
            $message .= "Otto Investments Finance Team";

            try {
                NotificationService::notify(
                    $partner->user,
                    'payment_reminder',
                    $title,
                    $message,
                    [
                        'installment_id' => $installment->id,
                        'due_date' => $installment->due_date->toDateString(),
                        'amount' => $installment->amount,
                        'penalty_amount' => $installment->penalty_amount ?? 0,
                        'days_overdue' => $daysOverdue,
                        'payment_link' => $paymentLink,
                    ],
                    'email'
                );

                // Track reminder sent properly
                $installment->update([
                    'reminder_sent_at' => now(),
                    'last_reminder_sent_at' => now(),
                    'reminder_count' => ($installment->reminder_count ?? 0) + 1,
                ]);

                $sent++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Failed to send reminder to {$partner->name}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Reminders sent: {$sent} successful, {$failed} failed.",
            'sent' => $sent,
            'failed' => $failed,
            'errors' => $errors,
        ]);
    }

    /**
     * Export payment reminders to CSV.
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin') && !$user->hasAdminRole('chairman') && !$user->hasAdminRole('treasurer')) {
            abort(403, 'You do not have permission to export payment reminders.');
        }

        $query = PaymentPlanInstallment::with(['paymentPlan.entryContribution.partner']);

        // Apply same filters as index
        $statusFilter = $request->get('status', 'all');
        if ($statusFilter === 'overdue') {
            $query->where('status', '!=', 'paid')
                ->where('due_date', '<', now());
        } elseif ($statusFilter === 'upcoming') {
            $query->where('status', 'pending')
                ->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(30));
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('paymentPlan.entryContribution.partner', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('due_date', '>=', Carbon::parse($request->date_from));
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('due_date', '<=', Carbon::parse($request->date_to));
        }

        $installments = $query->orderBy('due_date')->get();

        $filename = 'payment_reminders_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($installments) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Partner Name',
                'Partner Email',
                'Installment #',
                'Due Date',
                'Amount',
                'Paid Amount',
                'Outstanding',
                'Penalty',
                'Status',
                'Days Overdue',
            ]);

            // Data rows
            foreach ($installments as $installment) {
                $partner = $installment->paymentPlan->entryContribution->partner;
                $daysOverdue = $installment->due_date < now() ? now()->diffInDays($installment->due_date) : 0;
                $outstanding = $installment->amount - $installment->paid_amount;

                fputcsv($file, [
                    $partner->name,
                    $partner->email,
                    $installment->installment_number,
                    $installment->due_date->format('Y-m-d'),
                    number_format($installment->amount, 2),
                    number_format($installment->paid_amount, 2),
                    number_format($outstanding, 2),
                    number_format($installment->penalty_amount ?? 0, 2),
                    $installment->status,
                    $daysOverdue > 0 ? $daysOverdue : 0,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
