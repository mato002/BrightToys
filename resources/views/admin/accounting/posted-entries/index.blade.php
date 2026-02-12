@extends('layouts.admin')

@section('page_title', 'Posted Entries')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Books of Account
            </a>
            <h1 class="text-lg font-semibold text-slate-900">
                @if(request('month'))
                    {{ Carbon\Carbon::parse(request('month'))->format('M Y') }} Posted Entries
                @else
                    Posted Entries
                @endif
            </h1>
            <p class="text-xs text-slate-500">View and manage all posted journal entries</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs grid md:grid-cols-5 gap-3">
        <div>
            <label class="block text-[10px] font-semibold mb-1 text-slate-700">Year</label>
            <select name="year" class="border border-slate-200 rounded px-3 py-2 text-sm w-full">
                <option value="">All Years</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-semibold mb-1 text-slate-700">Month</label>
            <select name="month" class="border border-slate-200 rounded px-3 py-2 text-sm w-full">
                <option value="">All Months</option>
                @for($m = 1; $m <= 12; $m++)
                    @php
                        $monthDate = Carbon\Carbon::create(request('year', date('Y')), $m, 1);
                        $monthValue = $monthDate->format('Y-m');
                        $monthLabel = $monthDate->format('M Y');
                    @endphp
                    <option value="{{ $monthValue }}" {{ request('month') == $monthValue ? 'selected' : '' }}>
                        {{ $monthLabel }}
                    </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-semibold mb-1 text-slate-700">Day</label>
            <input type="date" name="day" value="{{ request('day') }}"
                   class="border border-slate-200 rounded px-3 py-2 text-sm w-full">
        </div>
        <div>
            <label class="block text-[10px] font-semibold mb-1 text-slate-700">Branch</label>
            <select name="branch" class="border border-slate-200 rounded px-3 py-2 text-sm w-full">
                <option value="">All Branches</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch }}" {{ request('branch') == $branch ? 'selected' : '' }}>{{ $branch }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-semibold mb-1 text-slate-700">Search Transaction</label>
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Q Search Transaction"
                       class="border border-slate-200 rounded px-3 py-2 text-sm flex-1 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-xs">
                    Search
                </button>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="overflow-x-auto admin-table-scroll">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Posted By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Trans Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Credit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Debit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Refno</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Trans Id</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($entries as $entry)
                        @php
                            $debitLines = $entry->lines->where('entry_type', 'debit');
                            $creditLines = $entry->lines->where('entry_type', 'credit');
                            $totalAmount = $entry->total_debit;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="text-slate-900 font-medium">{{ $entry->poster->name ?? $entry->creator->name ?? 'System' }}</div>
                                <div class="text-xs text-slate-500">{{ $entry->posted_at ? $entry->posted_at->format('M d, H:i') : $entry->created_at->format('M d, H:i') }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $entry->transaction_date->format('d-m-Y') }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ number_format($totalAmount, 2) }}</td>
                            <td class="px-4 py-3">
                                @foreach($creditLines as $line)
                                    <div class="text-xs text-slate-700 mb-1">
                                        <span class="font-medium">i. {{ $line->account->name }} - KES {{ number_format($line->amount, 2) }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-4 py-3">
                                @foreach($debitLines as $line)
                                    <div class="text-xs text-slate-700 mb-1">
                                        <span class="font-medium">i. {{ $line->account->name }} - KES {{ number_format($line->amount, 2) }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $entry->reference_number ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 font-mono text-xs">{{ $entry->transaction_id }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No posted entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($entries->hasPages())
        <div class="mt-4">
            {{ $entries->links() }}
        </div>
    @endif
@endsection
