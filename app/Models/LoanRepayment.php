<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    protected $fillable = [
        'loan_id',
        'loan_schedule_id',
        'date_paid',
        'amount_paid',
        'bank_reference',
        'document_path',
        'document_name',
        'created_by',
        'confirmed_by',
        'reconciliation_status',
        'reconciliation_note',
    ];

    protected $casts = [
        'date_paid' => 'date',
        'amount_paid' => 'float',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function schedule()
    {
        return $this->belongsTo(LoanSchedule::class, 'loan_schedule_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}

