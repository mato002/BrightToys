<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
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

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'salesLast7Days' => $salesLast7Days,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function export()
    {
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
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($stats) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Products', $stats['products']]);
            fputcsv($file, ['Total Orders', $stats['orders']]);
            fputcsv($file, ['Total Users', $stats['users']]);
            fputcsv($file, ['Total Revenue', $stats['revenue']]);
            fputcsv($file, ['Pending Orders', $stats['pending_orders']]);
            fputcsv($file, ['Completed Orders', $stats['completed_orders']]);
            fputcsv($file, ['Today Revenue', $stats['today_revenue']]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function report()
    {
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

        $html = view('admin.reports.dashboard', compact('stats', 'salesLast7Days', 'statusCounts', 'topProducts'))->render();
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->stream('dashboard_report_' . date('Y-m-d_His') . '.pdf');
    }
}

