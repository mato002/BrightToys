@extends('layouts.account')

@section('title', 'Group Loans')
@section('page_title', 'Group Loans')

@section('content')
    <div class="space-y-4">
        <div class="bg-white border rounded-lg p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-lg font-semibold">Group Loans</h1>
                    <p class="text-xs text-slate-500 mt-1">View all group loans, balances, and repayment progress (read-only)</p>
                </div>
            </div>

            <form method="GET" class="mb-4 grid md:grid-cols-3 gap-3 text-xs">
                <div>
                    <label class="block text-[11px] font-semibold mb-1 text-gray-600">Status</label>
                    <select name="status" class="border rounded px-3 py-1.5 text-xs w-full">
                        <option value="">All statuses</option>
                        <option value="active" @selected(request('status') === 'active')>On Track</option>
                        <option value="in_arrears" @selected(request('status') === 'in_arrears')>Behind Schedule</option>
                        <option value="repaid" @selected(request('status') === 'repaid')>Fully Repaid</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold mb-1 text-gray-600">Search Lender</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Bank/SACCO name"
                           class="border rounded px-3 py-1.5 text-xs w-full">
                </div>
                <div class="flex items-end gap-2">
                    <button class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-1.5 rounded">
                        Filter
                    </button>
                    @if(request()->hasAny(['status', 'search']))
                        <a href="{{ route('account.loans') }}" class="text-xs text-gray-500 hover:text-gray-700">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            @forelse($loans as $loan)
                @php
                    $totalScheduled = $loan->schedules->sum('total_due');
                    $totalPaid = $loan->repayments->sum('amount_paid');
                    $totalOutstanding = max(0, $totalScheduled - $totalPaid);
                    $progress = $totalScheduled > 0 ? ($totalPaid / $totalScheduled) * 100 : 0;
                @endphp
                <div class="border border-slate-200 rounded-xl mb-4 p-5 bg-white shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3 pb-4 border-b border-slate-100">
                        <div>
                            <p class="font-bold text-slate-900 text-base">{{ $loan->lender_name }}</p>
                            <p class="text-xs text-slate-500 mt-1">
                                Amount: <span class="font-semibold">Ksh {{ number_format($loan->amount, 0) }}</span>
                                · Interest: <span class="font-semibold">{{ number_format($loan->interest_rate * 100, 2) }}% p.a.</span>
                                · Tenure: <span class="font-semibold">{{ $loan->tenure_months }} months</span>
                            </p>
                            @if($loan->project)
                                <p class="text-xs text-slate-500 mt-1">Project: <span class="font-medium">{{ $loan->project->name }}</span></p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-red-600 text-lg">
                                Ksh {{ number_format($totalOutstanding, 0) }}
                            </p>
                            <p class="text-[10px] text-slate-500 mt-1">Outstanding</p>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium mt-2
                                @if($loan->status === 'repaid') bg-blue-100 text-blue-700
                                @elseif($loan->status === 'in_arrears') bg-red-100 text-red-700
                                @else bg-emerald-100 text-emerald-700
                                @endif">
                                @if($loan->status === 'repaid')
                                    Fully Repaid
                                @elseif($loan->status === 'in_arrears')
                                    Behind Schedule
                                @else
                                    On Track
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-slate-600">Repayment Progress</span>
                            <span class="text-xs font-semibold text-slate-900">{{ number_format($progress, 1) }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-emerald-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="flex items-center justify-between mt-1 text-[10px] text-slate-500">
                            <span>Paid: Ksh {{ number_format($totalPaid, 0) }}</span>
                            <span>Total: Ksh {{ number_format($totalScheduled, 0) }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('account.loans.show', $loan) }}" 
                           class="text-xs text-amber-600 hover:text-amber-700 font-semibold px-3 py-1.5 border border-amber-200 rounded-lg hover:bg-amber-50 transition-colors">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 border border-slate-200 rounded-xl bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                    <p class="text-sm font-medium text-slate-500">No loans found</p>
                    <p class="text-xs text-slate-400 mt-1">No loans match your search criteria.</p>
                </div>
            @endforelse

            @if($loans->hasPages())
                <div class="mt-4">
                    {{ $loans->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
