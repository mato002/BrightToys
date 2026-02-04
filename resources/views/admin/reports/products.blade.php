<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h1 { color: #333; border-bottom: 2px solid #f59e0b; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f3f4f6; padding: 10px; text-align: left; border: 1px solid #ddd; }
        td { padding: 8px; border: 1px solid #ddd; }
        .summary { background-color: #fef3c7; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .summary h2 { margin-top: 0; color: #92400e; }
        .summary p { margin: 5px 0; }
    </style>
</head>
<body>
    <h1>Products Report</h1>
    <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>

    <div class="summary">
        <h2>Summary</h2>
        <p><strong>Total Products:</strong> {{ $totalProducts }}</p>
        <p><strong>Total Inventory Value:</strong> Ksh {{ number_format($totalValue, 2) }}</p>
        <p><strong>Low Stock Items (less than 10):</strong> {{ $lowStock }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Featured</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku ?? '-' }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>Ksh {{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ ucfirst($product->status ?? 'N/A') }}</td>
                    <td>{{ $product->featured ? 'Yes' : 'No' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No products found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
