<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenaltyRate;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenaltyRateController extends Controller
{
    /**
     * Check if user has permission to manage penalty rates.
     */
    protected function checkPermission()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin') && !$user->hasAdminRole('chairman')) {
            abort(403, 'You do not have permission to manage penalty rates.');
        }
    }

    /**
     * Display a listing of penalty rates.
     */
    public function index()
    {
        $this->checkPermission();

        $penaltyRates = PenaltyRate::with('creator')
            ->latest()
            ->get();

        $activeRate = PenaltyRate::where('is_active', true)->first();

        return view('admin.penalty-rates.index', compact('penaltyRates', 'activeRate'));
    }

    /**
     * Show the form for creating a new penalty rate.
     */
    public function create()
    {
        $this->checkPermission();

        return view('admin.penalty-rates.create');
    }

    /**
     * Store a newly created penalty rate.
     */
    public function store(Request $request)
    {
        $this->checkPermission();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0'],
            'calculation_method' => ['required', 'in:percentage_per_day,percentage_of_installment,fixed_amount'],
            'grace_period_days' => ['required', 'integer', 'min:0'],
            'max_penalty_amount' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // If this is being set as active, deactivate all others
        if (!empty($validated['is_active'])) {
            PenaltyRate::where('is_active', true)->update(['is_active' => false]);
        }

        $penaltyRate = PenaltyRate::create([
            'name' => $validated['name'],
            'rate' => $validated['rate'],
            'calculation_method' => $validated['calculation_method'],
            'grace_period_days' => $validated['grace_period_days'],
            'max_penalty_amount' => $validated['max_penalty_amount'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => !empty($validated['is_active']),
            'created_by' => Auth::id(),
        ]);

        ActivityLogService::log('penalty_rate_created', $penaltyRate, $validated);

        return redirect()->route('admin.penalty-rates.index')
            ->with('success', 'Penalty rate created successfully.');
    }

    /**
     * Show the form for editing the specified penalty rate.
     */
    public function edit(PenaltyRate $penaltyRate)
    {
        $this->checkPermission();

        return view('admin.penalty-rates.edit', compact('penaltyRate'));
    }

    /**
     * Update the specified penalty rate.
     */
    public function update(Request $request, PenaltyRate $penaltyRate)
    {
        $this->checkPermission();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0'],
            'calculation_method' => ['required', 'in:percentage_per_day,percentage_of_installment,fixed_amount'],
            'grace_period_days' => ['required', 'integer', 'min:0'],
            'max_penalty_amount' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // If this is being set as active, deactivate all others
        if (!empty($validated['is_active']) && !$penaltyRate->is_active) {
            PenaltyRate::where('is_active', true)->update(['is_active' => false]);
        }

        $penaltyRate->update([
            'name' => $validated['name'],
            'rate' => $validated['rate'],
            'calculation_method' => $validated['calculation_method'],
            'grace_period_days' => $validated['grace_period_days'],
            'max_penalty_amount' => $validated['max_penalty_amount'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => !empty($validated['is_active']),
        ]);

        ActivityLogService::log('penalty_rate_updated', $penaltyRate, $validated);

        return redirect()->route('admin.penalty-rates.index')
            ->with('success', 'Penalty rate updated successfully.');
    }

    /**
     * Activate a penalty rate (deactivates others).
     */
    public function activate(PenaltyRate $penaltyRate)
    {
        $this->checkPermission();

        PenaltyRate::where('is_active', true)->update(['is_active' => false]);
        $penaltyRate->update(['is_active' => true]);

        ActivityLogService::log('penalty_rate_activated', $penaltyRate, []);

        return redirect()->back()
            ->with('success', 'Penalty rate activated successfully.');
    }
}
