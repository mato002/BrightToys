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
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.projects.edit', $project) }}"
                   class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    Edit
                </a>
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
                        <dd class="font-medium text-slate-900 capitalize">{{ $project->type }}</dd>
                    </div>
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
