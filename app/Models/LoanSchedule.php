<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanSchedule extends Model
{
    protected $fillable = [
        'loan_id',
        'period_number',
        'due_date',
        'principal_due',
        'interest_due',
        'total_due',
    ];

    protected $casts = [
        'due_date' => 'date',
        'principal_due' => 'float',
        'interest_due' => 'float',
        'total_due' => 'float',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class);
    }
}

