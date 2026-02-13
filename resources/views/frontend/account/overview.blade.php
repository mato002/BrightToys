@extends('layouts.account')

@section('title', 'Dashboard Overview')
@section('page_title', 'Dashboard Overview')

@section('content')
    <div class="space-y-6">
        {{-- Welcome Section --}}
        <div class="mb-8 bg-gradient-to-br from-amber-500 via-amber-600 to-orange-600 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 md:p-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <div class="relative">
                            <div class="w-20 h-20 md:w-28 md:h-28 rounded-full bg-white/20 backdrop-blur-sm border-4 border-white/30 text-white flex items-center justify-center text-3xl md:text-4xl font-bold shadow-2xl">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="absolute -bottom-2 -right-2 w-7 h-7 md:w-8 md:h-8 rounded-full bg-emerald-500 border-4 border-white flex items-center justify-center">
                                <span class="h-2 w-2 md:h-2.5 md:w-2.5 rounded-full bg-white animate-pulse"></span>
                            </div>
                        </div>
                        <div class="text-white">
                            <h1 class="text-2xl md:text-3xl font-bold mb-2">Welcome back, {{ $user->name }}!</h1>
                            <p class="text-amber-50 text-base md:text-lg mb-3">{{ $user->email }}</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-sm text-white text-sm font-semibold border border-white/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                    </svg>
                                    Customer Account
                                </span>
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/90 text-white text-sm font-medium border border-emerald-400/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Key Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 mb-2">Total Orders</p>
                        <p class="text-3xl font-bold text-amber-900">{{ $stats['total_orders'] }}</p>
                        <p class="text-xs text-amber-600 mt-1">{{ $stats['completed_orders'] }} completed</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-amber-200/50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <path d="M20 8v6M23 11h-6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700 mb-2">Total Spent</p>
                        <p class="text-3xl font-bold text-blue-900">Ksh {{ number_format($stats['total_spent'], 0) }}</p>
                        <p class="text-xs text-blue-600 mt-1">Ksh {{ number_format($stats['spent_this_month'], 0) }} this month</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-blue-200/50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-purple-700 mb-2">Cart Items</p>
                        <p class="text-3xl font-bold text-purple-900">{{ $stats['cart_items'] }}</p>
                        <p class="text-xs text-purple-600 mt-1">{{ $stats['wishlist_items'] }} in wishlist</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-purple-200/50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"/>
                            <circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 mb-2">Orders This Month</p>
                        <p class="text-3xl font-bold text-emerald-900">{{ $stats['orders_this_month'] }}</p>
                        <p class="text-xs text-emerald-600 mt-1">{{ $stats['saved_addresses'] }} saved addresses</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-emerald-200/50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Status Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <div class="bg-white border-2 border-slate-200 rounded-xl p-4 text-center">
                <p class="text-xs font-semibold text-slate-500 mb-2">Pending</p>
                <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_orders'] }}</p>
            </div>
            <div class="bg-white border-2 border-slate-200 rounded-xl p-4 text-center">
                <p class="text-xs font-semibold text-slate-500 mb-2">Processing</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['processing_orders'] }}</p>
            </div>
            <div class="bg-white border-2 border-slate-200 rounded-xl p-4 text-center">
                <p class="text-xs font-semibold text-slate-500 mb-2">Shipped</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['shipped_orders'] }}</p>
            </div>
            <div class="bg-white border-2 border-slate-200 rounded-xl p-4 text-center">
                <p class="text-xs font-semibold text-slate-500 mb-2">Delivered</p>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['delivered_orders'] }}</p>
            </div>
            <div class="bg-white border-2 border-slate-200 rounded-xl p-4 text-center">
                <p class="text-xs font-semibold text-slate-500 mb-2">Completed</p>
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['completed_orders'] }}</p>
            </div>
            <div class="bg-white border-2 border-slate-200 rounded-xl p-4 text-center">
                <p class="text-xs font-semibold text-slate-500 mb-2">Cancelled</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled_orders'] }}</p>
            </div>
        </div>

        {{-- Recent Orders & Activity --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Orders --}}
            <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-slate-900">Recent Orders</h2>
                    <a href="{{ route('account.orders') }}" class="text-xs text-amber-600 hover:text-amber-700 font-semibold">
                        View All →
                    </a>
                </div>
                @if($recentOrders->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentOrders as $order)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-slate-900">Order #{{ $order->order_number ?? $order->id }}</p>
                                    <p class="text-xs text-slate-500">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-amber-600">Ksh {{ number_format($order->total, 0) }}</p>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                        @if($order->status === 'completed') bg-emerald-100 text-emerald-700
                                        @elseif($order->status === 'delivered') bg-purple-100 text-purple-700
                                        @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-700
                                        @elseif($order->status === 'processing') bg-amber-100 text-amber-700
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                                        @else bg-slate-100 text-slate-700
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <path d="M20 8v6M23 11h-6"/>
                        </svg>
                        <p class="text-sm">No orders yet</p>
                    </div>
                @endif
            </div>

            {{-- Orders Activity (Last 7 Days) --}}
            <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-5">Orders Activity (Last 7 Days)</h2>
                <div class="space-y-3">
                    @foreach($ordersLast7Days as $day)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-900">{{ $day['date'] }}</p>
                                <p class="text-xs text-slate-500">{{ $day['count'] }} order(s)</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-900">Ksh {{ number_format($day['total'], 0) }}</p>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="border-slate-200">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white border-2 border-slate-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('shop.index') }}" 
                   class="flex items-center gap-3 p-4 bg-amber-50 border-2 border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-amber-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Browse Store</p>
                        <p class="text-xs text-slate-500">Shop for products</p>
                    </div>
                </a>
                <a href="{{ route('account.cart.index') }}" 
                   class="flex items-center gap-3 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"/>
                            <circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">View Cart</p>
                        <p class="text-xs text-slate-500">{{ $stats['cart_items'] }} item(s)</p>
                    </div>
                </a>
                <a href="{{ route('account.orders') }}" 
                   class="flex items-center gap-3 p-4 bg-purple-50 border-2 border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                    <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <path d="M20 8v6M23 11h-6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">My Orders</p>
                        <p class="text-xs text-slate-500">{{ $stats['total_orders'] }} total orders</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
