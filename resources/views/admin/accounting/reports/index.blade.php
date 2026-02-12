@extends('layouts.admin')

@section('page_title', 'Accruals & Reports')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Books of Account
            </a>
            <h1 class="text-lg font-semibold text-slate-900">Accruals & Reports</h1>
            <p class="text-xs text-slate-500">Income statement, trial balance and balance sheet reports (placeholder).</p>
        </div>
        <div class="flex items-center gap-2">
            <select class="border border-slate-200 rounded px-3 py-2 text-sm">
                <option>Income Report</option>
                <option>Trial Balance</option>
                <option>Balance Sheet</option>
            </select>
            <select class="border border-slate-200 rounded px-3 py-2 text-sm">
                <option>{{ now()->year }}</option>
            </select>
            <select class="border border-slate-200 rounded px-3 py-2 text-sm">
                <option>All Months</option>
            </select>
            <button class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Export
            </button>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="admin-table-scroll overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Income Account</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Current Period</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Previous Period</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="px-4 py-3 text-slate-700">Loan Interest</td>
                        <td class="px-4 py-3 text-right text-slate-700">0</td>
                        <td class="px-4 py-3 text-right text-slate-700">0</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-slate-500 text-sm">
                            Reports implementation placeholder. Hook up real data when ready.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

