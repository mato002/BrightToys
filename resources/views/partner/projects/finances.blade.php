@extends('layouts.partner')

@section('page_title', $project->name . ' - Finances')

@section('partner_content')
    <div class="mb-4">
        <div class="flex items-center justify-between gap-3 mb-1">
            <div class="flex items-center gap-3">
                <a href="{{ route('partner.projects.manage') }}" class="text-slate-500 hover:text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <h1 class="text-lg font-semibold">{{ $project->name }} - Financial Overview</h1>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                Read-only view for members
            </span>
        </div>
        <p class="text-xs text-slate-500">You can monitor project KPIs, revenue, expenses and assets here. Edits are managed by leadership.</p>
    </div>

    {{-- Financial Summary + Capital Mix --}}
    <div class="mb-2 text-[11px] text-slate-500 flex items-center justify-between">
        <span>Period: <span class="font-semibold text-slate-800">{{ $periodLabel }}</span></span>
        <div class="space-x-2">
            <a href="{{ route('partner.projects.finances', ['project' => $project, 'period' => 'day']) }}"
               class="px-2 py-0.5 rounded-full border text-[10px]
                    {{ ($period ?? 'month') === 'day' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'border-slate-200 text-slate-500' }}">
                Today
            </a>
            <a href="{{ route('partner.projects.finances', ['project' => $project, 'period' => 'month']) }}"
               class="px-2 py-0.5 rounded-full border text-[10px]
                    {{ ($period ?? 'month') === 'month' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'border-slate-200 text-slate-500' }}">
                This Month
            </a>
            <a href="{{ route('partner.projects.finances', ['project' => $project, 'period' => 'all']) }}"
               class="px-2 py-0.5 rounded-full border text-[10px]
                    {{ ($period ?? 'month') === 'all' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'border-slate-200 text-slate-500' }}">
                All Time
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Sales Revenue</p>
            <p class="text-2xl font-bold text-emerald-600">Ksh {{ number_format($salesRevenue, 0) }}</p>
            <p class="text-[10px] text-slate-500 mt-1">From completed orders in {{ strtolower($periodLabel) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Other Income</p>
            <p class="text-2xl font-bold text-blue-600">Ksh {{ number_format($otherIncome, 0) }}</p>
            <p class="text-[10px] text-slate-500 mt-1">From approved other income records</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Net Operating Income</p>
            <p class="text-2xl font-bold text-slate-900">Ksh {{ number_format($netOperatingIncome, 0) }}</p>
            <p class="text-[10px] text-slate-500 mt-1">Revenue - operating expenses (this period)</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            @php $mix = $project->capital_mix; @endphp
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Capital Mix</p>
            @if($mix['total'] > 0)
                <p class="text-sm font-semibold text-slate-900">
                    {{ $mix['equity'] }}% equity / {{ $mix['debt'] }}% debt
                </p>
                <p class="text-[10px] text-slate-500 mt-1">
                    Based on member capital vs outstanding loan balance.
                </p>
            @else
                <p class="text-xs text-slate-500">
                    Capital structure not set yet.
                </p>
            @endif
        </div>
    </div>

    @if($kpiSnapshot)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
            <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
                <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">KPI Targets</p>
                <ul class="text-[11px] text-slate-700 space-y-1">
                    @if(!is_null($kpiSnapshot['targets']['monthly_revenue_target']))
                        <li>Monthly revenue target: <span class="font-semibold">Ksh {{ number_format($kpiSnapshot['targets']['monthly_revenue_target'], 0) }}</span></li>
                    @endif
                    @if(!is_null($kpiSnapshot['targets']['gross_margin_target_pct']))
                        <li>Gross margin target: <span class="font-semibold">{{ $kpiSnapshot['targets']['gross_margin_target_pct'] }}%</span></li>
                    @endif
                    @if(!is_null($kpiSnapshot['targets']['operating_expense_ratio_target_pct']))
                        <li>Opex ratio target: <span class="font-semibold">{{ $kpiSnapshot['targets']['operating_expense_ratio_target_pct'] }}%</span></li>
                    @endif
                    @if(!is_null($kpiSnapshot['targets']['break_even_revenue']))
                        <li>Break-even revenue: <span class="font-semibold">Ksh {{ number_format($kpiSnapshot['targets']['break_even_revenue'], 0) }}</span></li>
                    @endif
                    @if(!is_null($kpiSnapshot['targets']['loan_coverage_ratio_target']))
                        <li>Loan coverage target: <span class="font-semibold">{{ $kpiSnapshot['targets']['loan_coverage_ratio_target'] }}</span></li>
                    @endif
                    @if(!is_null($kpiSnapshot['targets']['target_annual_value_growth_pct']))
                        <li>Target annual value growth: <span class="font-semibold">{{ $kpiSnapshot['targets']['target_annual_value_growth_pct'] }}%</span></li>
                    @endif
                </ul>
            </div>

            <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
                <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">KPI Status</p>
                <ul class="text-[11px] text-slate-700 space-y-1">
                    @if(isset($kpiSnapshot['actuals']['period_revenue']))
                        <li>Current period revenue: <span class="font-semibold">Ksh {{ number_format($kpiSnapshot['actuals']['period_revenue'], 0) }}</span></li>
                    @endif
                    @if(isset($kpiSnapshot['actuals']['gross_margin_pct']) && !is_null($kpiSnapshot['actuals']['gross_margin_pct']))
                        <li>Gross margin: <span class="font-semibold">{{ round($kpiSnapshot['actuals']['gross_margin_pct'], 1) }}%</span></li>
                    @endif
                    @if(isset($kpiSnapshot['actuals']['operating_expense_ratio_pct']) && !is_null($kpiSnapshot['actuals']['operating_expense_ratio_pct']))
                        <li>Opex ratio: <span class="font-semibold">{{ round($kpiSnapshot['actuals']['operating_expense_ratio_pct'], 1) }}%</span></li>
                    @endif
                    @if(isset($kpiSnapshot['actuals']['loan_coverage_ratio']) && !is_null($kpiSnapshot['actuals']['loan_coverage_ratio']))
                        <li>Loan coverage ratio: <span class="font-semibold">{{ round($kpiSnapshot['actuals']['loan_coverage_ratio'], 2) }}</span></li>
                    @endif
                    @if(isset($kpiSnapshot['actuals']['current_asset_value']))
                        <li>Current asset value: <span class="font-semibold">Ksh {{ number_format($kpiSnapshot['actuals']['current_asset_value'], 0) }}</span></li>
                    @endif
                    @if(isset($kpiSnapshot['actuals']['value_growth_pct']) && !is_null($kpiSnapshot['actuals']['value_growth_pct']))
                        <li>Value growth vs cost: <span class="font-semibold">{{ round($kpiSnapshot['actuals']['value_growth_pct'], 1) }}%</span></li>
                    @endif
                </ul>
            </div>
        </div>

        @if(!empty($kpiAlerts))
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                <p class="text-xs font-semibold text-red-700 mb-1">KPI Alerts</p>
                <ul class="text-[11px] text-red-700 list-disc list-inside space-y-1">
                    @foreach($kpiAlerts as $alert)
                        <li>{{ $alert }}</li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-lg p-3">
                <p class="text-[11px] text-emerald-700">
                    KPIs are currently on track. No breaches detected for {{ strtolower($periodLabel) }}.
                </p>
            </div>
        @endif
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
        {{-- Financial Records --}}
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Financial Records</h2>
                <a href="{{ route('partner.financial-records') }}" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                    View all
                </a>
            </div>
            @if($financialRecords->count() > 0)
                <div class="space-y-2">
                    @foreach($financialRecords->take(5) as $record)
                        <div class="flex items-center justify-between p-2 rounded-lg border border-slate-100">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ \Illuminate\Support\Str::limit($record->description, 40) }}</p>
                                <p class="text-[10px] text-slate-500">
                                    {{ $record->occurred_at->format('M d, Y') }}
                                    @if($record->category)
                                        · {{ $record->category }}
                                    @endif
                                    @if($record->paid_from)
                                        · Paid from: {{ $record->paid_from }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold {{ $record->type === 'expense' ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ $record->type === 'expense' ? '-' : '+' }}Ksh {{ number_format($record->amount, 0) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">No financial records found.</p>
            @endif
        </div>

        {{-- Contributions --}}
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Contributions</h2>
                <a href="{{ route('partner.contributions') }}" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                    View all
                </a>
            </div>
            @if($contributions->count() > 0)
                <div class="space-y-2">
                    @foreach($contributions->take(5) as $contribution)
                        <div class="flex items-center justify-between p-2 rounded-lg border border-slate-100">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-900 capitalize">{{ str_replace('_', ' ', $contribution->type) }}</p>
                                <p class="text-[10px] text-slate-500">{{ $contribution->contributed_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-slate-900">Ksh {{ number_format($contribution->amount, 0) }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px]
                                    {{ $contribution->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                                       ($contribution->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-100' :
                                        'bg-red-50 text-red-700 border border-red-100') }}">
                                    {{ ucfirst($contribution->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">No contributions recorded.</p>
            @endif
        </div>
    </div>

    {{-- Project Details --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Project Information</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
                <p class="text-slate-500 text-[11px] mb-1">Project Name</p>
                <p class="font-semibold text-slate-900">{{ $project->name }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-[11px] mb-1">Type</p>
                <p class="font-semibold text-slate-900 capitalize">{{ $project->type }}</p>
            </div>
            <div>
                <p class="text-slate-500 text-[11px] mb-1">Status</p>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-medium
                    {{ $project->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-50 text-slate-700 border border-slate-100' }}">
                    {{ $project->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div>
                <p class="text-slate-500 text-[11px] mb-1">Created</p>
                <p class="font-semibold text-slate-900">{{ $project->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Assets Acquired --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Assets Acquired</h2>
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

            <div class="divide-y divide-slate-100">
                @foreach($assets as $asset)
                    <div class="py-2 flex items-start justify-between gap-3 text-xs">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-900 truncate">{{ $asset->name }}</p>
                            <p class="text-[10px] text-slate-500">
                                {{ $asset->category ?? 'Asset' }}
                                @if($asset->date_acquired)
                                    · Acquired {{ $asset->date_acquired->format('M d, Y') }}
                                @endif
                            </p>
                            @if($asset->notes)
                                <p class="text-[10px] text-slate-500 mt-1">{{ \Illuminate\Support\Str::limit($asset->notes, 80) }}</p>
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
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-500">
                No assets have been recorded for this project yet. Once you add land, stock, or equipment with values,
                they will appear here so partners can track real asset value, not guesswork.
            </p>
        @endif
    </div>
@endsection
