<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Check if user has permission to access store management.
     */
    protected function checkStoreAdminPermission()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('store_admin')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public function index()
    {
        $this->checkStoreAdminPermission();
    {
        $tickets = SupportTicket::latest()->paginate(15);

        return view('admin.support.index', compact('tickets'));
    }

    public function show(SupportTicket $supportTicket)
    {
        $this->checkStoreAdminPermission();
        return view('admin.support.show', [
            'ticket' => $supportTicket,
        ]);
    }

    public function update(Request $request, SupportTicket $supportTicket)
    {
        $this->checkStoreAdminPermission();
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
        $this->checkStoreAdminPermission();
        try {
            // Clear any output buffering
            if (ob_get_level()) {
                ob_end_clean();
            }

            $tickets = SupportTicket::with('user')->latest()->get();

            $filename = 'support_tickets_export_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($tickets) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, ['ID', 'User Name', 'User Email', 'Subject', 'Status', 'Created At']);
                
                foreach ($tickets as $ticket) {
                    fputcsv($file, [
                        $ticket->id,
                        $ticket->name ?? ($ticket->user->name ?? 'N/A'),
                        $ticket->email ?? ($ticket->user->email ?? 'N/A'),
                        $ticket->subject ?? 'N/A',
                        ucfirst(str_replace('_', ' ', $ticket->status ?? 'open')),
                        $ticket->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function report()
    {
        $this->checkStoreAdminPermission();
        try {
            // Clear any output buffering
            if (ob_get_level()) {
                ob_end_clean();
            }

            $tickets = SupportTicket::with('user')->latest()->get();
            $totalTickets = $tickets->count();
            $statusCounts = $tickets->groupBy('status')->map->count();

            $html = view('admin.reports.support-tickets', compact('tickets', 'totalTickets', 'statusCounts'))->render();
            
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->setOption('isRemoteEnabled', true);
            $dompdf->setOption('isHtml5ParserEnabled', true);
            $dompdf->render();
            
            return $dompdf->stream('support_tickets_report_' . date('Y-m-d_His') . '.pdf', ['Attachment' => false]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }
}

