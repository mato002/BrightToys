<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LoanSeeder extends Seeder
{
    /**
     * Seed some sample loans tied to existing projects so
     * the loans dashboard, schedules and reports have data.
     */
    public function run(): void
    {
        // Try to attach loans to existing projects; fall back gracefully if none exist.
        $projects = Project::query()
            ->orderBy('id')
            ->take(3)
            ->get();

        if ($projects->isEmpty()) {
            $this->command?->warn('LoanSeeder: no projects found – skipping loan seed.');
            return;
        }

        $today = Carbon::today();

        $definitions = [
            [
                'lender_name'         => 'Cooperative Bank',
                'amount'              => 2_000_000,
                'interest_rate'       => 13.5,
                'tenure_months'       => 36,
                'repayment_frequency' => 'monthly',
                'status'              => 'active',
                'start_date'          => $today->copy()->subMonths(6),
            ],
            [
                'lender_name'         => 'Equity Bank',
                'amount'              => 1_200_000,
                'interest_rate'       => 14.0,
                'tenure_months'       => 24,
                'repayment_frequency' => 'monthly',
                'status'              => 'active',
                'start_date'          => $today->copy()->subMonths(3),
            ],
            [
                'lender_name'         => 'Sacco Bridge Facility',
                'amount'              => 750_000,
                'interest_rate'       => 12.0,
                'tenure_months'       => 18,
                'repayment_frequency' => 'monthly',
                'status'              => 'active',
                'start_date'          => $today->copy()->addMonth(),
            ],
        ];

        foreach ($definitions as $index => $data) {
            /** @var \App\Models\Project $project */
            $project = $projects[$index] ?? $projects->first();

            Loan::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'lender_name' => $data['lender_name'],
                ],
                $data + [
                    'project_id' => $project->id,
                ]
            );
        }

        $this->command?->info('LoanSeeder: sample loans seeded successfully.');
    }
}

