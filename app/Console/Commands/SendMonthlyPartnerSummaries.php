<?php

namespace App\Console\Commands;

use App\Models\Partner;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMonthlyPartnerSummaries extends Command
{
    protected $signature = 'partners:send-monthly-summaries';

    protected $description = 'Generate monthly summary notifications for partners based on last month\'s activity.';

    public function handle(): int
    {
        $start = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $end = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        $partners = Partner::with('user')
            ->where('status', 'active')
            ->get();

        foreach ($partners as $partner) {
            if (! $partner->user) {
                continue;
            }

            // Basic summary: total approved contributions in last month
            $totalContributions = $partner->contributions()
                ->where('status', 'approved')
                ->whereBetween('contributed_at', [$start, $end])
                ->sum('amount');

            $message = "Your summary for {$start->format('F Y')}:\n"
                . "- Approved contributions: {$totalContributions} KES\n"
                . "Visit your dashboard for full details.";

            NotificationService::notify(
                $partner->user,
                'monthly_summary_generated',
                'Monthly summary report',
                $message,
                [
                    'period_start' => $start->toDateString(),
                    'period_end' => $end->toDateString(),
                    'total_contributions' => $totalContributions,
                ],
                'email'
            );
        }

        $this->info('Monthly partner summaries generated.');

        return self::SUCCESS;
    }
}

