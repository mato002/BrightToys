@extends('layouts.partner')

@section('page_title', 'Payroll')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Employee Payroll</h1>
        <p class="text-xs text-slate-500">
            View payroll information and employee compensation records.
        </p>
    </div>

    <div class="bg-white rounded-lg border border-slate-100 shadow-sm p-8">
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="text-sm font-semibold text-slate-700 mb-2">Payroll Management</h3>
            <p class="text-xs text-slate-500 mb-4">Payroll functionality is currently under development.</p>
            <p class="text-xs text-slate-400">This feature will allow you to view employee payroll records, salary information, and compensation details.</p>
        </div>
    </div>
@endsection
