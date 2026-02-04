<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::latest()->paginate(15);

        return view('admin.support.index', compact('tickets'));
    }

    public function show(SupportTicket $supportTicket)
    {
        return view('admin.support.show', [
            'ticket' => $supportTicket,
        ]);
    }

    public function update(Request $request, SupportTicket $supportTicket)
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved'],
        ]);

        $supportTicket->update([
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('admin.support-tickets.show', $supportTicket)
            ->with('status', 'Ticket updated successfully.');
    }

    public function export()
    {
        $tickets = SupportTicket::with('user')->latest()->get();

        $filename = 'support_tickets_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tickets) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['ID', 'User Name', 'User Email', 'Subject', 'Status', 'Created At']);
            
            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->id,
                    $ticket->name ?? ($ticket->user->name ?? 'N/A'),
                    $ticket->email ?? ($ticket->user->email ?? 'N/A'),
                    $ticket->subject,
                    ucfirst(str_replace('_', ' ', $ticket->status)),
                    $ticket->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function report()
    {
        $tickets = SupportTicket::with('user')->latest()->get();
        $totalTickets = $tickets->count();
        $statusCounts = $tickets->groupBy('status')->map->count();

        $html = view('admin.reports.support-tickets', compact('tickets', 'totalTickets', 'statusCounts'))->render();
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->stream('support_tickets_report_' . date('Y-m-d_His') . '.pdf');
    }
}

