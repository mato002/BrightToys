@extends('layouts.partner')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Welcome, {{ $partner->name }}</h1>
        <p class="text-xs text-slate-500">
            Comprehensive financial overview and business insights for your investment in BrightToys.
        </p>
    </div>

    {{-- Key Metrics Row --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Current Ownership</p>
            <p class="text-2xl font-bold text-emerald-600">
                {{ optional($currentOwnership)->percentage ? number_format($currentOwnership->percentage, 2) . '%' : 'N/A' }}
            </p>
            <p class="text-[11px] text-slate-500 mt-1">
                @if($currentOwnership)
                    Effective from {{ \Illuminate\Support\Carbon::parse($currentOwnership->effective_from)->format('M d, Y') }}
                @else
                    No active ownership record.
                @endif
            </p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Total Contributions</p>
            <p class="text-2xl font-bold text-slate-900">Ksh {{ number_format($totalContributions, 0) }}</p>
            @if($pendingContributions > 0)
                <p class="text-[10px] text-amber-600 mt-1">{{ $pendingContributions }} pending approval</p>
            @endif
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Your Share (All Time)</p>
            <p class="text-2xl font-bold text-amber-600">Ksh {{ number_format($partnerShare, 0) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Based on {{ optional($currentOwnership)->percentage ?? 0 }}% ownership</p>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">YTD Your Share</p>
            <p class="text-2xl font-bold text-emerald-600">Ksh {{ number_format($ytdPartnerShare, 0) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Year to date ({{ now()->format('Y') }})</p>
        </div>
    </div>

    {{-- Revenue & Performance Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-3">Revenue Performance</p>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">This Month</span>
                    <span class="font-semibold text-emerald-700">Ksh {{ number_format($thisMonthRevenue, 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Last Month</span>
                    <span class="font-semibold text-slate-700">Ksh {{ number_format($lastMonthRevenue, 0) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                    <span class="text-slate-600">Growth</span>
                    <span class="font-semibold {{ $revenueGrowth >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}%
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">YTD Revenue</span>
                    <span class="font-semibold text-slate-900">Ksh {{ number_format($ytdRevenue, 0) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-3">Financial Summary</p>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Total Revenue</span>
                    <span class="font-semibold text-emerald-700">Ksh {{ number_format($totalRevenue, 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Total Expenses</span>
                    <span class="font-semibold text-red-600">Ksh {{ number_format($totalExpenses, 0) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                    <span class="text-slate-600">Net Profit</span>
                    <span class="font-semibold text-slate-900">Ksh {{ number_format($netProfit, 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Profit Margin</span>
                    <span class="font-semibold text-slate-900">
                        {{ $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-3">Your Transactions</p>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Contributions</span>
                    <span class="font-semibold text-blue-600">Ksh {{ number_format($totalContributions, 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Withdrawals</span>
                    <span class="font-semibold text-slate-700">Ksh {{ number_format($totalWithdrawals, 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Profit Distributed</span>
                    <span class="font-semibold text-emerald-600">Ksh {{ number_format($totalProfitDistribution, 0) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                    <span class="text-slate-600">Net Position</span>
                    <span class="font-semibold text-slate-900">
                        Ksh {{ number_format($totalContributions - $totalWithdrawals - $totalProfitDistribution, 0) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Chart & Top Products --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Revenue Trend (Last 6 Months)</h2>
            </div>
            <div class="h-48 flex items-end justify-between gap-1">
                @foreach($revenueLast6Months as $month)
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-emerald-100 rounded-t relative group cursor-pointer" 
                             style="height: {{ $revenueLast6Months->max('revenue') > 0 ? ($month['revenue'] / $revenueLast6Months->max('revenue')) * 100 : 0 }}%"
                             title="Ksh {{ number_format($month['revenue'], 0) }}">
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-slate-900 text-white text-[10px] px-2 py-1 rounded whitespace-nowrap z-10">
                                {{ $month['month'] }}<br>Ksh {{ number_format($month['revenue'], 0) }}
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1 transform -rotate-45 origin-left">{{ $month['month'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Top Products by Revenue</h2>
                <a href="{{ route('shop.index') }}" target="_blank" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                    View Shop
                </a>
            </div>
            @if($topProducts->count() > 0)
                <div class="space-y-2">
                    @foreach($topProducts as $product)
                        <div class="flex items-center justify-between p-2 rounded-lg border border-slate-100 hover:bg-slate-50">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ $product->name }}</p>
                                <p class="text-[10px] text-slate-500">Revenue: Ksh {{ number_format($product->total_revenue ?? 0, 0) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">No product sales data available.</p>
            @endif
        </div>
    </div>

    {{-- Expense Categories & Recent Orders --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Expense Categories</h2>
                <a href="{{ route('partner.financial-records', ['type' => 'expense']) }}" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                    View all
                </a>
            </div>
            @if($expenseCategories->count() > 0)
                <div class="space-y-2">
                    @foreach($expenseCategories as $category)
                        @php
                            $percentage = $totalExpenses > 0 ? ($category->total / $totalExpenses) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-slate-700">{{ $category->category }}</span>
                                <span class="text-xs font-semibold text-slate-900">Ksh {{ number_format($category->total, 0) }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">No expense categories recorded.</p>
            @endif
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" target="_blank" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                    View all
                </a>
            </div>
            @if($recentOrders->count() > 0)
                <div class="space-y-2">
                    @foreach($recentOrders as $order)
                        <div class="flex items-center justify-between p-2 rounded-lg border border-slate-100 hover:bg-slate-50">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-900">Order #{{ $order->id }}</p>
                                <p class="text-[10px] text-slate-500">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-emerald-600">Ksh {{ number_format($order->total, 0) }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">No recent orders.</p>
            @endif
        </div>
    </div>

    {{-- Projects & Contributions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Project Management</h2>
                <div class="flex gap-2">
                    <a href="{{ route('partner.projects.manage') }}" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                        Manage
                    </a>
                    <a href="{{ route('partner.projects.index') }}" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                        View all
                    </a>
                </div>
            </div>
            @php
                $quickProjects = \App\Models\Project::where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->limit(3)
                    ->get();
            @endphp
            @if($quickProjects->count() > 0)
                <div class="space-y-2">
                    @foreach($quickProjects as $project)
                        <a href="{{ route('partner.projects.redirect', $project) }}" 
                           target="_blank"
                           class="flex items-center gap-3 p-2 rounded-lg border border-slate-100 hover:border-amber-300 hover:bg-amber-50/50 transition-colors group">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                @if($project->icon)
                                    <i class="{{ $project->icon }} text-emerald-600 text-sm"></i>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M4 7l8-4 8 4-8 4-8-4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4 17l8 4 8-4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-900 truncate">{{ $project->name }}</p>
                                <p class="text-[10px] text-slate-500 capitalize">{{ $project->type }}</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400 group-hover:text-amber-600 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15 3h6v6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 14L21 3" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500">No projects available.</p>
            @endif
        </div>

        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Recent Contributions</h2>
                <a href="{{ route('partner.contributions') }}" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                    View all
                </a>
            </div>

            @if($recentContributions->isEmpty())
                <p class="text-xs text-slate-500">No contributions recorded yet.</p>
            @else
                <div class="space-y-2">
                    @foreach($recentContributions->take(5) as $contribution)
                        <div class="flex items-center justify-between p-2 rounded-lg border border-slate-100 hover:bg-slate-50">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-900 capitalize">{{ str_replace('_', ' ', $contribution->type) }}</p>
                                <p class="text-[10px] text-slate-500">{{ $contribution->contributed_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-slate-900">Ksh {{ number_format($contribution->amount, 0) }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px]
                                    {{ $contribution->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                                       ($contribution->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-100' :
                                        'bg-red-50 text-red-700 border border-red-100') }}">
                                    {{ ucfirst($contribution->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Partner Details Card --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Partner Information</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
                <p class="text-slate-500 text-[11px] mb-1">Name</p>
                <p class="font-semibold text-slate-900">{{ $partner->name }}</p>
            </div>
            @if($partner->email)
                <div>
                    <p class="text-slate-500 text-[11px] mb-1">Email</p>
                    <p class="font-semibold text-slate-900">{{ $partner->email }}</p>
                </div>
            @endif
            @if($partner->phone)
                <div>
                    <p class="text-slate-500 text-[11px] mb-1">Phone</p>
                    <p class="font-semibold text-slate-900">{{ $partner->phone }}</p>
                </div>
            @endif
            <div>
                <p class="text-slate-500 text-[11px] mb-1">Status</p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                    {{ $partner->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-50 text-slate-700 border border-slate-100' }}">
                    {{ ucfirst($partner->status) }}
                </span>
            </div>
        </div>
    </div>
@endsection
