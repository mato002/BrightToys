<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - BrightToys</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0; color: #1e293b; font-size: 28px;">🎉 Order Confirmed!</h1>
        <p style="margin: 10px 0 0; color: #64748b; font-size: 14px;">Thank you for your purchase</p>
    </div>

    <div style="background: #ffffff; padding: 30px; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 10px 10px;">
        <p style="margin: 0 0 20px; font-size: 16px;">Hello {{ $order->user->name ?? 'Customer' }},</p>
        
        <p style="margin: 0 0 20px; font-size: 14px; color: #475569;">
            We're excited to confirm that we've received your order! Your toys are being prepared and will be on their way soon.
        </p>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h2 style="margin: 0 0 15px; font-size: 18px; color: #1e293b;">Order Details</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-size: 14px; color: #64748b;">Order Number:</td>
                    <td style="padding: 8px 0; font-size: 14px; font-weight: bold; color: #1e293b; text-align: right;">#{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-size: 14px; color: #64748b;">Tracking Number:</td>
                    <td style="padding: 8px 0; font-size: 14px; font-weight: bold; color: #1e293b; text-align: right;">{{ $order->tracking_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-size: 14px; color: #64748b;">Order Date:</td>
                    <td style="padding: 8px 0; font-size: 14px; color: #1e293b; text-align: right;">{{ $order->created_at->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-size: 14px; color: #64748b;">Total Amount:</td>
                    <td style="padding: 8px 0; font-size: 16px; font-weight: bold; color: #f59e0b; text-align: right;">KES {{ number_format($order->total, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-size: 14px; color: #64748b;">Payment Method:</td>
                    <td style="padding: 8px 0; font-size: 14px; color: #1e293b; text-align: right; text-transform: uppercase;">{{ $order->payment_method }}</td>
                </tr>
            </table>
        </div>

        <div style="margin: 20px 0;">
            <h3 style="margin: 0 0 15px; font-size: 16px; color: #1e293b;">Items Ordered:</h3>
            <table style="width: 100%; border-collapse: collapse;">
                @foreach($order->items as $item)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 12px 0; font-size: 14px; color: #1e293b;">
                        <strong>{{ $item->product->name ?? 'Product' }}</strong><br>
                        <span style="color: #64748b; font-size: 12px;">Quantity: {{ $item->quantity }} × KES {{ number_format($item->price, 2) }}</span>
                    </td>
                    <td style="padding: 12px 0; font-size: 14px; font-weight: bold; color: #1e293b; text-align: right;">
                        KES {{ number_format($item->price * $item->quantity, 2) }}
                    </td>
                </tr>
                @endforeach
            </table>
        </div>

        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; font-size: 14px; color: #92400e;">
                <strong>📦 Shipping Address:</strong><br>
                {{ $order->shipping_address }}
            </p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('account.orders') }}" style="display: inline-block; background: #f59e0b; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px;">
                View Order Details
            </a>
        </div>

        <p style="margin: 20px 0 0; font-size: 12px; color: #94a3b8; text-align: center;">
            If you have any questions, please contact our support team.<br>
            Thank you for shopping with BrightToys! 🎁
        </p>
    </div>
</body>
</html>
