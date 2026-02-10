<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerOwnership;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     * Check if user has permission to access finance/partnership management (view only for partners).
     */
    protected function checkFinancePermission($allowPartners = false)
    {
        $user = auth()->user();
        if ($allowPartners && $user->is_partner) {
            return; // Partners can view
        }
        // Allow Super Admin, Finance Admin and Chairman to access partnership management
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('finance_admin') && !$user->hasAdminRole('chairman')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public function index()
    {
        $this->checkFinancePermission(true); // Allow partners to view

        $query = Partner::with(['ownerships', 'user']);

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($ownership = request('ownership')) {
            if ($ownership === 'with_ownership') {
                $query->whereHas('ownerships');
            } elseif ($ownership === 'without_ownership') {
                $query->whereDoesntHave('ownerships');
            }
        }

        if ($linked = request('linked')) {
            if ($linked === 'with_user') {
                $query->whereNotNull('user_id');
            } elseif ($linked === 'without_user') {
                $query->whereNull('user_id');
            }
        }

        $partners = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        $this->checkFinancePermission();
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $this->checkFinancePermission();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
            'user_id' => ['nullable', 'exists:users,id'],
            'ownership_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'effective_from' => ['nullable', 'date'],
        ]);

        $partner = Partner::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
        ]);

        // Create ownership record if percentage provided
        if (!empty($validated['ownership_percentage'])) {
            PartnerOwnership::create([
                'partner_id' => $partner->id,
                'percentage' => $validated['ownership_percentage'],
                'effective_from' => $validated['effective_from'] ?? now(),
            ]);
        }

        ActivityLogService::logPartner('created', $partner, $validated);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner created successfully.');
    }

    public function show(Partner $partner)
    {
        $this->checkFinancePermission(true); // Allow partners to view

        $partner->load(['ownerships', 'user', 'contributions', 'financialRecords']);
        
        // Get current ownership
        $currentOwnership = $partner->ownerships()
            ->where('effective_from', '<=', now())
            ->where(function($q) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
            })
            ->first();

        // Calculate totals
        $totalContributions = $partner->contributions()
            ->where('type', 'contribution')
            ->where('status', 'approved')
            ->sum('amount');
        
        $totalWithdrawals = $partner->contributions()
            ->whereIn('type', ['withdrawal', 'profit_distribution'])
            ->where('status', 'approved')
            ->sum('amount');

        return view('admin.partners.show', compact('partner', 'currentOwnership', 'totalContributions', 'totalWithdrawals'));
    }

    public function edit(Partner $partner)
    {
        $this->checkFinancePermission();

        $currentOwnership = $partner->ownerships()
            ->where('effective_from', '<=', now())
            ->where(function($q) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
            })
            ->first();

        return view('admin.partners.edit', compact('partner', 'currentOwnership'));
    }

    public function update(Request $request, Partner $partner)
    {
        $this->checkFinancePermission();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
            'user_id' => ['nullable', 'exists:users,id'],
            'ownership_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'effective_from' => ['nullable', 'date'],
        ]);

        $partner->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
        ]);

        // Handle ownership update
        if (!empty($validated['ownership_percentage'])) {
            $currentOwnership = $partner->ownerships()
                ->where('effective_from', '<=', now())
                ->where(function($q) {
                    $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
                })
                ->first();

            if ($currentOwnership) {
                // End current ownership
                $currentOwnership->update([
                    'effective_to' => $validated['effective_from'] ?? now()->subDay(),
                ]);
            }

            // Create new ownership record
            PartnerOwnership::create([
                'partner_id' => $partner->id,
                'percentage' => $validated['ownership_percentage'],
                'effective_from' => $validated['effective_from'] ?? now(),
            ]);
        }

        ActivityLogService::logPartner('updated', $partner, $validated);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner updated successfully.');
    }
}
