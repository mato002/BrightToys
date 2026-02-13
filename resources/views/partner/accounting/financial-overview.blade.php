@extends('layouts.partner')

@section('page_title', 'Financial Overview')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Financial Overview</h1>
        <p class="text-xs text-slate-500">
            Real-time financial snapshot for the group and individual members, including contributions, welfare, loans, assets and net worth.
        </p>
    </div>

    <div class="grid gap-4 lg:grid-cols-3 mb-6">
        {{-- Group-Level Summary --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Group Financial Snapshot --}}
            <section class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wide">Group Financial Snapshot</h2>
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700">
                        Real-time
                    </span>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="rounded-lg border border-slate-100 bg-slate-50/60 p-3">
                        <p class="text-[11px] text-slate-500 mb-1">Total Contributions</p>
                        <p class="text-base font-semibold text-slate-900">
                            KES {{ number_format($groupSummary['total_contributions'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-emerald-100 bg-emerald-50/60 p-3">
                        <p class="text-[11px] text-emerald-700 mb-1">Welfare Total</p>
                        <p class="text-base font-semibold text-emerald-900">
                            KES {{ number_format($groupSummary['welfare_total'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-sky-100 bg-sky-50/60 p-3">
                        <p class="text-[11px] text-sky-700 mb-1">Investment Total</p>
                        <p class="text-base font-semibold text-sky-900">
                            KES {{ number_format($groupSummary['investment_total'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-indigo-100 bg-indigo-50/60 p-3">
                        <p class="text-[11px] text-indigo-700 mb-1">Net Worth</p>
                        <p class="text-base font-semibold text-indigo-900">
                            KES {{ number_format($groupSummary['net_worth'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- Bank & SACCO Balances + Loans & Assets --}}
            <section class="grid md:grid-cols-2 gap-4">
                {{-- Bank & SACCO Balances --}}
                <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-wide">Bank & SACCO Balances</h3>
                        <span class="text-[10px] text-slate-400">Per account</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[11px]">
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
                                            KES {{ number_format($account['reconciled'] ?? 0, 2) }}
                                        </td>
                                        <td class="px-3 py-2 text-right text-amber-700">
                                            KES {{ number_format($account['unreconciled'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-6 text-center text-slate-400 text-xs">
                                            No bank accounts available.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Outstanding Loans --}}
                <div class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-wide">Outstanding Loans</h3>
                        <span class="text-[10px] text-slate-400">Active</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-[11px]">
                            <thead class="border-b border-slate-100 bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-slate-600">Borrower</th>
                                    <th class="px-3 py-2 text-right font-semibold text-slate-600">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($outstandingLoans as $loan)
                                    <tr>
                                        <td class="px-3 py-2 text-slate-700">{{ $loan['borrower'] ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 text-right text-slate-900">
                                            KES {{ number_format($loan['balance'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-3 py-6 text-center text-slate-400 text-xs">
                                            No outstanding loans.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- Assets Summary --}}
            <section class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
                <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-wide mb-3">Assets Summary</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-[11px]">
                        <thead class="border-b border-slate-100 bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-slate-600">Asset</th>
                                <th class="px-3 py-2 text-right font-semibold text-slate-600">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($assetsSummary as $asset)
                                <tr>
                                    <td class="px-3 py-2 text-slate-700">{{ $asset['name'] ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 text-right text-slate-900">
                                        KES {{ number_format($asset['value'] ?? 0, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-3 py-6 text-center text-slate-400 text-xs">
                                        No assets recorded.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- Welfare Stats --}}
        <div class="space-y-4">
            <section class="bg-white border border-slate-100 rounded-lg p-4 shadow-sm">
                <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-wide mb-3">Welfare Fund</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-[11px] text-slate-500 mb-1">Total Inflows</p>
                        <p class="text-base font-semibold text-emerald-900">
                            KES {{ number_format($welfareStats['total_inflows'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[11px] text-slate-500 mb-1">Total Disbursements</p>
                        <p class="text-base font-semibold text-red-900">
                            KES {{ number_format($welfareStats['total_disbursements'] ?? 0, 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[11px] text-slate-500 mb-1">Remaining Balance</p>
                        <p class="text-base font-semibold text-slate-900">
                            KES {{ number_format($welfareStats['remaining_balance'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
