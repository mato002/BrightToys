@extends('layouts.app')

@section('title', $project->name . ' Dashboard')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $project->name }}</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ $project->description ?? 'Project management dashboard' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        {{ $userRole === 'manager' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
                           ($userRole === 'editor' ? 'bg-blue-50 text-blue-700 border border-blue-100' :
                            'bg-slate-50 text-slate-700 border border-slate-100') }}">
                        {{ ucfirst($userRole) }}
                    </span>
                    <a href="{{ route('home') }}" class="text-sm text-slate-600 hover:text-slate-900">
                        Back to Site
                    </a>
                </div>
            </div>
        </div>

        @if($project->route_name === 'home' || $project->type === 'ecommerce')
            {{-- E-Commerce Project Dashboard --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm">
                    <p class="text-xs text-slate-500 mb-1">Total Products</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $stats['products'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm">
                    <p class="text-xs text-slate-500 mb-1">Total Orders</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $stats['orders'] ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm">
                    <p class="text-xs text-slate-500 mb-1">Total Revenue</p>
                    <p class="text-2xl font-bold text-emerald-600">Ksh {{ number_format($stats['revenue'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm">
                    <p class="text-xs text-slate-500 mb-1">Pending Orders</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_orders'] ?? 0 }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Recent Orders --}}
                <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Recent Orders</h2>
                    @if(isset($recentOrders) && $recentOrders->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentOrders as $order)
                                <div class="flex items-center justify-between p-3 border border-slate-100 rounded-lg">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">Order #{{ $order->id }}</p>
                                        <p class="text-xs text-slate-500">{{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-emerald-600">Ksh {{ number_format($order->total, 0) }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
                                            {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-700' :
                                               ($order->status === 'pending' ? 'bg-amber-50 text-amber-700' :
                                                'bg-slate-50 text-slate-700') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500">No recent orders.</p>
                    @endif
                </div>

                {{-- Top Products --}}
                <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Top Products</h2>
                    @if(isset($topProducts) && $topProducts->count() > 0)
                        <div class="space-y-3">
                            @foreach($topProducts as $product)
                                <div class="flex items-center justify-between p-3 border border-slate-100 rounded-lg">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $product->order_items_count ?? 0 }} orders</p>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">Ksh {{ number_format($product->price, 0) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500">No product data available.</p>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="mt-6 bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <a href="{{ route('admin.products.index') }}" class="p-4 border border-slate-200 rounded-lg hover:border-emerald-300 hover:bg-emerald-50 transition-colors text-center">
                        <p class="text-sm font-semibold text-slate-900">Manage Products</p>
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="p-4 border border-slate-200 rounded-lg hover:border-emerald-300 hover:bg-emerald-50 transition-colors text-center">
                        <p class="text-sm font-semibold text-slate-900">View Orders</p>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="p-4 border border-slate-200 rounded-lg hover:border-emerald-300 hover:bg-emerald-50 transition-colors text-center">
                        <p class="text-sm font-semibold text-slate-900">Categories</p>
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="p-4 border border-slate-200 rounded-lg hover:border-emerald-300 hover:bg-emerald-50 transition-colors text-center">
                        <p class="text-sm font-semibold text-slate-900">Full Dashboard</p>
                    </a>
                </div>
            </div>
        @else
            {{-- Generic Project Dashboard --}}
            <div class="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Project Overview</h2>
                <p class="text-sm text-slate-600">Project management dashboard for {{ $project->name }}.</p>
                <p class="text-xs text-slate-500 mt-2">Your role: <strong>{{ ucfirst($userRole) }}</strong></p>
            </div>
        @endif
    </div>
</div>
@endsection
