@extends('layouts.account')

@section('title', 'Dashboard Overview')
@section('page_title', 'Dashboard Overview')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">Overview</span>
@endsection

@section('content')
    <div class="space-y-8">
        {{-- Welcome: compact strip --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="relative flex-shrink-0">
                    <div class="w-14 h-14 rounded-2xl bg-slate-800 text-white flex items-center justify-center text-xl font-semibold shadow-lg">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white"></span>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-slate-900 tracking-tight">Welcome back, {{ $user->name }}</h1>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">Customer</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>
                    </div>
                </div>
            </div>
            @if(isset($profileCompletenessPercent) && $profileCompletenessPercent < 100)
                <a href="{{ route('account.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200/80 hover:bg-amber-100/80 transition-colors group">
                    <div class="flex-1 min-w-[100px]">
                        <p class="text-xs font-medium text-amber-800">Profile {{ $profileCompletenessPercent }}%</p>
                        <div class="h-1.5 bg-amber-200 rounded-full overflow-hidden mt-1">
                            <div class="h-full bg-amber-500 rounded-full transition-all" style="width: {{ $profileCompletenessPercent }}%"></div>
                        </div>
                    </div>
                    <span class="text-amber-600 text-sm font-medium group-hover:text-amber-700">Complete →</span>
                </a>
            @endif
        </div>

        {{-- Key metrics: single row, minimal cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-sm hover:shadow transition-shadow">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total orders</p>
                <p class="text-2xl font-bold text-slate-900">{{ $stats['total_orders'] }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $stats['completed_orders'] }} completed</p>
            </div>
            <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-sm hover:shadow transition-shadow">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total spent</p>
                <p class="text-2xl font-bold text-slate-900">Ksh {{ number_format($stats['total_spent'], 0) }}</p>
                <p class="text-xs text-slate-500 mt-0.5">Ksh {{ number_format($stats['spent_this_month'], 0) }} this month</p>
            </div>
            <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-sm hover:shadow transition-shadow">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Cart & wishlist</p>
                <p class="text-2xl font-bold text-slate-900">{{ $stats['cart_items'] }} <span class="text-lg font-normal text-slate-400">/ {{ $stats['wishlist_items'] }}</span></p>
                <p class="text-xs text-slate-500 mt-0.5">in cart / wishlist</p>
            </div>
            <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-sm hover:shadow transition-shadow">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">This month</p>
                <p class="text-2xl font-bold text-slate-900">{{ $stats['orders_this_month'] }} orders</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $stats['saved_addresses'] }} saved addresses</p>
            </div>
        </div>

        {{-- Charts row: Last 7 days bar + Order status doughnut --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="text-base font-semibold text-slate-900">Orders & spending (last 7 days)</h2>
                </div>
                <div class="p-5">
                    <div class="h-64">
                        <canvas id="chartLast7Days" aria-label="Orders and spending last 7 days"></canvas>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="text-base font-semibold text-slate-900">Orders by status</h2>
                </div>
                <div class="p-5 flex items-center justify-center">
                    <div class="h-64 w-full max-w-xs">
                        <canvas id="chartOrderStatus" aria-label="Orders by status"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly trend chart (full width) --}}
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-900">Orders & spending this year ({{ now()->year }})</h2>
            </div>
            <div class="p-5">
                <div class="h-72">
                    <canvas id="chartMonthly" aria-label="Monthly orders and spending"></canvas>
                </div>
            </div>
        </div>

        {{-- Two columns: Recent orders + Activity (7 days list kept as backup / optional) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-slate-900">Recent orders</h2>
                    <a href="{{ route('account.orders') }}" class="text-sm font-medium text-amber-600 hover:text-amber-700">View all</a>
                </div>
                <div class="p-5">
                    @if($recentOrders->count() > 0)
                        <ul class="space-y-4">
                            @foreach($recentOrders as $order)
                                @php
                                    $statusOrder = ['pending' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4, 'completed' => 5];
                                    $progressPercent = ($order->status !== 'cancelled' && isset($statusOrder[$order->status])) ? ($statusOrder[$order->status] / 5) * 100 : 0;
                                @endphp
                                <li class="group">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-slate-900 truncate">#{{ $order->order_number ?? $order->id }}</p>
                                            <p class="text-xs text-slate-500">{{ $order->created_at->format('M d, Y') }} · Ksh {{ number_format($order->total, 0) }}</p>
                                            @if($order->status !== 'cancelled' && in_array($order->status, ['pending','processing','shipped','delivered','completed']))
                                                <div class="mt-2 h-1 bg-slate-100 rounded-full overflow-hidden">
                                                    <div class="h-full bg-amber-500 rounded-full" style="width: {{ $progressPercent }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="flex-shrink-0 inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                            @if($order->status === 'completed') bg-emerald-50 text-emerald-700
                                            @elseif($order->status === 'delivered') bg-indigo-50 text-indigo-700
                                            @elseif($order->status === 'shipped') bg-blue-50 text-blue-700
                                            @elseif($order->status === 'processing') bg-amber-50 text-amber-700
                                            @elseif($order->status === 'cancelled') bg-red-50 text-red-700
                                            @else bg-slate-100 text-slate-700
                                            @endif">{{ ucfirst($order->status) }}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <a href="{{ route('account.orders.track', $order) }}" class="text-xs font-medium text-amber-600 hover:text-amber-700">Track</a>
                                        @if(in_array($order->status, ['completed', 'delivered', 'shipped', 'processing', 'pending']))
                                            <form action="{{ route('account.orders.reorder', $order) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs font-medium text-slate-600 hover:text-slate-900">Reorder</button>
                                            </form>
                                        @endif
                                    </div>
                                    @if(!$loop->last)<hr class="mt-4 border-slate-100">@endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="py-10 text-center">
                            <p class="text-sm text-slate-500">No orders yet</p>
                            <a href="{{ route('account.shop') }}" class="inline-block mt-2 text-sm font-medium text-amber-600 hover:text-amber-700">Browse shop</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-slate-900">Last 7 days summary</h2>
                    <a href="{{ route('account.analytics') }}" class="text-sm font-medium text-amber-600 hover:text-amber-700">Full analytics</a>
                </div>
                <div class="p-5">
                    @php
                        $totalLast7 = collect($ordersLast7Days)->sum('total');
                        $ordersLast7 = collect($ordersLast7Days)->sum('count');
                    @endphp
                    <div class="flex flex-wrap gap-6">
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider">Orders</p>
                            <p class="text-2xl font-bold text-slate-900">{{ $ordersLast7 }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wider">Spent (completed)</p>
                            <p class="text-2xl font-bold text-slate-900">Ksh {{ number_format($totalLast7, 0) }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-3">See the chart above for daily breakdown.</p>
                </div>
            </div>
        </div>

        {{-- Recently viewed --}}
        @if(isset($recentlyViewed) && $recentlyViewed->count() > 0)
            <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="text-base font-semibold text-slate-900">Recently viewed</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                        @foreach($recentlyViewed->take(6) as $product)
                            <a href="{{ route('product.show', $product->slug) }}" class="block group">
                                <div class="aspect-square rounded-xl overflow-hidden bg-slate-100 ring-1 ring-slate-200/80 group-hover:ring-amber-300 transition-all">
                                    @if($product->image_url)
                                        <img src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fas fa-image fa-lg"></i></div>
                                    @endif
                                </div>
                                <p class="text-xs font-medium text-slate-900 mt-2 truncate group-hover:text-amber-600">{{ $product->name }}</p>
                                <p class="text-xs font-semibold text-amber-600">Ksh {{ number_format($product->price, 0) }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Recommended --}}
        @if(isset($recommended) && $recommended->count() > 0)
            <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-slate-900">Recommended for you</h2>
                    <a href="{{ route('account.shop') }}" class="text-sm font-medium text-amber-600 hover:text-amber-700">View all</a>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
                        @foreach($recommended->take(6) as $product)
                            <a href="{{ route('product.show', $product->slug) }}" class="block group">
                                <div class="aspect-square rounded-xl overflow-hidden bg-slate-100 ring-1 ring-slate-200/80 group-hover:ring-amber-300 transition-all">
                                    @if($product->image_url)
                                        <img src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fas fa-image fa-lg"></i></div>
                                    @endif
                                </div>
                                <p class="text-xs font-medium text-slate-900 mt-2 truncate group-hover:text-amber-600">{{ $product->name }}</p>
                                <p class="text-xs font-semibold text-amber-600">Ksh {{ number_format($product->price, 0) }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Quick actions --}}
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-900">Quick actions</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <a href="{{ route('account.shop') }}" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-100 transition-colors">
                        <div class="w-11 h-11 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-600">
                            <i class="fas fa-store text-lg"></i>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">Browse shop</p>
                            <p class="text-xs text-slate-500">Discover products</p>
                        </div>
                    </a>
                    <a href="{{ route('account.cart.index') }}" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-100 transition-colors">
                        <div class="w-11 h-11 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-600">
                            <i class="fas fa-shopping-cart text-lg"></i>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">Cart</p>
                            <p class="text-xs text-slate-500">{{ $stats['cart_items'] }} item(s)</p>
                        </div>
                    </a>
                    <a href="{{ route('account.orders') }}" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-100 transition-colors">
                        <div class="w-11 h-11 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-600">
                            <i class="fas fa-receipt text-lg"></i>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">My orders</p>
                            <p class="text-xs text-slate-500">{{ $stats['total_orders'] }} total</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const last7Labels = @json($ordersLast7Days->pluck('date'));
            const last7Orders = @json($ordersLast7Days->pluck('count'));
            const last7Spent = @json($ordersLast7Days->pluck('total'));

            new Chart(document.getElementById('chartLast7Days'), {
                type: 'bar',
                data: {
                    labels: last7Labels,
                    datasets: [
                        {
                            label: 'Orders',
                            data: last7Orders,
                            backgroundColor: 'rgba(245, 158, 11, 0.6)',
                            borderColor: 'rgb(245, 158, 11)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Spent (Ksh)',
                            data: last7Spent,
                            backgroundColor: 'rgba(30, 58, 138, 0.5)',
                            borderColor: 'rgb(30, 58, 138)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Orders' }, beginAtZero: true },
                        y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Ksh' }, beginAtZero: true, grid: { drawOnChartArea: false } }
                    }
                }
            });

            const statusLabels = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Completed', 'Cancelled'];
            const statusCounts = [
                {{ $stats['pending_orders'] ?? 0 }},
                {{ $stats['processing_orders'] ?? 0 }},
                {{ $stats['shipped_orders'] ?? 0 }},
                {{ $stats['delivered_orders'] ?? 0 }},
                {{ $stats['completed_orders'] ?? 0 }},
                {{ $stats['cancelled_orders'] ?? 0 }}
            ];
            const statusColors = ['#64748b', '#f59e0b', '#3b82f6', '#6366f1', '#10b981', '#ef4444'];

            new Chart(document.getElementById('chartOrderStatus'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusCounts,
                        backgroundColor: statusColors,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    cutout: '55%'
                }
            });

            const monthLabels = @json(isset($ordersByMonth) ? $ordersByMonth->pluck('month') : []);
            const monthOrders = @json(isset($ordersByMonth) ? $ordersByMonth->pluck('orders') : []);
            const monthSpent = @json(isset($ordersByMonth) ? $ordersByMonth->pluck('spent') : []);

            new Chart(document.getElementById('chartMonthly'), {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [
                        {
                            label: 'Orders',
                            data: monthOrders,
                            backgroundColor: 'rgba(245, 158, 11, 0.6)',
                            borderColor: 'rgb(245, 158, 11)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Spent (Ksh)',
                            data: monthSpent,
                            backgroundColor: 'rgba(30, 58, 138, 0.5)',
                            borderColor: 'rgb(30, 58, 138)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Orders' }, beginAtZero: true },
                        y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Ksh' }, beginAtZero: true, grid: { drawOnChartArea: false } }
                    }
                }
            });
        });
    </script>
@endsection
