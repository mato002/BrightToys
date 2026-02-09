<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use App\Models\PartnerContribution;
use App\Models\Order;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display partner dashboard with read-only financial access.
     */
    public function index()
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        // Get current ownership
        $currentOwnership = $partner->ownerships()
            ->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', now());
            })
            ->first();

        // Financial summaries (read-only)
        $totalContributions = PartnerContribution::where('partner_id', $partner->id)
            ->where('status', 'approved')
            ->where('type', 'contribution')
            ->where('is_archived', false)
            ->sum('amount');

        $totalWithdrawals = PartnerContribution::where('partner_id', $partner->id)
            ->where('status', 'approved')
            ->where('type', 'withdrawal')
            ->where('is_archived', false)
            ->sum('amount');

        $totalProfitDistribution = PartnerContribution::where('partner_id', $partner->id)
            ->where('status', 'approved')
            ->where('type', 'profit_distribution')
            ->where('is_archived', false)
            ->sum('amount');

        // Recent contributions
        $recentContributions = PartnerContribution::where('partner_id', $partner->id)
            ->where('is_archived', false)
            ->latest('contributed_at')
            ->limit(10)
            ->get();

        // Business financial overview (read-only)
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        $totalExpenses = FinancialRecord::where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->sum('amount');

        $netProfit = $totalRevenue - $totalExpenses;

        // Partner's share based on ownership percentage
        $partnerShare = $currentOwnership 
            ? ($netProfit * ($currentOwnership->percentage / 100))
            : 0;

        // Revenue trends - Last 6 months
        $revenueLast6Months = collect(range(5, 0))->map(function ($monthsAgo) {
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

        // Monthly breakdowns
        $thisMonthRevenue = Order::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');
        
        $lastMonthRevenue = Order::where('status', 'completed')
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total');

        $thisMonthExpenses = FinancialRecord::where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->whereYear('occurred_at', now()->year)
            ->whereMonth('occurred_at', now()->month)
            ->sum('amount');

        // Recent orders
        $recentOrders = Order::where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get();

        // Top products by revenue
        $topProducts = \App\Models\Product::with(['orderItems.order'])
            ->get()
            ->map(function ($product) {
                $product->total_revenue = $product->orderItems
                    ->filter(function ($item) {
                        return $item->order && $item->order->status === 'completed';
                    })
                    ->sum(function ($item) {
                        return ($item->price ?? 0) * ($item->quantity ?? 0);
                    });
                return $product;
            })
            ->filter(function ($product) {
                return $product->total_revenue > 0;
            })
            ->sortByDesc('total_revenue')
            ->take(5)
            ->values();

        // Expense categories breakdown
        $expenseCategories = FinancialRecord::where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Pending contributions
        $pendingContributions = PartnerContribution::where('partner_id', $partner->id)
            ->where('status', 'pending')
            ->where('is_archived', false)
            ->count();

        // Year-to-date totals
        $ytdRevenue = Order::where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->sum('total');
        
        $ytdExpenses = FinancialRecord::where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->whereYear('occurred_at', now()->year)
            ->sum('amount');

        $ytdProfit = $ytdRevenue - $ytdExpenses;
        $ytdPartnerShare = $currentOwnership ? ($ytdProfit * ($currentOwnership->percentage / 100)) : 0;

        // Growth indicators
        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        return view('partner.dashboard', compact(
            'partner',
            'currentOwnership',
            'totalContributions',
            'totalWithdrawals',
            'totalProfitDistribution',
            'recentContributions',
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'partnerShare',
            'revenueLast6Months',
            'thisMonthRevenue',
            'lastMonthRevenue',
            'thisMonthExpenses',
            'recentOrders',
            'topProducts',
            'expenseCategories',
            'pendingContributions',
            'ytdRevenue',
            'ytdExpenses',
            'ytdProfit',
            'ytdPartnerShare',
            'revenueGrowth'
        ));
    }

    /**
     * Display financial records (read-only).
     */
    public function financialRecords()
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        $query = FinancialRecord::with(['creator', 'approver', 'order'])
            ->where('is_archived', false)
            ->where('status', 'approved');

        if ($type = request('type')) {
            $query->where('type', $type);
        }

        if ($category = request('category')) {
            $query->where('category', 'like', "%{$category}%");
        }

        if ($dateFrom = request('date_from')) {
            $query->whereDate('occurred_at', '>=', $dateFrom);
        }

        $records = $query->latest('occurred_at')
            ->paginate(20)
            ->withQueryString();

        return view('partner.financial-records', compact('records'));
    }

    /**
     * Display contributions (read-only).
     */
    public function contributions()
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        $query = PartnerContribution::where('partner_id', $partner->id)
            ->where('is_archived', false);

        if ($type = request('type')) {
            $query->where('type', $type);
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($dateFrom = request('date_from')) {
            $query->whereDate('contributed_at', '>=', $dateFrom);
        }

        $contributions = $query->latest('contributed_at')
            ->paginate(20)
            ->withQueryString();

        return view('partner.contributions', compact('contributions'));
    }

    /**
     * Show the form for creating a new contribution.
     */
    public function createContribution()
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        return view('partner.contributions.create', compact('partner'));
    }

    /**
     * Store a newly created contribution.
     */
    public function storeContribution(Request $request)
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:contribution,withdrawal'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'max:3'],
            'contributed_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $contribution = PartnerContribution::create([
            'partner_id' => $partner->id,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'KES',
            'contributed_at' => $validated['contributed_at'],
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        ActivityLogService::log('contribution_created', $contribution, $validated);

        return redirect()->route('partner.contributions')
            ->with('success', 'Contribution submitted successfully. Awaiting approval.');
    }

    /**
     * Display partner earnings (profit distributions and earnings history).
     */
    public function earnings()
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (!$partner) {
            abort(403, 'You are not associated with a partner account.');
        }

        // Get current ownership
        $currentOwnership = $partner->ownerships()
            ->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', now());
            })
            ->first();

        // Total profit distributions (earnings)
        $totalEarnings = PartnerContribution::where('partner_id', $partner->id)
            ->where('status', 'approved')
            ->where('type', 'profit_distribution')
            ->where('is_archived', false)
            ->sum('amount');

        // Earnings history (profit distributions)
        $earningsHistory = PartnerContribution::where('partner_id', $partner->id)
            ->where('type', 'profit_distribution')
            ->where('is_archived', false)
            ->latest('contributed_at')
            ->paginate(20)
            ->withQueryString();

        // Year-to-date earnings
        $ytdEarnings = PartnerContribution::where('partner_id', $partner->id)
            ->where('status', 'approved')
            ->where('type', 'profit_distribution')
            ->where('is_archived', false)
            ->whereYear('contributed_at', now()->year)
            ->sum('amount');

        // Monthly earnings breakdown (last 12 months)
        $monthlyEarnings = collect(range(11, 0))->map(function ($monthsAgo) use ($partner) {
            $date = now()->subMonths($monthsAgo);
            $monthEarnings = PartnerContribution::where('partner_id', $partner->id)
                ->where('status', 'approved')
                ->where('type', 'profit_distribution')
                ->where('is_archived', false)
                ->whereYear('contributed_at', $date->year)
                ->whereMonth('contributed_at', $date->month)
                ->sum('amount');
            return [
                'month' => $date->format('M Y'),
                'earnings' => $monthEarnings,
            ];
        });

        // Projected earnings based on current profit and ownership
        $totalRevenue = Order::where('status', 'completed')->sum('total');
        $totalExpenses = FinancialRecord::where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;
        $projectedEarnings = $currentOwnership 
            ? ($netProfit * ($currentOwnership->percentage / 100)) - $totalEarnings
            : 0;

        return view('partner.earnings', compact(
            'partner',
            'currentOwnership',
            'totalEarnings',
            'earningsHistory',
            'ytdEarnings',
            'monthlyEarnings',
            'projectedEarnings'
        ));
    }
}
