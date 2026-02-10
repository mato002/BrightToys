@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
    {{-- Header / intro --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-500 mb-2">Overview</p>
            <h2 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Store performance snapshot</h2>
            <p class="text-xs md:text-sm text-slate-500 mt-1 max-w-xl">
                High level metrics for products, orders, customers and revenue to help you monitor how BrightToys is performing today.
            </p>
        </div>
        <div class="flex items-center gap-2 md:gap-3">
            <div class="flex gap-2">
                <a href="{{ route('admin.dashboard.export') }}"
                   class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                    Export CSV
                </a>
                <a href="{{ route('admin.dashboard.report') }}"
                   class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-md shadow-sm">
                    Generate Report
                </a>
            </div>
            <div class="flex items-center gap-2 md:gap-3 text-[11px]">
                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-500/20 bg-emerald-50 px-2.5 py-1 text-emerald-600">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live data
                </span>
                <span class="hidden sm:inline text-slate-500">Last updated: <span class="text-slate-700">{{ now()->format('d M Y, H:i') }}</span></span>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid md:grid-cols-4 gap-4 mb-8">
        <div class="relative overflow-hidden rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm shadow-emerald-50">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400 via-emerald-500 to-emerald-300 opacity-80"></div>
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] text-slate-500 uppercase tracking-[0.18em]">Products</p>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 text-[13px]">
                    {{-- Grid icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="4" y="4" width="6" height="6" rx="1.5" stroke-width="1.6"/>
                        <rect x="14" y="4" width="6" height="6" rx="1.5" stroke-width="1.6"/>
                        <rect x="4" y="14" width="6" height="6" rx="1.5" stroke-width="1.6"/>
                        <rect x="14" y="14" width="6" height="6" rx="1.5" stroke-width="1.6"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-semibold text-slate-900">{{ $stats['products'] ?? 0 }}</p>
            <p class="mt-1 text-[11px] text-slate-500">
                Total active products in your catalogue.
            </p>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-sky-100 bg-white p-4 shadow-sm shadow-sky-50">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-sky-400 via-sky-500 to-sky-300 opacity-80"></div>
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] text-slate-500 uppercase tracking-[0.18em]">Orders</p>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-sky-50 text-sky-600 text-[13px]">
                    {{-- Cart icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 5h2l1 12h10l1-9H7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="10" cy="19" r="1" stroke-width="1.6"/>
                        <circle cx="17" cy="19" r="1" stroke-width="1.6"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-semibold text-slate-900">{{ $stats['orders'] ?? 0 }}</p>
            <p class="text-[11px] text-slate-500 mt-1 flex flex-wrap gap-x-2 gap-y-0.5">
                <span>Pending:
                    <span class="font-semibold text-amber-500">{{ $stats['pending_orders'] ?? 0 }}</span>
                </span>
                <span class="text-slate-400">·</span>
                <span>Completed:
                    <span class="font-semibold text-emerald-500">{{ $stats['completed_orders'] ?? 0 }}</span>
                </span>
            </p>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-violet-100 bg-white p-4 shadow-sm shadow-violet-50">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-violet-400 via-violet-500 to-violet-300 opacity-80"></div>
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] text-slate-500 uppercase tracking-[0.18em]">Customers</p>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-violet-50 text-violet-600 text-[13px]">
                    {{-- Users icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M9 11a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17 11a3 3 0 1 0-3-3" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 20a6 6 0 0 1 12 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 20a5 5 0 0 1 7 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-semibold text-slate-900">{{ $stats['users'] ?? 0 }}</p>
            <p class="mt-1 text-[11px] text-slate-500">
                Registered users that have interacted with your store.
            </p>
        </div>
        <div class="relative overflow-hidden rounded-2xl border border-emerald-100 bg-gradient-to-b from-white to-emerald-50 p-4 shadow-sm shadow-emerald-50">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400 via-emerald-500 to-emerald-300 opacity-90"></div>
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] text-emerald-700 uppercase tracking-[0.18em]">Revenue</p>
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 text-[13px]">
                    {{-- Currency icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 4v16" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 8a4 4 0 0 1 4-4h2a4 4 0 0 1 0 8h-4a4 4 0 0 0 0 8h2a4 4 0 0 0 4-4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-semibold text-emerald-700">
                Ksh {{ number_format($stats['revenue'] ?? 0, 0) }}
            </p>
            <p class="text-[11px] text-emerald-700 mt-1">
                Today: Ksh {{ number_format($stats['today_revenue'] ?? 0, 0) }}
            </p>
            <p class="mt-1.5 text-[11px] text-emerald-600">
                Keep an eye on this for daily performance and growth.
            </p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-8">
        {{-- Sales chart --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Sales – last 7 days</h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">Daily revenue trend in Ksh.</p>
                </div>
            </div>
            <div class="p-4">
                <canvas id="salesChart" style="max-height: 208px;"></canvas>
            </div>
        </div>

        {{-- Status chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Order status</h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">Distribution of orders by status.</p>
                </div>
            </div>
            <div class="p-4">
                <canvas id="statusChart" style="max-height: 208px;"></canvas>
            </div>
        </div>

        {{-- Group Financial Snapshot --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Group Financial Snapshot</h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">Contributions, welfare, assets and net worth.</p>
                </div>
            </div>
            <div class="p-4 text-[11px] text-slate-700 space-y-2">
                <div class="flex items-center justify-between">
                    <span>Total Investment Contributions</span>
                    <span class="font-semibold text-slate-900">
                        Ksh {{ number_format($groupFinancials['total_contributions_investment'] ?? 0, 0) }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Welfare Balance</span>
                    <span class="font-semibold text-emerald-700">
                        Ksh {{ number_format($groupFinancials['welfare_balance'] ?? 0, 0) }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Investment Wallets Total</span>
                    <span class="font-semibold text-slate-900">
                        Ksh {{ number_format($groupFinancials['investment_wallet_total'] ?? 0, 0) }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Outstanding Loans (Principal + Interest)</span>
                    <span class="font-semibold text-red-600">
                        Ksh {{ number_format(
                            ($groupFinancials['loan_principal_outstanding'] ?? 0) + ($groupFinancials['loan_interest_outstanding'] ?? 0),
                            0
                        ) }}
                    </span>
                </div>
                <div class="pt-2 mt-2 border-t border-slate-100 space-y-1">
                    <div class="flex items-center justify-between">
                        <span>Assets – Land</span>
                        <span class="font-semibold text-slate-900">
                            Ksh {{ number_format($groupFinancials['assets']['land'] ?? 0, 0) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Assets – Toy Shop</span>
                        <span class="font-semibold text-slate-900">
                            Ksh {{ number_format($groupFinancials['assets']['toy_shop'] ?? 0, 0) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Assets – Inventory</span>
                        <span class="font-semibold text-slate-900">
                            Ksh {{ number_format($groupFinancials['assets']['inventory'] ?? 0, 0) }}
                        </span>
                    </div>
                </div>
                <div class="pt-2 mt-2 border-t border-slate-100 flex items-center justify-between">
                    <span>Net Worth (Assets - Liabilities)</span>
                    <span class="font-semibold text-emerald-700">
                        Ksh {{ number_format($groupFinancials['net_worth'] ?? 0, 0) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Recent orders --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Recent Orders</h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">Latest activity from your customers.</p>
                </div>
                <a href="{{ route('admin.orders.index') }}"
                   class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[11px] font-medium text-emerald-700 hover:bg-emerald-100">
                    <span>View all</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M9 5l7 7-7 7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
            <div class="overflow-x-auto text-sm">
                <table class="min-w-full border-separate border-spacing-y-0.5">
                    <thead class="text-xs text-slate-500 uppercase tracking-[0.18em]">
                    <tr class="bg-slate-50">
                        <th class="px-3 py-2 text-left font-medium">Order</th>
                        <th class="px-3 py-2 text-left font-medium">Customer</th>
                        <th class="px-3 py-2 text-right font-medium">Total</th>
                        <th class="px-3 py-2 text-left font-medium">Status</th>
                        <th class="px-3 py-2 text-left font-medium">Date</th>
                        <th class="px-3 py-2 text-right font-medium"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($recentOrders as $order)
                        <tr class="border-t border-slate-100 bg-white hover:bg-slate-50 transition-colors">
                            <td class="px-3 py-2 text-xs text-slate-900 font-medium">#{{ $order->id }}</td>
                            <td class="px-3 py-2 text-xs text-slate-800">
                                {{ $order->user->name ?? 'Guest' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-right font-semibold text-slate-900">
                                Ksh {{ number_format($order->total, 0) }}
                            </td>
                            <td class="px-3 py-2 text-xs">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border
                                    @class([
                                        'bg-amber-50 text-amber-700 border-amber-200' => $order->status === 'pending',
                                        'bg-sky-50 text-sky-700 border-sky-200' => $order->status === 'processing',
                                        'bg-emerald-50 text-emerald-700 border-emerald-200' => $order->status === 'completed',
                                        'bg-slate-50 text-slate-700 border-slate-200' => $order->status === 'shipped',
                                        'bg-red-50 text-red-700 border-red-200' => $order->status === 'cancelled',
                                    ])>
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-[11px] text-slate-500">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                            <td class="px-3 py-2 text-right text-xs">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-2.5 py-1 text-[11px] text-slate-700 hover:border-emerald-400 hover:text-emerald-700">
                                    <span>View</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M5 12h14M13 6l6 6-6 6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-xs text-slate-500">
                                No recent orders.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top products --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Top Products</h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">Best performing items by order volume.</p>
                </div>
                <a href="{{ route('admin.products.index') }}"
                   class="text-[11px] text-emerald-700 hover:text-emerald-800 hover:underline">
                    Manage
                </a>
            </div>
            <div class="p-4 text-sm">
                @forelse($topProducts as $product)
                    <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
                        <div class="pr-3">
                            <p class="text-xs font-semibold text-slate-900">{{ $product->name }}</p>
                            <p class="text-[11px] text-slate-500">
                                Ksh {{ number_format($product->price, 0) }} ·
                                {{ $product->order_items_count }} orders
                            </p>
                        </div>
                        <span class="text-[11px] px-2.5 py-1 rounded-full bg-slate-50 text-slate-700 border border-slate-200">
                            Stock: <span class="font-medium">{{ $product->stock }}</span>
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-500">No products yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const salesCtx = document.getElementById('salesChart');
            const statusCtx = document.getElementById('statusChart');

            // Sales Chart (Line Chart)
            if (salesCtx) {
                const salesData = @json($salesLast7Days->pluck('total')->toArray());
                const salesLabels = @json($salesLast7Days->pluck('date')->toArray());

                // Ensure we have data
                if (!salesData || salesData.length === 0) {
                    salesCtx.parentElement.innerHTML = '<div class="flex items-center justify-center h-52 text-slate-400 text-sm">No sales data available</div>';
                } else {
                    new Chart(salesCtx, {
                    type: 'line',
                    data: {
                        labels: salesLabels,
                        datasets: [{
                            label: 'Revenue (Ksh)',
                            data: salesData,
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.08)',
                            tension: 0.35,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: '#059669',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { 
                                display: false 
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 12 },
                                bodyFont: { size: 11 },
                                callbacks: {
                                    label: function(context) {
                                        return 'Ksh ' + Number(context.parsed.y).toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { 
                                ticks: { 
                                    font: { size: 10 },
                                    color: '#64748b'
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                ticks: { 
                                    font: { size: 10 },
                                    color: '#64748b',
                                    callback: function(value) {
                                        return 'Ksh ' + value.toLocaleString();
                                    }
                                },
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.1)'
                                }
                            }
                        }
                    }
                    });
                }
            }

            // Status Chart (Doughnut Chart)
            if (statusCtx) {
                @php
                    $statusLabels = $statusCounts->keys()->map(function($key) {
                        return ucfirst(str_replace('_', ' ', $key));
                    })->toArray();
                    $statusData = $statusCounts->values()->toArray();
                    $statusKeys = $statusCounts->keys()->toArray();
                    $statusColorMap = [
                        'pending' => '#f97316',
                        'processing' => '#0ea5e9',
                        'completed' => '#22c55e',
                        'shipped' => '#64748b',
                        'cancelled' => '#ef4444'
                    ];
                    $backgroundColors = array_map(function($key) use ($statusColorMap) {
                        return $statusColorMap[$key] ?? '#94a3b8';
                    }, $statusKeys);
                @endphp
                
                const statusData = @json($statusData);
                const statusLabels = @json($statusLabels);
                const backgroundColors = @json($backgroundColors);

                // Ensure we have data
                if (!statusData || statusData.length === 0 || statusData.every(v => v === 0)) {
                    statusCtx.parentElement.innerHTML = '<div class="flex items-center justify-center h-52 text-slate-400 text-sm">No order data available</div>';
                } else {
                    new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusData,
                            backgroundColor: backgroundColors,
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { 
                                    font: { size: 10 },
                                    padding: 12,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 12 },
                                bodyFont: { size: 11 },
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        cutout: '60%',
                    }
                    });
                }
            }
        });
    </script>
@endpush

