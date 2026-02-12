<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'reconciliation_date',
        'opening_balance',
        'closing_balance',
        'reconciled_amount',
        'notes',
        'status',
        'reconciled_by',
        'reconciled_at',
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'reconciled_amount' => 'decimal:2',
        'reconciled_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function reconciler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }
}
