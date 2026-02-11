@extends('layouts.admin')

@section('page_title', $project->name)

@section('content')
    <div class="mb-4">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.projects.index') }}" class="text-slate-500 hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">{{ $project->name }}</h1>
        </div>
    </div>

    <div class="bg-white border border-slate-100 rounded-lg p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">{{ $project->name }}</h2>
                <p class="text-sm text-slate-500 capitalize">{{ $project->type }} Project</p>

                @php
                    $mix = $project->capital_mix;
                @endphp

                @if($mix['total'] > 0)
                    <p class="mt-1 text-xs text-slate-500">
                        Capital structure: <span class="font-semibold text-slate-900">{{ $mix['equity'] }}% equity</span> ·
                        <span class="font-semibold text-slate-900">{{ $mix['debt'] }}% debt</span>
                    </p>
                @else
                    <p class="mt-1 text-xs text-amber-600">
                        Capital structure not yet configured for this project.
                    </p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.projects.edit', $project) }}"
                   class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Edit
                </a>
                <form action="{{ route('admin.projects.sync-metrics', $project) }}"
                      method="POST"
                      class="inline-block"
                      title="Sync key metrics from external systems (e.g. toy shop e-commerce)">
                    @csrf
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                        Sync Metrics
                    </button>
                </form>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    {{ $project->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-50 text-slate-700 border border-slate-100' }}">
                    {{ $project->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Project Details</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Slug</dt>
                        <dd class="font-medium text-slate-900"><code class="bg-slate-100 px-2 py-0.5 rounded">{{ $project->slug }}</code></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Type</dt>
                        <dd class="font-medium text-slate-900">{{ \App\Models\Project::getTypeLabel($project->type) }}</dd>
                    </div>
                    @if($project->objective)
                        <div class="mt-3 pt-3 border-t border-slate-100">
                            <dt class="text-slate-500 mb-1">Objective</dt>
                            <dd class="font-medium text-slate-900 text-sm">{{ $project->objective }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Color Theme</dt>
                        <dd class="font-medium text-slate-900 capitalize">{{ $project->color }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Sort Order</dt>
                        <dd class="font-medium text-slate-900">{{ $project->sort_order }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Capital & Funding Structure</h3>
                <dl class="space-y-2 text-sm">
                    @php $funding = $project->funding; @endphp

                    @if($funding)
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Member Capital Injected</dt>
                            <dd class="font-medium text-slate-900">
                                Ksh {{ number_format($funding->member_capital_amount, 0) }}
                            </dd>
                        </div>
                        @if($funding->member_capital_date)
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Capital Date</dt>
                                <dd class="font-medium text-slate-900">
                                    {{ $funding->member_capital_date->format('M d, Y') }}
                                </dd>
                            </div>
                        @endif
                        @if($funding->member_capital_source)
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Source (Investment Pool)</dt>
                                <dd class="font-medium text-slate-900">
                                    {{ $funding->member_capital_source }}
                                </dd>
                            </div>
                        @endif

                        <div class="pt-2 border-t border-slate-100 mt-3">
                            <p class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wide">Loan / Debt</p>

                            @if($funding->has_loan)
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Lender</dt>
                                    <dd class="font-medium text-slate-900">
                                        {{ $funding->lender_name ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Loan Amount</dt>
                                    <dd class="font-medium text-slate-900">
                                        Ksh {{ number_format($funding->loan_amount ?? 0, 0) }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Interest Rate</dt>
                                    <dd class="font-medium text-slate-900">
                                        {{ $funding->interest_rate ? rtrim(rtrim(number_format($funding->interest_rate, 2), '0'), '.') : '0' }}%
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Tenure</dt>
                                    <dd class="font-medium text-slate-900">
                                        {{ $funding->tenure_months ?? 0 }} months
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Monthly Repayment</dt>
                                    <dd class="font-medium text-slate-900">
                                        Ksh {{ number_format($funding->monthly_repayment ?? 0, 0) }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Outstanding Balance</dt>
                                    <dd class="font-medium text-slate-900">
                                        Ksh {{ number_format($funding->outstanding_balance ?? 0, 0) }}
                                    </dd>
                                </div>
                            @else
                                <p class="text-xs text-slate-500">
                                    No loan associated with this project. 100% equity-funded.
                                </p>
                            @endif
                        </div>
                    @else
                        <p class="text-xs text-amber-600">
                            No capital & funding record yet. Add capital and (optional) loan details in the project edit form.
                        </p>
                    @endif
                </dl>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Access Information</h3>
                <dl class="space-y-2 text-sm">
                    @if($project->route_name)
                        <div>
                            <dt class="text-slate-500 mb-1">Laravel Route</dt>
                            <dd class="font-medium text-slate-900">
                                <code class="bg-slate-100 px-2 py-0.5 rounded">{{ $project->route_name }}</code>
                            </dd>
                        </div>
                    @endif
                    @if($project->url)
                        <div>
                            <dt class="text-slate-500 mb-1">External URL</dt>
                            <dd class="font-medium text-slate-900">
                                <a href="{{ $project->url }}" target="_blank" class="text-emerald-600 hover:underline">
                                    {{ $project->url }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    @if($project->icon)
                        <div>
                            <dt class="text-slate-500 mb-1">Icon</dt>
                            <dd class="font-medium text-slate-900">
                                <code class="bg-slate-100 px-2 py-0.5 rounded">{{ $project->icon }}</code>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        @php
            $funding = $project->funding;
            $loanRequirements = $funding && $funding->has_loan
                ? $funding->loanRequirements()->orderBy('due_date')->get()
                : collect();
        @endphp

        @if($funding && $funding->has_loan)
            <div class="mt-6 pt-6 border-t border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Loan Requirements & Compliance</h3>
                <p class="text-xs text-slate-500 mb-3">
                    All conditions attached to this loan – use this to avoid default risk from missing paperwork or milestones.
                </p>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50 border border-slate-100">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Requirement</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Responsible</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Status</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Due</th>
                                <th class="px-3 py-2 text-left font-semibold text-slate-700">Documents / Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($loanRequirements as $requirement)
                                <tr>
                                    <td class="px-3 py-2">
                                        <div class="font-medium text-slate-900">{{ $requirement->name }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            Created {{ $requirement->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($requirement->responsibleOfficer)
                                            <span class="text-slate-800">{{ $requirement->responsibleOfficer->name }}</span>
                                        @else
                                            <span class="text-slate-400">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        @php $status = $requirement->status; @endphp
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                            @if($status === 'approved') bg-emerald-100 text-emerald-700
                                            @elseif($status === 'submitted') bg-blue-100 text-blue-700
                                            @else bg-amber-100 text-amber-700 @endif">
                                            {{ ucfirst($status) }}
                                        </span>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            @if($requirement->approved_at)
                                                Approved {{ $requirement->approved_at->diffForHumans() }}
                                            @elseif($requirement->submitted_at)
                                                Submitted {{ $requirement->submitted_at->diffForHumans() }}
                                            @else
                                                Pending
                                            @endif
                                        </div>
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
                                    <td class="px-3 py-2 text-[11px] text-slate-600">
                                        @if($requirement->notes)
                                            {{ \Illuminate\Support\Str::limit($requirement->notes, 120) }}
                                        @else
                                            <span class="text-slate-400">No notes / documents referenced yet.</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-slate-400">
                                        No loan conditions captured yet. Add them from the Edit Project screen.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @php
            $assets = $project->assets()->orderBy('date_acquired')->get();
        @endphp

        <div class="mt-6 pt-6 border-t border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-slate-900">Assets Acquired</h3>
                <a href="{{ route('admin.project-assets.create', ['project_id' => $project->id]) }}"
                   class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-3 py-1.5 rounded">
                    Add Asset
                </a>
            </div>

            @if($assets->count() > 0)
                @php
                    $totalAcquisition = $assets->sum('acquisition_cost');
                    $totalCurrent = $assets->sum(function($asset) { return $asset->current_value ?? $asset->acquisition_cost; });
                @endphp
                <div class="flex items-center justify-between mb-3 text-xs">
                    <div>
                        <p class="text-slate-500 text-[11px] mb-1">Total Acquisition Cost</p>
                        <p class="font-semibold text-slate-900">Ksh {{ number_format($totalAcquisition, 0) }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-[11px] mb-1">Current Estimated Value</p>
                        <p class="font-semibold text-emerald-700">Ksh {{ number_format($totalCurrent, 0) }}</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($assets as $asset)
                        <div class="py-2 flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-900 truncate">{{ $asset->name }}</p>
                                <p class="text-[10px] text-slate-500">
                                    {{ $asset->category ?? 'Asset' }}
                                    @if($asset->date_acquired)
                                        · Acquired {{ $asset->date_acquired->format('M d, Y') }}
                                    @endif
                                </p>
                                @if($asset->notes)
                                    <p class="text-[10px] text-slate-500 mt-1">
                                        {{ \Illuminate\Support\Str::limit($asset->notes, 80) }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-[11px] text-slate-500">Acquisition</p>
                                <p class="font-semibold text-slate-900">Ksh {{ number_format($asset->acquisition_cost, 0) }}</p>
                                <p class="text-[11px] text-slate-500 mt-1">Current</p>
                                <p class="font-semibold text-emerald-700">
                                    Ksh {{ number_format($asset->current_value ?? $asset->acquisition_cost, 0) }}
                                </p>
                                @if($asset->supporting_document_path)
                                    <a href="{{ asset('storage/' . $asset->supporting_document_path) }}"
                                       target="_blank"
                                       class="block mt-1 text-[10px] text-emerald-600 hover:text-emerald-700 underline">
                                        {{ $asset->supporting_document_name ?? 'View document' }}
                                    </a>
                                @endif
                                <div class="mt-1 flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.project-assets.edit', $asset) }}"
                                       class="text-[10px] text-amber-600 hover:text-amber-700">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.project-assets.destroy', $asset) }}" method="POST"
                                          onsubmit="return confirm('Delete this asset? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-[10px] text-red-500 hover:text-red-600">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">
                    No assets have been recorded for this project yet. Once you add land, stock, or equipment,
                    this section will help you see real asset value, not guesswork.
                </p>
            @endif
        </div>

        @php $kpi = $project->kpi; @endphp
        @if($kpi)
            <div class="mt-6 pt-6 border-t border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">KPIs & Performance Targets</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    @if($project->type === 'land')
                        <div>
                            <h4 class="text-xs font-semibold text-slate-700 mb-2 uppercase tracking-wide">Land / Real Estate KPIs</h4>
                            <dl class="space-y-2 text-sm">
                                @if($kpi->target_annual_value_growth_pct)
                                    <div class="flex justify-between">
                                        <dt class="text-slate-500">Target Annual Value Growth</dt>
                                        <dd class="font-medium text-slate-900">{{ number_format($kpi->target_annual_value_growth_pct, 1) }}%</dd>
                                    </div>
                                @endif
                                @if($kpi->expected_holding_period_years)
                                    <div class="flex justify-between">
                                        <dt class="text-slate-500">Expected Holding Period</dt>
                                        <dd class="font-medium text-slate-900">{{ number_format($kpi->expected_holding_period_years, 1) }} years</dd>
                                    </div>
                                @endif
                                @if($kpi->minimum_acceptable_roi_pct)
                                    <div class="flex justify-between">
                                        <dt class="text-slate-500">Minimum Acceptable ROI</dt>
                                        <dd class="font-medium text-slate-900">{{ number_format($kpi->minimum_acceptable_roi_pct, 1) }}%</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif
                    @if(in_array($project->type, ['ecommerce', 'business', 'trading']))
                        <div>
                            <h4 class="text-xs font-semibold text-slate-700 mb-2 uppercase tracking-wide">Operating Business KPIs</h4>
                            <dl class="space-y-2 text-sm">
                                @if($kpi->monthly_revenue_target)
                                    <div class="flex justify-between">
                                        <dt class="text-slate-500">Monthly Revenue Target</dt>
                                        <dd class="font-medium text-slate-900">Ksh {{ number_format($kpi->monthly_revenue_target, 0) }}</dd>
                                    </div>
                                @endif
                                @if($kpi->gross_margin_target_pct)
                                    <div class="flex justify-between">
                                        <dt class="text-slate-500">Gross Margin Target</dt>
                                        <dd class="font-medium text-slate-900">{{ number_format($kpi->gross_margin_target_pct, 1) }}%</dd>
                                    </div>
                                @endif
                                @if($kpi->operating_expense_ratio_target_pct)
                                    <div class="flex justify-between">
                                        <dt class="text-slate-500">Operating Expense Ratio Target</dt>
                                        <dd class="font-medium text-slate-900">{{ number_format($kpi->operating_expense_ratio_target_pct, 1) }}%</dd>
                                    </div>
                                @endif
                                @if($kpi->break_even_revenue)
                                    <div class="flex justify-between">
                                        <dt class="text-slate-500">Break-even Revenue</dt>
                                        <dd class="font-medium text-slate-900">Ksh {{ number_format($kpi->break_even_revenue, 0) }}</dd>
                                    </div>
                                @endif
                                @if($kpi->loan_coverage_ratio_target)
                                    <div class="flex justify-between">
                                        <dt class="text-slate-500">Loan Coverage Ratio Target</dt>
                                        <dd class="font-medium text-slate-900">{{ number_format($kpi->loan_coverage_ratio_target, 2) }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($project->description)
            <div class="mt-6 pt-6 border-t border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900 mb-2">Description</h3>
                <p class="text-sm text-slate-600">{{ $project->description }}</p>
            </div>
        @endif

        <div class="mt-6 pt-6 border-t border-slate-100">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.projects.edit', $project) }}"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Edit Project
                </a>
                <a href="{{ route('admin.projects.index') }}"
                   class="border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold px-4 py-2 rounded-lg">
                    Back to Projects
                </a>
            </div>
        </div>
    </div>
@endsection
