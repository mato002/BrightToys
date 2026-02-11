@extends('layouts.admin')

@section('page_title', 'Penalty Rates')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Penalty Rates</h1>
            <p class="text-xs text-slate-500">Configure penalty rates for overdue entry contribution payments.</p>
        </div>
        <a href="{{ route('admin.penalty-rates.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow-sm">
            Add Penalty Rate
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Active Rate Highlight --}}
    @if($activeRate)
    <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-emerald-900 mb-1">Currently Active Rate</h3>
                <p class="text-xs text-emerald-700">
                    <strong>{{ $activeRate->name }}</strong> — 
                    @if($activeRate->calculation_method === 'percentage_per_day')
                        {{ number_format($activeRate->rate, 2) }}% per day
                    @elseif($activeRate->calculation_method === 'percentage_of_installment')
                        {{ number_format($activeRate->rate, 2) }}% of installment amount
                    @else
                        {{ number_format($activeRate->rate, 2) }} per day
                    @endif
                    @if($activeRate->grace_period_days > 0)
                        ({{ $activeRate->grace_period_days }} day grace period)
                    @endif
                    @if($activeRate->max_penalty_amount)
                        | Max: {{ number_format($activeRate->max_penalty_amount, 2) }}
                    @endif
                </p>
                @if($activeRate->description)
                    <p class="text-[10px] text-emerald-600 mt-1">{{ $activeRate->description }}</p>
                @endif
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Calculation Method</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Grace Period</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Max Penalty</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
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
                            <td class="px-4 py-3 text-slate-700 font-medium">{{ number_format($rate->rate, 2) }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                <span class="text-xs">
                                    @if($rate->calculation_method === 'percentage_per_day')
                                        % per day
                                    @elseif($rate->calculation_method === 'percentage_of_installment')
                                        % of installment
                                    @else
                                        Fixed per day
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $rate->grace_period_days }} days</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $rate->max_penalty_amount ? number_format($rate->max_penalty_amount, 2) : 'No limit' }}
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
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if(!$rate->is_active)
                                        <form action="{{ route('admin.penalty-rates.activate', $rate) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-emerald-600 hover:text-emerald-700 text-xs underline">
                                                Activate
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.penalty-rates.edit', $rate) }}"
                                       class="text-amber-600 hover:text-amber-700 text-xs">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No penalty rates configured. <a href="{{ route('admin.penalty-rates.create') }}" class="text-emerald-600 hover:underline">Create one</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
