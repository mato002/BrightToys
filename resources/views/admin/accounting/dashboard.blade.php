@extends('layouts.admin')

@section('page_title', 'Books of Account')

@section('content')
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="text-emerald-600 hover:text-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Books of Account</h1>
        </div>
        <p class="text-xs md:text-sm text-slate-500">
            Comprehensive accounting module for managing journal entries, chart of accounts, financial reports, and more.
        </p>
    </div>

    {{-- Accounting Features Grid --}}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Create Journal Entry --}}
        <a href="{{ route('admin.accounting.journal.create') }}"
           class="group relative overflow-hidden rounded-2xl border border-emerald-100 bg-white p-6 shadow-sm shadow-emerald-50 hover:shadow-md hover:border-emerald-200 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="1.6"/>
                        <path d="M12 8v8M8 12h8" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Create Journal Entry</h3>
            <p class="text-xs text-slate-500">Manual Post an Entry to Journal Record</p>
        </a>

        {{-- View Posted Entries --}}
        <a href="{{ route('admin.accounting.posted-entries.index') }}"
           class="group relative overflow-hidden rounded-2xl border border-sky-100 bg-white p-6 shadow-sm shadow-sky-50 hover:shadow-md hover:border-sky-200 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-600 group-hover:bg-sky-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="8" stroke-width="1.6"/>
                        <path d="M21 21l-4.35-4.35" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">View Posted Entries</h3>
            <p class="text-xs text-slate-500">Retrieve and Manage posted entries</p>
        </a>

        {{-- Chart of Accounts & Rules --}}
        <a href="{{ route('admin.accounting.chart-of-accounts.index') }}"
           class="group relative overflow-hidden rounded-2xl border border-violet-100 bg-white p-6 shadow-sm shadow-violet-50 hover:shadow-md hover:border-violet-200 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600 group-hover:bg-violet-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.6"/>
                        <path d="M3 9h18M9 3v18" stroke-width="1.6"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Chart of Accounts & Rules</h3>
            <p class="text-xs text-slate-500">List of Accounts used by the Company & accounting rules</p>
        </a>

        {{-- Company Expenses --}}
        <a href="{{ route('admin.accounting.expenses.index') }}"
           class="group relative overflow-hidden rounded-2xl border border-amber-100 bg-white p-6 shadow-sm shadow-amber-50 hover:shadow-md hover:border-amber-200 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 group-hover:bg-amber-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Company Expenses</h3>
            <p class="text-xs text-slate-500">View and manage Company Expenses</p>
        </a>

        {{-- General Ledger --}}
        <a href="{{ route('admin.accounting.ledger.index') }}"
           class="group relative overflow-hidden rounded-2xl border border-indigo-100 bg-white p-6 shadow-sm shadow-indigo-50 hover:shadow-md hover:border-indigo-200 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 7h18M3 12h18M3 17h18" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">General Ledger</h3>
            <p class="text-xs text-slate-500">Description for the accounts activities</p>
        </a>

        {{-- Accruals & Reports --}}
        <a href="{{ route('admin.accounting.reports.index') }}"
           class="group relative overflow-hidden rounded-2xl border border-rose-100 bg-white p-6 shadow-sm shadow-rose-50 hover:shadow-md hover:border-rose-200 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-600 group-hover:bg-rose-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.6"/>
                        <path d="M3 9h18M9 3v18" stroke-width="1.6"/>
                        <path d="M9 12h6" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Accruals & Reports</h3>
            <p class="text-xs text-slate-500">Income statement, Trial Balance & Balance sheet reports</p>
        </a>

        {{-- Employee Payroll --}}
        <a href="{{ route('admin.accounting.payroll.index') }}"
           class="group relative overflow-hidden rounded-2xl border border-teal-100 bg-white p-6 shadow-sm shadow-teal-50 hover:shadow-md hover:border-teal-200 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-50 text-teal-600 group-hover:bg-teal-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 2L4 5v6c0 5.55 3.84 10.74 8 12 4.16-1.26 8-6.45 8-12V5l-8-3z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Employee Payroll</h3>
            <p class="text-xs text-slate-500">Salary dues, payroll and Payslips</p>
        </a>

        {{-- Budget Reports --}}
        <a href="{{ route('admin.accounting.budget.index') }}"
           class="group relative overflow-hidden rounded-2xl border border-purple-100 bg-white p-6 shadow-sm shadow-purple-50 hover:shadow-md hover:border-purple-200 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 7h18M3 12h18M3 17h18" stroke-width="1.6" stroke-linecap="round"/>
                        <circle cx="6" cy="7" r="1" fill="currentColor"/>
                        <circle cx="6" cy="12" r="1" fill="currentColor"/>
                        <circle cx="6" cy="17" r="1" fill="currentColor"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Budget Reports</h3>
            <p class="text-xs text-slate-500">Budget analysis for branches & Estimates</p>
        </a>

        {{-- Company Assets --}}
        <a href="{{ route('admin.accounting.assets.index') }}"
           class="group relative overflow-hidden rounded-2xl border border-cyan-100 bg-white p-6 shadow-sm shadow-cyan-50 hover:shadow-md hover:border-cyan-200 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600 group-hover:bg-cyan-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 7l8-4 8 4-8 4-8-4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 17l8 4 8-4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Company Assets</h3>
            <p class="text-xs text-slate-500">Register and manage company assets</p>
        </a>

        {{-- Accounts Reconciliation --}}
        <a href="{{ route('admin.accounting.reconciliation.index') }}"
           class="group relative overflow-hidden rounded-2xl border border-orange-100 bg-white p-6 shadow-sm shadow-orange-50 hover:shadow-md hover:border-orange-200 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600 group-hover:bg-orange-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M8 7h8M8 12h8M8 17h8" stroke-width="1.6" stroke-linecap="round"/>
                        <path d="M3 3v18h18V3z" stroke-width="1.6"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Accounts Reconciliation</h3>
            <p class="text-xs text-slate-500">Reconcile Operating Financial accounts</p>
        </a>

        {{-- Loans Management --}}
        <a href="{{ route('admin.loans.index') }}"
           class="group relative overflow-hidden rounded-2xl border border-sky-100 bg-white p-6 shadow-sm shadow-sky-50 hover:shadow-md hover:border-sky-200 transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-600 group-hover:bg-sky-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 6h16v12H4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 10h16" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-base font-semibold text-slate-900 mb-2">Loans Management</h3>
            <p class="text-xs text-slate-500">Register loans, view schedules, and track repayments & reconciliation</p>
        </a>
    </div>
@endsection
