<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
</head>
<body style="font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; background-color:#f9fafb; padding:24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:8px;border:1px solid #e5e7eb;">
        <tr>
            <td style="padding:20px 24px 12px 24px;">
                <h1 style="font-size:18px;margin:0 0 8px 0;color:#111827;">{{ $title }}</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:0 24px 24px 24px;font-size:14px;line-height:1.5;color:#374151;">
                <p style="margin:0 0 12px 0;white-space:pre-line;">{{ $messageBody }}</p>
                <p style="margin:16px 0 0 0;font-size:12px;color:#6b7280;">
                    This is an automated message from the Otto Investments partner system.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>

