<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #1e293b;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #64748b;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            flex: 1;
        }
        .info-box h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 12px;
            color: #475569;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #f8fafc;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }
        .text-right {
            text-align: right;
        }
        .total-section {
            margin-top: 20px;
            border-top: 2px solid #e2e8f0;
            padding-top: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .total-row.final {
            font-size: 16px;
            font-weight: bold;
            color: #f59e0b;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #f59e0b;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Otto Investments</h1>
        <p>Invoice #{{ $order->order_number }}</p>
        <p>Date: {{ $order->created_at->format('F d, Y') }}</p>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>Bill To:</h3>
            <p><strong>{{ $order->user->name ?? 'Customer' }}</strong></p>
            <p>{{ $order->user->email ?? '' }}</p>
            <p>{{ $order->shipping_address }}</p>
            @if($order->phone)
                <p>Phone: {{ $order->phone }}</p>
            @endif
        </div>
        <div class="info-box">
            <h3>Order Information:</h3>
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            @if($order->tracking_number)
                <p><strong>Tracking Number:</strong> {{ $order->tracking_number }}</p>
            @endif
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? 'N/A') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name ?? 'Product' }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">KES {{ number_format($item->price, 2) }}</td>
                <td class="text-right">KES {{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>KES {{ number_format($order->items->sum(function($item) { return $item->price * $item->quantity; }), 2) }}</span>
        </div>
        <div class="total-row">
            <span>Shipping:</span>
            <span>KES {{ number_format($order->total - $order->items->sum(function($item) { return $item->price * $item->quantity; }), 2) }}</span>
        </div>
        <div class="total-row final">
            <span>Total:</span>
            <span>KES {{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    @if($order->notes)
    <div style="margin-top: 30px; padding: 15px; background: #f8fafc; border-left: 4px solid #f59e0b; border-radius: 4px;">
        <h3 style="margin: 0 0 10px; font-size: 12px; color: #1e293b;">Order Notes:</h3>
        <p style="margin: 0; font-size: 11px; color: #475569;">{{ $order->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>Otto Investments - Investment Management System</p>
        <p>For inquiries, please contact our support team.</p>
    </div>
</body>
</html>
