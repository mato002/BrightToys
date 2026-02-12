<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\PartnerContribution;
use App\Models\FinancialRecord;
use App\Models\PenaltyAdjustment;
use App\Models\VotingTopic;
use App\Models\Approval;
use App\Models\ApprovalDecision;
use App\Services\FinancialOverviewService;
use App\Services\ApprovalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isChairman = $user->hasAdminRole('chairman');

        // Group-level financial snapshot (contributions, welfare, net worth, etc.)
        // Always available for all admin users
        $groupFinancials = FinancialOverviewService::getGroupSnapshot();

        // E-commerce data (products, orders, customers, revenue) - only for non-chairman roles
        $stats = null;
        $salesLast7Days = null;
        $statusCounts = null;
        $recentOrders = null;
        $topProducts = null;

        if (!$isChairman) {
            $stats = [
                'products' => Product::count(),
                'orders' => Order::count(),
                'users' => User::count(),
                'revenue' => Order::sum('total'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'completed_orders' => Order::where('status', 'completed')->count(),
                'today_revenue' => Order::whereDate('created_at', Carbon::today())->sum('total'),
            ];

            // Sales last 7 days for line chart
            $salesLast7Days = collect(range(6, 0))->map(function ($daysAgo) {
                $date = Carbon::today()->subDays($daysAgo);
                return [
                    'date' => $date->format('M d'),
                    'total' => Order::whereDate('created_at', $date)->sum('total'),
                ];
            });

            // Order status distribution for doughnut chart
            $statusCounts = Order::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            $recentOrders = Order::with('user')
                ->latest()
                ->take(6)
                ->get();

            $topProducts = Product::withCount('orderItems')
                ->orderByDesc('order_items_count')
                ->take(5)
                ->get();
        } else {
            // Chairman-specific data
            // Pending approvals
            $pendingContributions = PartnerContribution::where('status', 'pending')
                ->with(['partner', 'creator'])
                ->latest('created_at')
                ->take(5)
                ->get();

            $pendingFinancialRecords = FinancialRecord::where('status', 'pending_approval')
                ->with(['creator', 'partner', 'project'])
                ->latest('created_at')
                ->take(5)
                ->get();

            $pendingPenalties = PenaltyAdjustment::where('status', 'pending')
                ->with(['partner', 'creator'])
                ->latest('created_at')
                ->take(5)
                ->get();

            // Count pending approvals that Chairman can approve
            $pendingApprovalCounts = [
                'contributions' => PartnerContribution::where('status', 'pending')->count(),
                'financial_records' => FinancialRecord::where('status', 'pending_approval')->count(),
                'penalties' => PenaltyAdjustment::where('status', 'pending')->count(),
            ];

            // Voting topics
            $openVotingTopics = VotingTopic::open()
                ->withCount('votes')
                ->orderBy('opens_at', 'desc')
                ->take(5)
                ->get();

            $recentVotingTopics = VotingTopic::withCount('votes')
                ->orderByDesc('created_at')
                ->take(5)
                ->get();

            // Recent approval decisions by Chairman
            $recentApprovalDecisions = ApprovalDecision::where('user_id', $user->id)
                ->with(['approval.creator'])
                ->latest()
                ->take(10)
                ->get();

            // Approval statistics (last 30 days)
            $approvalStats = [
                'total_approved' => ApprovalDecision::where('user_id', $user->id)
                    ->where('decision', 'approve')
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->count(),
                'total_rejected' => ApprovalDecision::where('user_id', $user->id)
                    ->where('decision', 'reject')
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->count(),
                'pending_awaiting' => Approval::where('status', 'pending')
                    ->whereHas('decisions', function($q) use ($user) {
                        $q->where('user_id', '!=', $user->id);
                    })
                    ->whereDoesntHave('decisions', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->count(),
            ];
        }

        return view('admin.dashboard', [
            'stats' => $stats ?? null,
            'recentOrders' => $recentOrders ?? null,
            'topProducts' => $topProducts ?? null,
            'salesLast7Days' => $salesLast7Days ?? null,
            'statusCounts' => $statusCounts ?? null,
            'groupFinancials' => $groupFinancials,
            'isChairman' => $isChairman,
            // Chairman-specific data
            'pendingContributions' => $pendingContributions ?? null,
            'pendingFinancialRecords' => $pendingFinancialRecords ?? null,
            'pendingPenalties' => $pendingPenalties ?? null,
            'pendingApprovalCounts' => $pendingApprovalCounts ?? null,
            'openVotingTopics' => $openVotingTopics ?? null,
            'recentVotingTopics' => $recentVotingTopics ?? null,
            'recentApprovalDecisions' => $recentApprovalDecisions ?? null,
            'approvalStats' => $approvalStats ?? null,
        ]);
    }

    public function export()
    {
        $user = Auth::user();
        $isChairman = $user->hasAdminRole('chairman');

        // Chairman should not have access to e-commerce export
        if ($isChairman) {
            abort(403, 'E-commerce data export is not available for your role.');
        }

        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'users' => User::count(),
            'revenue' => Order::sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'today_revenue' => Order::whereDate('created_at', Carbon::today())->sum('total'),
        ];

        $filename = 'dashboard_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($stats) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Products', $stats['products']]);
            fputcsv($file, ['Total Orders', $stats['orders']]);
            fputcsv($file, ['Total Users', $stats['users']]);
            fputcsv($file, ['Total Revenue', number_format($stats['revenue'], 2)]);
            fputcsv($file, ['Pending Orders', $stats['pending_orders']]);
            fputcsv($file, ['Completed Orders', $stats['completed_orders']]);
            fputcsv($file, ['Today Revenue', number_format($stats['today_revenue'], 2)]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function report()
    {
        $user = Auth::user();
        $isChairman = $user->hasAdminRole('chairman');

        // Chairman should not have access to e-commerce report
        if ($isChairman) {
            abort(403, 'E-commerce data report is not available for your role.');
        }

        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'users' => User::count(),
            'revenue' => Order::sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'today_revenue' => Order::whereDate('created_at', Carbon::today())->sum('total'),
        ];

        $salesLast7Days = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'date' => $date->format('M d'),
                'total' => Order::whereDate('created_at', $date)->sum('total'),
            ];
        });

        $statusCounts = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $topProducts = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(10)
            ->get();

        try {
            $html = view('admin.reports.dashboard', compact('stats', 'salesLast7Days', 'statusCounts', 'topProducts'))->render();
            
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->getOptions()->set('isRemoteEnabled', true);
            $dompdf->getOptions()->set('isHtml5ParserEnabled', true);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            return $dompdf->stream('dashboard_report_' . date('Y-m-d_His') . '.pdf', ['Attachment' => false]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }
}

