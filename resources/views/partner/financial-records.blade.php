@extends('layouts.partner')

@section('page_title', 'Financial Records')

@section('partner_content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Financial Records</h1>
                <p class="text-sm text-slate-600 mt-1">
                    Comprehensive view of all approved business financial transactions and records.
                </p>
            </div>
        </div>
    </div>

    @php
        $totalRevenue = $records->where('type', 'revenue')->sum('amount');
        $totalExpenses = $records->where('type', 'expense')->sum('amount');
        $totalOtherIncome = $records->where('type', 'other_income')->sum('amount');
        $totalAdjustments = $records->where('type', 'adjustment')->sum('amount');
        $netTotal = $totalRevenue + $totalOtherIncome - $totalExpenses + $totalAdjustments;
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Total Revenue</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-emerald-900">Ksh {{ number_format($totalRevenue, 0) }}</p>
            <p class="text-xs text-emerald-700 mt-1">{{ $records->where('type', 'revenue')->count() }} records</p>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-red-700 uppercase tracking-wide">Total Expenses</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-red-900">Ksh {{ number_format($totalExpenses, 0) }}</p>
            <p class="text-xs text-red-700 mt-1">{{ $records->where('type', 'expense')->count() }} records</p>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Other Income</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-blue-900">Ksh {{ number_format($totalOtherIncome, 0) }}</p>
            <p class="text-xs text-blue-700 mt-1">{{ $records->where('type', 'other_income')->count() }} records</p>
        </div>

        <div class="bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Net Total</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 3v18h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18 7l-5 5-4-4-3 3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-slate-900">Ksh {{ number_format($netTotal, 0) }}</p>
            <p class="text-xs text-slate-600 mt-1">{{ $records->count() }} total records</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white border border-slate-200 rounded-lg p-4 mb-6 shadow-sm">
        <form method="GET" class="grid md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Type</label>
                <select name="type" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">All Types</option>
                    <option value="revenue" @selected(request('type') === 'revenue')>Revenue</option>
                    <option value="expense" @selected(request('type') === 'expense')>Expense</option>
                    <option value="adjustment" @selected(request('type') === 'adjustment')>Adjustment</option>
                    <option value="other_income" @selected(request('type') === 'other_income')>Other Income</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Category</label>
                <input type="text" name="category" value="{{ request('category') }}" placeholder="Filter by category"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                    Apply Filters
                </button>
                @if(request()->hasAny(['type', 'category', 'date_from']))
                    <a href="{{ route('partner.financial-records') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-900 border border-slate-300 rounded-lg hover:bg-slate-50">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Financial Records Table --}}
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Currency</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Order</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($records as $record)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">
                                    {{ \Illuminate\Support\Carbon::parse($record->occurred_at)->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ \Illuminate\Support\Carbon::parse($record->occurred_at)->format('h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $record->type === 'revenue' ? 'bg-emerald-100 text-emerald-800' :
                                       ($record->type === 'expense' ? 'bg-red-100 text-red-800' :
                                        ($record->type === 'other_income' ? 'bg-blue-100 text-blue-800' :
                                         'bg-amber-100 text-amber-800')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $record->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900 font-medium">
                                    {{ $record->category ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-900 max-w-md">
                                    {{ $record->description ? \Illuminate\Support\Str::limit($record->description, 60) : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold 
                                    {{ $record->type === 'expense' ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ $record->type === 'expense' ? '-' : '+' }}Ksh {{ number_format($record->amount, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $record->currency ?? 'KES' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($record->order)
                                    <a href="{{ route('admin.orders.show', $record->order) }}" target="_blank" 
                                       class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                        #{{ $record->order->id }}
                                    </a>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-300 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p class="text-sm font-medium text-slate-900 mb-1">No financial records found</p>
                                <p class="text-xs text-slate-500">Financial records will appear here once approved by administrators</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($records->hasPages())
        <div class="mt-6">
            {{ $records->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
