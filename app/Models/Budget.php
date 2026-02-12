<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'branch_name',
        'year',
        'month',
        'expense_book',
        'budget_amount',
        'spent_amount',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'budget_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function getBalanceAttribute(): float
    {
        return $this->budget_amount - $this->spent_amount;
    }

    public function getVarianceAttribute(): float
    {
        if ($this->budget_amount == 0) {
            return 0;
        }
        return (($this->spent_amount - $this->budget_amount) / $this->budget_amount) * 100;
    }
}
