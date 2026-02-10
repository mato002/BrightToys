@extends('layouts.admin')

@section('page_title', 'Loans')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-lg font-semibold">Loans</h1>
            <p class="text-xs text-slate-500">Registered bank and SACCO loans linked to projects.</p>
        </div>
        @if(auth()->user()->hasPermission('loans.create'))
            <a href="{{ route('admin.loans.create') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                Register Loan
            </a>
        @endif
    </div>

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Lender</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Project</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Amount</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Interest</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Tenure</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Status</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($loans as $loan)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2 text-xs text-slate-800">{{ $loan->lender_name }}</td>
                    <td class="px-4 py-2 text-xs text-slate-600">
                        {{ optional($loan->project)->name ?? '—' }}
                    </td>
                    <td class="px-4 py-2 text-xs font-semibold text-slate-900">
                        Ksh {{ number_format($loan->amount, 0) }}
                    </td>
                    <td class="px-4 py-2 text-xs text-slate-600">
                        {{ number_format($loan->interest_rate * 100, 2) }}% p.a.
                    </td>
                    <td class="px-4 py-2 text-xs text-slate-600">
                        {{ $loan->tenure_months }} months
                    </td>
                    <td class="px-4 py-2 text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                            @if($loan->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-100
                            @elseif($loan->status === 'repaid') bg-blue-50 text-blue-700 border border-blue-100
                            @else bg-amber-50 text-amber-700 border border-amber-100 @endif">
                            {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-xs">
                        <a href="{{ route('admin.loans.show', $loan) }}"
                           class="text-emerald-600 hover:text-emerald-700">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-xs text-slate-500">
                        No loans registered yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $loans->links() }}
    </div>
@endsection

