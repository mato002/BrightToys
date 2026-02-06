@extends('layouts.app')

@section('title', 'My Contributions')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">My Contributions</h1>
            <p class="text-sm text-slate-600 mt-1">View your capital contributions, withdrawals, and profit distributions.</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($contributions as $contribution)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $contribution->contributed_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                        {{ $contribution->type === 'contribution' ? 'bg-blue-100 text-blue-700' : 
                                           ($contribution->type === 'withdrawal' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700') }}">
                                        {{ ucfirst(str_replace('_', ' ', $contribution->type)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold">{{ $contribution->currency }} {{ number_format($contribution->amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                        {{ $contribution->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 
                                           ($contribution->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ ucfirst($contribution->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $contribution->reference ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                    No contributions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $contributions->links() }}
        </div>

        <div class="mt-6">
            <a href="{{ route('partner.dashboard') }}" class="text-sm text-emerald-600 hover:text-emerald-700">
                ← Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
