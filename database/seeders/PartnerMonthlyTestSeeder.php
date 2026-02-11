<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\PartnerContribution;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PartnerMonthlyTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'partner1@brighttoys.com')->first();
        if (! $user || ! $user->partner) {
            $this->command?->warn('Partner user partner1@brighttoys.com not found or has no partner profile.');
            return;
        }

        $partner = $user->partner;

        $base = Carbon::now()->startOfMonth();

        // Helper to create one month's contributions
        $makeMonth = function (Partner $partner, Carbon $monthStart, float $welfare, float $investment): void {
            if ($welfare > 0) {
                PartnerContribution::create([
                    'partner_id'     => $partner->id,
                    'type'           => 'contribution',
                    'fund_type'      => 'welfare',
                    'amount'         => $welfare,
                    'currency'       => 'KES',
                    'contributed_at' => $monthStart->copy()->addDays(25),
                    'status'         => 'approved',
                    'is_archived'    => false,
                    'created_by'     => 1,
                ]);
            }

            if ($investment > 0) {
                PartnerContribution::create([
                    'partner_id'     => $partner->id,
                    'type'           => 'contribution',
                    'fund_type'      => 'investment',
                    'amount'         => $investment,
                    'currency'       => 'KES',
                    'contributed_at' => $monthStart->copy()->addDays(25),
                    'status'         => 'approved',
                    'is_archived'    => false,
                    'created_by'     => 1,
                ]);
            }
        };

        // Clear any previous test contributions for clarity (optional)
        PartnerContribution::where('partner_id', $partner->id)
            ->where('type', 'contribution')
            ->where('is_archived', false)
            ->whereBetween('contributed_at', [$base->copy()->subMonthsNoOverflow(6), $base->copy()->endOfMonth()])
            ->delete();

        // Create 6 months of data: 2 on-time, 3 missing, current partial
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = $base->copy()->subMonthsNoOverflow($i);

            if (in_array($i, [5, 4], true)) {
                // Oldest 2 months: full 55,000 (on time)
                $makeMonth($partner, $monthStart, 5000, 50000);
            } elseif (in_array($i, [3, 2, 1], true)) {
                // Next 3 months: no payment (arrears)
                $makeMonth($partner, $monthStart, 0, 0);
            } else {
                // Current month: half paid (27,500) to show partial arrear
                $makeMonth($partner, $monthStart, 2500, 25000);
            }
        }
    }
}

