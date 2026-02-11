@extends('layouts.partner')

@section('page_title', 'Contributions')

@section('partner_content')
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Contributions & Transactions</h1>
                <p class="text-sm text-slate-600 mt-1">
                    Complete history of your capital contributions, withdrawals, and profit distributions.
                </p>
            </div>
            <a href="{{ route('partner.contributions.create') }}" 
               class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg shadow-sm transition-all hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                New Contribution
            </a>
        </div>
    </div>

    @php
        $approvedContributions = $contributions->where('status', 'approved')->where('type', 'contribution')->sum('amount');
        $approvedWithdrawals = $contributions->where('status', 'approved')->where('type', 'withdrawal')->sum('amount');
        $approvedProfit = $contributions->where('status', 'approved')->where('type', 'profit_distribution')->sum('amount');
        $pendingCount = $contributions->where('status', 'pending')->count();
        $totalNet = $approvedContributions - $approvedWithdrawals - $approvedProfit;
    @endphp

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Total Contributions</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-blue-900">Ksh {{ number_format($approvedContributions, 0) }}</p>
            <p class="text-xs text-blue-700 mt-1">{{ $contributions->where('type', 'contribution')->count() }} transactions</p>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-red-700 uppercase tracking-wide">Total Withdrawals</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-red-900">Ksh {{ number_format($approvedWithdrawals, 0) }}</p>
            <p class="text-xs text-red-700 mt-1">{{ $contributions->where('type', 'withdrawal')->count() }} transactions</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">Profit Distributed</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-emerald-900">Ksh {{ number_format($approvedProfit, 0) }}</p>
            <p class="text-xs text-emerald-700 mt-1">{{ $contributions->where('type', 'profit_distribution')->count() }} distributions</p>
        </div>

        <div class="bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Net Position</p>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 3v18h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18 7l-5 5-4-4-3 3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <p class="text-2xl font-bold text-slate-900">Ksh {{ number_format($totalNet, 0) }}</p>
            @if($pendingCount > 0)
                <p class="text-xs text-amber-700 mt-1 font-medium">{{ $pendingCount }} pending approval</p>
            @else
                <p class="text-xs text-slate-600 mt-1">All approved</p>
            @endif
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white border border-slate-200 rounded-lg p-4 mb-6 shadow-sm">
        <form method="GET" class="grid md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Type</label>
                <select name="type" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">All Types</option>
                    <option value="contribution" @selected(request('type') === 'contribution')>Contributions</option>
                    <option value="withdrawal" @selected(request('type') === 'withdrawal')>Withdrawals</option>
                    <option value="profit_distribution" @selected(request('type') === 'profit_distribution')>Profit Distributions</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Fund Type</label>
                <select name="fund_type" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">All Funds</option>
                    <option value="investment" @selected(request('fund_type') === 'investment')>Investment</option>
                    <option value="welfare" @selected(request('fund_type') === 'welfare')>Welfare</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Status</label>
                <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">All Statuses</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>
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
                @if(request()->hasAny(['type', 'status', 'date_from']))
                    <a href="{{ route('partner.contributions') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-900 border border-slate-300 rounded-lg hover:bg-slate-50">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Fund</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Currency</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse($contributions as $contribution)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">
                                    {{ \Illuminate\Support\Carbon::parse($contribution->contributed_at)->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ \Illuminate\Support\Carbon::parse($contribution->contributed_at)->format('h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $contribution->type === 'contribution' ? 'bg-blue-100 text-blue-800' :
                                       ($contribution->type === 'withdrawal' ? 'bg-red-100 text-red-800' :
                                        'bg-emerald-100 text-emerald-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $contribution->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold 
                                    {{ $contribution->type === 'contribution' ? 'text-blue-600' :
                                       ($contribution->type === 'withdrawal' ? 'text-red-600' :
                                        'text-emerald-600') }}">
                                    {{ $contribution->type === 'withdrawal' ? '-' : '+' }}Ksh {{ number_format($contribution->amount, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $contribution->fund_type ? ucfirst($contribution->fund_type) : 'Investment' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ $contribution->currency ?? 'KES' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $contribution->status === 'approved' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' :
                                       ($contribution->status === 'pending' ? 'bg-amber-100 text-amber-800 border border-amber-200' :
                                        'bg-red-100 text-red-800 border border-red-200') }}">
                                    {{ ucfirst($contribution->status) }}
                                </span>
                                @if($contribution->approved_at)
                                    <div class="text-xs text-slate-500 mt-1">
                                        Approved: {{ \Illuminate\Support\Carbon::parse($contribution->approved_at)->format('M d, Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-900 font-mono">
                                    {{ $contribution->reference ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-600 max-w-xs">
                                    {{ $contribution->notes ? \Illuminate\Support\Str::limit($contribution->notes, 50) : '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-300 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p class="text-sm font-medium text-slate-900 mb-1">No contributions found</p>
                                <p class="text-xs text-slate-500">Get started by submitting your first contribution</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($contributions->hasPages())
        <div class="mt-6">
            {{ $contributions->appends(request()->query())->links() }}
        </div>
    @endif
@endsection
