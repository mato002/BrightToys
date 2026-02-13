@extends('layouts.partner')

@section('partner_content')
    <div class="mb-4">
        <h1 class="text-lg font-semibold">Welcome, {{ $partner->name }}</h1>
        <p class="text-xs text-slate-500">
            Comprehensive financial overview and business insights for your investment in Otto Investments.
        </p>
    </div>

    {{-- Monthly Contributions (Existing Members) --}}
    @if(isset($monthlyContribution))
    @php
        $mc = $monthlyContribution;
        $statusClass = match($mc['status']) {
            'on_time' => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
            'late' => 'bg-amber-50 text-amber-700 border border-amber-100',
            'critical' => 'bg-red-50 text-red-700 border border-red-100',
            default => 'bg-slate-50 text-slate-700 border border-slate-100',
        };
    @endphp
    <div class="mb-4 bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Monthly Contributions</p>
                <p class="text-[11px] text-slate-500">
                    Expected Ksh {{ number_format($mc['config']['monthly_total'], 0) }} per month 
                    ({{ number_format($mc['config']['monthly_welfare'], 0) }} welfare, {{ number_format($mc['config']['monthly_investment'], 0) }} investment).
                </p>
            </div>
            <div class="text-right text-xs">
                <span class="inline-flex items-center px-2 py-1 rounded-full {{ $statusClass }}">
                    @if($mc['status'] === 'on_time') On time
                    @elseif($mc['status'] === 'late') Late
                    @else Critical arrears
                    @endif
                </span>
                <div class="mt-1 text-[11px] text-slate-500">
                    Arrears: <span class="font-semibold text-amber-700">Ksh {{ number_format($mc['total_arrears'], 0) }}</span> ·
                    Penalties: <span class="font-semibold text-red-700">Ksh {{ number_format($mc['total_penalty'], 0) }}</span> ·
                    Months in arrears: <span class="font-semibold">{{ $mc['months_in_arrears'] }}</span>
                    @if($mc['days_in_arrears'] > 0)
                        · Days: <span class="font-semibold">{{ $mc['days_in_arrears'] }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-[11px]">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Month</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Expected</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Accumulated Arrears</th>
                        @if($mc['current'] && $mc['current']['is_current'])
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Amount Paid</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Balance Expected</th>
                        @else
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Paid</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Arrear</th>
                        @endif
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Penalty</th>
                        <th class="px-3 py-2 text-left font-semibold text-slate-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach(collect($mc['monthly'])->take(4) as $month)
                        @php
                            $rowStatus = 'On time';
                            $badgeClass = 'bg-emerald-100 text-emerald-700';
                            $statusNarration = '';
                            
                            if($month['arrear'] > 0 && $month['is_past']) {
                                $rowStatus = 'In arrears';
                                $badgeClass = 'bg-amber-100 text-amber-700';
                            }
                            
                            // For current month, show detailed status
                            if($month['is_current']) {
                                if($month['accumulated_arrears'] > 0 || $month['arrear'] > 0) {
                                    $rowStatus = 'In arrears';
                                    $badgeClass = 'bg-amber-100 text-amber-700';
                                    $penaltyRatePercent = $mc['config']['penalty_rate'] * 100;
                                    $statusNarration = "You are {$mc['months_in_arrears']} month(s) in arrears ({$mc['days_in_arrears']} days). ";
                                    if($mc['total_penalty'] > 0) {
                                        $statusNarration .= "⚠️ Arrears are accumulating penalties at a high rate ({$penaltyRatePercent}%). Current penalty: Ksh " . number_format($mc['total_penalty'], 0) . ". ";
                                    }
                                    $statusNarration .= "Please clear your arrears immediately to avoid further penalties.";
                                } else {
                                    $statusNarration = "Current month payment is on track.";
                                }
                            }
                        @endphp
                        <tr class="{{ $month['is_current'] ? 'bg-amber-50/50' : '' }}">
                            <td class="px-3 py-2 font-medium text-slate-900">
                                {{ $month['label'] }}
                                @if($month['is_current'])
                                    <span class="ml-1 text-[10px] text-amber-600 font-semibold">(Current)</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">Ksh {{ number_format($month['expected'], 0) }}</td>
                            <td class="px-3 py-2 {{ $month['accumulated_arrears'] > 0 ? 'text-amber-700 font-semibold' : 'text-slate-400' }}">
                                @if($month['accumulated_arrears'] > 0)
                                    Ksh {{ number_format($month['accumulated_arrears'], 0) }}
                                @else
                                    —
                                @endif
                            </td>
                            @if($month['is_current'])
                                <td class="px-3 py-2 {{ $month['paid'] > 0 ? 'text-emerald-700 font-semibold' : 'text-slate-700' }}">
                                    Ksh {{ number_format($month['paid'], 0) }}
                                </td>
                                <td class="px-3 py-2 {{ $month['balance_expected'] > 0 ? 'text-red-700 font-semibold' : 'text-slate-700' }}">
                                    Ksh {{ number_format($month['balance_expected'] ?? 0, 0) }}
                                </td>
                            @else
                            <td class="px-3 py-2">Ksh {{ number_format($month['paid'], 0) }}</td>
                            <td class="px-3 py-2 {{ $month['arrear'] > 0 && $month['is_past'] ? 'text-amber-700 font-semibold' : 'text-slate-700' }}">
                                Ksh {{ number_format($month['arrear'], 0) }}
                            </td>
                            @endif
                            <td class="px-3 py-2 {{ $month['penalty'] > 0 ? 'text-red-700 font-semibold' : 'text-slate-400' }}">
                                @if($month['penalty'] > 0)
                                    Ksh {{ number_format($month['penalty'], 0) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <div class="space-y-1">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium {{ $badgeClass }}">
                                    {{ $rowStatus }}
                                </span>
                                    @if($statusNarration)
                                        <p class="text-[10px] text-slate-600 leading-tight max-w-xs">{{ $statusNarration }}</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Entry Contribution Alert --}}
    @if($entryContribution && $entryContribution->outstanding_balance > 0)
    <div class="mb-4 bg-amber-50 border border-amber-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-amber-900 mb-1">Entry Contribution Payment Due</h3>
                <p class="text-xs text-amber-700">
                    Outstanding balance: <strong>{{ $entryContribution->currency }} {{ number_format($entryContribution->outstanding_balance, 2) }}</strong>
                    @if($overdueInstallments->count() > 0)
                        | <span class="text-red-600 font-semibold">{{ $overdueInstallments->count() }} overdue installment(s)</span>
                    @endif
                    @if($upcomingInstallments->count() > 0)
                        | <span class="text-amber-600">{{ $upcomingInstallments->count() }} upcoming in next 30 days</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('partner.profile') }}" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2 rounded">
                View Details
            </a>
        </div>
    </div>
    @endif

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
            @php
                $maxRevenue = $revenueLast6Months->max('revenue');
            @endphp
            @if($maxRevenue <= 0)
                <div class="h-48 flex items-center justify-center text-xs text-slate-400">
                    No revenue data available yet.
                </div>
            @else
                <div class="h-48 flex items-end justify-between gap-1">
                    @foreach($revenueLast6Months as $month)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-emerald-100 rounded-t relative group cursor-pointer" 
                                 style="height: {{ ($month['revenue'] / $maxRevenue) * 100 }}%"
                                 title="Ksh {{ number_format($month['revenue'], 0) }}">
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-slate-900 text-white text-[10px] px-2 py-1 rounded whitespace-nowrap z-10">
                                    {{ $month['month'] }}<br>Ksh {{ number_format($month['revenue'], 0) }}
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1 transform -rotate-45 origin-left">{{ $month['month'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
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
                <h2 class="text-sm font-semibold text-slate-900">Active Projects</h2>
                <a href="{{ route('partner.projects.index') }}" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                    View all
                </a>
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

    {{-- Entry Contribution Section --}}
    @if($entryContribution)
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-900">Entry Contribution</h2>
            <a href="{{ route('partner.profile') }}" class="text-emerald-600 hover:text-emerald-700 text-xs underline">
                View Full Details
            </a>
        </div>

        <div class="grid md:grid-cols-4 gap-4 mb-4">
            <div class="bg-slate-50 rounded-lg p-3">
                <div class="text-xs text-slate-500 mb-1">Total Amount</div>
                <div class="text-lg font-semibold text-slate-900">{{ $entryContribution->currency }} {{ number_format($entryContribution->total_amount, 2) }}</div>
            </div>
            <div class="bg-emerald-50 rounded-lg p-3">
                <div class="text-xs text-emerald-600 mb-1">Paid Amount</div>
                <div class="text-lg font-semibold text-emerald-700">{{ $entryContribution->currency }} {{ number_format($entryContribution->paid_amount, 2) }}</div>
            </div>
            <div class="bg-amber-50 rounded-lg p-3">
                <div class="text-xs text-amber-600 mb-1">Outstanding</div>
                <div class="text-lg font-semibold text-amber-700">{{ $entryContribution->currency }} {{ number_format($entryContribution->outstanding_balance, 2) }}</div>
            </div>
            <div class="bg-blue-50 rounded-lg p-3">
                <div class="text-xs text-blue-600 mb-1">Progress</div>
                <div class="text-lg font-semibold text-blue-700">
                    {{ $entryContribution->total_amount > 0 ? number_format(($entryContribution->paid_amount / $entryContribution->total_amount) * 100, 1) : 0 }}%
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="w-full bg-slate-200 rounded-full h-2">
                <div class="bg-emerald-600 h-2 rounded-full transition-all" 
                     style="width: {{ $entryContribution->total_amount > 0 ? ($entryContribution->paid_amount / $entryContribution->total_amount) * 100 : 0 }}%"></div>
            </div>
        </div>

        @if($paymentPlan && $installments->count() > 0)
        <div class="mt-4">
            <h3 class="text-xs font-semibold text-slate-700 mb-2">Upcoming Installments</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">#</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Due Date</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Amount</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Penalty</th>
                            <th class="px-3 py-2 text-left font-semibold text-slate-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($installments->take(5) as $installment)
                        <tr class="{{ $installment->status === 'overdue' || $installment->status === 'missed' ? 'bg-red-50' : ($installment->status === 'paid' ? 'bg-emerald-50' : '') }}">
                            <td class="px-3 py-2 font-medium">{{ $installment->installment_number }}</td>
                            <td class="px-3 py-2">{{ $installment->due_date->format('d M Y') }}</td>
                            <td class="px-3 py-2 font-medium">{{ $entryContribution->currency }} {{ number_format($installment->amount, 2) }}</td>
                            <td class="px-3 py-2">
                                @if($installment->penalty_amount > 0)
                                    <span class="text-red-600 font-semibold text-xs">{{ $entryContribution->currency }} {{ number_format($installment->penalty_amount, 2) }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                                    @if($installment->status === 'paid') bg-emerald-100 text-emerald-700
                                    @elseif($installment->status === 'overdue') bg-red-100 text-red-700
                                    @elseif($installment->status === 'missed') bg-red-200 text-red-800
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ ucfirst($installment->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($installments->count() > 5)
                <p class="text-xs text-slate-500 mt-2 text-center">
                    <a href="{{ route('partner.profile') }}" class="text-emerald-600 hover:text-emerald-700 underline">
                        View all {{ $installments->count() }} installments
                    </a>
                </p>
            @endif
        </div>
        @endif
    </div>
    @endif
@endsection
