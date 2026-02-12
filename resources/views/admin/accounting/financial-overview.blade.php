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
                <div class="border border-dashed border-slate-200 rounded-xl p-6 text-center text-xs text-slate-400">
                    Charts for monthly and yearly performance, plus trends over time, will appear here once you plug in reporting data.
                </div>
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
                    <input type="text"
                           class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="Search member by name, ID or phone (UI only – hook to members table)">
                    <button class="rounded-lg bg-emerald-500 px-3 py-2 text-[11px] font-semibold text-white hover:bg-emerald-600">
                        View Profile
                    </button>
                </div>
                <div class="border border-dashed border-emerald-100 rounded-xl p-3 text-[11px] text-slate-500">
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
                    <h3 class="text-xs font-semibold text-amber-900 uppercase tracking-[0.16em]">Welfare Fund & Approvals</h3>
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

                <div class="admin-table-scroll max-h-40">
                    <table class="w-full text-[11px]">
                        <thead class="border-b border-amber-50 bg-amber-50/60">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-amber-900">Date</th>
                            <th class="px-3 py-2 text-left font-semibold text-amber-900">Member / Purpose</th>
                            <th class="px-3 py-2 text-right font-semibold text-amber-900">Amount</th>
                            <th class="px-3 py-2 text-right font-semibold text-amber-900">Status</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-50">
                        @forelse($welfareStats['recent_disbursements'] ?? [] as $item)
                            <tr>
                                <td class="px-3 py-2 text-slate-700">{{ $item['date'] ?? '' }}</td>
                                <td class="px-3 py-2 text-slate-700">
                                    {{ $item['member'] ?? 'Member' }} – {{ $item['purpose'] ?? 'Purpose' }}
                                </td>
                                <td class="px-3 py-2 text-right text-slate-900">
                                    {{ number_format($item['amount'] ?? 0, 2) }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                                        {{ ($item['status'] ?? '') === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ ucfirst($item['status'] ?? 'pending') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-amber-500">
                                    Once welfare rules and workflows are wired, all disbursements and approvals will be listed here (read-only for members).
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
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
@endsection

