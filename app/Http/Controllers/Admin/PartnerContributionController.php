<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerContribution;
use App\Services\ActivityLogService;
use App\Services\ApprovalService;
use App\Services\WalletService;
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
        // Use permissions and also allow chairman as finance leader
        if (! $user->hasPermission('financial.records.view') && ! $user->hasAdminRole('chairman')) {
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

        // Filter by fund type (welfare / investment)
        if ($fundType = request('fund_type')) {
            $query->where('fund_type', $fundType);
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
            'fund_type' => ['required_if:type,contribution', 'in:welfare,investment'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'max:3'],
            'contributed_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $contribution = PartnerContribution::create([
            'partner_id' => $validated['partner_id'],
            'type' => $validated['type'],
            'fund_type' => $validated['type'] === 'contribution'
                ? ($validated['fund_type'] ?? 'investment')
                : 'investment',
            'amount' => $validated['amount'],
            'currency' => $validated['currency'] ?? 'KES',
            'contributed_at' => $validated['contributed_at'],
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        // Ensure approval workflow exists for this contribution
        ApprovalService::ensureApproval('contribution.approve', $contribution, auth()->user());

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

        $user = auth()->user();
        if (! $user->hasPermission('contributions.approve')) {
            abort(403, 'You do not have permission to approve contributions.');
        }

        if ($contribution->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending contributions can be approved.');
        }

        // Use approval workflow (treasurer + chairman, etc.)
        $approval = ApprovalService::ensureApproval('contribution.approve', $contribution, $contribution->creator);
        $approval = ApprovalService::approve($user, $approval, $request->input('comment'));

        if ($approval->status === 'approved') {
            $contribution->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            // Post to member wallets / ledger
            WalletService::applyContribution($contribution);

            ActivityLogService::log('contribution_approved', $contribution, [
                'approved_by' => $user->name,
            ]);

            return redirect()->back()
                ->with('success', 'Contribution approved successfully.');
        }

        return redirect()->back()
            ->with('success', 'Your approval has been recorded. Waiting for additional approvers.');
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
