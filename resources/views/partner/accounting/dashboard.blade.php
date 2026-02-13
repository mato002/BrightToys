@extends('layouts.partner')

@section('page_title', 'Accounting Dashboard')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Accounting Dashboard</h1>
        <p class="text-xs text-slate-500">
            Overview of accounting entries, account balances, and financial transactions.
        </p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Total Entries</p>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($totalEntries, 0) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Posted journal entries</p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Active Accounts</p>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalAccounts, 0) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Chart of accounts</p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Top Accounts</p>
            <p class="text-2xl font-bold text-amber-600">{{ $accountBalances->count() }}</p>
            <p class="text-[11px] text-slate-500 mt-1">By balance</p>
        </div>
    </div>

    {{-- Recent Entries & Top Accounts --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
        {{-- Recent Journal Entries --}}
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Recent Journal Entries</h2>
                <a href="{{ route('partner.accounting.ledger') }}" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                    View all
                </a>
            </div>
            @if($recentEntries->count() > 0)
                <div class="space-y-2">
                    @foreach($recentEntries->take(5) as $entry)
                        <div class="flex items-center justify-between p-2 rounded-lg border border-slate-100 hover:bg-slate-50">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ $entry->reference_number ?? 'N/A' }}</p>
                                <p class="text-[10px] text-slate-500">{{ $entry->transaction_date->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Posted
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">No journal entries found.</p>
            @endif
        </div>

        {{-- Top Accounts by Balance --}}
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Top Accounts by Balance</h2>
                <a href="{{ route('partner.accounting.reports') }}" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                    View reports
                </a>
            </div>
            @if($accountBalances->count() > 0)
                <div class="space-y-2">
                    @foreach($accountBalances->take(5) as $item)
                        <div class="flex items-center justify-between p-2 rounded-lg border border-slate-100 hover:bg-slate-50">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ $item['account']->name }}</p>
                                <p class="text-[10px] text-slate-500">{{ $item['account']->code }} · {{ $item['account']->type }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold {{ $item['balance'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    Ksh {{ number_format(abs($item['balance']), 2) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">No account balances available.</p>
            @endif
        </div>
    </div>

    {{-- Monthly Summary --}}
    @if($monthlySummary->count() > 0)
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Monthly Transaction Summary</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-[11px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Period</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Entries</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Total Debits</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($monthlySummary as $summary)
                        <tr>
                            <td class="px-3 py-2 font-medium text-slate-900">{{ $summary['period'] }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $summary['count'] }}</td>
                            <td class="px-3 py-2 text-right font-semibold text-emerald-600">Ksh {{ number_format($summary['total_debits'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Quick Links --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Quick Links</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <a href="{{ route('partner.accounting.ledger') }}" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-amber-300 hover:bg-amber-50/50 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-900">General Ledger</p>
                    <p class="text-[10px] text-slate-500">View all accounting entries</p>
                </div>
            </a>
            <a href="{{ route('partner.accounting.reports') }}" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-amber-300 hover:bg-amber-50/50 transition-colors">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 2v6h6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 13H8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 17H8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-900">Financial Reports</p>
                    <p class="text-[10px] text-slate-500">View account balances and summaries</p>
                </div>
            </a>
        </div>
    </div>
@endsection
