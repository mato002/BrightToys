@extends('layouts.app')

@section('title', 'Partner Dashboard')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Partner Dashboard</h1>
            <p class="text-sm text-slate-600 mt-1">Read-only access to financial records and your partnership information.</p>
        </div>

        {{-- Partner Info Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">{{ $partner->name }}</h2>
                    <p class="text-sm text-slate-600">{{ $partner->email ?? '—' }}</p>
                </div>
                @if($currentOwnership)
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Ownership Share</p>
                        <p class="text-2xl font-bold text-emerald-600">{{ number_format($currentOwnership->percentage, 2) }}%</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="grid md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs text-slate-500 mb-1">Your Contributions</p>
                <p class="text-lg font-semibold text-blue-600">${{ number_format($totalContributions ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs text-slate-500 mb-1">Your Withdrawals</p>
                <p class="text-lg font-semibold text-amber-600">${{ number_format($totalWithdrawals ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs text-slate-500 mb-1">Profit Distribution</p>
                <p class="text-lg font-semibold text-emerald-600">${{ number_format($totalProfitDistribution ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs text-slate-500 mb-1">Your Share ({{ $currentOwnership ? number_format($currentOwnership->percentage, 2) : 0 }}%)</p>
                <p class="text-lg font-semibold text-slate-900">${{ number_format($partnerShare ?? 0, 2) }}</p>
            </div>
        </div>

        {{-- Business Overview --}}
        <div class="grid md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs text-slate-500 mb-1">Total Revenue</p>
                <p class="text-lg font-semibold text-emerald-600">${{ number_format($totalRevenue ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs text-slate-500 mb-1">Total Expenses</p>
                <p class="text-lg font-semibold text-red-600">${{ number_format($totalExpenses ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs text-slate-500 mb-1">Net Profit</p>
                <p class="text-lg font-semibold {{ ($netProfit ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    ${{ number_format($netProfit ?? 0, 2) }}
                </p>
            </div>
        </div>

        {{-- Recent Contributions --}}
        @if($recentContributions->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Recent Contributions</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentContributions as $contribution)
                            <tr>
                                <td class="px-4 py-2">{{ $contribution->contributed_at->format('d M Y') }}</td>
                                <td class="px-4 py-2">{{ ucfirst(str_replace('_', ' ', $contribution->type)) }}</td>
                                <td class="px-4 py-2 font-medium">{{ $contribution->currency }} {{ number_format($contribution->amount, 2) }}</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                        {{ $contribution->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($contribution->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <a href="{{ route('partner.contributions') }}" class="text-sm text-emerald-600 hover:text-emerald-700">
                    View all contributions →
                </a>
            </div>
        </div>
        @endif

        {{-- Quick Links --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Quick Links</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <a href="{{ route('partner.financial-records') }}" 
                   class="flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50">
                    <span class="text-sm text-slate-700">View Financial Records</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ route('partner.contributions') }}" 
                   class="flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50">
                    <span class="text-sm text-slate-700">View Contributions</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
