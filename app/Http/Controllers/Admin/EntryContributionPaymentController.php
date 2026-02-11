<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EntryContribution;
use App\Models\PaymentPlanInstallment;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntryContributionPaymentController extends Controller
{
    /**
     * Check if user has permission to record payments.
     */
    protected function checkPermission()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin') && !$user->hasAdminRole('chairman') && !$user->hasAdminRole('treasurer')) {
            abort(403, 'You do not have permission to record entry contribution payments.');
        }
    }

    /**
     * Record a payment against an entry contribution (full payment or partial).
     */
    public function store(Request $request, EntryContribution $entryContribution)
    {
        $this->checkPermission();

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'apply_to_installment' => ['nullable', 'boolean'],
            'installment_id' => ['nullable', 'exists:payment_plan_installments,id'],
        ]);

        DB::transaction(function () use ($entryContribution, $validated) {
            $amount = $validated['amount'];
            $remainingAmount = $amount;

            // If applying to specific installment
            if (!empty($validated['apply_to_installment']) && !empty($validated['installment_id'])) {
                $installment = PaymentPlanInstallment::findOrFail($validated['installment_id']);
                
                $needed = $installment->amount - $installment->paid_amount;
                $toApply = min($remainingAmount, $needed);
                
                $installment->paid_amount += $toApply;
                $remainingAmount -= $toApply;
                
                if ($installment->paid_amount >= $installment->amount) {
                    $installment->status = 'paid';
                    $installment->paid_at = $validated['payment_date'];
                }
                
                $installment->notes = ($installment->notes ?? '') . "\nPayment recorded: " . number_format($toApply, 2) . " on " . $validated['payment_date'] . ($validated['reference'] ? " (Ref: {$validated['reference']})" : '');
                $installment->save();
            }

            // If there's a payment plan and remaining amount, auto-apply to next pending installments
            if ($entryContribution->paymentPlan && $remainingAmount > 0 && empty($validated['apply_to_installment'])) {
                $pendingInstallments = $entryContribution->paymentPlan->installments()
                    ->where('status', '!=', 'paid')
                    ->orderBy('due_date')
                    ->get();

                foreach ($pendingInstallments as $installment) {
                    if ($remainingAmount <= 0) break;

                    $needed = $installment->amount - $installment->paid_amount;
                    if ($needed > 0) {
                        $toApply = min($remainingAmount, $needed);
                        $installment->paid_amount += $toApply;
                        $remainingAmount -= $toApply;

                        if ($installment->paid_amount >= $installment->amount) {
                            $installment->status = 'paid';
                            $installment->paid_at = $validated['payment_date'];
                        }

                        $installment->notes = ($installment->notes ?? '') . "\nAuto-applied payment: " . number_format($toApply, 2) . " on " . $validated['payment_date'];
                        $installment->save();
                    }
                }
            }

            // Update entry contribution totals
            if ($entryContribution->paymentPlan) {
                $entryContribution->paid_amount = $entryContribution->paymentPlan->installments->sum('paid_amount');
            } else {
                $entryContribution->paid_amount += $amount;
            }
            $entryContribution->outstanding_balance = max(0, $entryContribution->total_amount - $entryContribution->paid_amount);
            $entryContribution->save();

            // Update installment statuses
            if ($entryContribution->paymentPlan) {
                $entryContribution->paymentPlan->installments->each(function ($installment) {
                    $installment->updateStatus();
                });
            }

            ActivityLogService::log('entry_contribution_payment_recorded', $entryContribution, [
                'amount' => $amount,
                'payment_date' => $validated['payment_date'],
                'reference' => $validated['reference'] ?? null,
                'recorded_by' => Auth::user()->name,
            ]);
        });

        return redirect()->back()
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Record payment against a specific installment.
     */
    public function recordInstallmentPayment(Request $request, PaymentPlanInstallment $installment)
    {
        $this->checkPermission();

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($installment, $validated) {
            $amount = $validated['amount'];
            $newPaidAmount = min($installment->paid_amount + $amount, $installment->amount);
            
            $installment->paid_amount = $newPaidAmount;
            
            if ($installment->paid_amount >= $installment->amount) {
                $installment->status = 'paid';
                $installment->paid_at = $validated['payment_date'];
            }
            
            $installment->notes = ($installment->notes ?? '') . "\nPayment: " . number_format($amount, 2) . " on " . $validated['payment_date'] . ($validated['reference'] ? " (Ref: {$validated['reference']})" : '');
            $installment->save();
            
            // Update entry contribution totals
            $entryContribution = $installment->paymentPlan->entryContribution;
            $entryContribution->paid_amount = $entryContribution->paymentPlan->installments->sum('paid_amount');
            $entryContribution->outstanding_balance = max(0, $entryContribution->total_amount - $entryContribution->paid_amount);
            $entryContribution->save();
            
            // Update all installment statuses
            $entryContribution->paymentPlan->installments->each(function ($inst) {
                $inst->updateStatus();
            });

            ActivityLogService::log('installment_payment_recorded', $installment, [
                'amount' => $amount,
                'payment_date' => $validated['payment_date'],
                'reference' => $validated['reference'] ?? null,
                'recorded_by' => Auth::user()->name,
            ]);
        });

        return redirect()->back()
            ->with('success', 'Installment payment recorded successfully.');
    }
}
