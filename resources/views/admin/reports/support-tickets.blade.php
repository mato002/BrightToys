<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Support Tickets Report</title>
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
    <h1>Support Tickets Report</h1>
    <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>

    <div class="summary">
        <h2>Summary</h2>
        <p><strong>Total Tickets:</strong> {{ $totalTickets }}</p>
        <p><strong>Status Breakdown:</strong>
            @foreach($statusCounts as $status => $count)
                {{ ucfirst(str_replace('_', ' ', $status)) }}: {{ $count }}{{ !$loop->last ? ', ' : '' }}
            @endforeach
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->id }}</td>
                    <td>{{ $ticket->name ?? ($ticket->user->name ?? 'N/A') }}</td>
                    <td>{{ $ticket->email ?? ($ticket->user->email ?? 'N/A') }}</td>
                    <td>{{ $ticket->subject }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</td>
                    <td>{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No tickets found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
