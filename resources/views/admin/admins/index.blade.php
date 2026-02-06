@extends('layouts.admin')

@section('page_title', 'Admins')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Admins</h1>
            <p class="text-xs text-slate-500">Manage admin accounts for BrightToys.</p>
        </div>
        <a href="{{ route('admin.admins.create') }}"
           class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Admin
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-lg p-3 text-xs text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs max-w-md">
        <label class="block text-[11px] font-semibold mb-1 text-slate-600">Search</label>
        <div class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Search by name or email"
                   class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
            <button class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 py-1.5 rounded-md">
                Filter
            </button>
        </div>
        @if(request()->has('q') && request('q') !== '')
            <a href="{{ route('admin.admins.index') }}"
               class="inline-block mt-2 text-xs text-slate-500 hover:text-slate-700">
                Clear
            </a>
        @endif
    </form>

    <div class="bg-white border border-slate-100 rounded-lg overflow-x-auto text-sm shadow-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
            <tr>
                <th class="px-3 py-2 text-left">Admin</th>
                <th class="px-3 py-2 text-left">Email</th>
                <th class="px-3 py-2 text-left">Joined</th>
                <th class="px-3 py-2 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($admins as $admin)
                <tr class="hover:bg-slate-50">
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-full bg-emerald-500 flex items-center justify-center text-white text-xs font-semibold">
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-medium text-slate-900">{{ $admin->name }}</div>
                                @if($admin->id === auth()->id())
                                    <div class="text-[10px] text-amber-600 font-medium">You</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-slate-600">{{ $admin->email }}</td>
                    <td class="px-3 py-2 text-slate-500 text-xs">{{ $admin->created_at->format('M d, Y') }}</td>
                    <td class="px-3 py-2">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.admins.show', $admin) }}"
                               class="text-xs text-slate-600 hover:text-amber-600 font-medium">
                                View
                            </a>
                            <a href="{{ route('admin.admins.edit', $admin) }}"
                               class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                Edit
                            </a>
                            @if($admin->id !== auth()->id())
                                <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-medium">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-3 py-8 text-center text-sm text-slate-500">
                        No admins found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $admins->links() }}
    </div>
@endsection
