@extends('layouts.admin')

@section('page_title', 'Projects')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Projects</h1>
            <p class="text-xs text-slate-500">Manage projects that partners can access from their dashboard.</p>
        </div>
        <a href="{{ route('admin.projects.create') }}"
           class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded">
            Add Project
        </a>
    </div>

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs grid md:grid-cols-3 gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search projects..."
               class="border border-slate-200 rounded w-full px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
        <select name="status" class="border border-slate-200 rounded px-3 py-2 text-sm">
            <option value="">All Statuses</option>
            <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded text-xs">
            Filter
        </button>
    </form>

    @if(session('success'))
        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-100 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Created By</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Assigned Users</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Capital Mix</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($projects as $project)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-900">{{ $project->name }}</div>
                                @if($project->description)
                                    <div class="text-xs text-slate-500 mt-0.5">{{ Str::limit($project->description, 50) }}</div>
                                @endif
                                @if($project->objective)
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        <span class="font-semibold">Objective:</span>
                                        {{ Str::limit($project->objective, 60) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ \App\Models\Project::getTypeLabel($project->type) }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                @if($project->creator)
                                    <span class="font-medium">{{ $project->creator->name }}</span>
                                    <span class="text-slate-400">(Partner)</span>
                                @else
                                    <span class="text-slate-400">Admin</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                @if($project->assignedUsers->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($project->assignedUsers->take(2) as $user)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                                {{ $user->name }}
                                            </span>
                                        @endforeach
                                        @if($project->assignedUsers->count() > 2)
                                            <span class="text-slate-400">+{{ $project->assignedUsers->count() - 2 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400">None</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                        @if(($project->status ?? 'planning') === 'active') bg-emerald-100 text-emerald-700
                                        @elseif(($project->status ?? 'planning') === 'completed') bg-blue-100 text-blue-700
                                        @elseif(($project->status ?? 'planning') === 'suspended') bg-amber-100 text-amber-700
                                        @else bg-slate-100 text-slate-600 @endif">
                                        {{ ucfirst($project->status ?? 'planning') }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium
                                        {{ $project->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-50 text-slate-500' }}">
                                        {{ $project->is_active ? 'Linked Active' : 'Not Active' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                @php $mix = $project->capital_mix; @endphp
                                @if($mix['total'] > 0)
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        {{ $mix['equity'] }}% equity / {{ $mix['debt'] }}% debt
                                    </span>
                                @else
                                    <span class="text-slate-400">Not set</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.projects.show', $project) }}"
                                       class="text-emerald-600 hover:text-emerald-700 text-xs">View</a>
                                    <a href="{{ route('admin.projects.edit', $project) }}"
                                       class="text-amber-600 hover:text-amber-700 text-xs">Edit</a>
                                    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline" data-confirm="Delete this project?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-600 text-xs">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500 text-sm">
                                No projects found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $projects->links() }}
    </div>
@endsection
