<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerOwnership;
use App\Models\EntryContribution;
use App\Models\PaymentPlan;
use App\Models\PaymentPlanInstallment;
use App\Models\MemberWallet;
use App\Services\ActivityLogService;
use App\Services\MonthlyContributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        // Allow Super Admin, Finance Admin, Treasurer and Chairman to access partnership management
        if (
            ! $user->isSuperAdmin()
            && ! $user->hasAdminRole('finance_admin')
            && ! $user->hasAdminRole('treasurer')
            && ! $user->hasAdminRole('chairman')
        ) {
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

        if ($onboarding = request('onboarding')) {
            if ($onboarding === 'link_active') {
                $query->whereNotNull('onboarding_token')
                      ->where('onboarding_token_expires_at', '>', now());
            } elseif ($onboarding === 'expired') {
                $query->whereNotNull('onboarding_token')
                      ->where('onboarding_token_expires_at', '<=', now());
            } elseif ($onboarding === 'completed') {
                $query->whereNotNull('biodata_completed_at');
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
            // Entry contribution fields
            'entry_total_amount' => ['required', 'numeric', 'min:0'],
            'entry_initial_deposit' => ['nullable', 'numeric', 'min:0'],
            'entry_payment_method' => ['required', 'in:full,installments'],
            // Payment plan fields (required if installments)
            'installment_count' => ['required_if:entry_payment_method,installments', 'integer', 'min:2', 'max:60'],
            'installment_frequency' => ['required_if:entry_payment_method,installments', 'in:weekly,monthly,quarterly'],
            'installment_start_date' => ['required_if:entry_payment_method,installments', 'date'],
            'installment_terms' => ['nullable', 'string'],
        ]);

        // Generate onboarding token if partner doesn't have a user account yet
        $onboardingToken = null;
        $onboardingTokenExpiresAt = null;
        if (empty($validated['user_id'])) {
            $onboardingToken = Str::random(40);
            $onboardingTokenExpiresAt = now()->addDays(14);
        }

        $partner = Partner::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
            'onboarding_token' => $onboardingToken,
            'onboarding_token_expires_at' => $onboardingTokenExpiresAt,
        ]);

        // Create ownership record if percentage provided
        if (!empty($validated['ownership_percentage'])) {
            PartnerOwnership::create([
                'partner_id' => $partner->id,
                'percentage' => $validated['ownership_percentage'],
                'effective_from' => $validated['effective_from'] ?? now(),
            ]);
        }

        // Create empty wallets
        MemberWallet::create([
            'partner_id' => $partner->id,
            'type' => MemberWallet::TYPE_WELFARE,
            'balance' => 0,
        ]);
        MemberWallet::create([
            'partner_id' => $partner->id,
            'type' => MemberWallet::TYPE_INVESTMENT,
            'balance' => 0,
        ]);

        // Create entry contribution
        $initialDeposit = $validated['entry_initial_deposit'] ?? 0;
        $outstandingBalance = $validated['entry_total_amount'] - $initialDeposit;

        $entryContribution = EntryContribution::create([
            'partner_id' => $partner->id,
            'total_amount' => $validated['entry_total_amount'],
            'initial_deposit' => $initialDeposit,
            'paid_amount' => $initialDeposit,
            'outstanding_balance' => $outstandingBalance,
            'payment_method' => $validated['entry_payment_method'],
            'currency' => 'KES',
            'created_by' => Auth::id(),
        ]);

        // Create payment plan if installments
        if ($validated['entry_payment_method'] === 'installments') {
            $paymentPlan = PaymentPlan::create([
                'entry_contribution_id' => $entryContribution->id,
                'total_installments' => $validated['installment_count'],
                'start_date' => $validated['installment_start_date'],
                'frequency' => $validated['installment_frequency'],
                'terms' => $validated['installment_terms'] ?? null,
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);

            // Generate installments
            $installmentAmount = $outstandingBalance / $validated['installment_count'];
            $startDate = \Carbon\Carbon::parse($validated['installment_start_date']);
            
            for ($i = 1; $i <= $validated['installment_count']; $i++) {
                $dueDate = match($validated['installment_frequency']) {
                    'weekly' => $startDate->copy()->addWeeks($i - 1),
                    'monthly' => $startDate->copy()->addMonths($i - 1),
                    'quarterly' => $startDate->copy()->addMonths(($i - 1) * 3),
                    default => $startDate->copy()->addMonths($i - 1),
                };

                PaymentPlanInstallment::create([
                    'payment_plan_id' => $paymentPlan->id,
                    'installment_number' => $i,
                    'amount' => $installmentAmount,
                    'due_date' => $dueDate,
                    'status' => 'pending',
                ]);
            }
        }

        ActivityLogService::logPartner('created', $partner, $validated);

        $message = 'Partner created successfully.';
        if ($onboardingToken) {
            $message .= ' Onboarding link generated.';
        }
        $message .= ' Entry contribution set.';

        return redirect()->route('admin.partners.index')
            ->with('success', $message);
    }

    public function show(Partner $partner)
    {
        $this->checkFinancePermission(true); // Allow partners to view

        $partner->load(['ownerships', 'user', 'contributions', 'financialRecords', 'entryContribution.paymentPlan.installments']);
        
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

        // Get entry contribution data
        $entryContribution = $partner->entryContribution;
        $paymentPlan = $entryContribution?->paymentPlan;
        $installments = $paymentPlan?->installments ?? collect();
        
        // Update installment statuses and calculate penalties
        $installments->each(function ($installment) {
            $installment->updateStatus();
        });
        
        // Refresh installments after status updates
        $installments = $paymentPlan?->installments()->orderBy('installment_number')->get() ?? collect();
        
        // Calculate payment statistics
        $overdueInstallments = $installments->whereIn('status', ['overdue', 'missed']);
        $upcomingInstallments = $installments->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(30))
            ->sortBy('due_date');

        // Monthly contributions (55,000 per month split welfare/investment)
        $monthlyContribution = MonthlyContributionService::forPartner($partner);

        return view('admin.partners.show', compact(
            'partner', 
            'currentOwnership', 
            'totalContributions', 
            'totalWithdrawals',
            'entryContribution',
            'paymentPlan',
            'installments',
            'overdueInstallments',
            'upcomingInstallments',
            'monthlyContribution'
        ));
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
