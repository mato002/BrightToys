@extends('layouts.partner')

@section('page_title', 'Reports & Analytics')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Reports & Analytics</h1>
        <p class="text-xs text-slate-500">
            High-level sales and expense overview so you can see how the business is performing at a glance.
        </p>
    </div>

    {{-- Headline Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Total Revenue (All Time)</p>
            <p class="text-2xl font-bold text-emerald-600">Ksh {{ number_format($totalRevenue, 0) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Total Expenses (All Time)</p>
            <p class="text-2xl font-bold text-red-600">Ksh {{ number_format($totalExpenses, 0) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Net Profit (All Time)</p>
            <p class="text-2xl font-bold text-slate-900">Ksh {{ number_format($netProfit, 0) }}</p>
        </div>
    </div>

    {{-- Revenue Trend & Expense Breakdown --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Revenue Trend (Last 6 Months)</h2>
            </div>
            <div class="h-48 flex items-end justify-between gap-1">
                @php $maxRevenue = $revenueLast6Months->max('revenue'); @endphp
                @foreach($revenueLast6Months as $month)
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-emerald-100 rounded-t relative group cursor-pointer"
                             style="height: {{ $maxRevenue > 0 ? max(5, ($month['revenue'] / $maxRevenue) * 100) : 0 }}%"
                             title="Ksh {{ number_format($month['revenue'], 0) }}">
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-slate-900 text-white text-[10px] px-2 py-1 rounded whitespace-nowrap z-10">
                                {{ $month['month'] }}<br>Ksh {{ number_format($month['revenue'], 0) }}
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1 transform -rotate-45 origin-left">{{ $month['month'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Top Expense Categories</h2>
            </div>
            @if($expenseCategories->count() > 0)
                @php $totalExp = $expenseCategories->sum('total'); @endphp
                <div class="space-y-2 text-xs">
                    @foreach($expenseCategories as $category)
                        @php
                            $percentage = $totalExp > 0 ? ($category->total / $totalExp) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-slate-700">{{ $category->category ?? 'Uncategorised' }}</span>
                                <span class="font-semibold text-slate-900">Ksh {{ number_format($category->total, 0) }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">No expense categories recorded yet.</p>
            @endif
        </div>
    </div>

    {{-- Recent Financial Records --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-slate-900">Recent Financial Records (Approved)</h2>
            <a href="{{ route('partner.financial-records') }}" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                View full list
            </a>
        </div>

        @if($records->count() > 0)
            <div class="partner-table-scroll border border-slate-100 rounded-lg">
                <table class="min-w-full text-[11px]">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Date</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Type</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Category</th>
                            <th class="px-3 py-2 text-right font-semibold text-slate-700">Amount</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($records as $record)
                            <tr>
                                <td class="px-3 py-2 text-slate-600">{{ $record->occurred_at->format('M d, Y') }}</td>
                                <td class="px-3 py-2 capitalize">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full
                                        {{ $record->type === 'expense' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ str_replace('_', ' ', $record->type) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-slate-700">{{ $record->category ?? '—' }}</td>
                                <td class="px-3 py-2 text-right font-semibold {{ $record->type === 'expense' ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ $record->type === 'expense' ? '-' : '+' }}Ksh {{ number_format($record->amount, 0) }}
                                </td>
                                <td class="px-3 py-2 text-slate-600">
                                    {{ \Illuminate\Support\Str::limit($record->description, 60) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-xs text-slate-500">No approved financial records yet.</p>
        @endif
    </div>
@endsection

