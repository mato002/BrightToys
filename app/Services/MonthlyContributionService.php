<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\PartnerContribution;
use App\Models\PenaltyAdjustment;
use Carbon\Carbon;

class MonthlyContributionService
{
    public const MONTHLY_WELFARE = 5000;
    public const MONTHLY_INVESTMENT = 50000;
    public const MONTHLY_TOTAL = self::MONTHLY_WELFARE + self::MONTHLY_INVESTMENT; // 55,000

    public const ARREARS_PENALTY_RATE = 0.10; // 10%
    public const CRITICAL_MONTHS_THRESHOLD = 3;

    /**
     * Build monthly contribution status for a partner.
     */
    public static function forPartner(Partner $partner): array
    {
        $now = Carbon::now();

        // Determine when to start monthly tracking:
        // - Prefer the earlier of partner creation date and first contribution date
        //   so that historical months (with or without payments) are evaluated.
        $firstContribution = PartnerContribution::where('partner_id', $partner->id)
            ->where('type', 'contribution')
            ->where('is_archived', false)
            ->orderBy('contributed_at')
            ->first();

        $start = $partner->created_at ? $partner->created_at->copy() : $now->copy();
        if ($firstContribution && $firstContribution->contributed_at && $firstContribution->contributed_at->lt($start)) {
            $start = $firstContribution->contributed_at->copy();
        }

        $start = $start->startOfMonth();

        if ($start->greaterThan($now)) {
            $start = $now->copy()->startOfMonth();
        }

        $monthly = [];
        $totalArrears = 0;
        $monthsInArrears = 0;
        $totalPenalty = 0;

        $cursor = $start->copy();

        while ($cursor <= $now) {
            $expected = self::MONTHLY_TOTAL;

            // Sum approved contributions (welfare + investment) for this month
            $paid = PartnerContribution::where('partner_id', $partner->id)
                ->where('status', 'approved')
                ->where('type', 'contribution')
                ->where('is_archived', false)
                ->whereYear('contributed_at', $cursor->year)
                ->whereMonth('contributed_at', $cursor->month)
                ->sum('amount');

            $arrear = max(0, $expected - $paid);

            $endOfMonth = $cursor->copy()->endOfMonth();
            $isPastMonth = $endOfMonth->lt($now->copy()->startOfDay());

            $penalty = 0;

            $isPaused = \App\Services\PenaltyService::isPausedForMonth($partner, $cursor->year, $cursor->month);

            if ($arrear > 0 && $isPastMonth) {
                $monthsInArrears++;
                $totalArrears += $arrear;

                if (! $isPaused) {
                    $penalty = $arrear * self::ARREARS_PENALTY_RATE;
                }
            }

            // Manual penalty adjustments (apply/waive) for this month
            $manualApply = PenaltyAdjustment::approved()
                ->where('partner_id', $partner->id)
                ->where('scope', 'monthly_contribution')
                ->where('type', 'apply')
                ->where('target_year', $cursor->year)
                ->where('target_month', $cursor->month)
                ->sum('amount');

            $manualWaive = PenaltyAdjustment::approved()
                ->where('partner_id', $partner->id)
                ->where('scope', 'monthly_contribution')
                ->where('type', 'waive')
                ->where('target_year', $cursor->year)
                ->where('target_month', $cursor->month)
                ->sum('amount');

            $penalty = max(0, $penalty + $manualApply - $manualWaive);

            $totalPenalty += $penalty;

            $monthly[] = [
                'year' => $cursor->year,
                'month_number' => $cursor->month,
                'label' => $cursor->format('M Y'),
                'expected' => $expected,
                'paid' => $paid,
                'arrear' => $arrear,
                'penalty' => $penalty,
                'is_past' => $isPastMonth,
                'end_of_month' => $endOfMonth,
            ];

            $cursor->addMonth();
        }

        $current = end($monthly) ?: null;
        $arrearsThresholdAmount = self::MONTHLY_TOTAL * self::CRITICAL_MONTHS_THRESHOLD;

        if ($monthsInArrears === 0) {
            $status = 'on_time';
        } elseif ($monthsInArrears < self::CRITICAL_MONTHS_THRESHOLD && $totalArrears < $arrearsThresholdAmount) {
            $status = 'late';
        } else {
            $status = 'critical';
        }

        return [
            'config' => [
                'monthly_welfare' => self::MONTHLY_WELFARE,
                'monthly_investment' => self::MONTHLY_INVESTMENT,
                'monthly_total' => self::MONTHLY_TOTAL,
                'penalty_rate' => self::ARREARS_PENALTY_RATE,
                'critical_months_threshold' => self::CRITICAL_MONTHS_THRESHOLD,
            ],
            'current' => $current,
            'monthly' => array_reverse($monthly), // latest first
            'total_arrears' => $totalArrears,
            'months_in_arrears' => $monthsInArrears,
            'total_penalty' => $totalPenalty,
            'status' => $status,
        ];
    }
}

