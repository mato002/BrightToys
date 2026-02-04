<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $query = Order::with(['user', 'items']);

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }

                $q->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        if ($from = request('from_date')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = request('to_date')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        $order->load('items.product', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function update(\Illuminate\Http\Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,completed,cancelled',
        ]);

        $order->update($data);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order status updated.');
    }

    public function export()
    {
        $query = Order::with(['user', 'items']);

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }

                $q->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        if ($from = request('from_date')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = request('to_date')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->get();

        $filename = 'orders_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['ID', 'Customer Name', 'Customer Email', 'Total', 'Status', 'Payment Method', 'Items Count', 'Created At']);
            
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->user->name ?? 'N/A',
                    $order->user->email ?? 'N/A',
                    $order->total,
                    ucfirst($order->status),
                    $order->payment_method ?? '-',
                    $order->items->count(),
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function report()
    {
        $query = Order::with(['user', 'items']);

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }

                $q->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        if ($from = request('from_date')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = request('to_date')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->get();
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total');
        $statusCounts = $orders->groupBy('status')->map->count();

        $html = view('admin.reports.orders', compact('orders', 'totalOrders', 'totalRevenue', 'statusCounts'))->render();
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        return $dompdf->stream('orders_report_' . date('Y-m-d_His') . '.pdf');
    }
}

