@extends('layouts.admin')

@section('page_title', 'Partner Details')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">{{ $partner->name }}</h1>
            <p class="text-xs text-slate-500">Partner details and ownership information.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.partners.edit', $partner) }}"
               class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Edit Partner
            </a>
            <a href="{{ route('admin.partners.index') }}" class="text-sm text-slate-600 hover:text-slate-800">
                Back to list
            </a>
        </div>
    </div>

    {{-- Penalty Management --}}
    <div class="mt-6 bg-white border border-slate-100 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-900">Penalties & Waivers</h2>
            <a href="{{ route('admin.penalties.create', ['partner_id' => $partner->id]) }}"
               class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-3 py-1.5 rounded">
                New Penalty Action
            </a>
        </div>
        <p class="text-[11px] text-slate-500 mb-3">
            Use this to apply additional penalties, waive penalties, or pause penalty accumulation for this member.
            All actions require approval according to configured rules and are fully logged.
        </p>
        <a href="{{ route('admin.penalties.index', ['partner_id' => $partner->id]) }}"
           class="text-[11px] text-emerald-600 hover:text-emerald-700 underline">
            View penalty history for this partner
        </a>
    </div>

    {{-- Monthly Contributions (55,000 per month) --}}
    @if(isset($monthlyContribution))
    @php
        $mc = $monthlyContribution;
        $statusClass = match($mc['status']) {
            'on_time' => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
            'late' => 'bg-amber-50 text-amber-700 border border-amber-100',
            'critical' => 'bg-red-50 text-red-700 border border-red-100',
            default => 'bg-slate-50 text-slate-700 border border-slate-100',
        };
    @endphp
    <div class="mt-6 bg-white border border-slate-100 rounded-lg p-4">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Monthly Contributions (Existing Members)</h2>
                <p class="text-[11px] text-slate-500">
                    Expected Ksh {{ number_format($mc['config']['monthly_total'], 0) }} per month 
                    (Welfare: {{ number_format($mc['config']['monthly_welfare'], 0) }}, Investment: {{ number_format($mc['config']['monthly_investment'], 0) }}),
                    Penalty: {{ $mc['config']['penalty_rate'] * 100 }}% on arrears.
                </p>
            </div>
            <div class="text-right text-xs">
                <span class="inline-flex items-center px-2 py-1 rounded-full {{ $statusClass }}">
                    @if($mc['status'] === 'on_time') On time
                    @elseif($mc['status'] === 'late') Late
                    @else Critical arrears
                    @endif
                </span>
                <div class="mt-1 text-[11px] text-slate-500">
                    Arrears: <span class="font-semibold text-amber-700">Ksh {{ number_format($mc['total_arrears'], 0) }}</span> ·
                    Penalties: <span class="font-semibold text-red-700">Ksh {{ number_format($mc['total_penalty'], 0) }}</span> ·
                    Months in arrears: <span class="font-semibold">{{ $mc['months_in_arrears'] }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Month</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Expected</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Paid</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Arrear</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Penalty (10%)</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach(collect($mc['monthly'])->take(6) as $month)
                        @php
                            $rowStatus = 'On time';
                            $rowClass = 'bg-white';
                            if($month['arrear'] > 0 && $month['is_past']) {
                                $rowStatus = 'In arrears';
                                $rowClass = 'bg-amber-50';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="px-3 py-2 font-medium text-slate-900">{{ $month['label'] }}</td>
                            <td class="px-3 py-2">Ksh {{ number_format($month['expected'], 0) }}</td>
                            <td class="px-3 py-2">Ksh {{ number_format($month['paid'], 0) }}</td>
                            <td class="px-3 py-2 {{ $month['arrear'] > 0 && $month['is_past'] ? 'text-amber-700 font-semibold' : 'text-slate-700' }}">
                                Ksh {{ number_format($month['arrear'], 0) }}
                            </td>
                            <td class="px-3 py-2 {{ $month['penalty'] > 0 ? 'text-red-700 font-semibold' : 'text-slate-400' }}">
                                @if($month['penalty'] > 0)
                                    Ksh {{ number_format($month['penalty'], 0) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                                    @if($month['arrear'] === 0) bg-emerald-100 text-emerald-700
                                    @elseif($month['is_past']) bg-amber-100 text-amber-700
                                    @else bg-slate-100 text-slate-700 @endif">
                                    {{ $rowStatus }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Partner Information</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Name</dt>
                    <dd class="font-medium text-slate-900">{{ $partner->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Email</dt>
                    <dd class="text-slate-700">{{ $partner->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Phone</dt>
                    <dd class="text-slate-700">{{ $partner->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Status</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                            {{ $partner->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($partner->status) }}
                        </span>
                    </dd>
                </div>
                @if($partner->notes)
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Notes</dt>
                    <dd class="text-slate-700">{{ $partner->notes }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs text-slate-500 mb-1">Linked User Account</dt>
                    <dd class="text-slate-700">
                        @if($partner->user)
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium bg-blue-50 text-blue-700">
                                {{ $partner->user->email }}
                            </span>
                        @else
                            <span class="text-slate-400">Not linked</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white border border-slate-100 rounded-lg p-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Current Ownership</h2>
            @if($currentOwnership)
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-slate-500 mb-1">Ownership Percentage</dt>
                        <dd class="font-semibold text-emerald-600 text-lg">{{ number_format($currentOwnership->percentage, 2) }}%</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 mb-1">Effective From</dt>
                        <dd class="text-slate-700">{{ $currentOwnership->effective_from->format('d M Y') }}</dd>
                    </div>
                    @if($currentOwnership->effective_to)
                    <div>
                        <dt class="text-xs text-slate-500 mb-1">Effective To</dt>
                        <dd class="text-slate-700">{{ $currentOwnership->effective_to->format('d M Y') }}</dd>
                    </div>
                    @endif
                </dl>
            @else
                <p class="text-sm text-slate-500">No ownership information available.</p>
            @endif
        </div>
    </div>

    <div class="mt-6 bg-white border border-slate-100 rounded-lg p-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-4">Onboarding Status</h2>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-xs text-slate-500 mb-1">Biodata Completion</dt>
                <dd class="text-slate-700">
                    @if($partner->biodata_completed_at)
                        <span class="text-emerald-600 font-medium">Completed {{ $partner->biodata_completed_at->format('d M Y') }}</span>
                    @else
                        <span class="text-amber-600">Not completed</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs text-slate-500 mb-1">ID Verification</dt>
                <dd class="text-slate-700">
                    @if($partner->id_verified_at)
                        <span class="text-emerald-600 font-medium">Verified {{ $partner->id_verified_at->format('d M Y') }}</span>
                    @else
                        <span class="text-amber-600">Not verified</span>
                    @endif
                </dd>
            </div>
            @if($partner->onboarding_token)
            <div>
                <dt class="text-xs text-slate-500 mb-1">Onboarding Link</dt>
                <dd class="text-slate-700">
                    @if($partner->onboarding_token_expires_at && $partner->onboarding_token_expires_at->isFuture())
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   value="{{ url('/onboarding/'.$partner->onboarding_token) }}" 
                                   readonly
                                   class="flex-1 border border-slate-200 rounded px-3 py-2 text-xs bg-slate-50">
                            <button type="button"
                                    onclick="navigator.clipboard.writeText('{{ url('/onboarding/'.$partner->onboarding_token) }}'); alert('Onboarding link copied to clipboard!');"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded">
                                Copy Link
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1">Link expires {{ $partner->onboarding_token_expires_at->diffForHumans() }}</p>
                    @elseif($partner->onboarding_token_expires_at && $partner->onboarding_token_expires_at->isPast())
                        <span class="text-red-600">Link expired on {{ $partner->onboarding_token_expires_at->format('d M Y') }}</span>
                    @else
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   value="{{ url('/onboarding/'.$partner->onboarding_token) }}" 
                                   readonly
                                   class="flex-1 border border-slate-200 rounded px-3 py-2 text-xs bg-slate-50">
                            <button type="button"
                                    onclick="navigator.clipboard.writeText('{{ url('/onboarding/'.$partner->onboarding_token) }}'); alert('Onboarding link copied to clipboard!');"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded">
                                Copy Link
                            </button>
                        </div>
                    @endif
                </dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Entry Contribution Section --}}
    @if($entryContribution)
    <div class="mt-6 bg-white border border-slate-100 rounded-lg p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-900">Entry Contribution</h2>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasAdminRole('finance_admin') || auth()->user()->hasAdminRole('chairman') || auth()->user()->hasAdminRole('treasurer'))
                <button onclick="document.getElementById('payment_modal').classList.remove('hidden')"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded">
                    Record Payment
                </button>
            @endif
        </div>

        <div class="grid md:grid-cols-4 gap-4 mb-4">
            <div class="bg-slate-50 rounded-lg p-3">
                <div class="text-xs text-slate-500 mb-1">Total Amount</div>
                <div class="text-lg font-semibold text-slate-900">{{ $entryContribution->currency }} {{ number_format($entryContribution->total_amount, 2) }}</div>
            </div>
            <div class="bg-emerald-50 rounded-lg p-3">
                <div class="text-xs text-emerald-600 mb-1">Paid Amount</div>
                <div class="text-lg font-semibold text-emerald-700">{{ $entryContribution->currency }} {{ number_format($entryContribution->paid_amount, 2) }}</div>
            </div>
            <div class="bg-amber-50 rounded-lg p-3">
                <div class="text-xs text-amber-600 mb-1">Outstanding</div>
                <div class="text-lg font-semibold text-amber-700">{{ $entryContribution->currency }} {{ number_format($entryContribution->outstanding_balance, 2) }}</div>
            </div>
            <div class="bg-blue-50 rounded-lg p-3">
                <div class="text-xs text-blue-600 mb-1">Progress</div>
                <div class="text-lg font-semibold text-blue-700">
                    {{ $entryContribution->total_amount > 0 ? number_format(($entryContribution->paid_amount / $entryContribution->total_amount) * 100, 1) : 0 }}%
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="w-full bg-slate-200 rounded-full h-2">
                <div class="bg-emerald-600 h-2 rounded-full transition-all" 
                     style="width: {{ $entryContribution->total_amount > 0 ? ($entryContribution->paid_amount / $entryContribution->total_amount) * 100 : 0 }}%"></div>
            </div>
        </div>

        <div class="text-xs text-slate-600 mb-4">
            <div class="grid md:grid-cols-2 gap-2">
                <div><strong>Payment Method:</strong> {{ ucfirst($entryContribution->payment_method) }}</div>
                <div><strong>Initial Deposit:</strong> {{ $entryContribution->currency }} {{ number_format($entryContribution->initial_deposit, 2) }}</div>
            </div>
        </div>

        {{-- Payment Plan Installments --}}
        @if($paymentPlan && $installments->count() > 0)
        <div class="mt-4 border-t border-slate-200 pt-4">
            <h3 class="text-xs font-semibold text-slate-900 mb-3">Payment Plan Installments</h3>
            
            @if($overdueInstallments->count() > 0)
            <div class="mb-3 p-2 bg-red-50 border border-red-200 rounded text-xs text-red-700">
                <strong>⚠️ {{ $overdueInstallments->count() }} overdue installment(s)</strong>
            </div>
            @endif

            @if($upcomingInstallments->count() > 0)
            <div class="mb-3 p-2 bg-amber-50 border border-amber-200 rounded text-xs text-amber-700">
                <strong>📅 {{ $upcomingInstallments->count() }} upcoming installment(s) in next 30 days</strong>
            </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">#</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Due Date</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Amount</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Paid</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Penalty</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Status</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($installments as $installment)
                        <tr class="{{ $installment->status === 'overdue' || $installment->status === 'missed' ? 'bg-red-50' : ($installment->status === 'paid' ? 'bg-emerald-50' : '') }}">
                            <td class="px-3 py-2 font-medium">{{ $installment->installment_number }}</td>
                            <td class="px-3 py-2">
                                {{ $installment->due_date->format('d M Y') }}
                                @if($installment->due_date->isPast() && $installment->status !== 'paid')
                                    <span class="text-red-600">({{ $installment->due_date->diffForHumans() }})</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 font-medium">{{ $entryContribution->currency }} {{ number_format($installment->amount, 2) }}</td>
                            <td class="px-3 py-2">{{ $entryContribution->currency }} {{ number_format($installment->paid_amount, 2) }}</td>
                            <td class="px-3 py-2">
                                @if($installment->penalty_amount > 0)
                                    <span class="text-red-600 font-semibold">{{ $entryContribution->currency }} {{ number_format($installment->penalty_amount, 2) }}</span>
                                    @if($installment->days_overdue > 0)
                                        <div class="text-[10px] text-red-500">({{ $installment->days_overdue }} days)</div>
                                    @endif
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                                    @if($installment->status === 'paid') bg-emerald-100 text-emerald-700
                                    @elseif($installment->status === 'overdue') bg-red-100 text-red-700
                                    @elseif($installment->status === 'missed') bg-red-200 text-red-800
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ ucfirst($installment->status) }}
                                </span>
                                @if($installment->days_overdue > 0)
                                    <span class="text-[10px] text-red-600 ml-1">({{ $installment->days_overdue }} days)</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($installment->status !== 'paid' && (auth()->user()->isSuperAdmin() || auth()->user()->hasAdminRole('finance_admin') || auth()->user()->hasAdminRole('chairman') || auth()->user()->hasAdminRole('treasurer')))
                                    <button onclick="openInstallmentPaymentModal({{ $installment->id }}, {{ $installment->amount - $installment->paid_amount }})"
                                            class="text-emerald-600 hover:text-emerald-700 text-xs underline">
                                        Pay
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- Payment Recording Modal --}}
    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasAdminRole('finance_admin') || auth()->user()->hasAdminRole('chairman') || auth()->user()->hasAdminRole('treasurer'))
    <div id="payment_modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-slate-900">Record Payment</h3>
                <button onclick="document.getElementById('payment_modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            <form action="{{ route('admin.entry-contributions.payments.store', $entryContribution) }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Amount (KES) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Payment Method</label>
                    <input type="text" name="payment_method" placeholder="e.g. Bank Transfer, M-Pesa"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Reference</label>
                    <input type="text" name="reference" placeholder="Transaction reference"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Notes</label>
                    <textarea name="notes" rows="2" class="border border-slate-200 rounded w-full px-3 py-2 text-sm"></textarea>
                </div>
                @if($paymentPlan)
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="apply_to_installment" value="1" id="apply_to_installment">
                        <span class="text-xs">Apply to specific installment</span>
                    </label>
                    <select name="installment_id" id="installment_select" class="mt-2 border border-slate-200 rounded w-full px-3 py-2 text-sm hidden">
                        <option value="">Select installment...</option>
                        @foreach($installments->where('status', '!=', 'paid') as $inst)
                            <option value="{{ $inst->id }}">Installment #{{ $inst->installment_number }} - Due: {{ $inst->due_date->format('d M Y') }} (Outstanding: {{ number_format($inst->amount - $inst->paid_amount, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded">
                        Record Payment
                    </button>
                    <button type="button" onclick="document.getElementById('payment_modal').classList.add('hidden')"
                            class="px-4 py-2 text-xs text-slate-600 hover:text-slate-800">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Installment Payment Modal --}}
    <div id="installment_payment_modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-semibold text-slate-900">Record Installment Payment</h3>
                <button onclick="document.getElementById('installment_payment_modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            <form id="installment_payment_form" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Amount (KES) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="installment_amount" step="0.01" min="0.01" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Payment Method</label>
                    <input type="text" name="payment_method" placeholder="e.g. Bank Transfer, M-Pesa"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Reference</label>
                    <input type="text" name="reference" placeholder="Transaction reference"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1 text-slate-700">Notes</label>
                    <textarea name="notes" rows="2" class="border border-slate-200 rounded w-full px-3 py-2 text-sm"></textarea>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded">
                        Record Payment
                    </button>
                    <button type="button" onclick="document.getElementById('installment_payment_modal').classList.add('hidden')"
                            class="px-4 py-2 text-xs text-slate-600 hover:text-slate-800">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('apply_to_installment')?.addEventListener('change', function() {
            const select = document.getElementById('installment_select');
            if (this.checked) {
                select.classList.remove('hidden');
            } else {
                select.classList.add('hidden');
            }
        });

        function openInstallmentPaymentModal(installmentId, outstandingAmount) {
            document.getElementById('installment_payment_modal').classList.remove('hidden');
            document.getElementById('installment_payment_form').action = '/admin/installments/' + installmentId + '/payment';
            document.getElementById('installment_amount').value = outstandingAmount.toFixed(2);
        }
    </script>
    @endif
    @endif

    @if($partner->contributions->count() > 0)
    <div class="mt-6 bg-white border border-slate-100 rounded-lg p-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-4">Recent Contributions</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Amount</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($partner->contributions as $contribution)
                        <tr>
                            <td class="px-4 py-2">{{ $contribution->contributed_at->format('d M Y') }}</td>
                            <td class="px-4 py-2">{{ ucfirst(str_replace('_', ' ', $contribution->type)) }}</td>
                            <td class="px-4 py-2 font-medium">{{ $contribution->currency }} {{ number_format($contribution->amount, 2) }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                    {{ $contribution->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ ucfirst($contribution->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection
