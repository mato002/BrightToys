@extends('layouts.admin')

@section('page_title', 'General Ledger')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Books of Account
            </a>
            <h1 class="text-lg font-semibold text-slate-900">General Ledger</h1>
            <p class="text-xs text-slate-500">Run account-wise ledger statements by date range and branch.</p>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg p-4 text-sm mb-4 max-w-5xl w-full">
        <form method="GET" class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Ledger Account to Run</label>
                <select name="account_id" class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                    <option value="">Run all accounts</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" @selected(request('account_id') == $account->id)>
                            {{ $account->code }} - {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Branch</label>
                <select name="branch" class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch }}" @selected(request('branch') == $branch)>
                            {{ $branch }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Report From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Report To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-6 py-2.5 rounded-md shadow-sm">
                    Run
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="admin-table-scroll overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Description</th>
                        @if(!request('account_id'))
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Account</th>
                        @endif
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Debit</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Credit</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ledgerData as $item)
                        @php
                            $entry = $item['entry'];
                            $balance = $item['balance'];
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2 text-slate-700">
                                {{ $entry->journalEntry->transaction_date->format('d-m-Y') }}
                            </td>
                            <td class="px-4 py-2">
                                <div class="text-slate-900">
                                    {{ $entry->description ?? $entry->journalEntry->transaction_details ?? '—' }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    Ref: {{ $entry->journalEntry->reference_number ?? 'N/A' }}
                                </div>
                            </td>
                            @if(!request('account_id'))
                                <td class="px-4 py-2">
                                    <div class="text-xs font-medium text-slate-900">{{ $item['account_code'] }}</div>
                                    <div class="text-xs text-slate-500">{{ $item['account_name'] }}</div>
                                </td>
                            @endif
                            <td class="px-4 py-2 text-right font-semibold text-slate-900">
                                @if($entry->entry_type === 'debit')
                                    KES {{ number_format($entry->amount, 2) }}
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right font-semibold text-slate-900">
                                @if($entry->entry_type === 'credit')
                                    KES {{ number_format($entry->amount, 2) }}
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right font-semibold {{ $balance >= 0 ? 'text-slate-900' : 'text-red-600' }}">
                                KES {{ number_format(abs($balance), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ request('account_id') ? '5' : '6' }}" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No ledger entries found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

