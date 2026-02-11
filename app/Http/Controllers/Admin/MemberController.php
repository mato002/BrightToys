<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner; // Members and Partners are the same - using Partner model
use App\Models\User;
use App\Models\MemberWallet;
use App\Models\EntryContribution;
use App\Models\PaymentPlan;
use App\Models\PaymentPlanInstallment;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    /**
     * Only Chairperson (and optionally Super Admin) can manage members.
     */
    protected function ensureChairperson(): void
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return;
        }

        if (! $user->hasAdminRole('chairman')) {
            abort(403, 'Only the Chairperson can manage members.');
        }
    }

    public function index()
    {
        $this->ensureChairperson();

        $query = Partner::with(['user', 'approvalDocument']);

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

        $members = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        $this->ensureChairperson();

        // Get meeting minutes and resolution documents for linking
        $approvalDocuments = \App\Models\Document::whereIn('type', ['meeting_minutes', 'resolution'])
            ->where('is_archived', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.members.create', compact('approvalDocuments'));
    }

    public function store(Request $request)
    {
        $this->ensureChairperson();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'approval_document_id' => ['required', 'exists:documents,id'],
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

        $onboardingToken = Str::random(40);

        // Create partner (member and partner are the same)
        $partner = Partner::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'approval_document_id' => $validated['approval_document_id'],
            'status' => 'pending',
            'onboarding_token' => $onboardingToken,
            'onboarding_token_expires_at' => now()->addDays(14),
        ]);

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

            // Calculate installment amount (remaining balance divided by installments)
            $installmentAmount = $outstandingBalance / $validated['installment_count'];

            // Create installments
            $startDate = Carbon::parse($validated['installment_start_date']);
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

        ActivityLogService::log('member_created', $partner, $validated);

        // TODO: send secure onboarding link via mail/SMS using $onboardingToken

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Member registered. Onboarding link generated.');
    }

    public function show(Partner $member)
    {
        $this->ensureChairperson();

        $member->load(['user', 'wallets', 'approvalDocument']);

        $welfareWallet = $member->wallets->firstWhere('type', MemberWallet::TYPE_WELFARE);
        $investmentWallet = $member->wallets->firstWhere('type', MemberWallet::TYPE_INVESTMENT);

        return view('admin.members.show', compact('member', 'welfareWallet', 'investmentWallet'));
    }
}

