@extends('layouts.admin')

@section('page_title', 'Orders')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.orders.index') }}" class="text-slate-600 hover:text-emerald-600 transition-colors">Orders</a>
@endsection

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Orders</h1>
            <p class="text-xs text-slate-500">Track and manage all customer orders.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.orders.export') . '?' . http_build_query(request()->query()) }}"
               class="no-print inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm tooltip"
               data-tooltip="Export orders to CSV (Ctrl/Cmd + E)"
               aria-label="Export orders">
                Export CSV
            </a>
            <a href="{{ route('admin.orders.report') . '?' . http_build_query(request()->query()) }}"
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

    {{-- Quick Filter Presets --}}
    <div class="mb-3 flex flex-wrap gap-2">
        <a href="{{ route('admin.orders.index', ['created_at' => 'today']) }}" 
           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-md border transition-colors {{ request('created_at') == 'today' ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
            Today
        </a>
        <a href="{{ route('admin.orders.index', ['created_at' => 'week']) }}" 
           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-md border transition-colors {{ request('created_at') == 'week' ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
            This Week
        </a>
        <a href="{{ route('admin.orders.index', ['created_at' => 'month']) }}" 
           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-md border transition-colors {{ request('created_at') == 'month' ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
            This Month
        </a>
        @if(request()->hasAny(['q', 'status', 'from_date', 'to_date', 'created_at']))
            <a href="{{ route('admin.orders.index') }}"
               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-md border bg-white border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors">
                Clear Filters
            </a>
        @endif
    </div>

    <form method="GET" class="mb-4 bg-white border border-slate-100 rounded-lg p-3 text-xs grid md:grid-cols-4 gap-3">
        <div>
            <label class="block text-[11px] font-semibold mb-1 text-slate-600">Search</label>
            <input type="text"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Order ID, customer name or email"
                   class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
        </div>
        <div>
            <label class="block text-[11px] font-semibold mb-1 text-slate-600">Status</label>
            <select name="status" class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
                <option value="">All statuses</option>
                @foreach(['pending','processing','shipped','completed','cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-[11px] font-semibold mb-1 text-slate-600">From</label>
                <input type="date"
                       name="from_date"
                       value="{{ request('from_date') }}"
                       class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
            </div>
            <div>
                <label class="block text-[11px] font-semibold mb-1 text-slate-600">To</label>
                <input type="date"
                       name="to_date"
                       value="{{ request('to_date') }}"
                       class="border border-slate-200 rounded w-full px-3 py-1.5 text-xs">
            </div>
        </div>
        <div class="flex items-end gap-2">
            <button class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold px-4 py-1.5 rounded-md">
                Filter
            </button>
            @if(request()->hasAny(['q','status','from_date','to_date']) && (request('q') || request('status') || request('from_date') || request('to_date')))
                <a href="{{ route('admin.orders.index') }}"
                   class="text-xs text-slate-500 hover:text-slate-700">
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Include bulk actions form --}}
    @include('admin.partials.bulk-actions', ['route' => 'admin.orders.bulk'])

    <div class="bg-white border border-slate-100 rounded-lg overflow-x-auto admin-table-scroll text-sm shadow-sm">
        <table class="min-w-full">
            <thead class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
            <tr>
                <th class="px-3 py-2 w-12">
                    <input type="checkbox" id="select-all" aria-label="Select all orders">
                </th>
                <th class="px-3 py-2 text-left" data-sortable data-column="id">Order #</th>
                <th class="px-3 py-2 text-left">Customer</th>
                <th class="px-3 py-2 text-left">Items</th>
                <th class="px-3 py-2 text-left" data-sortable data-column="total">Total</th>
                <th class="px-3 py-2 text-left" data-sortable data-column="status">Status</th>
                <th class="px-3 py-2 text-left" data-sortable data-column="created_at">Date</th>
                <th class="px-3 py-2 text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr class="border-t border-slate-100 hover:bg-slate-50">
                    <td class="px-3 py-2">
                        <input type="checkbox" class="item-checkbox" value="{{ $order->id }}" aria-label="Select order {{ $order->id }}">
                    </td>
                    <td class="px-3 py-2">
                        <div class="text-xs font-semibold text-slate-900">#{{ $order->id }}</div>
                    </td>
                    <td class="px-3 py-2">
                        <div class="text-xs text-slate-900">{{ $order->user->name ?? 'Guest' }}</div>
                        @if($order->user)
                            <div class="text-[10px] text-slate-500">{{ $order->user->email }}</div>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-xs text-slate-600">
                        {{ $order->items->sum('quantity') }} item(s)
                    </td>
                    <td class="px-3 py-2 text-xs font-semibold text-slate-900">Ksh {{ number_format($order->total, 0) }}</td>
                    <td class="px-3 py-2 text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium
                            @class([
                                'bg-amber-50 text-amber-700 border border-amber-100' => $order->status === 'pending',
                                'bg-blue-50 text-blue-700 border border-blue-100' => $order->status === 'processing',
                                'bg-indigo-50 text-indigo-700 border border-indigo-100' => $order->status === 'shipped',
                                'bg-purple-50 text-purple-700 border border-purple-100' => $order->status === 'delivered',
                                'bg-emerald-50 text-emerald-700 border border-emerald-100' => $order->status === 'completed',
                                'bg-red-50 text-red-700 border border-red-100' => $order->status === 'cancelled',
                            ])>
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-[11px] text-slate-500">
                        {{ $order->created_at->format('M d, Y') }}<br>
                        <span class="text-[10px]">{{ $order->created_at->format('H:i') }}</span>
                    </td>
                    <td class="px-3 py-2 text-right space-x-2">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-amber-600 hover:text-amber-700 hover:underline font-medium">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-3 py-12">
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M7 3h10a1 1 0 0 1 1 1v16l-3-2-3 2-3-2-3 2V4a1 1 0 0 1 1-1z" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 8h6M9 12h4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <h3>No orders found</h3>
                            <p>No orders match your search criteria. Try adjusting your filters.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Enhanced Pagination --}}
    @include('admin.partials.pagination', ['paginator' => $orders])
@endsection

@push('scripts')
<script>
    // Sortable table functionality
    function sortTable(column) {
        const currentSort = '{{ request('sort') }}';
        const currentDirection = '{{ request('direction', 'desc') }}';
        const newDirection = (currentSort === column && currentDirection === 'asc') ? 'desc' : 'asc';
        
        const url = new URL(window.location.href);
        url.searchParams.set('sort', column);
        url.searchParams.set('direction', newDirection);
        url.searchParams.delete('page'); // Reset to first page
        
        window.location.href = url.toString();
    }
</script>
@endpush
