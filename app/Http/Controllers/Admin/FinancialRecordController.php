<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use App\Models\FinancialRecordDocument;
use App\Models\Order;
use App\Models\Partner;
use App\Models\Project;
use App\Services\ActivityLogService;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinancialRecordController extends Controller
{
    /**
     * Check if user has permission to access finance management (view only for partners).
     */
    protected function checkFinancePermission($allowPartners = false)
    {
        $user = auth()->user();
        if ($allowPartners && $user->is_partner) {
            return; // Partners can view
        }
        if (! $user->hasPermission('financial.records.view')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public function index()
    {
        $this->checkFinancePermission(true); // Allow partners to view

        $query = FinancialRecord::with(['order', 'partner', 'creator', 'approver', 'documents']);

        // Filter by type
        if ($type = request('type')) {
            $query->where('type', $type);
        }

        // Filter by status
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // Filter archived
        if (request('archived') === '1') {
            $query->where('is_archived', true);
        } else {
            $query->where('is_archived', false);
        }

        // Search
        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $records = $query->latest('occurred_at')->paginate(20)->withQueryString();
        $partners = Partner::where('status', 'active')->orderBy('name')->get();

        // Calculate summary statistics
        $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('total');
        $totalContributions = \App\Models\PartnerContribution::where('type', 'contribution')
            ->where('status', 'approved')
            ->sum('amount');
        $totalExpenses = FinancialRecord::where('type', 'expense')
            ->where('status', 'approved')
            ->where('is_archived', false)
            ->sum('amount');
        $pendingApprovals = FinancialRecord::where('status', 'pending_approval')
            ->where('is_archived', false)
            ->count() + \App\Models\PartnerContribution::where('status', 'pending')
            ->where('is_archived', false)
            ->count();

        return view('admin.financial.index', compact('records', 'partners', 'totalRevenue', 'totalContributions', 'totalExpenses', 'pendingApprovals'));
    }

    public function create()
    {
        if (! auth()->user()->hasPermission('financial.records.create')) {
            abort(403, 'You do not have permission to create financial records.');
        }
        $partners = Partner::where('status', 'active')->orderBy('name')->get();
        $orders = Order::latest()->take(50)->get(['id', 'order_number', 'total', 'created_at']);
        $projects = Project::orderBy('name')->get(['id', 'name']);
        return view('admin.financial.create', compact('partners', 'orders', 'projects'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->hasPermission('financial.records.create')) {
            abort(403, 'You do not have permission to create financial records.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:expense,adjustment,other_income'],
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'max:3'],
            'paid_from' => ['nullable', 'string', 'max:255'],
            'occurred_at' => ['required', 'date'],
            'description' => ['required', 'string'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'partner_id' => ['nullable', 'exists:partners,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // 10MB max
        ]);

        $record = FinancialRecord::create([
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'KES',
            'paid_from' => $validated['paid_from'] ?? null,
            'occurred_at' => $validated['occurred_at'],
            'description' => $validated['description'],
            'order_id' => $validated['order_id'] ?? null,
            'partner_id' => $validated['partner_id'] ?? null,
            'project_id' => $validated['project_id'] ?? null,
            'status' => 'pending_approval',
            'created_by' => auth()->id(),
        ]);

        // Ensure an approval workflow exists for this record
        ApprovalService::ensureApproval('financial_record.approve', $record, auth()->user());

        // Handle receipt/uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('financial-receipts', 'public');
                
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

        ActivityLogService::logFinancial('created', $record, $validated);

        return redirect()->route('admin.financial.index')
            ->with('success', 'Financial record created. Awaiting approval.');
    }

    public function show(FinancialRecord $financialRecord)
    {
        $this->checkFinancePermission(true); // Allow partners to view
        $financialRecord->load(['order', 'partner', 'creator', 'approver', 'archiver', 'documents.uploader']);
        return view('admin.financial.show', compact('financialRecord'));
    }

    public function approve(Request $request, FinancialRecord $financialRecord)
    {
        if (! auth()->user()->hasPermission('financial.records.approve')) {
            abort(403, 'You do not have permission to approve financial records.');
        }

        if ($financialRecord->status !== 'pending_approval') {
            return redirect()->back()
                ->with('error', 'Only pending records can be approved.');
        }

        $approval = ApprovalService::ensureApproval('financial_record.approve', $financialRecord, $financialRecord->creator);
        $approval = ApprovalService::approve(auth()->user(), $approval, $request->input('comment'));

        // When the aggregated approval passes its threshold, mark the record as approved
        if ($approval->status === 'approved') {
            $financialRecord->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            ActivityLogService::logFinancial('approved', $financialRecord, [
                'approved_by' => auth()->user()->name,
            ]);

            return redirect()->back()
                ->with('success', 'Financial record approved successfully.');
        }

        return redirect()->back()
            ->with('success', 'Your approval has been recorded. Waiting for additional approvers.');
    }

    public function reject(Request $request, FinancialRecord $financialRecord)
    {
        if (! auth()->user()->hasPermission('financial.records.approve')) {
            abort(403, 'You do not have permission to reject financial records.');
        }

        if ($financialRecord->status !== 'pending_approval') {
            return redirect()->back()
                ->with('error', 'Only pending records can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string'],
        ]);

        $financialRecord->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'description' => $financialRecord->description . "\n\nRejection reason: " . ($validated['rejection_reason'] ?? 'No reason provided'),
        ]);

        ActivityLogService::logFinancial('rejected', $financialRecord, [
            'rejected_by' => auth()->user()->name,
            'reason' => $validated['rejection_reason'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Financial record rejected.');
    }

    public function archive(Request $request, FinancialRecord $financialRecord)
    {
        $this->checkFinancePermission();

        if ($financialRecord->is_archived) {
            return redirect()->back()
                ->with('error', 'This record is already archived.');
        }

        $financialRecord->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => auth()->id(),
        ]);

        ActivityLogService::logFinancial('archived', $financialRecord, [
            'archived_by' => auth()->user()->name,
        ]);

        return redirect()->back()
            ->with('success', 'Financial record archived successfully.');
    }
}
