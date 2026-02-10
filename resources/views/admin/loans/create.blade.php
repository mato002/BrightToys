@extends('layouts.admin')

@section('page_title', 'Register Loan')

@section('content')
    <div class="mb-4">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.loans.index') }}" class="text-slate-500 hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">Register New Loan</h1>
        </div>
        <p class="text-xs text-slate-500">Capture bank/SACCO loans and link them to projects.</p>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg p-6 max-w-2xl">
        <form action="{{ route('admin.loans.store') }}" method="POST" class="space-y-4">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700 mb-2">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Lender (Bank / SACCO) <span class="text-red-500">*</span></label>
                <input type="text" name="lender_name" value="{{ old('lender_name') }}" required
                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Loan Amount (Ksh) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" min="0.01" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Interest Rate (% per year) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="interest_rate" value="{{ old('interest_rate') }}" min="0"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                    <p class="text-[10px] text-slate-500 mt-1">e.g. 12 for 12% p.a.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tenure (months) <span class="text-red-500">*</span></label>
                    <input type="number" name="tenure_months" value="{{ old('tenure_months') }}" min="1" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Repayment Frequency <span class="text-red-500">*</span></label>
                    <select name="repayment_frequency"
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="monthly" {{ old('repayment_frequency', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('repayment_frequency') === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Linked Project</label>
                    <select name="project_id"
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">None</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Save Loan & Generate Schedule
                </button>
                <a href="{{ route('admin.loans.index') }}"
                   class="border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold px-4 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

