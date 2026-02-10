@extends('layouts.admin')

@section('page_title', 'Create Project')

@section('content')
    <div class="mb-4">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.projects.index') }}" class="text-slate-500 hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">Create New Project</h1>
        </div>
        <p class="text-xs text-slate-500">Add a new project that partners can access from their dashboard.</p>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg p-6">
        <form action="{{ route('admin.projects.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Project Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="objective" class="block text-xs font-semibold text-slate-700 mb-1">
                        Objective (Why this project exists) <span class="text-red-500">*</span>
                    </label>
                    <textarea id="objective" name="objective" rows="3"
                              class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                              placeholder="e.g. Capital appreciation, cash flow, diversification">{{ old('objective') }}</textarea>
                    @error('objective')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-xs font-semibold text-slate-700 mb-1">
                        Project Type <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type"
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            required>
                        <option value="">Select type</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ old('type', 'ecommerce') === $type ? 'selected' : '' }}>
                                @if($type === 'land') Land / Real Estate
                                @elseif($type === 'business') Business
                                @elseif($type === 'trading') Trading / Imports
                                @elseif($type === 'other') Other
                                @else E-Commerce
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status"
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                            required>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ old('status', 'planning') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Active flag is now driven by status; keep hidden to align with controller logic --}}
                <input type="hidden" name="is_active" value="{{ old('status', 'planning') === 'active' ? 1 : 0 }}">
            </div>

            {{-- Capital & Funding Structure --}}
            <div class="mt-6 pt-6 border-t border-slate-100">
                <h3 class="text-xs font-semibold text-slate-900 mb-2">Capital & Funding Structure</h3>
                <p class="text-[11px] text-slate-500 mb-3">
                    Capture how this project is funded so partners can always see the equity vs debt mix.
                </p>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-700 mb-2 uppercase tracking-wide">
                            Member Capital (Equity)
                        </p>

                        <div class="mb-3">
                            <label class="block text-xs font-medium text-slate-700 mb-1">
                                Member Capital Injected (Ksh)
                            </label>
                            <input type="number" step="0.01" name="member_capital_amount"
                                   value="{{ old('member_capital_amount') }}"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            @error('member_capital_amount')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="block text-xs font-medium text-slate-700 mb-1">
                                Capital Date
                            </label>
                            <input type="date" name="member_capital_date"
                                   value="{{ old('member_capital_date') }}"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            @error('member_capital_date')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">
                                Source (Investment Pool)
                            </label>
                            <input type="text" name="member_capital_source"
                                   value="{{ old('member_capital_source') }}"
                                   placeholder="e.g. Members investment pool"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            @error('member_capital_source')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <p class="text-[11px] font-semibold text-slate-700 mb-2 uppercase tracking-wide">
                            Loan / Debt (if any)
                        </p>

                        <div class="mb-3 flex items-center gap-2">
                            <input type="checkbox" id="has_loan" name="has_loan" value="1"
                                   {{ old('has_loan') ? 'checked' : '' }}
                                   class="h-4 w-4 text-emerald-600 border-slate-300 rounded">
                            <label for="has_loan" class="text-xs font-medium text-slate-700">
                                This project uses a loan
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="block text-xs font-medium text-slate-700 mb-1">
                                Lender (Bank / SACCO)
                            </label>
                            <input type="text" name="lender_name"
                                   value="{{ old('lender_name') }}"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            @error('lender_name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="block text-xs font-medium text-slate-700 mb-1">
                                Loan Amount (Ksh)
                            </label>
                            <input type="number" step="0.01" name="loan_amount"
                                   value="{{ old('loan_amount') }}"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            @error('loan_amount')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Interest Rate (%)
                                </label>
                                <input type="number" step="0.01" name="interest_rate"
                                       value="{{ old('interest_rate') }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('interest_rate')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Tenure (months)
                                </label>
                                <input type="number" step="1" name="tenure_months"
                                       value="{{ old('tenure_months') }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('tenure_months')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Monthly Repayment (Ksh)
                                </label>
                                <input type="number" step="0.01" name="monthly_repayment"
                                       value="{{ old('monthly_repayment') }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('monthly_repayment')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Outstanding Balance (Ksh)
                                </label>
                                <input type="number" step="0.01" name="outstanding_balance"
                                       value="{{ old('outstanding_balance') }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('outstanding_balance')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Loan Requirements & Compliance Tracking --}}
            <div id="loan-requirements-section" class="mt-6 pt-6 border-t border-slate-100 {{ old('has_loan') ? '' : 'hidden' }}">
                <h3 class="text-xs font-semibold text-slate-900 mb-2">Loan Requirements & Compliance</h3>
                <p class="text-[11px] text-slate-500 mb-3">
                    If this project uses a loan, list the key conditions so you don’t miss insurance, land charges, registrations or reporting.
                </p>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50 border border-slate-100">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Requirement</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Responsible Officer</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Due Date</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @for($i = 0; $i < 3; $i++)
                                <tr>
                                    <td class="px-3 py-2">
                                        <input type="text"
                                               name="requirements[{{ $i }}][name]"
                                               value="{{ old('requirements.'.$i.'.name') }}"
                                               placeholder="e.g. Insurance cover, Land title charge"
                                               class="border border-slate-200 rounded w-full px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <select name="requirements[{{ $i }}][responsible_user_id]"
                                                class="border border-slate-200 rounded w-full px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                            <option value="">Select officer</option>
                                            @foreach($officers as $officer)
                                                <option value="{{ $officer->id }}"
                                                    {{ old('requirements.'.$i.'.responsible_user_id') == $officer->id ? 'selected' : '' }}>
                                                    {{ $officer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="date"
                                               name="requirements[{{ $i }}][due_date]"
                                               value="{{ old('requirements.'.$i.'.due_date') }}"
                                               class="border border-slate-200 rounded w-full px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text"
                                               name="requirements[{{ $i }}][notes]"
                                               value="{{ old('requirements.'.$i.'.notes') }}"
                                               placeholder="Optional notes / reference to documents"
                                               class="border border-slate-200 rounded w-full px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <p class="mt-2 text-[11px] text-slate-500">
                    You can add more detailed requirements later from the project edit screen.
                </p>
            </div>

            {{-- KPIs & Performance Targets --}}
            <div class="mt-6 pt-6 border-t border-slate-100">
                <h3 class="text-xs font-semibold text-slate-900 mb-2">KPIs & Performance Targets</h3>
                <p class="text-[11px] text-slate-500 mb-3">
                    Set clear targets so you can later see if this project is winning or drifting (for land and operating business).
                </p>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-700 mb-2 uppercase tracking-wide">
                            Land / Real Estate (if applicable)
                        </p>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Target Annual Value Growth (%)
                            </label>
                            <input type="number" step="0.1" name="target_annual_value_growth_pct"
                                   value="{{ old('target_annual_value_growth_pct') }}"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            @error('target_annual_value_growth_pct')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Expected Holding Period (years)
                            </label>
                            <input type="number" step="0.1" name="expected_holding_period_years"
                                   value="{{ old('expected_holding_period_years') }}"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            @error('expected_holding_period_years')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Minimum Acceptable ROI (%)
                            </label>
                            <input type="number" step="0.1" name="minimum_acceptable_roi_pct"
                                   value="{{ old('minimum_acceptable_roi_pct') }}"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            @error('minimum_acceptable_roi_pct')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <p class="text-[11px] font-semibold text-slate-700 mb-2 uppercase tracking-wide">
                            Operating Business / Toy Shop
                        </p>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Monthly Revenue Target (Ksh)
                            </label>
                            <input type="number" step="0.01" name="monthly_revenue_target"
                                   value="{{ old('monthly_revenue_target') }}"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            @error('monthly_revenue_target')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Gross Margin Target (%)
                            </label>
                            <input type="number" step="0.1" name="gross_margin_target_pct"
                                   value="{{ old('gross_margin_target_pct') }}"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            @error('gross_margin_target_pct')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Operating Expense Ratio Target (%)
                            </label>
                            <input type="number" step="0.1" name="operating_expense_ratio_target_pct"
                                   value="{{ old('operating_expense_ratio_target_pct') }}"
                                   class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            @error('operating_expense_ratio_target_pct')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Break-even Revenue (Ksh)
                                </label>
                                <input type="number" step="0.01" name="break_even_revenue"
                                       value="{{ old('break_even_revenue') }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('break_even_revenue')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Loan Coverage Ratio Target
                                </label>
                                <input type="number" step="0.01" name="loan_coverage_ratio_target"
                                       value="{{ old('loan_coverage_ratio_target') }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('loan_coverage_ratio_target')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Expenses & Assets Checklist (for leadership to review when creating a project) --}}
            <div class="mt-6 pt-6 border-t border-slate-100">
                <h3 class="text-xs font-semibold text-slate-900 mb-2">Expenses & Assets Tracking (What to plan for)</h3>
                <p class="text-[11px] text-slate-500 mb-3">
                    When you activate this project, make sure key expenses and acquired assets are recorded so partners see real numbers, not guesses.
                </p>

                <div class="grid md:grid-cols-2 gap-4 text-[11px] text-slate-600">
                    <div class="bg-slate-50 border border-dashed border-slate-200 rounded-md p-3">
                        <p class="font-semibold text-slate-800 mb-1">Expenses to record (via Financial module)</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Purchases (stock, materials, equipment)</li>
                            <li>Construction or setup costs</li>
                            <li>Logistics & shipping</li>
                            <li>Taxes & statutory fees</li>
                            <li>Operating expenses (rent, salaries, utilities)</li>
                            <li>Attach receipts / invoices under financial records.</li>
                        </ul>
                    </div>

                    <div class="bg-slate-50 border border-dashed border-slate-200 rounded-md p-3">
                        <p class="font-semibold text-slate-800 mb-1">Assets to capture (via Project Assets)</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Land parcels or buildings (with title documents)</li>
                            <li>Opening stock / inventory</li>
                            <li>Key equipment or fixtures</li>
                            <li>Acquisition cost & date acquired</li>
                            <li>Current estimated value</li>
                            <li>Attach supporting documents (title deed, invoices).</li>
                        </ul>
                    </div>
                </div>

                <p class="mt-3 text-[11px] text-slate-500">
                    After saving this project, use the Financial and Project Assets sections to link all expenses and assets back to this project.
                </p>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Create Project
                </button>
                <a href="{{ route('admin.projects.index') }}"
                   class="border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold px-4 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
