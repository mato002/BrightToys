<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\PenaltyRate;

class PaymentPlanInstallment extends Model
{
    protected $fillable = [
        'payment_plan_id',
        'installment_number',
        'amount',
        'due_date',
        'paid_amount',
        'paid_at',
        'status',
        'days_overdue',
        'penalty_amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'date',
        'days_overdue' => 'integer',
    ];

    public function paymentPlan()
    {
        return $this->belongsTo(PaymentPlan::class);
    }

    /**
     * Check and update installment status
     */
    public function updateStatus(): void
    {
        if ($this->paid_amount >= $this->amount) {
            $this->status = 'paid';
            if (!$this->paid_at) {
                $this->paid_at = now();
            }
        } elseif ($this->due_date < now()) {
            $daysOverdue = now()->diffInDays($this->due_date);
            $this->days_overdue = $daysOverdue;
            
            if ($daysOverdue > 30) {
                $this->status = 'missed';
            } else {
                $this->status = 'overdue';
            }
            
            // Calculate penalty if overdue
            $this->calculatePenalty();
        } else {
            $this->status = 'pending';
        }
        
        $this->save();
    }

    /**
     * Calculate penalty based on active penalty rates
     */
    public function calculatePenalty(): void
    {
        if ($this->status !== 'overdue' && $this->status !== 'missed') {
            return;
        }

        $penaltyRate = PenaltyRate::where('is_active', true)->first();
        if (!$penaltyRate) {
            return;
        }

        $daysOverdue = max(0, $this->days_overdue - $penaltyRate->grace_period_days);
        if ($daysOverdue <= 0) {
            return;
        }

        switch ($penaltyRate->calculation_method) {
            case 'percentage_per_day':
                $penalty = ($this->amount * ($penaltyRate->rate / 100)) * $daysOverdue;
                break;
            case 'percentage_of_installment':
                $penalty = $this->amount * ($penaltyRate->rate / 100);
                break;
            case 'fixed_amount':
                $penalty = $penaltyRate->rate * $daysOverdue;
                break;
            default:
                $penalty = 0;
        }

        if ($penaltyRate->max_penalty_amount) {
            $penalty = min($penalty, $penaltyRate->max_penalty_amount);
        }

        $this->penalty_amount = round($penalty, 2);
    }
}
