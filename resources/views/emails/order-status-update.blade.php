<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update - BrightToys</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0; color: #1e293b; font-size: 28px;">📦 Order Status Update</h1>
    </div>

    <div style="background: #ffffff; padding: 30px; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 10px 10px;">
        <p style="margin: 0 0 20px; font-size: 16px;">Hello {{ $order->user->name ?? 'Customer' }},</p>
        
        <p style="margin: 0 0 20px; font-size: 14px; color: #475569;">
            Your order status has been updated:
        </p>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">
            <p style="margin: 0 0 10px; font-size: 14px; color: #64748b;">Previous Status:</p>
            <p style="margin: 0 0 20px; font-size: 16px; font-weight: bold; color: #1e293b; text-transform: capitalize;">{{ ucfirst($oldStatus) }}</p>
            
            <p style="margin: 0 0 10px; font-size: 14px; color: #64748b;">New Status:</p>
            <p style="margin: 0; font-size: 20px; font-weight: bold; color: #3b82f6; text-transform: capitalize;">{{ ucfirst($newStatus) }}</p>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h2 style="margin: 0 0 15px; font-size: 18px; color: #1e293b;">Order Information</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-size: 14px; color: #64748b;">Order Number:</td>
                    <td style="padding: 8px 0; font-size: 14px; font-weight: bold; color: #1e293b; text-align: right;">#{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-size: 14px; color: #64748b;">Tracking Number:</td>
                    <td style="padding: 8px 0; font-size: 14px; font-weight: bold; color: #1e293b; text-align: right;">{{ $order->tracking_number }}</td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('account.orders') }}" style="display: inline-block; background: #3b82f6; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px;">
                Track Your Order
            </a>
        </div>

        <p style="margin: 20px 0 0; font-size: 12px; color: #94a3b8; text-align: center;">
            If you have any questions, please contact our support team.<br>
            Thank you for shopping with BrightToys! 🎁
        </p>
    </div>
</body>
</html>
