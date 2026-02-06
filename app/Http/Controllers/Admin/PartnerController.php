<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerOwnership;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
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
     * Display a listing of partners.
     */
    public function index()
    {
        $this->checkFinanceAdminPermission();
        
        $query = Partner::with(['user', 'ownerships']);

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $partners = $query->latest()->paginate(20)->withQueryString();

        return view('admin.partners.index', compact('partners'));
    }

    /**
     * Show the form for creating a new partner.
     */
    public function create()
    {
        $this->checkFinanceAdminPermission();
        return view('admin.partners.create');
    }

    /**
     * Store a newly created partner.
     */
    public function store(Request $request)
    {
        $this->checkFinanceAdminPermission();
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
            'ownership_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'effective_from' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($validated) {
            $partner = Partner::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'status' => $validated['status'],
                'notes' => $validated['notes'],
            ]);

            PartnerOwnership::create([
                'partner_id' => $partner->id,
                'percentage' => $validated['ownership_percentage'],
                'effective_from' => $validated['effective_from'],
            ]);

            ActivityLogService::logPartner('created', $partner, [
                'name' => $partner->name,
                'ownership_percentage' => $validated['ownership_percentage'],
            ]);
        });

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner created successfully.');
    }

    /**
     * Display the specified partner.
     */
    public function show(Partner $partner)
    {
        $this->checkFinanceAdminPermission();
        $partner->load(['user', 'ownerships', 'contributions' => function ($q) {
            $q->latest()->limit(10);
        }]);

        $currentOwnership = $partner->ownerships()
            ->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', now());
            })
            ->first();

        return view('admin.partners.show', compact('partner', 'currentOwnership'));
    }

    /**
     * Show the form for editing the specified partner.
     */
    public function edit(Partner $partner)
    {
        $this->checkFinanceAdminPermission();
        $currentOwnership = $partner->ownerships()
            ->where('effective_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', now());
            })
            ->first();

        return view('admin.partners.edit', compact('partner', 'currentOwnership'));
    }

    /**
     * Update the specified partner.
     */
    public function update(Request $request, Partner $partner)
    {
        $this->checkFinanceAdminPermission();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);

        $partner->update($validated);

        ActivityLogService::logPartner('updated', $partner, $validated);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner updated successfully.');
    }

    /**
     * Remove the specified partner (soft delete/archive).
     */
    public function destroy(Partner $partner)
    {
        $this->checkFinanceAdminPermission();
        $partner->update(['status' => 'inactive']);

        ActivityLogService::logPartner('archived', $partner);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner archived successfully.');
    }
}
