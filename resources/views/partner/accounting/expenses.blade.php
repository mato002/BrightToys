@extends('layouts.partner')

@section('page_title', 'Expenses')

@section('partner_content')
    <div class="mb-4">
        <div class="flex items-end justify-between">
            <div>
                <h1 class="text-lg font-semibold">Company Expenses</h1>
                <p class="text-xs text-slate-500">
                    View all expense transactions and records.
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500 mb-1">Total</p>
                <p class="text-lg font-semibold text-slate-900">KES {{ number_format($total, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Year</label>
                <select name="year" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Month</label>
                <input type="month" name="month" value="{{ request('month') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Day</label>
                <input type="date" name="day" value="{{ request('day') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Branch</label>
                <select name="branch" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch }}" @selected(request('branch') == $branch)>{{ $branch }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Search</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Expenses Table --}}
    <div class="bg-white rounded-lg border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-[11px] responsive-table">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Date</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Account</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Description</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Reference</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($expenses as $expense)
                        <tr>
                            <td class="px-3 py-2" data-label="Date">{{ $expense->journalEntry->transaction_date->format('M d, Y') }}</td>
                            <td class="px-3 py-2" data-label="Account">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $expense->account->code }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $expense->account->name }}</p>
                                </div>
                            </td>
                            <td class="px-3 py-2" data-label="Description">{{ Str::limit($expense->description ?? '—', 50) }}</td>
                            <td class="px-3 py-2" data-label="Reference">{{ $expense->reference_number ?? '—' }}</td>
                            <td class="px-3 py-2 text-right font-semibold text-slate-900" data-label="Amount">KES {{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center text-slate-500">No expenses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200">
            {{ $expenses->links() }}
        </div>
    </div>
@endsection
