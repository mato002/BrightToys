@extends('layouts.admin')

@section('page_title', 'Financial Overview')

@section('content')
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Financial Overview</h1>
        </div>
        <p class="text-xs md:text-sm text-slate-500">
            Real-time financial snapshot for the group and individual members, including contributions, welfare, loans, assets and net worth.
        </p>
    </div>

    <div class="grid gap-4 lg:grid-cols-3 mb-6">
        {{-- Group-Level Summary --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- A. Group-Level Financial View --}}
            <section class="bg-white border border-emerald-50 rounded-2xl p-4 shadow-sm shadow-emerald-50">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900 uppercase tracking-[0.16em]">Group Financial Snapshot</h2>
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700">
                        Real-time
                    </span>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-3">
                        <p class="text-[11px] text-slate-500 mb-1">Total Contributions</p>
                        <p class="text-base font-semibold text-slate-900">
                            {{ number_format($groupSummary['total_contributions'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-3">
                        <p class="text-[11px] text-emerald-700 mb-1">Welfare Total</p>
                        <p class="text-base font-semibold text-emerald-900">
                            {{ number_format($groupSummary['welfare_total'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-sky-100 bg-sky-50/60 p-3">
                        <p class="text-[11px] text-sky-700 mb-1">Investment Total</p>
                        <p class="text-base font-semibold text-sky-900">
                            {{ number_format($groupSummary['investment_total'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-3">
                        <p class="text-[11px] text-indigo-700 mb-1">Net Worth</p>
                        <p class="text-base font-semibold text-indigo-900">
                            {{ number_format($groupSummary['net_worth'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Bank & SACCO Balances + Loans & Assets --}}
            <section class="grid md:grid-cols-2 gap-4">
                {{-- Bank & SACCO Balances --}}
                <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-[0.16em]">Bank & SACCO Balances</h3>
                        <span class="text-[10px] text-slate-400">Per account</span>
                    </div>
                    <div class="admin-table-scroll max-h-52">
                        <table class="w-full text-xs">
                            <thead class="border-b border-slate-100 bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-slate-600">Account</th>
                                <th class="px-3 py-2 text-right font-semibold text-slate-600">Reconciled</th>
                                <th class="px-3 py-2 text-right font-semibold text-slate-600">Unreconciled</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                            @forelse($bankBalances as $account)
                                <tr>
                                    <td class="px-3 py-2 text-slate-700">{{ $account['name'] ?? 'Account' }}</td>
                                    <td class="px-3 py-2 text-right text-emerald-700">
                                        {{ number_format($account['reconciled'] ?? 0, 2) }}
                                    </td>
                                    <td class="px-3 py-2 text-right text-amber-700">
                                        {{ number_format($account['unreconciled'] ?? 0, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-3 py-6 text-center text-slate-400">
                                        Hook bank & SACCO accounts here to see reconciled vs unreconciled balances.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Outstanding Loans + Assets --}}
                <div class="space-y-4">
                    <div class="bg-white border border-rose-100 rounded-2xl p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-semibold text-rose-900 uppercase tracking-[0.16em]">Outstanding Loans</h3>
                            <span class="text-[10px] text-rose-500">Principal & interest</span>
                        </div>
                        <div class="admin-table-scroll max-h-32">
                            <table class="w-full text-xs">
                                <thead class="border-b border-rose-50 bg-rose-50/60">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-rose-800">Loan</th>
                                    <th class="px-3 py-2 text-right font-semibold text-rose-800">Principal</th>
                                    <th class="px-3 py-2 text-right font-semibold text-rose-800">Interest</th>
                                    <th class="px-3 py-2 text-right font-semibold text-rose-800">Status</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-rose-50">
                                @forelse($outstandingLoans as $loan)
                                    <tr>
                                        <td class="px-3 py-2 text-rose-900">{{ $loan['name'] ?? 'Loan' }}</td>
                                        <td class="px-3 py-2 text-right text-rose-900">
                                            {{ number_format($loan['principal'] ?? 0, 2) }}
                                        </td>
                                        <td class="px-3 py-2 text-right text-rose-700">
                                            {{ number_format($loan['interest'] ?? 0, 2) }}
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                                                {{ ($loan['status'] ?? '') === 'on-track' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                                {{ ucfirst($loan['status'] ?? 'pending') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-6 text-center text-rose-400">
                                            Connect loans to display outstanding principal, interest and repayment status.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white border border-cyan-100 rounded-2xl p-4 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-semibold text-cyan-900 uppercase tracking-[0.16em]">Assets Value</h3>
                            <span class="text-[10px] text-cyan-500">Land, toy shop & stock</span>
                        </div>
                        <dl class="space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600">Land (valuation)</dt>
                                <dd class="font-semibold text-slate-900">
                                    {{ number_format($assetsSummary['land'] ?? 0, 2) }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600">Toy shop assets</dt>
                                <dd class="font-semibold text-slate-900">
                                    {{ number_format($assetsSummary['toy_shop'] ?? 0, 2) }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-slate-600">Inventory / stock</dt>
                                <dd class="font-semibold text-slate-900">
                                    {{ number_format($assetsSummary['inventory'] ?? 0, 2) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </section>

            {{-- Performance Trends --}}
            <section class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-[0.16em]">Performance</h3>
                    <div class="flex items-center gap-2 text-[10px] text-slate-500">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Monthly
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-sky-500"></span> Yearly
                        </span>
                    </div>
                </div>
                @if(!empty($performance['trends']) && count($performance['trends']) > 0)
                    <div class="mb-4">
                        <canvas id="performanceChart" height="80"></canvas>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-xs">
                        <div class="text-center">
                            <p class="text-slate-500">Monthly Revenue</p>
                            <p class="font-semibold text-emerald-700">Ksh {{ number_format($performance['monthly']['revenue'] ?? 0, 0) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-slate-500">Monthly Expenses</p>
                            <p class="font-semibold text-red-700">Ksh {{ number_format($performance['monthly']['expenses'] ?? 0, 0) }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-slate-500">Monthly Profit</p>
                            <p class="font-semibold text-sky-700">Ksh {{ number_format($performance['monthly']['profit'] ?? 0, 0) }}</p>
                        </div>
                    </div>
                @else
                    <div class="border border-dashed border-slate-200 rounded-xl p-6 text-center text-xs text-slate-400">
                        No performance data available yet. Charts will appear once transactions are recorded.
                    </div>
                @endif
            </section>
        </div>

        {{-- Member-Level & Welfare --}}
        <div class="space-y-4">
            {{-- B. Member-Level Financial View --}}
            <section class="bg-white border border-emerald-100 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-emerald-900 uppercase tracking-[0.16em]">Member Financial View</h3>
                    <span class="text-[10px] text-emerald-500">Read-only</span>
                </div>
                <p class="text-[11px] text-slate-500 mb-2">
                    Search a member to view their total contributions, welfare vs investment split, investment share and profit entitlement.
                </p>
                <div class="flex items-center gap-2 mb-3">
                    <input type="text" id="member-search-input"
                           class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Search member by name, email, phone or national ID">
                    <button id="member-search-btn" class="rounded-lg bg-emerald-500 px-3 py-2 text-[11px] font-semibold text-white hover:bg-emerald-600">
                        Search
                    </button>
                </div>
                <div id="member-results" class="hidden border border-emerald-200 rounded-xl p-4 bg-emerald-50/50">
                    <div id="member-loading" class="hidden text-center text-xs text-slate-500 py-2">Searching...</div>
                    <div id="member-error" class="hidden text-center text-xs text-red-600 py-2"></div>
                    <div id="member-details" class="hidden space-y-2 text-xs">
                        <div class="flex items-center justify-between border-b border-emerald-200 pb-2 mb-2">
                            <span class="font-semibold text-emerald-900" id="member-name"></span>
                            <a href="#" id="member-profile-link" class="text-emerald-600 hover:text-emerald-700 text-[10px]">View Full Profile →</a>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-slate-600">Total Contributed:</span>
                                <span class="font-semibold text-slate-900 ml-1" id="member-total-contributed"></span>
                            </div>
                            <div>
                                <span class="text-slate-600">Welfare:</span>
                                <span class="font-semibold text-slate-900 ml-1" id="member-welfare"></span>
                            </div>
                            <div>
                                <span class="text-slate-600">Investment:</span>
                                <span class="font-semibold text-slate-900 ml-1" id="member-investment"></span>
                            </div>
                            <div>
                                <span class="text-slate-600">Investment Share:</span>
                                <span class="font-semibold text-slate-900 ml-1" id="member-investment-share"></span>
                            </div>
                            <div>
                                <span class="text-slate-600">Ownership %:</span>
                                <span class="font-semibold text-slate-900 ml-1" id="member-ownership"></span>
                            </div>
                            <div>
                                <span class="text-slate-600">Profit Entitlement:</span>
                                <span class="font-semibold text-slate-900 ml-1" id="member-profit"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="member-placeholder" class="border border-dashed border-emerald-100 rounded-xl p-3 text-[11px] text-slate-500">
                    This panel will show:
                    <ul class="mt-1 list-disc list-inside space-y-0.5">
                        <li>Total contributed (welfare + investment)</li>
                        <li>Welfare vs investment contributions</li>
                        <li>Investment share & ownership % (from total investment)</li>
                        <li>Profit entitlement (where distributions apply)</li>
                    </ul>
                </div>
            </section>

            {{-- C & D. Welfare Fund Management & Approvals --}}
            <section class="bg-white border border-amber-100 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-semibold text-amber-900 uppercase tracking-[0.16em]">Welfare Fund & Approvals</h3>
                        @if(($welfareStats['pending_count'] ?? 0) > 0)
                            <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-[10px] font-medium">
                                {{ $welfareStats['pending_count'] }} pending
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] text-amber-600">Strict controls</span>
                </div>
                <dl class="grid grid-cols-3 gap-3 text-[11px] mb-4">
                    <div class="col-span-1">
                        <dt class="text-slate-500">Inflows (to date)</dt>
                        <dd class="text-sm font-semibold text-slate-900">
                            {{ number_format($welfareStats['total_inflows'] ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="col-span-1">
                        <dt class="text-slate-500">Disbursed</dt>
                        <dd class="text-sm font-semibold text-slate-900">
                            {{ number_format($welfareStats['total_disbursements'] ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="col-span-1">
                        <dt class="text-slate-500">Remaining balance</dt>
                        <dd class="text-sm font-semibold text-emerald-700">
                            {{ number_format($welfareStats['remaining_balance'] ?? 0, 2) }}
                        </dd>
                    </div>
                </dl>

                <div class="border border-dashed border-amber-100 rounded-xl p-3 text-[11px] mb-3">
                    <p class="text-slate-600 font-medium mb-1">Welfare Rules (configurable in settings):</p>
                    <ul class="list-disc list-inside space-y-0.5 text-slate-500">
                        <li>Eligible use cases (emergencies, support cases, group activities)</li>
                        <li>Maximum amounts per event</li>
                        <li>Approval thresholds per officer role</li>
                        <li>No welfare transaction can occur outside the rules</li>
                    </ul>
                </div>

                <div class="mb-3 flex items-center justify-between">
                    <p class="text-[11px] text-slate-500">Recent disbursements and pending approvals</p>
                    <a href="{{ route('admin.financial.create', ['fund_type' => 'welfare']) }}" 
                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-semibold rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        New Disbursement
                    </a>
                </div>
                <div class="admin-table-scroll max-h-40">
                    <table class="w-full text-[11px]">
                        <thead class="border-b border-amber-50 bg-amber-50/60">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-amber-900">Date</th>
                            <th class="px-3 py-2 text-left font-semibold text-amber-900">Member / Purpose</th>
                            <th class="px-3 py-2 text-right font-semibold text-amber-900">Amount</th>
                            <th class="px-3 py-2 text-right font-semibold text-amber-900">Status</th>
                            <th class="px-3 py-2 text-right font-semibold text-amber-900">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-50">
                        @forelse($welfareStats['recent_disbursements'] ?? [] as $item)
                            <tr class="hover:bg-amber-50/30">
                                <td class="px-3 py-2 text-slate-700">{{ $item['date'] ?? '' }}</td>
                                <td class="px-3 py-2 text-slate-700">
                                    <div class="font-medium">{{ $item['member'] ?? 'Member' }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $item['purpose'] ?? 'Purpose' }}</div>
                                </td>
                                <td class="px-3 py-2 text-right text-slate-900 font-semibold">
                                    Ksh {{ number_format($item['amount'] ?? 0, 2) }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    @php
                                        $status = $item['status'] ?? 'pending_approval';
                                        $statusClass = $status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                                      ($status === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : 
                                                      'bg-amber-50 text-amber-700 border-amber-200');
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium border {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.financial.show', $item['id'] ?? '') }}" 
                                           class="text-amber-600 hover:text-amber-700 text-[10px] font-medium">
                                            View
                                        </a>
                                        @if(($item['status'] ?? 'pending_approval') === 'pending_approval')
                                            @php
                                                $user = auth()->user();
                                                $canApprove = $user->hasPermission('financial.records.approve') 
                                                    || $user->isSuperAdmin() 
                                                    || $user->hasAdminRole('finance_admin')
                                                    || $user->hasAdminRole('treasurer')
                                                    || $user->hasAdminRole('chairman');
                                            @endphp
                                            @if($canApprove)
                                                <form action="{{ route('admin.financial.approve', $item['id'] ?? '') }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      data-confirm="Approve this welfare disbursement?">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-emerald-600 hover:text-emerald-700 text-[10px] font-medium">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.financial.reject', $item['id'] ?? '') }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      data-confirm="Reject this welfare disbursement?">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-700 text-[10px] font-medium">
                                                        Reject
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-amber-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <p class="text-[11px]">No welfare disbursements yet.</p>
                                        <a href="{{ route('admin.financial.create', ['fund_type' => 'welfare']) }}" 
                                           class="text-amber-600 hover:text-amber-700 text-[10px] font-medium underline">
                                            Create first disbursement
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if(count($welfareStats['recent_disbursements'] ?? []) > 0)
                    <div class="mt-2 text-center">
                        <a href="{{ route('admin.financial.index', ['fund_type' => 'welfare']) }}" 
                           class="text-[10px] text-amber-600 hover:text-amber-700 font-medium">
                            View all welfare disbursements →
                        </a>
                    </div>
                @endif
            </section>

            {{-- E. Data Integrity --}}
            <section class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
                <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-[0.16em] mb-2">Data Integrity</h3>
                <p class="text-[11px] text-slate-500">
                    All figures on this screen are intended to be sourced from bank-verified and reconciled records.
                    After each contribution approval, disbursement or reconciliation, the dashboard should refresh automatically
                    so that admins and members always see up-to-date numbers.
                </p>
            </section>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Member Search Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('member-search-input');
            const searchBtn = document.getElementById('member-search-btn');
            const resultsDiv = document.getElementById('member-results');
            const loadingDiv = document.getElementById('member-loading');
            const errorDiv = document.getElementById('member-error');
            const detailsDiv = document.getElementById('member-details');
            const placeholderDiv = document.getElementById('member-placeholder');

            function performSearch() {
                const query = searchInput.value.trim();
                if (query.length < 2) {
                    return;
                }

                // Show loading
                resultsDiv.classList.remove('hidden');
                loadingDiv.classList.remove('hidden');
                errorDiv.classList.add('hidden');
                detailsDiv.classList.add('hidden');
                placeholderDiv.classList.add('hidden');

                fetch('{{ route("admin.accounting.financial-overview.search-member") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ query: query })
                })
                .then(response => response.json())
                .then(data => {
                    loadingDiv.classList.add('hidden');
                    if (data.error) {
                        errorDiv.textContent = data.error;
                        errorDiv.classList.remove('hidden');
                    } else {
                        // Display member details
                        document.getElementById('member-name').textContent = data.partner.name;
                        document.getElementById('member-profile-link').href = '{{ url("/admin/partners") }}/' + data.partner.id;
                        document.getElementById('member-total-contributed').textContent = 'Ksh ' + parseFloat(data.financials.total_contributed).toLocaleString('en-KE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        document.getElementById('member-welfare').textContent = 'Ksh ' + parseFloat(data.financials.welfare_balance).toLocaleString('en-KE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        document.getElementById('member-investment').textContent = 'Ksh ' + parseFloat(data.financials.investment_balance).toLocaleString('en-KE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        document.getElementById('member-investment-share').textContent = data.financials.investment_share_percent + '%';
                        document.getElementById('member-ownership').textContent = data.financials.ownership_percentage + '%';
                        document.getElementById('member-profit').textContent = 'Ksh ' + parseFloat(data.financials.profit_entitlement).toLocaleString('en-KE', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        detailsDiv.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    loadingDiv.classList.add('hidden');
                    errorDiv.textContent = 'Error searching for member. Please try again.';
                    errorDiv.classList.remove('hidden');
                });
            }

            searchBtn.addEventListener('click', performSearch);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        });

        // Performance Chart
        @if(!empty($performance['trends']) && count($performance['trends']) > 0)
        const ctx = document.getElementById('performanceChart');
        if (ctx) {
            const trends = @json($performance['trends']);
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trends.map(t => t.month),
                    datasets: [{
                        label: 'Revenue',
                        data: trends.map(t => t.revenue),
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Ksh ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        @endif
    </script>
    @endpush
@endsection

