<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerContribution;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class PartnerContributionController extends Controller
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
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public function index()
    {
        $this->checkFinancePermission(true); // Allow partners to view

        $query = PartnerContribution::with(['partner', 'creator', 'approver']);

        // Filter by type
        if ($type = request('type')) {
            $query->where('type', $type);
        }

        // Filter by status
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // Filter by partner
        if ($partnerId = request('partner_id')) {
            $query->where('partner_id', $partnerId);
        }

        // Search
        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('partner', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $contributions = $query->latest('contributed_at')->paginate(20)->withQueryString();
        $partners = Partner::where('status', 'active')->orderBy('name')->get();

        return view('admin.financial.contributions', compact('contributions', 'partners'));
    }

    public function create()
    {
        $this->checkFinancePermission();
        $partners = Partner::where('status', 'active')->orderBy('name')->get();
        return view('admin.financial.create', compact('partners'));
    }

    public function store(Request $request)
    {
        $this->checkFinancePermission();

        $validated = $request->validate([
            'partner_id' => ['required', 'exists:partners,id'],
            'type' => ['required', 'in:contribution,withdrawal,profit_distribution'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'max:3'],
            'contributed_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $contribution = PartnerContribution::create([
            'partner_id' => $validated['partner_id'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'KES',
            'contributed_at' => $validated['contributed_at'],
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        ActivityLogService::log('contribution_created', $contribution, $validated);

        return redirect()->route('admin.financial.contributions')
            ->with('success', 'Contribution recorded. Awaiting approval.');
    }

    public function show(PartnerContribution $contribution)
    {
        $this->checkFinancePermission(true); // Allow partners to view
        $contribution->load(['partner', 'creator', 'approver', 'archiver']);
        return view('admin.financial.show', compact('contribution'));
    }

    public function approve(Request $request, PartnerContribution $contribution)
    {
        $this->checkFinancePermission();

        if ($contribution->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending contributions can be approved.');
        }

        $contribution->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        ActivityLogService::log('contribution_approved', $contribution, [
            'approved_by' => auth()->user()->name,
        ]);

        return redirect()->back()
            ->with('success', 'Contribution approved successfully.');
    }

    public function reject(Request $request, PartnerContribution $contribution)
    {
        $this->checkFinancePermission();

        if ($contribution->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending contributions can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string'],
        ]);

        $contribution->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => ($contribution->notes ?? '') . "\n\nRejection reason: " . ($validated['rejection_reason'] ?? 'No reason provided'),
        ]);

        ActivityLogService::log('contribution_rejected', $contribution, [
            'rejected_by' => auth()->user()->name,
            'reason' => $validated['rejection_reason'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Contribution rejected.');
    }

    public function archive(Request $request, PartnerContribution $contribution)
    {
        $this->checkFinancePermission();

        if ($contribution->is_archived) {
            return redirect()->back()
                ->with('error', 'This contribution is already archived.');
        }

        $contribution->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => auth()->id(),
        ]);

        ActivityLogService::log('contribution_archived', $contribution, [
            'archived_by' => auth()->user()->name,
        ]);

        return redirect()->back()
            ->with('success', 'Contribution archived successfully.');
    }
}
