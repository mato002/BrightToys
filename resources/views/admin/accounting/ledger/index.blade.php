@extends('layouts.admin')

@section('page_title', 'General Ledger')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Books of Account
            </a>
            <h1 class="text-lg font-semibold text-slate-900">General Ledger</h1>
            <p class="text-xs text-slate-500">Run account-wise ledger statements by date range and branch.</p>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg p-4 text-sm mb-4 max-w-5xl w-full">
        <form method="GET" class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Ledger Account to Run</label>
                <select name="account_id" class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                    <option value="">Run all accounts</option>
                    {{-- TODO: populate with Chart of Accounts --}}
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Branch</label>
                <select name="branch" class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
                    <option value="">Head Office</option>
                    {{-- TODO: populate branches --}}
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Report From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1 text-slate-700">Report To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-400">
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-6 py-2.5 rounded-md shadow-sm">
                    Run
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="admin-table-scroll overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Description</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Debit</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Credit</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    {{-- TODO: render ledger lines --}}
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-sm">
                            Ledger implementation placeholder. No data to display yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

