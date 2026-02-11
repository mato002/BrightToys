<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntryContribution extends Model
{
    protected $fillable = [
        'partner_id',
        'total_amount',
        'initial_deposit',
        'paid_amount',
        'outstanding_balance',
        'payment_method',
        'currency',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'initial_deposit' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function paymentPlan()
    {
        return $this->hasOne(PaymentPlan::class);
    }

    /**
     * Calculate outstanding balance
     */
    public function calculateOutstanding(): void
    {
        $this->outstanding_balance = $this->total_amount - $this->paid_amount;
        $this->save();
    }
}
