@extends('layouts.admin')

@section('page_title', 'Edit Project')

@section('content')
    <div class="mb-4">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.projects.index') }}" class="text-slate-500 hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">Edit Project: {{ $project->name }}</h1>
        </div>
        <p class="text-xs text-slate-500">Update project details and settings.</p>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg p-6">
        <form action="{{ route('admin.projects.update', $project) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Project Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $project->name) }}" required
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="block text-xs font-semibold text-slate-700 mb-1">
                        Slug
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $project->slug) }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('slug')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">{{ old('description', $project->description) }}</textarea>
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
                              placeholder="e.g. Capital appreciation, cash flow, diversification">{{ old('objective', $project->objective) }}</textarea>
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
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ old('type', $project->type) === $type ? 'selected' : '' }}>
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
                            <option value="{{ $status }}" {{ old('status', $project->status ?? 'planning') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="color" class="block text-xs font-semibold text-slate-700 mb-1">
                        Color Theme <span class="text-red-500">*</span>
                    </label>
                    <select id="color" name="color" required
                            class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="emerald" {{ old('color', $project->color) === 'emerald' ? 'selected' : '' }}>Emerald</option>
                        <option value="blue" {{ old('color', $project->color) === 'blue' ? 'selected' : '' }}>Blue</option>
                        <option value="amber" {{ old('color', $project->color) === 'amber' ? 'selected' : '' }}>Amber</option>
                        <option value="purple" {{ old('color', $project->color) === 'purple' ? 'selected' : '' }}>Purple</option>
                        <option value="red" {{ old('color', $project->color) === 'red' ? 'selected' : '' }}>Red</option>
                        <option value="indigo" {{ old('color', $project->color) === 'indigo' ? 'selected' : '' }}>Indigo</option>
                    </select>
                    @error('color')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="url" class="block text-xs font-semibold text-slate-700 mb-1">
                        External URL
                    </label>
                    <input type="url" id="url" name="url" value="{{ old('url', $project->url) }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('url')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="route_name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Laravel Route Name
                    </label>
                    <input type="text" id="route_name" name="route_name" value="{{ old('route_name', $project->route_name) }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('route_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="icon" class="block text-xs font-semibold text-slate-700 mb-1">
                        Icon Class (FontAwesome)
                    </label>
                    <input type="text" id="icon" name="icon" value="{{ old('icon', $project->icon) }}"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('icon')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sort_order" class="block text-xs font-semibold text-slate-700 mb-1">
                        Sort Order
                    </label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $project->sort_order) }}" min="0"
                           class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('sort_order')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Capital & Funding Structure --}}
                <div class="md:col-span-2 mt-4 pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-semibold text-slate-900 mb-2">Capital & Funding Structure</h3>
                    <p class="text-[11px] text-slate-500 mb-3">
                        Capture how this project is funded so partners always see the mix of equity vs debt.
                    </p>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-[11px] font-semibold text-slate-700 mb-2 uppercase tracking-wide">
                                Member Capital (Equity)
                            </p>
                            <div class="mb-3">
                                <label for="member_capital_amount" class="block text-xs font-medium text-slate-700 mb-1">
                                    Member Capital Injected
                                </label>
                                <input type="number" step="0.01" id="member_capital_amount" name="member_capital_amount"
                                       value="{{ old('member_capital_amount', optional($project->funding)->member_capital_amount) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('member_capital_amount')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="member_capital_date" class="block text-xs font-medium text-slate-700 mb-1">
                                    Capital Date
                                </label>
                                <input type="date" id="member_capital_date" name="member_capital_date"
                                       value="{{ old('member_capital_date', optional(optional($project->funding)->member_capital_date)->format('Y-m-d')) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('member_capital_date')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="member_capital_source" class="block text-xs font-medium text-slate-700 mb-1">
                                    Source (Investment Pool)
                                </label>
                                <input type="text" id="member_capital_source" name="member_capital_source"
                                       value="{{ old('member_capital_source', optional($project->funding)->member_capital_source) }}"
                                       placeholder="e.g. Investment Pool A"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('member_capital_source')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold text-slate-700 mb-2 uppercase tracking-wide">
                                Loan / Debt
                            </p>

                            <div class="mb-3 flex items-center gap-2">
                                <input type="checkbox" id="has_loan" name="has_loan" value="1"
                                       {{ old('has_loan', optional($project->funding)->has_loan) ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <label for="has_loan" class="text-xs text-slate-700">
                                    This project uses a loan
                                </label>
                            </div>

                            <div class="mb-3">
                                <label for="lender_name" class="block text-xs font-medium text-slate-700 mb-1">
                                    Lender (Bank / SACCO)
                                </label>
                                <input type="text" id="lender_name" name="lender_name"
                                       value="{{ old('lender_name', optional($project->funding)->lender_name) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('lender_name')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="loan_amount" class="block text-xs font-medium text-slate-700 mb-1">
                                    Loan Amount
                                </label>
                                <input type="number" step="0.01" id="loan_amount" name="loan_amount"
                                       value="{{ old('loan_amount', optional($project->funding)->loan_amount) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('loan_amount')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="interest_rate" class="block text-xs font-medium text-slate-700 mb-1">
                                    Interest Rate (% per year)
                                </label>
                                <input type="number" step="0.01" id="interest_rate" name="interest_rate"
                                       value="{{ old('interest_rate', optional($project->funding)->interest_rate) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('interest_rate')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tenure_months" class="block text-xs font-medium text-slate-700 mb-1">
                                    Tenure (months)
                                </label>
                                <input type="number" id="tenure_months" name="tenure_months"
                                       value="{{ old('tenure_months', optional($project->funding)->tenure_months) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('tenure_months')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="monthly_repayment" class="block text-xs font-medium text-slate-700 mb-1">
                                    Monthly Repayment
                                </label>
                                <input type="number" step="0.01" id="monthly_repayment" name="monthly_repayment"
                                       value="{{ old('monthly_repayment', optional($project->funding)->monthly_repayment) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('monthly_repayment')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="outstanding_balance" class="block text-xs font-medium text-slate-700 mb-1">
                                    Outstanding Balance
                                </label>
                                <input type="number" step="0.01" id="outstanding_balance" name="outstanding_balance"
                                       value="{{ old('outstanding_balance', optional($project->funding)->outstanding_balance) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                @error('outstanding_balance')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                 </div>

                 @php
                     $funding = $project->funding;
                     $loanRequirements = $funding ? $funding->loanRequirements()->orderBy('due_date')->get() : collect();
                 @endphp

                 @if($funding && $funding->has_loan)
                     <div class="md:col-span-2 mt-6 pt-6 border-t border-slate-100">
                         <h3 class="text-xs font-semibold text-slate-900 mb-2">Loan Requirements & Compliance</h3>
                         <p class="text-[11px] text-slate-500 mb-3">
                             Track all conditions tied to this loan (e.g. insurance, land charge, registration, statements, milestones, reporting).
                         </p>

                         <div class="mb-4 overflow-x-auto">
                             <table class="min-w-full text-xs">
                                 <thead class="bg-slate-50 border border-slate-100">
                                     <tr>
                                         <th class="px-3 py-2 text-left font-semibold text-slate-700">Requirement</th>
                                         <th class="px-3 py-2 text-left font-semibold text-slate-700">Responsible</th>
                                         <th class="px-3 py-2 text-left font-semibold text-slate-700">Status</th>
                                         <th class="px-3 py-2 text-left font-semibold text-slate-700">Due</th>
                                         <th class="px-3 py-2 text-left font-semibold text-slate-700">Last Change</th>
                                     </tr>
                                 </thead>
                                 <tbody class="divide-y divide-slate-100">
                                     @forelse($loanRequirements as $requirement)
                                         <tr>
                                             <td class="px-3 py-2">
                                                 <div class="font-medium text-slate-900">{{ $requirement->name }}</div>
                                                 @if($requirement->notes)
                                                     <div class="text-[11px] text-slate-500 mt-0.5">
                                                         {{ \Illuminate\Support\Str::limit($requirement->notes, 80) }}
                                                     </div>
                                                 @endif
                                             </td>
                                             <td class="px-3 py-2">
                                                 @if($requirement->responsibleOfficer)
                                                     <span class="text-slate-800">{{ $requirement->responsibleOfficer->name }}</span>
                                                 @else
                                                     <span class="text-slate-400">Unassigned</span>
                                                 @endif
                                             </td>
                                             <td class="px-3 py-2">
                                                 @php
                                                     $status = $requirement->status;
                                                 @endphp
                                                 <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                                     @if($status === 'approved') bg-emerald-100 text-emerald-700
                                                     @elseif($status === 'submitted') bg-blue-100 text-blue-700
                                                     @else bg-amber-100 text-amber-700 @endif">
                                                     {{ ucfirst($status) }}
                                                 </span>
                                             </td>
                                             <td class="px-3 py-2">
                                                 @if($requirement->due_date)
                                                     <span class="text-slate-800">
                                                         {{ $requirement->due_date->format('M d, Y') }}
                                                     </span>
                                                 @else
                                                     <span class="text-slate-400">Not set</span>
                                                 @endif
                                             </td>
                                             <td class="px-3 py-2 text-slate-500">
                                                 @if($requirement->approved_at)
                                                     <span>Approved {{ $requirement->approved_at->diffForHumans() }}</span>
                                                 @elseif($requirement->submitted_at)
                                                     <span>Submitted {{ $requirement->submitted_at->diffForHumans() }}</span>
                                                 @else
                                                     <span class="text-slate-400">Created {{ $requirement->created_at->diffForHumans() }}</span>
                                                 @endif
                                             </td>
                                         </tr>
                                     @empty
                                         <tr>
                                             <td colspan="5" class="px-3 py-4 text-center text-slate-400">
                                                 No loan conditions captured yet.
                                             </td>
                                         </tr>
                                     @endforelse
                                 </tbody>
                             </table>
                         </div>

                         <div class="bg-slate-50 border border-dashed border-slate-200 rounded-md p-3">
                             <p class="text-[11px] font-semibold text-slate-700 mb-2">Add New Requirement</p>
                             <p class="text-[11px] text-slate-500 mb-2">
                                 Use this project edit form to capture new conditions; in a later step we can add inline create/update actions.
                             </p>
                             <ul class="list-disc list-inside text-[11px] text-slate-500">
                                 <li>Examples: Insurance cover, Land title charge, Business registration, Bank statements, Milestone sign-offs, Financial reports.</li>
                                 <li>Each requirement should have a clear owner, due date, and status.</li>
                                 <li>Upload supporting documents under Documents and reference them in the notes.</li>
                             </ul>
                         </div>
                     </div>
                 @endif

                {{-- Active flag is now driven by status; keep hidden to align with controller logic --}}
                <input type="hidden" name="is_active" value="{{ old('status', $project->status ?? 'planning') === 'active' ? 1 : 0 }}">

                {{-- KPI Targets --}}
                @php $kpi = $project->kpi; @endphp
                <div class="md:col-span-2 mt-6 pt-6 border-t border-slate-100">
                    <h3 class="text-xs font-semibold text-slate-900 mb-2">KPIs & Performance Targets</h3>
                    <p class="text-[11px] text-slate-500 mb-3">
                        Set clear targets so you can quickly see if this project is winning or drifting.
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
                                       value="{{ old('target_annual_value_growth_pct', optional($kpi)->target_annual_value_growth_pct) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Expected Holding Period (years)
                                </label>
                                <input type="number" step="0.1" name="expected_holding_period_years"
                                       value="{{ old('expected_holding_period_years', optional($kpi)->expected_holding_period_years) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Minimum Acceptable ROI (%)
                                </label>
                                <input type="number" step="0.1" name="minimum_acceptable_roi_pct"
                                       value="{{ old('minimum_acceptable_roi_pct', optional($kpi)->minimum_acceptable_roi_pct) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
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
                                       value="{{ old('monthly_revenue_target', optional($kpi)->monthly_revenue_target) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Gross Margin Target (%)
                                </label>
                                <input type="number" step="0.1" name="gross_margin_target_pct"
                                       value="{{ old('gross_margin_target_pct', optional($kpi)->gross_margin_target_pct) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Operating Expense Ratio Target (%)
                                </label>
                                <input type="number" step="0.1" name="operating_expense_ratio_target_pct"
                                       value="{{ old('operating_expense_ratio_target_pct', optional($kpi)->operating_expense_ratio_target_pct) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Break-even Revenue (Ksh per month)
                                </label>
                                <input type="number" step="0.01" name="break_even_revenue"
                                       value="{{ old('break_even_revenue', optional($kpi)->break_even_revenue) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Loan Coverage Ratio Target
                                </label>
                                <input type="number" step="0.01" name="loan_coverage_ratio_target"
                                       value="{{ old('loan_coverage_ratio_target', optional($kpi)->loan_coverage_ratio_target) }}"
                                       class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                       placeholder="e.g., 1.2 (NOI / debt service)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Update Project
                </button>

                @if(($project->status ?? 'planning') !== 'active')
                    <form action="{{ route('admin.projects.activate', $project) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                            Activate Project (Leadership)
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.projects.index') }}"
                   class="border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold px-4 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
