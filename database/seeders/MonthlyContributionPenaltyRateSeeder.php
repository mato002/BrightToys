<?php

namespace Database\Seeders;

use App\Models\MonthlyContributionPenaltyRate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MonthlyContributionPenaltyRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user or create a default one
        $admin = User::whereHas('adminRoles')->first() ?? User::first();

        if (!$admin) {
            $this->command->warn('No admin user found. Please create an admin user first.');
            return;
        }

        // Check if a rate already exists
        if (MonthlyContributionPenaltyRate::count() > 0) {
            $this->command->info('Monthly contribution penalty rates already exist. Skipping seeder.');
            return;
        }

        // Create default rate (10%) effective from the start of current month
        MonthlyContributionPenaltyRate::create([
            'name' => 'Standard Monthly Contribution Penalty',
            'rate' => 0.10, // 10%
            'effective_from' => Carbon::now()->startOfMonth(),
            'effective_to' => null,
            'is_active' => true,
            'description' => 'Default penalty rate for monthly contribution arrears. 10% of outstanding amount per month.',
            'created_by' => $admin->id,
        ]);

        $this->command->info('Default monthly contribution penalty rate created successfully.');
    }
}
