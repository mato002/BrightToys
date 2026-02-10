@extends('layouts.partner')

@section('page_title', $project->name)
@section('partner_content')
    <div class="mb-4">
        <div class="flex items-center justify-between gap-3 mb-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('partner.projects.index') }}" 
                   class="text-slate-500 hover:text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <h1 class="text-lg font-semibold">{{ $project->name }}</h1>
            </div>

            @if($isMyProject ?? false)
                <a href="{{ route('partner.projects.manage.edit', $project) }}"
                   class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Edit Project
                </a>
            @endif
        </div>
        <p class="text-xs text-slate-500">
            Project details, management shortcuts and access information.
        </p>
    </div>

    <div class="bg-white rounded-lg border border-slate-100 p-6 shadow-sm">
        @php
            $colorClasses = [
                'emerald' => 'bg-emerald-100 text-emerald-600',
                'blue' => 'bg-blue-100 text-blue-600',
                'amber' => 'bg-amber-100 text-amber-600',
                'purple' => 'bg-purple-100 text-purple-600',
                'red' => 'bg-red-100 text-red-600',
                'indigo' => 'bg-indigo-100 text-indigo-600',
            ];
            $colorClass = $colorClasses[$project->color] ?? 'bg-slate-100 text-slate-600';
            $buttonColorClasses = [
                'emerald' => 'bg-emerald-600 hover:bg-emerald-700',
                'blue' => 'bg-blue-600 hover:bg-blue-700',
                'amber' => 'bg-amber-600 hover:bg-amber-700',
                'purple' => 'bg-purple-600 hover:bg-purple-700',
                'red' => 'bg-red-600 hover:bg-red-700',
                'indigo' => 'bg-indigo-600 hover:bg-indigo-700',
            ];
            $buttonColorClass = $buttonColorClasses[$project->color] ?? 'bg-slate-600 hover:bg-slate-700';
        @endphp
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-4">
                @if($project->icon)
                    <div class="w-16 h-16 rounded-xl {{ $colorClass }} flex items-center justify-center">
                        <i class="{{ $project->icon }} text-2xl"></i>
                    </div>
                @else
                    <div class="w-16 h-16 rounded-xl {{ $colorClass }} flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M4 7l8-4 8 4-8 4-8-4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 17l8 4 8-4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">{{ $project->name }}</h2>
                    <p class="text-sm text-slate-500 capitalize">{{ $project->type }} Project</p>

                    @php $mix = $project->capital_mix; @endphp
                    @if($mix['total'] > 0)
                        <p class="mt-1 text-xs text-slate-500">
                            Capital structure: <span class="font-semibold text-slate-900">{{ $mix['equity'] }}% equity</span> ·
                            <span class="font-semibold text-slate-900">{{ $mix['debt'] }}% debt</span>
                        </p>
                    @else
                        <p class="mt-1 text-xs text-amber-600">
                            Capital structure not yet configured.
                        </p>
                    @endif
                </div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                {{ $project->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-50 text-slate-700 border border-slate-100' }}">
                {{ $project->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        @if($project->description)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-2">Description</h3>
                <p class="text-sm text-slate-600">{{ $project->description }}</p>
            </div>
        @endif

        <div class="grid md:grid-cols-2 gap-4 mb-6">
            <div class="border border-slate-100 rounded-lg p-4">
                <p class="text-xs text-slate-500 mb-1">Project Type</p>
                <p class="text-sm font-semibold text-slate-900 capitalize">{{ $project->type }}</p>
            </div>

            <div class="border border-slate-100 rounded-lg p-4">
                <p class="text-xs text-slate-500 mb-1">Status</p>
                <p class="text-sm font-semibold text-slate-900">{{ $project->is_active ? 'Active' : 'Inactive' }}</p>
            </div>
        </div>

        <div class="border border-slate-100 rounded-lg p-4 mb-6">
            <p class="text-xs font-semibold text-slate-900 mb-2">Capital & Funding Structure</p>
            @php $funding = $project->funding; @endphp

            @if($funding)
                <div class="grid md:grid-cols-2 gap-3 text-xs">
                    <div class="space-y-1">
                        <p class="text-[11px] text-slate-500 uppercase">Member Capital</p>
                        <p class="flex items-center justify-between">
                            <span class="text-slate-500">Amount</span>
                            <span class="font-semibold text-slate-900">
                                Ksh {{ number_format($funding->member_capital_amount, 0) }}
                            </span>
                        </p>
                        @if($funding->member_capital_date)
                            <p class="flex items-center justify-between">
                                <span class="text-slate-500">Date</span>
                                <span class="font-medium text-slate-900">
                                    {{ $funding->member_capital_date->format('M d, Y') }}
                                </span>
                            </p>
                        @endif
                        @if($funding->member_capital_source)
                            <p class="flex items-center justify-between">
                                <span class="text-slate-500">Source</span>
                                <span class="font-medium text-slate-900">
                                    {{ $funding->member_capital_source }}
                                </span>
                            </p>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <p class="text-[11px] text-slate-500 uppercase">Loan / Debt</p>
                        @if($funding->has_loan)
                            <p class="flex items-center justify-between">
                                <span class="text-slate-500">Lender</span>
                                <span class="font-medium text-slate-900">{{ $funding->lender_name ?? 'N/A' }}</span>
                            </p>
                            <p class="flex items-center justify-between">
                                <span class="text-slate-500">Loan Amount</span>
                                <span class="font-semibold text-slate-900">
                                    Ksh {{ number_format($funding->loan_amount ?? 0, 0) }}
                                </span>
                            </p>
                            <p class="flex items-center justify-between">
                                <span class="text-slate-500">Outstanding</span>
                                <span class="font-semibold text-slate-900">
                                    Ksh {{ number_format($funding->outstanding_balance ?? 0, 0) }}
                                </span>
                            </p>
                        @else
                            <p class="text-xs text-slate-500">
                                No loan on this project (equity only).
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-500">
                    Capital & funding details have not been set up yet.
                </p>
            @endif
        </div>

        @if($project->url || $project->route_name)
            <div class="mb-6 border border-slate-100 rounded-lg p-4">
                <p class="text-xs text-slate-500 mb-1">Project URL</p>
                <p class="text-sm font-semibold text-slate-900 break-all">
                    @if($project->route_name)
                        Route: <code class="bg-slate-100 px-2 py-0.5 rounded text-xs">{{ $project->route_name }}</code>
                    @elseif($project->url)
                        <a href="{{ $project->url }}" target="_blank" class="text-emerald-600 hover:underline">{{ $project->url }}</a>
                    @endif
                </p>
            </div>
        @else
            <div class="mb-6 border border-amber-100 bg-amber-50 rounded-lg p-4">
                <p class="text-xs text-amber-800 font-semibold mb-1">⚠️ Project Not Yet Developed</p>
                <p class="text-sm text-amber-700">This project is in planning phase. Add a URL or route name in the edit page when it's ready to launch.</p>
            </div>
        @endif

        <div class="space-y-3 pt-4 border-t border-slate-100">
            @if($project->url || $project->route_name)
                {{-- Open public-facing project --}}
                <a href="{{ route('partner.projects.redirect', $project) }}" 
                   target="_blank"
                   class="inline-flex items-center justify-center gap-2 w-full {{ $buttonColorClass }} text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors">
                    <span>Open Public Site</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 3h6v6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 14L21 3" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            @endif

            @if($isMyProject ?? false)
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('partner.projects.performance', $project) }}"
                       class="text-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-3 py-2 rounded-lg">
                        Performance
                    </a>
                    <a href="{{ route('partner.projects.finances', $project) }}"
                       class="text-center bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-3 py-2 rounded-lg">
                        Finances
                    </a>
                </div>

                <form action="{{ route('partner.projects.manage.destroy', $project) }}"
                      method="POST"
                      class="flex justify-end"
                      data-confirm="Delete this project?">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                        Delete Project
                    </button>
                </form>
            @endif

            <a href="{{ route('partner.projects.index') }}" 
               class="inline-flex items-center justify-center gap-2 border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors w-full">
                Back to Projects
            </a>
        </div>
    </div>
@endsection
