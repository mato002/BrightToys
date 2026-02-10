<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Partner;
use App\Models\User;
use App\Models\MemberWallet;
use App\Services\ActivityLogService;
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

        $query = Member::with(['partner', 'user']);

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

        if ($partnerId = request('partner_id')) {
            $query->where('partner_id', $partnerId);
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

        $partners = Partner::orderBy('name')->get();

        return view('admin.members.create', compact('partners'));
    }

    public function store(Request $request)
    {
        $this->ensureChairperson();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'partner_id' => ['nullable', 'exists:partners,id'],
        ]);

        $onboardingToken = Str::random(40);

        $member = Member::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'partner_id' => $validated['partner_id'] ?? null,
            'status' => 'pending',
            'onboarding_token' => $onboardingToken,
            'onboarding_token_expires_at' => now()->addDays(14),
        ]);

        // Create empty wallets
        MemberWallet::create([
            'member_id' => $member->id,
            'type' => MemberWallet::TYPE_WELFARE,
            'balance' => 0,
        ]);
        MemberWallet::create([
            'member_id' => $member->id,
            'type' => MemberWallet::TYPE_INVESTMENT,
            'balance' => 0,
        ]);

        ActivityLogService::log('member_created', $member, $validated);

        // TODO: send secure onboarding link via mail/SMS using $onboardingToken

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Member registered. Onboarding link generated.');
    }

    public function show(Member $member)
    {
        $this->ensureChairperson();

        $member->load(['partner', 'user', 'wallets']);

        $welfareWallet = $member->wallets->firstWhere('type', MemberWallet::TYPE_WELFARE);
        $investmentWallet = $member->wallets->firstWhere('type', MemberWallet::TYPE_INVESTMENT);

        return view('admin.members.show', compact('member', 'welfareWallet', 'investmentWallet'));
    }
}

