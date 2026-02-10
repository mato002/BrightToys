<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Order;
use App\Models\Product;
use App\Models\FinancialRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectPerformanceController extends Controller
{
    /**
     * Display project performance dashboard for partners.
     */
    public function show(Project $project)
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        // Only the creator can view performance
        if ($project->created_by !== $partner->id) {
            abort(403, 'You can only view performance of projects you created.');
        }

        // Get project-specific data
        $projectData = $this->getProjectData($project);

        return view('partner.projects.performance', compact('project', 'projectData'));
    }

    /**
     * Get project-specific performance data.
     */
    protected function getProjectData(Project $project)
    {
        $data = [
            'project' => $project,
            'assigned_users' => $project->assignedUsers,
        ];

        // If this is the e-commerce project, get e-commerce specific stats
        if ($project->route_name === 'home' || $project->type === 'ecommerce') {
            $data['stats'] = [
                'products' => Product::count(),
                'orders' => Order::count(),
                'revenue' => Order::where('status', 'completed')->sum('total'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'completed_orders' => Order::where('status', 'completed')->count(),
                'today_revenue' => Order::where('status', 'completed')
                    ->whereDate('created_at', today())
                    ->sum('total'),
            ];

            // Revenue last 6 months
            $data['revenueLast6Months'] = collect(range(5, 0))->map(function ($monthsAgo) {
                $date = now()->subMonths($monthsAgo);
                $monthRevenue = Order::where('status', 'completed')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total');
                return [
                    'month' => $date->format('M Y'),
                    'revenue' => $monthRevenue,
                ];
            });

            // Recent orders
            $data['recentOrders'] = Order::where('status', 'completed')
                ->latest()
                ->limit(10)
                ->get();

            // Top products
            $data['topProducts'] = Product::withCount('orderItems')
                ->orderByDesc('order_items_count')
                ->take(5)
                ->get();
        }

        return $data;
    }

    /**
     * Display project-specific financial records.
     */
    public function finances(Project $project)
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        // Only the creator can view finances
        if ($project->created_by !== $partner->id) {
            abort(403, 'You can only view finances of projects you created.');
        }

        // Determine period for revenue/expense view
        $period = request('period', 'month'); // day, month, all
        $periodLabel = 'This month';
        $from = null;
        $to = now();

        if ($period === 'day') {
            $from = now()->startOfDay();
            $periodLabel = 'Today';
        } elseif ($period === 'month') {
            $from = now()->startOfMonth();
            $periodLabel = now()->format('F Y');
        }

        // Get financial records related to this project (all-time list, latest first)
        $financialRecordsQuery = FinancialRecord::where('is_archived', false)
            ->where('status', 'approved')
            ->where('project_id', $project->id)
            ->latest('occurred_at');

        $financialRecords = $financialRecordsQuery->get();

        // Get contributions related to this project (if we add project_id to contributions later)
        $contributions = \App\Models\PartnerContribution::where('partner_id', $partner->id)
            ->where('is_archived', false)
            ->latest('contributed_at')
            ->paginate(20);

        // Sales revenue (from orders) if this is the e-commerce project
        $salesRevenue = 0;
        if ($project->route_name === 'home' || $project->type === 'ecommerce') {
            $ordersQuery = Order::where('status', 'completed');
            if ($from) {
                $ordersQuery->whereBetween('created_at', [$from, $to]);
            }
            $salesRevenue = $ordersQuery->sum('total');
        }

        // Other income for this project from financial records
        $otherIncomeQuery = FinancialRecord::where('type', 'other_income')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->where('project_id', $project->id);

        if ($from) {
            $otherIncomeQuery->whereBetween('occurred_at', [$from, $to]);
        }

        $otherIncome = $otherIncomeQuery->sum('amount');

        // Project expenses for the same period
        $expensesQuery = FinancialRecord::where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->where('project_id', $project->id);

        if ($from) {
            $expensesQuery->whereBetween('occurred_at', [$from, $to]);
        }

        $projectExpenses = $expensesQuery->sum('amount');

        $projectRevenue = $salesRevenue + $otherIncome;
        $netOperatingIncome = $projectRevenue - $projectExpenses;

        // Break expenses into COGS vs operating (simple heuristic using category = 'purchase')
        $cogsExpenses = (clone $expensesQuery)
            ->whereRaw('LOWER(category) = ?', ['purchase'])
            ->sum('amount');
        $operatingExpenses = $projectExpenses - $cogsExpenses;

        // KPI evaluation
        $kpi = $project->kpi;
        $kpiSnapshot = null;
        $kpiAlerts = [];

        if ($kpi) {
            $kpiSnapshot = [
                'targets' => [
                    'target_annual_value_growth_pct' => $kpi->target_annual_value_growth_pct,
                    'expected_holding_period_years' => $kpi->expected_holding_period_years,
                    'minimum_acceptable_roi_pct' => $kpi->minimum_acceptable_roi_pct,
                    'monthly_revenue_target' => $kpi->monthly_revenue_target,
                    'gross_margin_target_pct' => $kpi->gross_margin_target_pct,
                    'operating_expense_ratio_target_pct' => $kpi->operating_expense_ratio_target_pct,
                    'break_even_revenue' => $kpi->break_even_revenue,
                    'loan_coverage_ratio_target' => $kpi->loan_coverage_ratio_target,
                ],
                'actuals' => [],
            ];

            // Land / real estate metrics
            if ($project->type === 'land') {
                $assets = $project->assets;
                $totalAcquisition = $assets->sum('acquisition_cost');
                $currentValue = $assets->sum(function ($asset) {
                    return $asset->current_value ?? $asset->acquisition_cost;
                });

                $valueGrowthPct = null;
                if ($totalAcquisition > 0 && $currentValue !== null) {
                    $valueGrowthPct = (($currentValue - $totalAcquisition) / $totalAcquisition) * 100;
                }

                $kpiSnapshot['actuals']['current_asset_value'] = $currentValue;
                $kpiSnapshot['actuals']['value_growth_pct'] = $valueGrowthPct;

                if (!is_null($valueGrowthPct) && !is_null($kpi->target_annual_value_growth_pct) && $valueGrowthPct < $kpi->target_annual_value_growth_pct) {
                    $kpiAlerts[] = 'Land value growth is below target. Current: ' . round($valueGrowthPct, 1) . '%, Target: ' . $kpi->target_annual_value_growth_pct . '%.';
                }
            }

            // Operating business / toy shop metrics (use current period figures)
            if ($project->route_name === 'home' || $project->type === 'ecommerce' || $project->type === 'business') {
                $grossMargin = null;
                $grossMarginPct = null;
                $opexRatioPct = null;

                if ($projectRevenue > 0) {
                    $grossMargin = $projectRevenue - $cogsExpenses;
                    $grossMarginPct = ($grossMargin / $projectRevenue) * 100;
                    $opexRatioPct = ($operatingExpenses / $projectRevenue) * 100;
                }

                // Simple debt service coverage ratio using monthly repayment and current period NOI
                $funding = $project->funding;
                $loanCoverageRatio = null;
                if ($funding && $funding->monthly_repayment > 0 && $netOperatingIncome > 0) {
                    $loanCoverageRatio = $netOperatingIncome / $funding->monthly_repayment;
                }

                $kpiSnapshot['actuals'] += [
                    'period_revenue' => $projectRevenue,
                    'gross_margin_pct' => $grossMarginPct,
                    'operating_expense_ratio_pct' => $opexRatioPct,
                    'loan_coverage_ratio' => $loanCoverageRatio,
                ];

                if (!is_null($kpi->monthly_revenue_target) && $projectRevenue < $kpi->monthly_revenue_target) {
                    $kpiAlerts[] = 'Revenue is below monthly target. Current: Ksh ' . number_format($projectRevenue, 0) . ', Target: Ksh ' . number_format($kpi->monthly_revenue_target, 0) . '.';
                }

                if (!is_null($kpi->gross_margin_target_pct) && !is_null($grossMarginPct) && $grossMarginPct < $kpi->gross_margin_target_pct) {
                    $kpiAlerts[] = 'Gross margin is below target. Current: ' . round($grossMarginPct, 1) . '%, Target: ' . $kpi->gross_margin_target_pct . '%.';
                }

                if (!is_null($kpi->operating_expense_ratio_target_pct) && !is_null($opexRatioPct) && $opexRatioPct > $kpi->operating_expense_ratio_target_pct) {
                    $kpiAlerts[] = 'Operating expense ratio is above target. Current: ' . round($opexRatioPct, 1) . '%, Target: ' . $kpi->operating_expense_ratio_target_pct . '%.';
                }

                if (!is_null($kpi->break_even_revenue) && $projectRevenue < $kpi->break_even_revenue) {
                    $kpiAlerts[] = 'Revenue is below break-even. Current: Ksh ' . number_format($projectRevenue, 0) . ', Break-even: Ksh ' . number_format($kpi->break_even_revenue, 0) . '.';
                }

                if (!is_null($kpi->loan_coverage_ratio_target) && !is_null($loanCoverageRatio) && $loanCoverageRatio < $kpi->loan_coverage_ratio_target) {
                    $kpiAlerts[] = 'Loan coverage ratio is below target. Current: ' . round($loanCoverageRatio, 2) . ', Target: ' . $kpi->loan_coverage_ratio_target . '.';
                }
            }
        }

        // --- All–time profit / loss & capital health metrics ---
        // Capital & debt from funding record
        $funding = $project->funding;
        $totalCapitalInvested = $funding?->member_capital_amount ?? 0;
        $totalDebtOutstanding = $funding?->outstanding_balance ?? 0;

        // All–time revenue & expenses for this project from financial records
        $allTimeRevenue = FinancialRecord::where('project_id', $project->id)
            ->whereIn('type', ['revenue', 'other_income'])
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->sum('amount');

        $allTimeExpenses = FinancialRecord::where('project_id', $project->id)
            ->where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->sum('amount');

        $netProfitLoss = $allTimeRevenue - $allTimeExpenses;

        // ROI based on capital invested
        $roiPercentage = null;
        if ($totalCapitalInvested > 0) {
            $roiPercentage = ($netProfitLoss / $totalCapitalInvested) * 100;
        }

        // Project health indicator driven by ROI, profit and leverage
        $targetRoi = $kpi?->minimum_acceptable_roi_pct ?? 15; // default 15% if not configured
        $debtToCapitalRatio = $totalCapitalInvested > 0
            ? ($totalDebtOutstanding / $totalCapitalInvested)
            : null;

        $healthStatus = [
            'label' => 'Watch',
            'level' => 'watch',
            'badge_class' => 'bg-amber-50 text-amber-700 border border-amber-200',
            'text_class' => 'text-amber-700',
        ];

        if (!is_null($roiPercentage)) {
            if ($roiPercentage >= $targetRoi && $netProfitLoss > 0 && ($debtToCapitalRatio === null || $debtToCapitalRatio <= 1.0)) {
                // Strong ROI, profitable, and debt not overwhelming
                $healthStatus = [
                    'label' => 'Healthy',
                    'level' => 'healthy',
                    'badge_class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                    'text_class' => 'text-emerald-700',
                ];
            } elseif ($roiPercentage < 0 || ($debtToCapitalRatio !== null && $debtToCapitalRatio > 1.5)) {
                // Losing money or very high leverage
                $healthStatus = [
                    'label' => 'Risk',
                    'level' => 'risk',
                    'badge_class' => 'bg-red-50 text-red-700 border border-red-200',
                    'text_class' => 'text-red-700',
                ];
            }
        }

        // Assets for this project
        $assets = $project->assets()->orderByDesc('date_acquired')->get();

        return view('partner.projects.finances', compact(
            'project',
            'financialRecords',
            'contributions',
            'projectRevenue',
            'projectExpenses',
            'assets',
            'salesRevenue',
            'otherIncome',
            'netOperatingIncome',
            'cogsExpenses',
            'operatingExpenses',
            'kpiSnapshot',
            'kpiAlerts',
            'totalCapitalInvested',
            'totalDebtOutstanding',
            'allTimeRevenue',
            'allTimeExpenses',
            'netProfitLoss',
            'roiPercentage',
            'healthStatus',
            'period',
            'periodLabel'
        ));
    }
}
