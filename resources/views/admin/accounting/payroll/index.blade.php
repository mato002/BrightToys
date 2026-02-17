@extends('layouts.admin')

@section('page_title', 'Employee Payroll')

@section('content')
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.accounting.dashboard') }}" class="text-emerald-600 hover:text-emerald-700 text-sm mb-2 inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Books of Account
            </a>
            <h1 class="text-lg font-semibold text-slate-900">Employee Payroll</h1>
            <p class="text-xs text-slate-500">Salary dues, payroll and payslips overview.</p>
        </div>
        <form method="GET" class="flex items-center gap-2 text-xs">
            <select name="year" class="border border-slate-200 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="month" class="border border-slate-200 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
            </select>
        </form>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden mb-4">
        <div class="px-6 py-4 border-b border-slate-200">
            <div class="flex justify-between items-center">
                <span class="text-sm font-semibold text-slate-900">Total Payroll for {{ date('F', mktime(0, 0, 0, $month ?? now()->month, 1)) }} {{ $year ?? now()->year }}</span>
                <span class="text-lg font-bold text-slate-900">Ksh {{ number_format($totalPayroll ?? 0, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="admin-table-scroll overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Employee</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-700">Amount</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-700">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payrollData ?? [] as $payroll)
                        <tr>
                            <td class="px-4 py-3 text-slate-700">{{ $payroll['employee'] }}</td>
                            <td class="px-4 py-3 text-right text-slate-700">Ksh {{ number_format($payroll['amount'], 2) }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $payroll['date'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $payroll['description'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No payroll records found for {{ date('F', mktime(0, 0, 0, $month ?? now()->month, 1)) }} {{ $year ?? now()->year }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
