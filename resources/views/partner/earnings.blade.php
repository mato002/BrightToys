@extends('layouts.partner')

@section('page_title', 'My Earnings')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">My Earnings</h1>
        <p class="text-xs text-slate-500">
            View your profit distributions, earnings history, and projected earnings based on your ownership share.
        </p>
    </div>

    {{-- Earnings Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Total Earnings</p>
            <p class="text-2xl font-bold text-emerald-600">Ksh {{ number_format($totalEarnings, 0) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">All-time profit distributions</p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">YTD Earnings</p>
            <p class="text-2xl font-bold text-amber-600">Ksh {{ number_format($ytdEarnings, 0) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Year to date ({{ now()->format('Y') }})</p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Projected Earnings</p>
            <p class="text-2xl font-bold {{ $projectedEarnings >= 0 ? 'text-slate-900' : 'text-red-600' }}">
                Ksh {{ number_format($projectedEarnings, 0) }}
            </p>
            <p class="text-[11px] text-slate-500 mt-1">
                Based on {{ optional($currentOwnership)->percentage ?? 0 }}% ownership
            </p>
        </div>
    </div>

    {{-- Ownership Info --}}
    @if($currentOwnership)
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-700 mb-1">Current Ownership Share</p>
                <p class="text-lg font-bold text-emerald-600">{{ number_format($currentOwnership->percentage, 2) }}%</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500">Effective from</p>
                <p class="text-xs font-medium text-slate-700">
                    {{ \Illuminate\Support\Carbon::parse($currentOwnership->effective_from)->format('M d, Y') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Monthly Earnings Chart --}}
    @if($monthlyEarnings->sum('earnings') > 0)
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Monthly Earnings (Last 12 Months)</h2>
        <div class="space-y-2">
            @foreach($monthlyEarnings as $month)
                @if($month['earnings'] > 0)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-600 w-20">{{ $month['month'] }}</span>
                    <div class="flex-1 mx-3">
                        <div class="h-4 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" 
                                 style="width: {{ min(100, ($month['earnings'] / $monthlyEarnings->max('earnings')) * 100) }}%">
                            </div>
                        </div>
                    </div>
                    <span class="font-semibold text-slate-900 w-24 text-right">Ksh {{ number_format($month['earnings'], 0) }}</span>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Earnings History Table --}}
    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden shadow-sm">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-900">Earnings History</h2>
            <span class="text-xs text-slate-500">{{ $earningsHistory->total() }} distribution{{ $earningsHistory->total() !== 1 ? 's' : '' }}</span>
        </div>

        @if($earningsHistory->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-500">No earnings recorded yet.</p>
                <p class="text-xs text-slate-400 mt-1">Profit distributions will appear here once approved by admins.</p>
            </div>
        @else
            <div class="overflow-x-auto partner-table-scroll">
                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-3 py-2 text-left">Date</th>
                        <th class="px-3 py-2 text-left">Amount</th>
                        <th class="px-3 py-2 text-left">Currency</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Reference</th>
                        <th class="px-3 py-2 text-left">Notes</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($earningsHistory as $earning)
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="px-3 py-2">
                                {{ \Illuminate\Support\Carbon::parse($earning->contributed_at)->format('M d, Y') }}
                            </td>
                            <td class="px-3 py-2 font-semibold text-emerald-600">
                                Ksh {{ number_format($earning->amount, 0) }}
                            </td>
                            <td class="px-3 py-2">
                                {{ $earning->currency ?? 'KES' }}
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                                    {{ $earning->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                                       ($earning->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-100' :
                                        'bg-red-50 text-red-700 border border-red-100') }}">
                                    {{ ucfirst($earning->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                {{ $earning->reference ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-slate-600">
                                {{ $earning->notes ? Str::limit($earning->notes, 50) : '-' }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-slate-100">
                {{ $earningsHistory->links() }}
            </div>
        @endif
    </div>

    {{-- Info Box --}}
    <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 mr-2 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-xs text-amber-700">
                <p class="font-semibold mb-1">About Earnings</p>
                <p class="text-amber-600">
                    Earnings represent profit distributions based on your ownership share. These are calculated from the business's net profit and distributed according to your percentage ownership. All distributions require admin approval before being finalized.
                </p>
            </div>
        </div>
    </div>
@endsection
