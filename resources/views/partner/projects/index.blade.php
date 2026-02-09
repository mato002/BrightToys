@extends('layouts.partner')

@section('page_title', 'Projects')
@section('partner_content')
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold">Projects</h1>
                <p class="text-xs text-slate-500">
                    Access and manage all partnership projects including the e-commerce platform.
                </p>
            </div>
            <a href="{{ route('partner.projects.manage') }}"
               class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                Manage My Projects
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-100 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Project</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Created By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Created</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($projects as $project)
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
                            $user = auth()->user();
                            $partner = $user->partner;
                            $isMyProject = $partner && $project->created_by === $partner->id;
                        @endphp
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-lg {{ $colorClass }} flex items-center justify-center text-[11px] font-semibold">
                                        {{ Str::upper(Str::substr($project->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $project->name }}</p>
                                        @if($project->description)
                                            <p class="text-[11px] text-slate-500 line-clamp-1">{{ $project->description }}</p>
                                        @endif
                                        @if($isMyProject)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100 mt-1">
                                                My Project
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-top text-xs text-slate-600 capitalize">
                                {{ $project->type }}
                            </td>
                            <td class="px-4 py-3 align-top">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                    {{ $project->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-50 text-slate-700 border border-slate-100' }}">
                                    {{ $project->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-top text-xs text-slate-600">
                                @if($project->creator)
                                    {{ $project->creator->name }}
                                @else
                                    <span class="text-slate-400">Admin</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top text-xs text-slate-500">
                                {{ $project->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 align-top text-right text-xs">
                                <div class="flex flex-wrap justify-end gap-1">
                                    <a href="{{ route('partner.projects.show', $project) }}"
                                       class="inline-flex items-center px-2 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50">
                                        View
                                    </a>
                                    @if($isMyProject)
                                        <a href="{{ route('partner.projects.manage.edit', $project) }}"
                                           class="inline-flex items-center px-2 py-1 rounded bg-amber-500 hover:bg-amber-600 text-white">
                                            Edit
                                        </a>
                                        <a href="{{ route('partner.projects.performance', $project) }}"
                                           class="inline-flex items-center px-2 py-1 rounded bg-emerald-500 hover:bg-emerald-600 text-white">
                                            Perf.
                                        </a>
                                        <a href="{{ route('partner.projects.finances', $project) }}"
                                           class="inline-flex items-center px-2 py-1 rounded bg-blue-500 hover:bg-blue-600 text-white">
                                            Finances
                                        </a>
                                    @endif
                                    @if($project->url || $project->route_name)
                                        <a href="{{ route('partner.projects.redirect', $project) }}" target="_blank"
                                           class="inline-flex items-center px-2 py-1 rounded border border-slate-200 text-slate-600 hover:bg-slate-50">
                                            Open
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
                                No projects available at the moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
