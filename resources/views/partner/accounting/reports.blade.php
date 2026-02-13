@extends('layouts.partner')

@section('page_title', 'Financial Reports')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Financial Reports</h1>
        <p class="text-xs text-slate-500">
            View account balances and financial summaries by account type.
        </p>
    </div>

    {{-- Date Range Filter --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">From Date</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">To Date</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Update Report
                </button>
            </div>
        </form>
    </div>

    {{-- Account Balances by Type --}}
    @foreach(['ASSET', 'LIABILITY', 'EQUITY', 'INCOME', 'EXPENSE'] as $type)
        @if(isset($accountsByType[$type]) && $accountsByType[$type]->count() > 0)
            <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900">{{ $type }} Accounts</h2>
                    <p class="text-xs font-semibold {{ $totalsByType[$type] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        Total: Ksh {{ number_format(abs($totalsByType[$type]), 2) }}
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-[11px] responsive-table">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Code</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Account Name</th>
                                <th class="px-3 py-2 text-right font-semibold text-slate-700">Debits</th>
                                <th class="px-3 py-2 text-right font-semibold text-slate-700">Credits</th>
                                <th class="px-3 py-2 text-right font-semibold text-slate-700">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($accountsByType[$type] as $item)
                                <tr>
                                    <td class="px-3 py-2 font-medium text-slate-900" data-label="Code">{{ $item['account']->code }}</td>
                                    <td class="px-3 py-2" data-label="Account Name">{{ $item['account']->name }}</td>
                                    <td class="px-3 py-2 text-right" data-label="Debits">Ksh {{ number_format($item['debits'], 2) }}</td>
                                    <td class="px-3 py-2 text-right" data-label="Credits">Ksh {{ number_format($item['credits'], 2) }}</td>
                                    <td class="px-3 py-2 text-right font-semibold {{ $item['balance'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}" data-label="Balance">
                                        Ksh {{ number_format(abs($item['balance']), 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach

    @if($accountsByType->isEmpty())
        <div class="bg-white rounded-lg border border-slate-100 p-8 text-center shadow-sm">
            <p class="text-xs text-slate-500">No account data available for the selected period.</p>
        </div>
    @endif
@endsection
