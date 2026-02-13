<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\PartnerContribution;
use App\Models\PenaltyAdjustment;
use App\Models\MonthlyContributionPenaltyRate;
use Carbon\Carbon;

class MonthlyContributionService
{
    public const MONTHLY_WELFARE = 5000;
    public const MONTHLY_INVESTMENT = 50000;
    public const MONTHLY_TOTAL = self::MONTHLY_WELFARE + self::MONTHLY_INVESTMENT; // 55,000

    public const ARREARS_PENALTY_RATE = 0.10; // 10% - Fallback if no rate in database
    public const CRITICAL_MONTHS_THRESHOLD = 3;

    /**
     * Get the penalty rate for a specific month
     */
    protected static function getPenaltyRateForMonth($year, $month)
    {
        $date = Carbon::create($year, $month, 1)->endOfMonth();
        $penaltyRate = MonthlyContributionPenaltyRate::getActiveRateForDate($date);
        
        return $penaltyRate ? $penaltyRate->rate : self::ARREARS_PENALTY_RATE;
    }

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
        $accumulatedArrears = 0; // Track accumulated arrears from past months

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
            $isCurrentMonth = $cursor->year == $now->year && $cursor->month == $now->month;

            $penalty = 0;

            $isPaused = \App\Services\PenaltyService::isPausedForMonth($partner, $cursor->year, $cursor->month);

            // Store accumulated arrears before processing this month
            $accumulatedBeforeThisMonth = $accumulatedArrears;

            if ($arrear > 0 && $isPastMonth) {
                $monthsInArrears++;
                $totalArrears += $arrear;
                $accumulatedArrears += $arrear; // Add to accumulated arrears

                if (! $isPaused) {
                    // Get the penalty rate for this specific month
                    $penaltyRate = self::getPenaltyRateForMonth($cursor->year, $cursor->month);
                    $penalty = $arrear * $penaltyRate;
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

            // For current month, calculate balance expected (expected + accumulated arrears - paid)
            $balanceExpected = $isCurrentMonth ? max(0, $expected + $accumulatedBeforeThisMonth - $paid) : null;

            $monthly[] = [
                'year' => $cursor->year,
                'month_number' => $cursor->month,
                'label' => $cursor->format('M Y'),
                'expected' => $expected,
                'paid' => $paid,
                'arrear' => $arrear,
                'accumulated_arrears' => $isCurrentMonth ? $accumulatedBeforeThisMonth : ($isPastMonth ? $accumulatedBeforeThisMonth : 0),
                'penalty' => $penalty,
                'is_past' => $isPastMonth,
                'is_current' => $isCurrentMonth,
                'balance_expected' => $balanceExpected,
                'end_of_month' => $endOfMonth,
            ];

            $cursor->addMonth();
        }

        $current = end($monthly) ?: null;
        $arrearsThresholdAmount = self::MONTHLY_TOTAL * self::CRITICAL_MONTHS_THRESHOLD;

        // Calculate days in arrears (from the first month with arrears)
        $daysInArrears = 0;
        $firstArrearsMonth = null;
        foreach ($monthly as $month) {
            if ($month['arrear'] > 0 && $month['is_past']) {
                if (!$firstArrearsMonth) {
                    $firstArrearsMonth = Carbon::create($month['year'], $month['month_number'], 1)->endOfMonth();
                }
            }
        }
        if ($firstArrearsMonth) {
            $daysInArrears = max(0, $now->diffInDays($firstArrearsMonth));
        }

        if ($monthsInArrears === 0) {
            $status = 'on_time';
        } elseif ($monthsInArrears < self::CRITICAL_MONTHS_THRESHOLD && $totalArrears < $arrearsThresholdAmount) {
            $status = 'late';
        } else {
            $status = 'critical';
        }

        // Get current penalty rate (for display purposes)
        $currentPenaltyRate = MonthlyContributionPenaltyRate::getActiveRateForDate() 
            ? MonthlyContributionPenaltyRate::getActiveRateForDate()->rate 
            : self::ARREARS_PENALTY_RATE;

        return [
            'config' => [
                'monthly_welfare' => self::MONTHLY_WELFARE,
                'monthly_investment' => self::MONTHLY_INVESTMENT,
                'monthly_total' => self::MONTHLY_TOTAL,
                'penalty_rate' => $currentPenaltyRate,
                'critical_months_threshold' => self::CRITICAL_MONTHS_THRESHOLD,
            ],
            'current' => $current,
            'monthly' => array_reverse($monthly), // latest first
            'total_arrears' => $totalArrears,
            'months_in_arrears' => $monthsInArrears,
            'days_in_arrears' => $daysInArrears,
            'total_penalty' => $totalPenalty,
            'status' => $status,
        ];
    }
}

