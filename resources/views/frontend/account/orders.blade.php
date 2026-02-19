@extends('layouts.account')

@section('title', 'My Orders')
@section('page_title', 'My Orders')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('account.orders') }}" class="text-slate-600 hover:text-amber-600 transition-colors">Orders</a>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">My Orders</h1>
                <p class="text-xs md:text-sm text-slate-500 mt-1">View and manage all your orders</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('account.orders.export', request()->query()) }}" 
                   class="no-print inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border-2 border-slate-300 text-sm font-semibold px-4 py-2 rounded-lg transition-all hover:shadow-md tooltip" 
                   data-tooltip="Export your orders to CSV file"
                   aria-label="Export orders">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Export CSV
                </a>
                <button onclick="window.print()" 
                        class="no-print inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 border-2 border-slate-300 text-sm font-semibold px-4 py-2 rounded-lg transition-all hover:shadow-md tooltip" 
                        data-tooltip="Print this page (Ctrl/Cmd + P)"
                        aria-label="Print page">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <path d="M6 14h12v8H6z"/>
                    </svg>
                    Print
                </button>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white border-2 border-slate-200 rounded-xl p-4 md:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-slate-900">Search & Filter</h3>
                <a href="#" class="text-xs text-amber-600 hover:text-amber-700 flex items-center gap-1 tooltip" data-tooltip="Use filters to find specific orders. Press Ctrl+F to focus search.">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3m.08 4h.01"/>
                    </svg>
                    Help
                </a>
            </div>
            <form method="GET" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Search</label>
                    <input type="text" 
                           name="search" 
                           id="search-input"
                           value="{{ request('search') }}" 
                           placeholder="Search by order number, tracking number, or product name..."
                           class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status</label>
                    <select name="status"
                            class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">All statuses</option>
                        @foreach(['pending','processing','shipped','completed','cancelled'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">From Date</label>
                    <input type="date"
                           name="from_date"
                           value="{{ request('from_date') }}"
                           class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">To Date</label>
                    <input type="date"
                           name="to_date"
                           value="{{ request('to_date') }}"
                           class="w-full border-2 border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-slate-200">
                    <button type="submit"
                            class="flex-1 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                        Apply Filters
                    </button>
                    @if(request()->hasAny(['status','from_date','to_date','search']) && (request('status') || request('from_date') || request('to_date') || request('search')))
                        <a href="{{ route('account.orders') }}"
                           class="px-4 py-2 text-sm text-slate-600 hover:text-slate-900 font-medium border-2 border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Orders List --}}
        @forelse($orders as $order)
            <div class="bg-white border-2 border-slate-200 rounded-xl p-5 md:p-6 shadow-sm hover:shadow-lg transition-all">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5 pb-5 border-b-2 border-slate-100">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg md:text-xl font-bold text-slate-900">Order #{{ $order->order_number ?? $order->id }}</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                @if($order->status === 'completed') bg-emerald-100 text-emerald-700 border border-emerald-200
                                @elseif($order->status === 'delivered') bg-purple-100 text-purple-700 border border-purple-200
                                @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-700 border border-indigo-200
                                @elseif($order->status === 'processing') bg-amber-100 text-amber-700 border border-amber-200
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-700 border border-red-200
                                @else bg-slate-100 text-slate-700 border border-slate-200
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        @if($order->tracking_number)
                            <p class="text-xs text-slate-600 mb-1">
                                <span class="font-semibold">Tracking:</span> 
                                <span class="font-mono">{{ $order->tracking_number }}</span>
                            </p>
                        @endif
                        <p class="text-xs text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-2xl md:text-3xl font-bold text-amber-600 mb-1">
                            Ksh {{ number_format($order->total, 0) }}
                        </p>
                        <p class="text-xs text-slate-500">{{ $order->items->count() }} item(s)</p>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="space-y-3 mb-5">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                            @if($item->product && $item->product->image_url)
                                <div class="w-16 h-16 md:w-20 md:h-20 rounded-lg overflow-hidden bg-white border-2 border-slate-200 flex-shrink-0">
                                    <img src="{{ str_starts_with($item->product->image_url, 'http') ? $item->product->image_url : asset('images/toys/' . $item->product->image_url) }}" 
                                         alt="{{ $item->product->name }}"
                                         class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-16 h-16 md:w-20 md:h-20 rounded-lg bg-slate-200 flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-900 text-sm md:text-base mb-1 truncate">
                                    {{ $item->product->name ?? 'Product' }}
                                </p>
                                <p class="text-xs text-slate-600">
                                    Quantity: <span class="font-semibold">{{ $item->quantity }}</span>
                                    <span class="mx-2">•</span>
                                    Unit Price: <span class="font-semibold">Ksh {{ number_format($item->price, 0) }}</span>
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-bold text-slate-900 text-base md:text-lg">
                                    Ksh {{ number_format($item->price * $item->quantity, 0) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order status progress bar --}}
                @if(!in_array($order->status, ['cancelled']))
                    @php
                        $steps = ['pending' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4, 'completed' => 5];
                        $pct = isset($steps[$order->status]) ? ($steps[$order->status] / 5) * 100 : 0;
                    @endphp
                    <div class="mb-3">
                        <div class="flex justify-between text-[10px] text-slate-500 mb-0.5">
                            <span>Placed</span><span>Processing</span><span>Shipped</span><span>Delivered</span><span>Done</span>
                        </div>
                        <div class="h-1.5 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endif
                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-2 pt-4 border-t-2 border-slate-100">
                    <a href="{{ route('account.orders.track', $order) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-amber-700 bg-amber-50 border-2 border-amber-200 rounded-lg hover:bg-amber-100 hover:border-amber-300 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        Track Order
                    </a>
                    <form action="{{ route('account.orders.reorder', $order) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-emerald-700 bg-emerald-50 border-2 border-emerald-200 rounded-lg hover:bg-emerald-100 hover:border-emerald-300 transition-colors">
                            <i class="fas fa-redo"></i> Reorder
                        </button>
                    </form>
                    <a href="{{ route('account.orders.invoice', $order) }}" 
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-blue-700 bg-blue-50 border-2 border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                        Download Invoice
                    </a>
                    @if(in_array($order->status, ['pending', 'processing']))
                        <form action="{{ route('account.orders.cancel', $order) }}" 
                              method="POST" 
                              class="inline"
                              data-confirm="Are you sure you want to cancel this order?">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-700 bg-red-50 border-2 border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                                Cancel Order
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white border-2 border-dashed border-slate-300 rounded-xl p-12 text-center">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 md:h-20 md:w-20 text-slate-300 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <path d="M20 8v6M23 11h-6"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No orders yet</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-md mx-auto">Start shopping to see your orders here. Browse our collection and place your first order!</p>
                <a href="{{ route('shop.index') }}" 
                   class="inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow-lg shadow-amber-500/30 transition-all hover:shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Browse Store
                </a>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
