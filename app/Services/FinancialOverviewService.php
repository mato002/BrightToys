<?php

namespace App\Services;

use App\Models\FinancialRecord;
use App\Models\MemberWallet;
use App\Models\Order;
use App\Models\PartnerContribution;
use App\Models\ProjectAsset;
use Carbon\Carbon;

class FinancialOverviewService
{
    /**
     * Group-level financial snapshot for the admin dashboard.
     */
    public static function getGroupSnapshot(): array
    {
        // Contributions (approved, non-archived)
        $totalContributionsInvestment = PartnerContribution::where('status', 'approved')
            ->where('is_archived', false)
            ->where('fund_type', 'investment')
            ->sum('amount');

        $totalContributionsWelfare = PartnerContribution::where('status', 'approved')
            ->where('is_archived', false)
            ->where('fund_type', 'welfare')
            ->sum('amount');

        // Welfare disbursements are financial records tagged as welfare expenses
        $welfareDisbursements = FinancialRecord::where('type', 'expense')
            ->where('fund_type', 'welfare')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->sum('amount');

        $welfareBalance = $totalContributionsWelfare - $welfareDisbursements;

        // Investment total can be approximated from member investment wallets
        $investmentWalletTotal = MemberWallet::where('type', MemberWallet::TYPE_INVESTMENT)->sum('balance');

        // Outstanding loans - financial records tagged as loan_principal / loan_interest or from a future loans table
        $loanPrincipalOutstanding = FinancialRecord::where('category', 'loan_principal')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->sum('amount');

        $loanInterestOutstanding = FinancialRecord::where('category', 'loan_interest')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->sum('amount');

        // Assets (by category)
        $landValue = ProjectAsset::where('category', 'land')->sum('current_value');
        $toyShopAssetsValue = ProjectAsset::where('category', 'toy_shop')->sum('current_value');
        $inventoryAssetsValue = ProjectAsset::where('category', 'inventory')->sum('current_value');

        $totalAssets = $landValue + $toyShopAssetsValue + $inventoryAssetsValue;
        $totalLiabilities = $loanPrincipalOutstanding + $loanInterestOutstanding;
        $netWorth = $totalAssets - $totalLiabilities;

        // Performance metrics
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        $monthlyRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)
            ->sum('total');

        $monthlyExpenses = FinancialRecord::where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->where('occurred_at', '>=', $thisMonth)
            ->sum('amount');

        $yearlyRevenue = Order::where('status', 'completed')
            ->where('created_at', '>=', $thisYear)
            ->sum('total');

        $yearlyExpenses = FinancialRecord::where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->where('occurred_at', '>=', $thisYear)
            ->sum('amount');

        // Simple revenue trend over the last 6 months
        $revenueTrend = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = Carbon::now()->subMonths($monthsAgo);
            $monthRevenue = Order::where('status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total');
            return [
                'month' => $date->format('M Y'),
                'revenue' => $monthRevenue,
            ];
        });

        return [
            'total_contributions_investment' => $totalContributionsInvestment,
            'total_contributions_welfare' => $totalContributionsWelfare,
            'welfare_balance' => $welfareBalance,
            'investment_wallet_total' => $investmentWalletTotal,
            'loan_principal_outstanding' => $loanPrincipalOutstanding,
            'loan_interest_outstanding' => $loanInterestOutstanding,
            'assets' => [
                'land' => $landValue,
                'toy_shop' => $toyShopAssetsValue,
                'inventory' => $inventoryAssetsValue,
                'total' => $totalAssets,
            ],
            'net_worth' => $netWorth,
            'performance' => [
                'monthly' => [
                    'revenue' => $monthlyRevenue,
                    'expenses' => $monthlyExpenses,
                    'profit' => $monthlyRevenue - $monthlyExpenses,
                ],
                'yearly' => [
                    'revenue' => $yearlyRevenue,
                    'expenses' => $yearlyExpenses,
                    'profit' => $yearlyRevenue - $yearlyExpenses,
                ],
                'trend' => $revenueTrend,
            ],
        ];
    }
}

