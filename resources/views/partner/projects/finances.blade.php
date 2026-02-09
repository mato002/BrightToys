@extends('layouts.partner')

@section('page_title', $project->name . ' - Finances')

@section('partner_content')
    <div class="mb-4">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('partner.projects.manage') }}" class="text-slate-500 hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">{{ $project->name }} - Financial Overview</h1>
        </div>
        <p class="text-xs text-slate-500">View financial records and contributions for this project.</p>
    </div>

    {{-- Financial Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Project Revenue</p>
            <p class="text-2xl font-bold text-emerald-600">Ksh {{ number_format($projectRevenue, 0) }}</p>
            @if($project->route_name === 'home' || $project->type === 'ecommerce')
                <p class="text-[10px] text-slate-500 mt-1">From completed orders</p>
            @endif
        </div>
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Project Expenses</p>
            <p class="text-2xl font-bold text-red-600">Ksh {{ number_format($projectExpenses, 0) }}</p>
            <p class="text-[10px] text-slate-500 mt-1">Approved expenses</p>
        </div>
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Net Profit</p>
            <p class="text-2xl font-bold text-slate-900">Ksh {{ number_format($projectRevenue - $projectExpenses, 0) }}</p>
            <p class="text-[10px] text-slate-500 mt-1">Revenue - Expenses</p>
        </div>
    </div>

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
                                <p class="text-[10px] text-slate-500">{{ $record->occurred_at->format('M d, Y') }}</p>
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
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
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
@endsection
