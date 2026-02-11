<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PenaltyAdjustment;
use App\Services\ActivityLogService;
use App\Services\ApprovalService;
use App\Services\PenaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenaltyAdjustmentController extends Controller
{
    protected function checkPermission(): void
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return;
        }

        if (! $user->hasAdminRole('treasurer') && ! $user->hasAdminRole('finance_admin') && ! $user->hasAdminRole('chairman')) {
            abort(403, 'You do not have permission to manage penalties.');
        }
    }

    public function index()
    {
        $this->checkPermission();

        $query = PenaltyAdjustment::with(['partner', 'creator', 'approver'])
            ->latest();

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($type = request('type')) {
            $query->where('type', $type);
        }

        if ($partnerId = request('partner_id')) {
            $query->where('partner_id', $partnerId);
        }

        $adjustments = $query->paginate(20)->withQueryString();
        $partners = Partner::orderBy('name')->get();

        return view('admin.penalties.index', compact('adjustments', 'partners'));
    }

    public function create(Request $request)
    {
        $this->checkPermission();
        $partners = Partner::orderBy('name')->get();
        $selectedPartnerId = $request->get('partner_id');
        $type = $request->get('type', 'apply');

        return view('admin.penalties.create', compact('partners', 'selectedPartnerId', 'type'));
    }

    public function store(Request $request)
    {
        $this->checkPermission();

        $type = $request->input('type');

        $rules = [
            'partner_id' => ['required', 'exists:partners,id'],
            'type' => ['required', 'in:apply,waive,pause'],
            'reason' => ['required', 'string', 'max:2000'],
        ];

        if (in_array($type, ['apply', 'waive'], true)) {
            $rules['amount'] = ['required', 'numeric', 'min:0.01'];
            $rules['target_year'] = ['required', 'integer', 'min:2000', 'max:2100'];
            $rules['target_month'] = ['required', 'integer', 'min:1', 'max:12'];
        } else {
            $rules['paused_from'] = ['required', 'date'];
            $rules['paused_to'] = ['nullable', 'date', 'after_or_equal:paused_from'];
        }

        $validated = $request->validate($rules);

        $adjustment = PenaltyAdjustment::create([
            'partner_id' => $validated['partner_id'],
            'type' => $validated['type'],
            'scope' => 'monthly_contribution',
            'target_year' => $validated['target_year'] ?? null,
            'target_month' => $validated['target_month'] ?? null,
            'amount' => $validated['amount'] ?? null,
            'paused_from' => $validated['paused_from'] ?? null,
            'paused_to' => $validated['paused_to'] ?? null,
            'status' => 'pending',
            'reason' => $validated['reason'],
            'created_by' => Auth::id(),
        ]);

        $action = match ($adjustment->type) {
            'apply' => 'penalty.apply',
            'waive' => 'penalty.waive',
            'pause' => 'penalty.pause',
        };

        ApprovalService::ensureApproval($action, $adjustment, Auth::user());

        ActivityLogService::log('penalty_adjustment_created', $adjustment, $validated);

        return redirect()->route('admin.penalties.index')
            ->with('success', 'Penalty action recorded and awaiting approval.');
    }

    public function approve(Request $request, PenaltyAdjustment $penaltyAdjustment)
    {
        $this->checkPermission();

        $user = Auth::user();

        if ($penaltyAdjustment->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending penalty actions can be approved.');
        }

        $action = match ($penaltyAdjustment->type) {
            'apply' => 'penalty.apply',
            'waive' => 'penalty.waive',
            'pause' => 'penalty.pause',
        };

        $approval = ApprovalService::ensureApproval($action, $penaltyAdjustment, $penaltyAdjustment->creator);
        $approval = ApprovalService::approve($user, $approval, $request->input('comment'));

        if ($approval->status === 'approved') {
            $penaltyAdjustment->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            PenaltyService::applyAdjustment($penaltyAdjustment);

            ActivityLogService::log('penalty_adjustment_approved', $penaltyAdjustment, [
                'approved_by' => $user->name,
            ]);

            return redirect()->back()->with('success', 'Penalty action approved.');
        }

        return redirect()->back()->with('success', 'Your approval has been recorded. Waiting for additional approvers.');
    }

    public function reject(Request $request, PenaltyAdjustment $penaltyAdjustment)
    {
        $this->checkPermission();

        if ($penaltyAdjustment->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending penalty actions can be rejected.');
        }

        $reason = $request->input('rejection_reason');

        $penaltyAdjustment->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        ActivityLogService::log('penalty_adjustment_rejected', $penaltyAdjustment, [
            'rejection_reason' => $reason,
        ]);

        return redirect()->back()->with('success', 'Penalty action rejected.');
    }
}

