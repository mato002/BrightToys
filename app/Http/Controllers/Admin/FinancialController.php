<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use App\Models\FinancialRecordDocument;
use App\Models\PartnerContribution;
use App\Models\Order;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinancialController extends Controller
{
    /**
     * Check if user has permission to access financial management.
     */
    protected function checkFinanceAdminPermission()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    /**
     * Display financial dashboard with summaries.
     */
    public function index()
    {
        $this->checkFinanceAdminPermission();
        
        $query = FinancialRecord::with(['creator', 'approver', 'order', 'partner']);

        // Filter by type
        if ($type = request('type')) {
            $query->where('type', $type);
        }

        // Filter by status
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // Filter by date range
        if ($from = request('from')) {
            $query->whereDate('occurred_at', '>=', $from);
        }
        if ($to = request('to')) {
            $query->whereDate('occurred_at', '<=', $to);
        }

        $records = $query->where('is_archived', false)
            ->latest('occurred_at')
            ->paginate(20)
            ->withQueryString();

        // Calculate summaries
        $totalRevenue = Order::where('status', 'completed')
            ->sum('total');
        
        $totalContributions = PartnerContribution::where('status', 'approved')
            ->where('type', 'contribution')
            ->where('is_archived', false)
            ->sum('amount');
        
        $totalExpenses = FinancialRecord::where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->sum('amount');

        $pendingApprovals = FinancialRecord::where('status', 'pending_approval')
            ->where('is_archived', false)
            ->count();

        return view('admin.financial.index', compact(
            'records',
            'totalRevenue',
            'totalContributions',
            'totalExpenses',
            'pendingApprovals'
        ));
    }

    /**
     * Show the form for creating a new financial record (expense).
     */
    public function create()
    {
        $this->checkFinanceAdminPermission();
        $partners = \App\Models\Partner::where('status', 'active')->get();
        return view('admin.financial.create', compact('partners'));
    }

    /**
     * Store a newly created financial record.
     */
    public function store(Request $request)
    {
        $this->checkFinanceAdminPermission();
        
        $validated = $request->validate([
            'type' => ['required', 'in:expense,adjustment,other_income'],
            'category' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'occurred_at' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'partner_id' => ['nullable', 'exists:partners,id'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        $record = FinancialRecord::create([
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'occurred_at' => $validated['occurred_at'],
            'description' => $validated['description'],
            'partner_id' => $validated['partner_id'] ?? null,
            'status' => 'pending_approval',
            'created_by' => auth()->id(),
        ]);

        // Handle document uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('financial-documents', 'public');
                
                FinancialRecordDocument::create([
                    'financial_record_id' => $record->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        ActivityLogService::logFinancial('created', $record, [
            'type' => $record->type,
            'amount' => $record->amount,
        ]);

        return redirect()->route('admin.financial.index')
            ->with('success', 'Financial record created and pending approval.');
    }

    /**
     * Display the specified financial record.
     */
    public function show(FinancialRecord $financial)
    {
        $this->checkFinanceAdminPermission();
        $financial->load(['creator', 'approver', 'order', 'partner', 'documents.uploader']);
        return view('admin.financial.show', compact('financial'));
    }

    /**
     * Approve a financial record.
     */
    public function approve(FinancialRecord $financial)
    {
        $this->checkFinanceAdminPermission();
        
        if ($financial->status !== 'pending_approval') {
            return redirect()->back()
                ->with('error', 'This record cannot be approved.');
        }

        $financial->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        ActivityLogService::logFinancial('approved', $financial);

        return redirect()->back()
            ->with('success', 'Financial record approved.');
    }

    /**
     * Reject a financial record.
     */
    public function reject(FinancialRecord $financial)
    {
        $this->checkFinanceAdminPermission();
        if ($financial->status !== 'pending_approval') {
            return redirect()->back()
                ->with('error', 'This record cannot be rejected.');
        }

        $financial->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        ActivityLogService::logFinancial('rejected', $financial);

        return redirect()->back()
            ->with('success', 'Financial record rejected.');
    }

    /**
     * Archive a financial record (soft delete).
     */
    public function archive(FinancialRecord $financial)
    {
        $this->checkFinanceAdminPermission();
        $financial->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => auth()->id(),
        ]);

        ActivityLogService::logFinancial('archived', $financial);

        return redirect()->back()
            ->with('success', 'Financial record archived.');
    }

    /**
     * Show contributions management.
     */
    public function contributions()
    {
        $this->checkFinanceAdminPermission();
        
        $query = PartnerContribution::with(['partner', 'creator', 'approver']);

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $contributions = $query->where('is_archived', false)
            ->latest('contributed_at')
            ->paginate(20)
            ->withQueryString();

        $partners = \App\Models\Partner::where('status', 'active')->get();
        return view('admin.financial.contributions', compact('contributions', 'partners'));
    }

    /**
     * Store a new contribution.
     */
    public function storeContribution(Request $request)
    {
        $this->checkFinanceAdminPermission();
        
        $validated = $request->validate([
            'partner_id' => ['required', 'exists:partners,id'],
            'type' => ['required', 'in:contribution,withdrawal,profit_distribution'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'contributed_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $contribution = PartnerContribution::create([
            'partner_id' => $validated['partner_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'contributed_at' => $validated['contributed_at'],
            'reference' => $validated['reference'],
            'notes' => $validated['notes'],
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        ActivityLogService::log('contribution_created', $contribution, [
            'partner_id' => $contribution->partner_id,
            'amount' => $contribution->amount,
        ]);

        return redirect()->route('admin.financial.contributions')
            ->with('success', 'Contribution recorded and pending approval.');
    }

    /**
     * Approve a contribution.
     */
    public function approveContribution(PartnerContribution $contribution)
    {
        $this->checkFinanceAdminPermission();
        if ($contribution->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This contribution cannot be approved.');
        }

        $contribution->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        ActivityLogService::log('contribution_approved', $contribution);

        return redirect()->back()
            ->with('success', 'Contribution approved.');
    }
}
