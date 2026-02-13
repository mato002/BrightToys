<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyContributionPenaltyRate;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MonthlyContributionPenaltyRateController extends Controller
{
    /**
     * Check if user has permission to manage penalty rates.
     */
    protected function checkPermission()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin') && !$user->hasAdminRole('chairman')) {
            abort(403, 'You do not have permission to manage monthly contribution penalty rates.');
        }
    }

    /**
     * Display a listing of penalty rates.
     */
    public function index()
    {
        $this->checkPermission();

        $penaltyRates = MonthlyContributionPenaltyRate::with('creator')
            ->orderBy('effective_from', 'desc')
            ->get();

        $currentRate = MonthlyContributionPenaltyRate::getActiveRateForDate();
        $nextMonthRate = MonthlyContributionPenaltyRate::getNextMonthRate();

        return view('admin.monthly-contribution-penalty-rates.index', compact('penaltyRates', 'currentRate', 'nextMonthRate'));
    }

    /**
     * Show the form for creating a new penalty rate.
     */
    public function create()
    {
        $this->checkPermission();

        // Default effective_from is start of next month
        $defaultEffectiveFrom = Carbon::now()->addMonth()->startOfMonth();

        return view('admin.monthly-contribution-penalty-rates.create', compact('defaultEffectiveFrom'));
    }

    /**
     * Store a newly created penalty rate.
     */
    public function store(Request $request)
    {
        $this->checkPermission();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0', 'max:1'], // Rate as decimal (0.10 = 10%)
            'effective_from' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $effectiveFrom = Carbon::parse($validated['effective_from'])->startOfDay();

        // Close any existing active rate that overlaps
        MonthlyContributionPenaltyRate::where('is_active', true)
            ->where(function ($query) use ($effectiveFrom) {
                $query->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', $effectiveFrom);
            })
            ->update(['effective_to' => $effectiveFrom->copy()->subDay(), 'is_active' => false]);

        $penaltyRate = MonthlyContributionPenaltyRate::create([
            'name' => $validated['name'],
            'rate' => $validated['rate'],
            'effective_from' => $effectiveFrom,
            'effective_to' => null, // Current rate has no end date
            'is_active' => true,
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        ActivityLogService::log('monthly_contribution_penalty_rate_created', $penaltyRate, $validated);

        return redirect()->route('admin.monthly-contribution-penalty-rates.index')
            ->with('success', 'Monthly contribution penalty rate created successfully. It will take effect from ' . $effectiveFrom->format('F Y') . '.');
    }

    /**
     * Show the form for editing the specified penalty rate.
     */
    public function edit(MonthlyContributionPenaltyRate $penalty_rate)
    {
        $this->checkPermission();

        return view('admin.monthly-contribution-penalty-rates.edit', compact('penalty_rate'));
    }

    /**
     * Update the specified penalty rate.
     */
    public function update(Request $request, MonthlyContributionPenaltyRate $penalty_rate)
    {
        $this->checkPermission();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0', 'max:1'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after:effective_from'],
            'description' => ['nullable', 'string'],
        ]);

        $effectiveFrom = Carbon::parse($validated['effective_from'])->startOfDay();
        $effectiveTo = isset($validated['effective_to']) ? Carbon::parse($validated['effective_to'])->endOfDay() : null;

        // If this is an active rate and we're changing the effective_from date, handle overlaps
        if ($penalty_rate->is_active && $effectiveFrom->ne($penalty_rate->effective_from)) {
            // Close overlapping rates
            MonthlyContributionPenaltyRate::where('id', '!=', $penalty_rate->id)
                ->where('is_active', true)
                ->where(function ($query) use ($effectiveFrom, $effectiveTo) {
                    $query->where(function ($q) use ($effectiveFrom) {
                        $q->where('effective_from', '<=', $effectiveFrom)
                          ->where(function ($q2) use ($effectiveFrom) {
                              $q2->whereNull('effective_to')
                                 ->orWhere('effective_to', '>=', $effectiveFrom);
                          });
                    });
                    if ($effectiveTo) {
                        $query->orWhere(function ($q) use ($effectiveTo) {
                            $q->where('effective_from', '<=', $effectiveTo)
                              ->where(function ($q2) use ($effectiveTo) {
                                  $q2->whereNull('effective_to')
                                     ->orWhere('effective_to', '>=', $effectiveTo);
                              });
                        });
                    }
                })
                ->update(['effective_to' => $effectiveFrom->copy()->subDay(), 'is_active' => false]);
        }

        $penalty_rate->update([
            'name' => $validated['name'],
            'rate' => $validated['rate'],
            'effective_from' => $effectiveFrom,
            'effective_to' => $effectiveTo,
            'description' => $validated['description'] ?? null,
        ]);

        ActivityLogService::log('monthly_contribution_penalty_rate_updated', $penalty_rate, $validated);

        return redirect()->route('admin.monthly-contribution-penalty-rates.index')
            ->with('success', 'Monthly contribution penalty rate updated successfully.');
    }
}
