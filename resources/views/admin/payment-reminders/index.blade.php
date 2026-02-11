@extends('layouts.admin')

@section('page_title', 'Payment Reminders')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Payment Reminders</h1>
            <p class="text-xs text-slate-500">Track overdue and upcoming entry contribution payments.</p>
        </div>
    </div>

    {{-- Overdue Installments --}}
    @if($overdueInstallments->count() > 0)
    <div class="mb-6 bg-white border border-red-200 rounded-lg p-4">
        <div class="flex items-center gap-2 mb-4">
            <span class="text-red-600 text-lg">⚠️</span>
            <h2 class="text-sm font-semibold text-red-900">Overdue Installments ({{ $overdueInstallments->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-red-50 border-b border-red-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Partner</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Installment #</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Due Date</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Amount</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Paid</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Penalty</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Days Overdue</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-red-100">
                    @foreach($overdueInstallments as $installment)
                    <tr>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.partners.show', $installment->paymentPlan->entryContribution->partner) }}" 
                               class="text-emerald-600 hover:text-emerald-700 underline">
                                {{ $installment->paymentPlan->entryContribution->partner->name }}
                            </a>
                        </td>
                        <td class="px-3 py-2 font-medium">#{{ $installment->installment_number }}</td>
                        <td class="px-3 py-2">{{ $installment->due_date->format('d M Y') }}</td>
                        <td class="px-3 py-2 font-medium">KES {{ number_format($installment->amount, 2) }}</td>
                        <td class="px-3 py-2">KES {{ number_format($installment->paid_amount, 2) }}</td>
                        <td class="px-3 py-2">
                            @if($installment->penalty_amount > 0)
                                <span class="font-semibold text-red-600">KES {{ number_format($installment->penalty_amount, 2) }}</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <span class="font-semibold text-red-600">{{ $installment->days_overdue ?? now()->diffInDays($installment->due_date) }}</span>
                        </td>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.partners.show', $installment->paymentPlan->entryContribution->partner) }}" 
                               class="text-emerald-600 hover:text-emerald-700 text-xs underline">
                                View Partner
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Upcoming Installments --}}
    @if($upcomingInstallments->count() > 0)
    <div class="mb-6 bg-white border border-amber-200 rounded-lg p-4">
        <div class="flex items-center gap-2 mb-4">
            <span class="text-amber-600 text-lg">📅</span>
            <h2 class="text-sm font-semibold text-amber-900">Upcoming Installments (Next 30 Days) - {{ $upcomingInstallments->count() }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-amber-50 border-b border-amber-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Partner</th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Installment #</th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Due Date</th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Amount</th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Days Until Due</th>
                        <th class="px-3 py-2 text-left font-semibold text-amber-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-100">
                    @foreach($upcomingInstallments as $installment)
                    <tr>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.partners.show', $installment->paymentPlan->entryContribution->partner) }}" 
                               class="text-emerald-600 hover:text-emerald-700 underline">
                                {{ $installment->paymentPlan->entryContribution->partner->name }}
                            </a>
                        </td>
                        <td class="px-3 py-2 font-medium">#{{ $installment->installment_number }}</td>
                        <td class="px-3 py-2">{{ $installment->due_date->format('d M Y') }}</td>
                        <td class="px-3 py-2 font-medium">KES {{ number_format($installment->amount, 2) }}</td>
                        <td class="px-3 py-2">
                            <span class="font-medium text-amber-600">{{ now()->diffInDays($installment->due_date) }}</span>
                        </td>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.partners.show', $installment->paymentPlan->entryContribution->partner) }}" 
                               class="text-emerald-600 hover:text-emerald-700 text-xs underline">
                                View Partner
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Partners Summary --}}
    @if($partnersWithContributions->count() > 0)
    <div class="bg-white border border-slate-100 rounded-lg p-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-4">Partners with Outstanding Balances</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Partner</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Total Amount</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Paid</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Outstanding</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Overdue</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Upcoming</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($partnersWithContributions as $item)
                    <tr>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.partners.show', $item['partner']) }}" 
                               class="text-emerald-600 hover:text-emerald-700 underline font-medium">
                                {{ $item['partner']->name }}
                            </a>
                        </td>
                        <td class="px-3 py-2">KES {{ number_format($item['entry_contribution']->total_amount, 2) }}</td>
                        <td class="px-3 py-2 text-emerald-600">KES {{ number_format($item['entry_contribution']->paid_amount, 2) }}</td>
                        <td class="px-3 py-2 font-semibold text-amber-600">KES {{ number_format($item['outstanding_balance'], 2) }}</td>
                        <td class="px-3 py-2">
                            @if($item['overdue_count'] > 0)
                                <span class="text-red-600 font-semibold">{{ $item['overdue_count'] }}</span>
                            @else
                                <span class="text-slate-400">0</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            @if($item['upcoming_count'] > 0)
                                <span class="text-amber-600 font-semibold">{{ $item['upcoming_count'] }}</span>
                            @else
                                <span class="text-slate-400">0</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <a href="{{ route('admin.partners.show', $item['partner']) }}" 
                               class="text-emerald-600 hover:text-emerald-700 text-xs underline">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white border border-slate-100 rounded-lg p-6 text-center">
        <p class="text-sm text-slate-500">No payment reminders at this time.</p>
    </div>
    @endif
@endsection
