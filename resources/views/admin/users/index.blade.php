@extends('layouts.admin')

@section('page_title', 'Users')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Customers</h1>
            <p class="text-xs text-slate-500">List of registered customers.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.export', request()->query()) }}"
               class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                Export CSV
            </a>
            <a href="{{ route('admin.users.report', request()->query()) }}"
               class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                Generate Report
            </a>
        </div>
    </div>

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
            <a href="{{ route('admin.users.index') }}"
               class="inline-block mt-2 text-xs text-slate-500 hover:text-slate-700">
                Clear
            </a>
        @endif
    </form>

    <div class="bg-white border border-slate-100 rounded-lg overflow-x-auto text-sm shadow-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
            <tr>
                <th class="px-3 py-2 text-left">Customer</th>
                <th class="px-3 py-2 text-left">Email</th>
                <th class="px-3 py-2 text-left">Orders</th>
                <th class="px-3 py-2 text-left">Joined</th>
                <th class="px-3 py-2 text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                    <td class="px-3 py-2">
                        <div class="text-xs font-semibold text-slate-900">{{ $user->name }}</div>
                    </td>
                    <td class="px-3 py-2 text-xs text-slate-600">{{ $user->email }}</td>
                    <td class="px-3 py-2 text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-medium">
                            {{ $user->orders_count ?? 0 }} order(s)
                        </span>
                    </td>
                    <td class="px-3 py-2 text-[11px] text-slate-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-3 py-2 text-right">
                        <a href="{{ route('admin.users.show', $user) }}" class="text-xs text-amber-600 hover:text-amber-700 hover:underline font-medium">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-3 py-4 text-center text-slate-500 text-sm">No customers found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection

