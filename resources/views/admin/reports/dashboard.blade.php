<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h1 { color: #333; border-bottom: 2px solid #f59e0b; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f3f4f6; padding: 10px; text-align: left; border: 1px solid #ddd; }
        td { padding: 8px; border: 1px solid #ddd; }
        .summary { background-color: #fef3c7; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .summary h2 { margin-top: 0; color: #92400e; }
        .summary p { margin: 5px 0; }
        .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin: 20px 0; }
        .stat-box { background-color: #f9fafb; padding: 15px; border-radius: 5px; border: 1px solid #e5e7eb; }
        .stat-box h3 { margin-top: 0; color: #374151; font-size: 14px; }
        .stat-box .value { font-size: 24px; font-weight: bold; color: #f59e0b; }
    </style>
</head>
<body>
    <h1>Dashboard Report</h1>
    <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>

    <div class="stats-grid">
        <div class="stat-box">
            <h3>Total Products</h3>
            <div class="value">{{ $stats['products'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Total Orders</h3>
            <div class="value">{{ $stats['orders'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Total Users</h3>
            <div class="value">{{ $stats['users'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Total Revenue</h3>
            <div class="value">Ksh {{ number_format($stats['revenue'], 2) }}</div>
        </div>
        <div class="stat-box">
            <h3>Pending Orders</h3>
            <div class="value">{{ $stats['pending_orders'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Completed Orders</h3>
            <div class="value">{{ $stats['completed_orders'] }}</div>
        </div>
        <div class="stat-box">
            <h3>Today's Revenue</h3>
            <div class="value">Ksh {{ number_format($stats['today_revenue'], 2) }}</div>
        </div>
    </div>

    <div class="summary">
        <h2>Sales Last 7 Days</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesLast7Days as $sale)
                    <tr>
                        <td>{{ $sale['date'] }}</td>
                        <td>Ksh {{ number_format($sale['total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="summary">
        <h2>Top Products</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Orders Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->order_items_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center;">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
