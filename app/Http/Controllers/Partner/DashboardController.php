<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use App\Models\PartnerContribution;
use App\Models\Order;
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
            'partnerShare'
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

        $contributions = PartnerContribution::where('partner_id', $partner->id)
            ->where('is_archived', false)
            ->latest('contributed_at')
            ->paginate(20)
            ->withQueryString();

        return view('partner.contributions', compact('contributions'));
    }
}
