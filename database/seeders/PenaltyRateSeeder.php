<?php

namespace Database\Seeders;

use App\Models\PenaltyRate;
use App\Models\User;
use Illuminate\Database\Seeder;

class PenaltyRateSeeder extends Seeder
{
    public function run(): void
    {
        // Get first admin user for created_by
        $admin = User::where('is_admin', true)->first();

        // Create default penalty rate
        PenaltyRate::firstOrCreate(
            ['name' => 'Standard Late Payment'],
            [
                'rate' => 2.00, // 2% per day
                'calculation_method' => 'percentage_per_day',
                'grace_period_days' => 7, // 7 days grace period
                'max_penalty_amount' => null, // No cap
                'is_active' => true,
                'description' => 'Standard penalty rate of 2% per day after 7-day grace period for overdue installments.',
                'created_by' => $admin?->id ?? 1,
            ]
        );

        // Alternative penalty rate (percentage of installment)
        PenaltyRate::firstOrCreate(
            ['name' => 'Fixed Percentage Penalty'],
            [
                'rate' => 5.00, // 5% of installment amount
                'calculation_method' => 'percentage_of_installment',
                'grace_period_days' => 14, // 14 days grace period
                'max_penalty_amount' => 50000.00, // Max Ksh 50,000
                'is_active' => false, // Not active by default
                'description' => 'Alternative penalty: 5% of installment amount, capped at Ksh 50,000.',
                'created_by' => $admin?->id ?? 1,
            ]
        );
    }
}
