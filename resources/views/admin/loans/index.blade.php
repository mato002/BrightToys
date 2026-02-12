@extends('layouts.admin')

@section('page_title', 'Loans')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-emerald-500 mb-1">Financial</p>
            <h1 class="text-lg font-semibold text-slate-900">Loans</h1>
            <p class="text-xs text-slate-500">Registered bank and SACCO loans linked to projects.</p>
        </div>
        <div class="flex flex-col md:flex-row gap-2 md:items-center">
            <form method="GET" class="flex flex-col md:flex-row gap-2 text-xs">
                <div class="flex items-center gap-2">
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Search lender or project..."
                           class="border border-slate-200 rounded px-3 py-1.5 text-xs w-full md:w-56 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                    <select name="status"
                            class="border border-slate-200 rounded px-2 py-1.5 text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">All statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="repaid" {{ request('status') === 'repaid' ? 'selected' : '' }}>Repaid</option>
                        <option value="in_arrears" {{ request('status') === 'in_arrears' ? 'selected' : '' }}>In arrears</option>
                    </select>
                    <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-500 hover:bg-emerald-600 text-white font-semibold">
                        Filter
                    </button>
                </div>
            </form>
            @if(auth()->user()->hasPermission('loans.create'))
                <a href="{{ route('admin.loans.create') }}"
                   class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Register Loan
                </a>
            @endif
        </div>
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

