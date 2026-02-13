@extends('layouts.admin')

@section('page_title', 'My Profile')

@section('content')
    {{-- Success message --}}
    @if(session('success'))
        <div class="mb-6 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 px-4 py-3 rounded-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Hero Profile Section --}}
    <div class="mb-8 bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8 md:p-12">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-6">
                    {{-- Large Avatar --}}
                    <div class="relative">
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-white/20 backdrop-blur-sm border-4 border-white/30 text-white flex items-center justify-center text-4xl md:text-5xl font-bold shadow-2xl">
                            {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-emerald-500 border-4 border-white flex items-center justify-center">
                            <span class="h-2.5 w-2.5 rounded-full bg-white animate-pulse"></span>
                        </div>
                    </div>
                    
                    {{-- User Info --}}
                    <div class="text-white">
                        <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $user->name }}</h1>
                        <p class="text-emerald-50 text-lg mb-3">{{ $user->email }}</p>
                        @php
                            $user->loadMissing('adminRoles');
                            $roleLabel = 'Customer';
                            $roleBadgeColor = 'bg-slate-500';
                            if ($user->is_admin) {
                                if ($user->isSuperAdmin()) {
                                    $roleLabel = 'Super Administrator';
                                    $roleBadgeColor = 'bg-purple-500';
                                } elseif ($user->hasAdminRole('chairman')) {
                                    $roleLabel = 'Chairman';
                                    $roleBadgeColor = 'bg-amber-500';
                                } elseif ($user->adminRoles->isNotEmpty()) {
                                    $roleLabel = $user->adminRoles->pluck('display_name')->implode(' · ');
                                    $roleBadgeColor = 'bg-blue-500';
                                } else {
                                    $roleLabel = 'Administrator';
                                    $roleBadgeColor = 'bg-emerald-500';
                                }
                            }
                            if ($user->is_partner) {
                                $roleLabel .= ' · Partner';
                            }
                        @endphp
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full {{ $roleBadgeColor }} text-white text-sm font-semibold shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                </svg>
                                {{ $roleLabel }}
                            </span>
                            @if($user->is_partner && $partner)
                                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-sm text-white text-sm font-medium border border-white/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                    @if($currentOwnership)
                                        {{ number_format($currentOwnership->percentage, 1) }}% Ownership
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="flex flex-col gap-3">
                    <a href="{{ route('admin.profile.edit') }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-semibold rounded-xl border border-white/30 transition-all shadow-lg hover:shadow-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit Profile
                    </a>
                    <a href="{{ route('admin.profile.sessions') }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-semibold rounded-xl border border-white/30 transition-all shadow-lg hover:shadow-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Active Sessions
                    </a>
                    <a href="{{ route('admin.settings') }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-semibold rounded-xl border border-white/30 transition-all shadow-lg hover:shadow-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
                        </svg>
                        Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['account_age_days'] }}</p>
            <p class="text-sm text-slate-500">Days Active</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                        <path d="M9 14l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['total_activity'] }}</p>
            <p class="text-sm text-slate-500">Activity Logs</p>
        </div>

        @if($user->is_partner && $partner)
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['total_contributions'] }}</p>
            <p class="text-sm text-slate-500">Contributions</p>
        </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-1">{{ $stats['total_orders'] }}</p>
            <p class="text-sm text-slate-500">Total Orders</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Left Column: Account Details & Partner Info --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Account Information --}}
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Account Information
                </h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Full Name</p>
                        <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Email Address</p>
                        <p class="text-sm font-semibold text-slate-900 break-all">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Member Since</p>
                        <p class="text-sm font-semibold text-slate-900">{{ $user->created_at->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Account Status</p>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active
                        </span>
                    </div>
                </div>
            </div>

            {{-- Partner Information (if applicable) --}}
            @if($user->is_partner && $partner)
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                    Partner Details
                </h2>
                <div class="space-y-4">
                    @if($currentOwnership)
                    <div>
                        <p class="text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">Ownership Stake</p>
                        <p class="text-2xl font-bold text-amber-700">{{ number_format($currentOwnership->percentage, 2) }}%</p>
                        <p class="text-xs text-slate-500 mt-1">Effective since {{ $currentOwnership->effective_from->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($partner->phone)
                    <div>
                        <p class="text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">Phone</p>
                        <p class="text-sm font-semibold text-slate-900">{{ $partner->phone }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">Partner Status</p>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $partner->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-700 border-slate-200' }} text-xs font-semibold border">
                            {{ ucfirst($partner->status) }}
                        </span>
                    </div>
                    @if($partner->notes)
                    <div>
                        <p class="text-xs font-medium text-slate-600 uppercase tracking-wide mb-1">Notes</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $partner->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Admin Roles & Permissions --}}
            @if($user->is_admin && $user->adminRoles->isNotEmpty())
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    Admin Roles
                </h2>
                <div class="space-y-3">
                    @foreach($user->adminRoles as $role)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $role->display_name }}</p>
                            @if($role->permissions->isNotEmpty())
                            <p class="text-xs text-slate-500 mt-1">{{ $role->permissions->count() }} permissions</p>
                            @endif
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4"/>
                            <path d="M21 12c-1 0-3-1-3-3s2-3 3-3 3 1 3 3-2 3-3 3"/>
                            <path d="M3 12c1 0 3-1 3-3s-2-3-3-3-3 1-3 3 2 3 3 3"/>
                        </svg>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Activity & Recent Actions --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Recent Activity --}}
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Recent Activity
                    </h2>
                    @if($recentActivity->count() > 0)
                    <a href="{{ route('admin.activity-logs.index', ['user_id' => $user->id]) }}" 
                       class="text-xs font-medium text-emerald-600 hover:text-emerald-700">
                        View All →
                    </a>
                    @endif
                </div>
                @if($recentActivity->count() > 0)
                <div class="space-y-3">
                    @foreach($recentActivity as $activity)
                    <div class="flex items-start gap-4 p-4 bg-slate-50 rounded-lg border border-slate-200 hover:border-emerald-300 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-900 mb-1">
                                {{ ucwords(str_replace(['_', 'partner_', 'financial_', 'admin_', 'document_'], [' ', '', '', '', ''], $activity->action)) }}
                            </p>
                            @if($activity->details && is_array($activity->details))
                            <p class="text-xs text-slate-500 mb-2">
                                @if(isset($activity->details['name']))
                                    {{ $activity->details['name'] }}
                                @elseif(isset($activity->details['amount']))
                                    Amount: {{ number_format($activity->details['amount'], 2) }}
                                @endif
                            </p>
                            @endif
                            <div class="flex items-center gap-3 text-xs text-slate-400">
                                <span>{{ $activity->created_at->diffForHumans() }}</span>
                                @if($activity->ip_address)
                                <span>•</span>
                                <span>{{ $activity->ip_address }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <p class="text-sm text-slate-500">No activity logged yet</p>
                </div>
                @endif
            </div>

            {{-- Recent Orders (if any) --}}
            @if($user->orders->count() > 0)
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                        Recent Orders
                    </h2>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">
                        View All →
                    </a>
                </div>
                <div class="space-y-3">
                    @foreach($user->orders->take(5) as $order)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Order #{{ $order->id }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-900">${{ number_format($order->total, 2) }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $order->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
