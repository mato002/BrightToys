@extends('layouts.partner')

@section('page_title', 'Journal Entries')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Posted Journal Entries</h1>
        <p class="text-xs text-slate-500">
            View all posted journal entries and transactions.
        </p>
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

    {{-- Entries Table --}}
    <div class="bg-white rounded-lg border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-[11px] responsive-table">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Date</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Transaction ID</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Reference</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Branch</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Details</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Posted By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($entries as $entry)
                        <tr>
                            <td class="px-3 py-2" data-label="Date">{{ $entry->transaction_date->format('M d, Y') }}</td>
                            <td class="px-3 py-2 font-mono text-slate-900" data-label="Transaction ID">{{ $entry->transaction_id }}</td>
                            <td class="px-3 py-2" data-label="Reference">{{ $entry->reference_number ?? '—' }}</td>
                            <td class="px-3 py-2" data-label="Branch">{{ $entry->branch_name ?? '—' }}</td>
                            <td class="px-3 py-2" data-label="Details">{{ Str::limit($entry->transaction_details ?? '—', 50) }}</td>
                            <td class="px-3 py-2" data-label="Posted By">{{ $entry->poster->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-slate-500">No entries found.</td>
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
