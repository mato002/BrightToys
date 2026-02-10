<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'lender_name',
        'amount',
        'interest_rate',
        'tenure_months',
        'repayment_frequency',
        'project_id',
        'start_date',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
        'interest_rate' => 'float',
        'tenure_months' => 'integer',
        'start_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function schedules()
    {
        return $this->hasMany(LoanSchedule::class);
    }

    public function repayments()
    {
        return $this->hasMany(LoanRepayment::class);
    }
}

