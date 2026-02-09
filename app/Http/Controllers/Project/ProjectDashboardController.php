<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectDashboardController extends Controller
{
    /**
     * Display project dashboard for assigned users.
     */
    public function index(Project $project)
    {
        $user = Auth::user();

        // Check if user is assigned to this project
        if (!$user->isAssignedToProject($project->id)) {
            abort(403, 'You are not assigned to this project.');
        }

        $userRole = $user->getProjectRole($project->id);

        // If this is the e-commerce project, show e-commerce specific stats
        if ($project->route_name === 'home' || $project->type === 'ecommerce') {
            $stats = [
                'products' => Product::count(),
                'orders' => Order::count(),
                'revenue' => Order::where('status', 'completed')->sum('total'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'completed_orders' => Order::where('status', 'completed')->count(),
            ];

            $recentOrders = Order::with('user')
                ->latest()
                ->take(5)
                ->get();

            $topProducts = Product::withCount('orderItems')
                ->orderByDesc('order_items_count')
                ->take(5)
                ->get();

            return view('project.dashboard', compact('project', 'userRole', 'stats', 'recentOrders', 'topProducts'));
        }

        // Generic project dashboard
        return view('project.dashboard', compact('project', 'userRole'));
    }
}
