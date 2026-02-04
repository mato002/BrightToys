<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        h1 { color: #333; border-bottom: 2px solid #f59e0b; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f3f4f6; padding: 8px; text-align: left; border: 1px solid #ddd; }
        td { padding: 6px; border: 1px solid #ddd; }
        .summary { background-color: #fef3c7; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .summary h2 { margin-top: 0; color: #92400e; }
        .summary p { margin: 5px 0; }
        .status { padding: 3px 8px; border-radius: 3px; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Orders Report</h1>
    <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>

    <div class="summary">
        <h2>Summary</h2>
        <p><strong>Total Orders:</strong> {{ $totalOrders }}</p>
        <p><strong>Total Revenue:</strong> Ksh {{ number_format($totalRevenue, 2) }}</p>
        <p><strong>Status Breakdown:</strong>
            @foreach($statusCounts as $status => $count)
                {{ ucfirst($status) }}: {{ $count }}{{ !$loop->last ? ', ' : '' }}
            @endforeach
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Total</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Items</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                    <td>{{ $order->user->email ?? 'N/A' }}</td>
                    <td>Ksh {{ number_format($order->total, 2) }}</td>
                    <td><span class="status">{{ ucfirst($order->status) }}</span></td>
                    <td>{{ $order->payment_method ?? '-' }}</td>
                    <td>{{ $order->items->count() }}</td>
                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No orders found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
