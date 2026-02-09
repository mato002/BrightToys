@extends('layouts.partner')

@section('page_title', $project->name . ' - Performance')

@section('partner_content')
    <div class="mb-4">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('partner.projects.manage') }}" class="text-slate-500 hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <h1 class="text-lg font-semibold">{{ $project->name }} - Performance</h1>
        </div>
        <p class="text-xs text-slate-500">Monitor how your project is performing.</p>
    </div>

    @if($project->route_name === 'home' || $project->type === 'ecommerce')
        {{-- E-Commerce Project Performance --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
            <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
                <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Total Products</p>
                <p class="text-2xl font-bold text-slate-900">{{ $projectData['stats']['products'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
                <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Total Orders</p>
                <p class="text-2xl font-bold text-slate-900">{{ $projectData['stats']['orders'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
                <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Total Revenue</p>
                <p class="text-2xl font-bold text-emerald-600">Ksh {{ number_format($projectData['stats']['revenue'] ?? 0, 0) }}</p>
            </div>
            <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
                <p class="text-[11px] uppercase tracking-wide text-slate-500 font-semibold mb-1">Today's Revenue</p>
                <p class="text-2xl font-bold text-amber-600">Ksh {{ number_format($projectData['stats']['today_revenue'] ?? 0, 0) }}</p>
            </div>
        </div>

        {{-- Revenue Chart --}}
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm mb-4">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Revenue Trend (Last 6 Months)</h2>
            <div class="h-48 flex items-end justify-between gap-1">
                @foreach($projectData['revenueLast6Months'] ?? [] as $month)
                    <div class="flex-1 flex flex-col items-center">
                        <div class="w-full bg-emerald-100 rounded-t relative group cursor-pointer" 
                             style="height: {{ ($projectData['revenueLast6Months']->max('revenue') ?? 1) > 0 ? ($month['revenue'] / max($projectData['revenueLast6Months']->max('revenue'), 1)) * 100 : 0 }}%"
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
            {{-- Recent Orders --}}
            <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900">Recent Orders</h2>
                    <a href="{{ route('admin.orders.index') }}" target="_blank" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                        View all
                    </a>
                </div>
                @if(isset($projectData['recentOrders']) && $projectData['recentOrders']->count() > 0)
                    <div class="space-y-2">
                        @foreach($projectData['recentOrders'] as $order)
                            <div class="flex items-center justify-between p-2 rounded-lg border border-slate-100">
                                <div>
                                    <p class="text-xs font-semibold text-slate-900">Order #{{ $order->id }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <p class="text-xs font-semibold text-emerald-600">Ksh {{ number_format($order->total, 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-500">No recent orders.</p>
                @endif
            </div>

            {{-- Top Products --}}
            <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-slate-900">Top Products</h2>
                    <a href="{{ route('admin.products.index') }}" target="_blank" class="text-[11px] text-amber-600 hover:text-amber-700 hover:underline">
                        View all
                    </a>
                </div>
                @if(isset($projectData['topProducts']) && $projectData['topProducts']->count() > 0)
                    <div class="space-y-2">
                        @foreach($projectData['topProducts'] as $product)
                            <div class="flex items-center justify-between p-2 rounded-lg border border-slate-100">
                                <div>
                                    <p class="text-xs font-semibold text-slate-900">{{ $product->name }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $product->order_items_count ?? 0 }} orders</p>
                                </div>
                                <p class="text-xs font-semibold text-slate-700">Ksh {{ number_format($product->price, 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-500">No product data available.</p>
                @endif
            </div>
        </div>
    @else
        {{-- Generic Project Performance --}}
        <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Project Overview</h2>
            <p class="text-xs text-slate-600 mb-4">{{ $project->description ?? 'No description available.' }}</p>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-[11px] text-slate-500 mb-1">Project Type</p>
                    <p class="text-sm font-semibold text-slate-900 capitalize">{{ $project->type }}</p>
                </div>
                <div>
                    <p class="text-[11px] text-slate-500 mb-1">Status</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        {{ $project->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-50 text-slate-700 border border-slate-100' }}">
                        {{ $project->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div>
                    <p class="text-[11px] text-slate-500 mb-1">Assigned Users</p>
                    <p class="text-sm font-semibold text-slate-900">{{ $projectData['assigned_users']->count() }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Assigned Users --}}
    <div class="bg-white rounded-lg border border-slate-100 p-4 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-900 mb-3">Assigned Team Members</h2>
        @if($projectData['assigned_users']->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($projectData['assigned_users'] as $user)
                    <div class="flex items-center justify-between p-3 border border-slate-100 rounded-lg">
                        <div>
                            <p class="text-xs font-semibold text-slate-900">{{ $user->name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $user->email }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-medium
                            {{ $user->pivot->role === 'manager' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                               ($user->pivot->role === 'editor' ? 'bg-blue-50 text-blue-700 border border-blue-100' :
                                'bg-slate-50 text-slate-700 border border-slate-100') }}">
                            {{ ucfirst($user->pivot->role) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-500">No users assigned to this project yet.</p>
        @endif
    </div>
@endsection
