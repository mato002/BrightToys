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

        // Get financial records related to this project
        // For e-commerce, we can link orders to the project
        $financialRecords = FinancialRecord::where('is_archived', false)
            ->where('status', 'approved')
            ->latest('occurred_at')
            ->paginate(20);

        // Get contributions related to this project (if we add project_id to contributions later)
        $contributions = \App\Models\PartnerContribution::where('partner_id', $partner->id)
            ->where('is_archived', false)
            ->latest('contributed_at')
            ->paginate(20);

        // Project revenue (if e-commerce)
        $projectRevenue = 0;
        if ($project->route_name === 'home' || $project->type === 'ecommerce') {
            $projectRevenue = Order::where('status', 'completed')->sum('total');
        }

        // Project expenses
        $projectExpenses = FinancialRecord::where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->sum('amount');

        return view('partner.projects.finances', compact(
            'project',
            'financialRecords',
            'contributions',
            'projectRevenue',
            'projectExpenses'
        ));
    }
}
