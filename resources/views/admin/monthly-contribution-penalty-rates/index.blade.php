@extends('layouts.admin')

@section('page_title', 'Monthly Contribution Penalty Rates')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Monthly Contribution Penalty Rates</h1>
            <p class="text-xs text-slate-500">Configure penalty rates for monthly contribution arrears. New rates take effect from the next month.</p>
        </div>
        <a href="{{ route('admin.monthly-contribution-penalty-rates.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow-sm">
            Add New Rate
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Current Rate Highlight --}}
    @if($currentRate)
    <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-emerald-900 mb-1">Currently Active Rate</h3>
                <p class="text-xs text-emerald-700">
                    <strong>{{ $currentRate->name }}</strong> — 
                    {{ number_format($currentRate->rate * 100, 2) }}% 
                    (Effective from {{ $currentRate->effective_from->format('F Y') }})
                </p>
                @if($currentRate->description)
                    <p class="text-[10px] text-emerald-600 mt-1">{{ $currentRate->description }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Next Month Rate Notice --}}
    @if($nextMonthRate && $nextMonthRate->id !== ($currentRate->id ?? null))
    <div class="mb-4 bg-amber-50 border border-amber-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-amber-900 mb-1">Upcoming Rate Change</h3>
                <p class="text-xs text-amber-700">
                    A new rate will take effect from <strong>{{ $nextMonthRate->effective_from->format('F Y') }}</strong>: 
                    <strong>{{ $nextMonthRate->name }}</strong> — {{ number_format($nextMonthRate->rate * 100, 2) }}%
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Rate</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Effective From</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Effective To</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Created By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penaltyRates as $rate)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $rate->name }}</div>
                                @if($rate->description)
                                    <div class="text-xs text-slate-500 mt-1">{{ Str::limit($rate->description, 60) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700 font-medium">{{ number_format($rate->rate * 100, 2) }}%</td>
                            <td class="px-4 py-3 text-slate-700">{{ $rate->effective_from->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $rate->effective_to ? $rate->effective_to->format('M d, Y') : 'Current' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($rate->is_active)
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium bg-emerald-100 text-emerald-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium bg-slate-100 text-slate-600">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                {{ $rate->creator->name ?? 'N/A' }}<br>
                                <span class="text-[10px]">{{ $rate->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.monthly-contribution-penalty-rates.edit', $rate) }}"
                                   class="text-amber-600 hover:text-amber-700 text-xs">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No penalty rates configured. <a href="{{ route('admin.monthly-contribution-penalty-rates.create') }}" class="text-emerald-600 hover:underline">Create one</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
