@extends('layouts.admin')

@section('page_title', 'Accounts Reconciliation')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Books of Account
            </a>
            <h1 class="text-lg font-semibold text-slate-900">Accounts Reconciliation</h1>
            <p class="text-xs text-slate-500">Reconcile operating financial accounts against statements.</p>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg p-4 text-sm mb-4 max-w-5xl w-full">
        <form method="GET" class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Account</label>
                <select name="account_id" class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400" onchange="this.form.submit()">
                    <option value="">Select account</option>
                    @foreach($accounts ?? [] as $account)
                        <option value="{{ $account->id }}" {{ $accountId == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Reconciliation Date</label>
                <input type="date" name="date" value="{{ request('date', now()->format('Y-m-d')) }}" class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-6 py-2.5 rounded-md shadow-sm">
                    Run Reconciliation
                </button>
            </div>
        </form>
    </div>

    @if($accountId)
        <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
            <div class="admin-table-scroll overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Opening Balance</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Closing Balance</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Reconciled Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Reconciled By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($reconciliations ?? [] as $reconciliation)
                            <tr>
                                <td class="px-4 py-3 text-slate-700">{{ $reconciliation->reconciliation_date->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-slate-700">Ksh {{ number_format($reconciliation->opening_balance, 2) }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($reconciliation->closing_balance, 2) }}</td>
                                <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($reconciliation->reconciled_amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded {{ $reconciliation->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($reconciliation->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $reconciliation->reconciler->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                                    No reconciliation records found for this account.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white border border-slate-100 rounded-lg p-8 text-center">
            <p class="text-slate-500">Please select an account to view reconciliation records.</p>
        </div>
    @endif
@endsection
