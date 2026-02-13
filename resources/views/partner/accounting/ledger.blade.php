@extends('layouts.partner')

@section('page_title', 'General Ledger')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">General Ledger</h1>
        <p class="text-xs text-slate-500">
            View all posted accounting entries and transactions.
        </p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Account</label>
                <select name="account_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" @selected(request('account_id') == $account->id)>
                            {{ $account->code }} - {{ $account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Ledger Entries --}}
    <div class="bg-white rounded-lg border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-[11px] responsive-table">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Date</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Reference</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Account</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Description</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Debit</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($entries as $entry)
                        <tr>
                            <td class="px-3 py-2" data-label="Date">{{ $entry->journalEntry->transaction_date->format('M d, Y') }}</td>
                            <td class="px-3 py-2" data-label="Reference">{{ $entry->journalEntry->reference_number ?? 'N/A' }}</td>
                            <td class="px-3 py-2" data-label="Account">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $entry->account->code }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $entry->account->name }}</p>
                                </div>
                            </td>
                            <td class="px-3 py-2" data-label="Description">{{ $entry->description ?? '—' }}</td>
                            <td class="px-3 py-2 text-right" data-label="Debit">
                                @if($entry->entry_type === 'debit')
                                    <span class="font-semibold text-slate-900">Ksh {{ number_format($entry->amount, 2) }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right" data-label="Credit">
                                @if($entry->entry_type === 'credit')
                                    <span class="font-semibold text-slate-900">Ksh {{ number_format($entry->amount, 2) }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-xs text-slate-500">
                                No ledger entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200">
            {{ $entries->links() }}
        </div>
    </div>
@endsection
