<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanSchedule;
use Carbon\Carbon;

class LoanScheduleService
{
    /**
    * Generate a simple amortization schedule (equal total payments) for a loan.
    */
    public static function generateForLoan(Loan $loan): void
    {
        // Clear any existing schedule
        $loan->schedules()->delete();

        $principal = $loan->amount;
        $ratePerPeriod = $loan->interest_rate / 12; // Assuming interest_rate is annual and frequency is monthly
        $n = max(1, (int) $loan->tenure_months);

        if ($ratePerPeriod <= 0) {
            // Simple straight-line principal only
            $principalPerPeriod = $principal / $n;
            $startDate = $loan->start_date ?: now();
            for ($i = 1; $i <= $n; $i++) {
                $dueDate = Carbon::parse($startDate)->addMonths($i - 1);
                LoanSchedule::create([
                    'loan_id' => $loan->id,
                    'period_number' => $i,
                    'due_date' => $dueDate,
                    'principal_due' => $principalPerPeriod,
                    'interest_due' => 0,
                    'total_due' => $principalPerPeriod,
                ]);
            }
            return;
        }

        // Standard amortization formula for fixed payment loans
        $r = $ratePerPeriod;
        $payment = $principal * ($r * pow(1 + $r, $n)) / (pow(1 + $r, $n) - 1);

        $remaining = $principal;
        $startDate = $loan->start_date ?: now();

        for ($i = 1; $i <= $n; $i++) {
            $interest = $remaining * $r;
            $principalComponent = $payment - $interest;

            if ($i === $n) {
                // Last payment: adjust for rounding
                $principalComponent = $remaining;
                $payment = $principalComponent + $interest;
            }

            $dueDate = Carbon::parse($startDate)->addMonths($i - 1);

            LoanSchedule::create([
                'loan_id' => $loan->id,
                'period_number' => $i,
                'due_date' => $dueDate,
                'principal_due' => $principalComponent,
                'interest_due' => $interest,
                'total_due' => $payment,
            ]);

            $remaining -= $principalComponent;
        }
    }
}

