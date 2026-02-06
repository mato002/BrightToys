<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
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
        $query = User::withCount('orders')->where('is_admin', false);

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $this->checkStoreAdminPermission();
        $user->load(['orders' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.users.show', compact('user'));
    }

    public function export()
    {
        $this->checkStoreAdminPermission();
        try {
            // Clear any output buffering
            if (ob_get_level()) {
                ob_end_clean();
            }

            $query = User::withCount('orders')->where('is_admin', false);

            if ($search = request('q')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $users = $query->latest()->get();

            $filename = 'users_export_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, ['ID', 'Name', 'Email', 'Orders Count', 'Registered At']);
                
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->orders_count ?? 0,
                        $user->created_at->format('Y-m-d H:i:s'),
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

            $query = User::withCount('orders')->where('is_admin', false);

            if ($search = request('q')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $users = $query->latest()->get();
            $totalUsers = $users->count();
            $totalOrders = $users->sum('orders_count');

            $html = view('admin.reports.users', compact('users', 'totalUsers', 'totalOrders'))->render();
            
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->setOption('isRemoteEnabled', true);
            $dompdf->setOption('isHtml5ParserEnabled', true);
            $dompdf->render();
            
            return $dompdf->stream('users_report_' . date('Y-m-d_His') . '.pdf', ['Attachment' => false]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }
}

