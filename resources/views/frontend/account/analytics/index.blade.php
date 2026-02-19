@extends('layouts.account')

@section('title', 'Spending Analytics')
@section('page_title', 'Spending Analytics')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">Analytics</span>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Spending Analytics</h1>
                <p class="text-xs md:text-sm text-slate-500 mt-1">Your purchase trends and spending overview</p>
            </div>
            <form method="GET" class="flex gap-2 items-center">
                <label class="text-sm font-medium text-slate-700">Year</label>
                <select name="year" onchange="this.form.submit()" class="border-2 border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500">
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-xl p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 mb-2">Total spending ({{ $year }})</p>
                <p class="text-3xl font-bold text-amber-900">Ksh {{ number_format($totalYearlySpent, 0) }}</p>
                <p class="text-xs text-amber-600 mt-1">Completed orders only</p>
            </div>
            <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600 mb-2">Orders in {{ $year }}</p>
                <p class="text-3xl font-bold text-slate-900">{{ $byMonth->sum() > 0 ? collect($byMonth)->filter()->count() : 0 }}</p>
            </div>
            <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600 mb-2">Budget tracking</p>
                <p class="text-3xl font-bold text-slate-900">Ksh {{ number_format($monthlyBudget, 0) }}</p>
                <p class="text-xs text-slate-500 mt-1">Set a monthly budget below</p>
            </div>
        </div>

        <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Monthly spending ({{ $year }})</h2>
            <div class="space-y-3">
                @php
                    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    $maxMonth = $byMonth->max() ?: 1;
                @endphp
                @foreach($months as $i => $label)
                    @php $m = $i + 1; $val = $byMonth[$m] ?? 0; @endphp
                    <div class="flex items-center gap-4">
                        <span class="w-10 text-sm font-medium text-slate-600">{{ $label }}</span>
                        <div class="flex-1 h-8 bg-slate-100 rounded-lg overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-lg transition-all" style="width: {{ $maxMonth > 0 ? min(100, 100 * $val / $maxMonth) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-slate-900 w-24 text-right">Ksh {{ number_format($val, 0) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Frequent purchases</h2>
            @if($frequentProducts->count() > 0)
                <ul class="space-y-2">
                    @foreach($frequentProducts as $item)
                        <li class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                            @if($item->product)
                                <a href="{{ route('product.show', $item->product->slug) }}" class="font-medium text-slate-900 hover:text-amber-600">{{ $item->product->name }}</a>
                            @else
                                <span class="font-medium text-slate-900">Product #{{ $item->product_id }}</span>
                            @endif
                            <span class="text-sm text-slate-600">{{ $item->total_qty }} bought</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-slate-500 text-sm">No completed orders in this period.</p>
            @endif
        </div>

        <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-3">Monthly budget (optional)</h2>
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <input type="hidden" name="year" value="{{ $year }}">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Budget amount (KES)</label>
                    <input type="number" name="budget" value="{{ $monthlyBudget }}" min="0" step="100" class="border-2 border-slate-300 rounded-lg px-3 py-2 w-40 focus:ring-2 focus:ring-amber-500">
                </div>
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-4 py-2 rounded-lg">Update</button>
            </form>
        </div>
    </div>
@endsection
