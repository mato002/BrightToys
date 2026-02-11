<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPlan extends Model
{
    protected $fillable = [
        'entry_contribution_id',
        'total_installments',
        'start_date',
        'frequency',
        'terms',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function entryContribution()
    {
        return $this->belongsTo(EntryContribution::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function installments()
    {
        return $this->hasMany(PaymentPlanInstallment::class)->orderBy('installment_number');
    }

    /**
     * Get total amount from installments
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->installments->sum('amount');
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->installments->sum('paid_amount');
    }

    /**
     * Get overdue installments
     */
    public function getOverdueInstallmentsAttribute()
    {
        return $this->installments()
            ->where('status', 'overdue')
            ->orWhere('status', 'missed')
            ->get();
    }
}
