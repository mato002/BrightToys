@extends('layouts.admin')

@section('page_title', 'Budget Reports')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Books of Account
            </a>
            <h1 class="text-lg font-semibold text-slate-900">Budget Reports</h1>
            <p class="text-xs text-slate-500">Budget analysis for branches & estimates (placeholder).</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <select class="border border-slate-200 rounded px-3 py-2 text-sm">
                <option>{{ now()->year }}</option>
            </select>
            <select class="border border-slate-200 rounded px-3 py-2 text-sm">
                <option>-- Month --</option>
            </select>
            <select class="border border-slate-200 rounded px-3 py-2 text-sm">
                <option>-- Expense Book --</option>
            </select>
            <select class="border border-slate-200 rounded px-3 py-2 text-sm">
                <option>-- Branch --</option>
            </select>
            <button class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded">
                Create
            </button>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="admin-table-scroll overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Expense Account</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Branch</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Budget</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Spent</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Balance</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Var %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                            No budget data yet. Hook up budgets to see performance here.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

