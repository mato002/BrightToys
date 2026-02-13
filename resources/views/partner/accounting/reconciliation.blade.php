@extends('layouts.partner')

@section('page_title', 'Account Reconciliation')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Account Reconciliation</h1>
        <p class="text-xs text-slate-500">
            View account reconciliation reports and statements.
        </p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Reconciled</p>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalReconciled ?? 0, 0) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Completed reconciliations</p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Pending</p>
            <p class="text-2xl font-bold text-amber-600">{{ number_format($totalPending ?? 0, 0) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Awaiting reconciliation</p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Discrepancies</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($totalDiscrepancies ?? 0, 0) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Require attention</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Account</label>
                <select name="account_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Accounts</option>
                    @foreach($accounts ?? [] as $account)
                        <option value="{{ $account->id }}" @selected(request('account_id') == $account->id)>{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status</label>
                <select name="status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses ?? [] as $status)
                        <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Actions</label>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                        Filter
                    </button>
                    <a href="{{ route('partner.accounting.reconciliation') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold px-4 py-2 rounded-lg">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Reconciliation Table --}}
    <div class="bg-white rounded-lg border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-[11px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Date</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Account</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Opening Balance</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Closing Balance</th>
                        <th class="px-3 py-2 text-right font-semibold text-slate-700">Reconciled Amount</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Status</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Reconciled By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reconciliations ?? [] as $reconciliation)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-2 text-slate-700">{{ $reconciliation->reconciliation_date->format('M d, Y') }}</td>
                            <td class="px-3 py-2 text-slate-700">
                                {{ $reconciliation->account->code ?? 'N/A' }} - {{ $reconciliation->account->name ?? 'N/A' }}
                            </td>
                            <td class="px-3 py-2 text-right text-slate-900">KES {{ number_format($reconciliation->opening_balance, 2) }}</td>
                            <td class="px-3 py-2 text-right text-slate-900">KES {{ number_format($reconciliation->closing_balance, 2) }}</td>
                            <td class="px-3 py-2 text-right text-emerald-600">KES {{ number_format($reconciliation->reconciled_amount, 2) }}</td>
                            <td class="px-3 py-2">
                                @if($reconciliation->status == 'reconciled')
                                    <span class="px-2 py-1 text-[10px] font-semibold rounded bg-emerald-100 text-emerald-800">Reconciled</span>
                                @elseif($reconciliation->status == 'pending')
                                    <span class="px-2 py-1 text-[10px] font-semibold rounded bg-amber-100 text-amber-800">Pending</span>
                                @else
                                    <span class="px-2 py-1 text-[10px] font-semibold rounded bg-red-100 text-red-800">Discrepancy</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-slate-600">
                                {{ $reconciliation->reconciler->name ?? 'N/A' }}
                                @if($reconciliation->reconciled_at)
                                    <br><span class="text-[10px] text-slate-400">{{ $reconciliation->reconciled_at->format('M d, Y') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-6 text-center text-slate-400 text-xs">
                                No reconciliation records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($reconciliations) && $reconciliations->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $reconciliations->links() }}
            </div>
        @endif
    </div>
@endsection
