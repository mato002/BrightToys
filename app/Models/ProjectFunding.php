<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFunding extends Model
{
    protected $fillable = [
        'project_id',
        // Member equity / capital
        'member_capital_amount',
        'member_capital_date',
        'member_capital_source',
        // Loan / debt
        'has_loan',
        'lender_name',
        'loan_amount',
        'interest_rate',
        'tenure_months',
        'monthly_repayment',
        'outstanding_balance',
    ];

    protected $casts = [
        'member_capital_amount' => 'float',
        'loan_amount' => 'float',
        'interest_rate' => 'float',
        'tenure_months' => 'integer',
        'monthly_repayment' => 'float',
        'outstanding_balance' => 'float',
        'member_capital_date' => 'date',
        'has_loan' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function loanRequirements()
    {
        return $this->hasMany(ProjectLoanRequirement::class);
    }
}

