@extends('layouts.admin')

@section('page_title', 'Users')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.users.index') }}" class="text-slate-600 hover:text-emerald-600 transition-colors">Customers</a>
@endsection

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Customers</h1>
            <p class="text-xs text-slate-500">List of registered customers.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.export') . '?' . http_build_query(request()->query()) }}"
               class="no-print inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm tooltip"
               data-tooltip="Export customers to CSV (Ctrl/Cmd + E)"
               aria-label="Export customers">
                Export CSV
            </a>
            <a href="{{ route('admin.users.report') . '?' . http_build_query(request()->query()) }}"
               class="no-print inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm tooltip"
               data-tooltip="Generate printable report"
               aria-label="Generate report">
                Generate Report
            </a>
            <button onclick="window.print()"
                    class="no-print inline-flex items-center justify-center bg-slate-500 hover:bg-slate-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm tooltip"
                    data-tooltip="Print this page (Ctrl/Cmd + P)"
                    aria-label="Print page">
                Print
            </button>
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

    <div class="bg-white border border-slate-100 rounded-lg overflow-x-auto admin-table-scroll text-sm shadow-sm">
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
                    <td colspan="5" class="px-3 py-12">
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4 20a8 8 0 0 1 16 0" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <h3>No customers found</h3>
                            <p>No customers match your search criteria. Try adjusting your search terms.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection

