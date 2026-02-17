<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Account - Otto Investments')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Mobile Sidebar Overlay */
        #account-sidebar {
            transition: transform 0.3s ease-in-out;
        }

        @media (max-width: 1023px) {
            #account-sidebar {
                transform: translateX(-100%);
                z-index: 50;
            }
            body.mobile-sidebar-open #account-sidebar {
                transform: translateX(0);
            }
            body.mobile-sidebar-open::before {
                content: '';
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 40;
                backdrop-filter: blur(2px);
            }
            body.mobile-sidebar-open {
                overflow: hidden;
            }
        }

        /* Fixed sidebar layout + collapse behaviour */
        @media (min-width: 1024px) {
            body.account-has-sidebar {
                padding-left: 16rem; /* 64 * 0.25rem */
            }
            body.account-sidebar-collapsed.account-has-sidebar {
                padding-left: 4.5rem; /* collapsed width */
            }
            #account-sidebar {
                transform: translateX(0) !important;
            }
        }

        @media (min-width: 1024px) {
            #account-sidebar {
                scrollbar-width: thin;
                scrollbar-color: rgba(245, 158, 11, 0.7) rgba(217, 119, 6, 0.4);
            }
            #account-sidebar::-webkit-scrollbar {
                width: 8px;
            }
            #account-sidebar::-webkit-scrollbar-track {
                background: rgba(217, 119, 6, 0.4);
            }
            #account-sidebar::-webkit-scrollbar-thumb {
                background: rgba(245, 158, 11, 0.7);
                border-radius: 9999px;
            }
            #account-sidebar::-webkit-scrollbar-thumb:hover {
                background: rgba(245, 158, 11, 0.9);
            }
            #account-sidebar nav {
                scrollbar-width: thin;
                scrollbar-color: rgba(245, 158, 11, 0.7) rgba(217, 119, 6, 0.4);
            }
            #account-sidebar nav::-webkit-scrollbar {
                width: 6px;
            }
            #account-sidebar nav::-webkit-scrollbar-track {
                background: transparent;
            }
            #account-sidebar nav::-webkit-scrollbar-thumb {
                background: rgba(245, 158, 11, 0.5);
                border-radius: 9999px;
            }
            #account-sidebar nav::-webkit-scrollbar-thumb:hover {
                background: rgba(245, 158, 11, 0.7);
            }
        }

        /* Collapse label text but keep icons */
        body.account-sidebar-collapsed #account-sidebar .sidebar-label,
        body.account-sidebar-collapsed #account-sidebar .sidebar-section-label {
            display: none;
        }
        @media (min-width: 1024px) {
            body.account-sidebar-collapsed #account-sidebar {
                width: 4.5rem;
            }
        }

        /* Responsive Tables - Mobile Cards */
        @media (max-width: 768px) {
            .responsive-table {
                display: block;
            }
            .responsive-table thead {
                display: none;
            }
            .responsive-table tbody {
                display: block;
            }
            .responsive-table tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                background: white;
                padding: 1rem;
            }
            .responsive-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 0;
                border: none;
                text-align: right;
            }
            .responsive-table td::before {
                content: attr(data-label);
                font-weight: 600;
                text-align: left;
                color: #64748b;
                margin-right: 1rem;
            }
        }

        /* Mobile Header Adjustments */
        @media (max-width: 768px) {
            header {
                padding: 0.75rem 1rem;
            }
        }

        /* Content Area Constraints */
        @media (min-width: 1024px) {
            body.account-has-sidebar {
                overflow-x: hidden;
            }
            body.account-has-sidebar .flex-1 {
                width: 100%;
                max-width: 100%;
                overflow-x: hidden;
            }
            body.account-sidebar-collapsed.account-has-sidebar .flex-1 {
                width: 100%;
                max-width: 100%;
            }
        }

        /* Ensure content doesn't overflow */
        main {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            box-sizing: border-box;
        }

        /* Content wrapper constraints */
        .account-content-wrapper {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            box-sizing: border-box;
        }

        /* Prevent any child elements from causing overflow */
        .account-content-wrapper * {
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Ensure tables and wide content are scrollable instead of overflowing */
        .account-content-wrapper table,
        .account-content-wrapper .overflow-x-auto {
            max-width: 100%;
        }

        /* Print Styles */
        @media print {
            body {
                padding-left: 0 !important;
                background: white;
            }
            
            #account-sidebar,
            header,
            footer,
            .no-print,
            button.no-print,
            a.no-print {
                display: none !important;
            }
            
            .account-content-wrapper {
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .bg-white {
                background: white !important;
            }
            
            .border,
            .shadow-sm,
            .shadow {
                border: 1px solid #e5e7eb !important;
                box-shadow: none !important;
            }
            
            @page {
                margin: 1cm;
            }
        }

        /* Tooltip Styles */
        .tooltip {
            position: relative;
            cursor: help;
        }
        
        .tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 6px 10px;
            background: #1f2937;
            color: white;
            font-size: 11px;
            white-space: nowrap;
            border-radius: 4px;
            z-index: 1000;
            margin-bottom: 5px;
            pointer-events: none;
            max-width: 250px;
            white-space: normal;
            text-align: center;
        }
        
        .tooltip:hover::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: #1f2937;
            z-index: 1000;
            margin-bottom: -5px;
            pointer-events: none;
        }

        /* Empty State Styles */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }
        
        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        /* Data Refresh Indicator */
        .data-refresh-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 10px;
            color: #6b7280;
        }
        
        .data-refresh-indicator .pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Breadcrumb Styles */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 11px;
            color: #64748b;
            margin-bottom: 1rem;
        }
        
        .breadcrumb a {
            color: #64748b;
            transition: color 0.2s;
        }
        
        .breadcrumb a:hover {
            color: #f59e0b;
        }
        
        .breadcrumb-separator {
            color: #cbd5e1;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 flex min-h-screen antialiased account-has-sidebar overflow-x-hidden">
    {{-- Mobile Sidebar Backdrop --}}
    <div id="mobile-sidebar-backdrop" class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300"></div>

    {{-- Sidebar --}}
    <aside id="account-sidebar"
           class="fixed inset-y-0 left-0 w-64 bg-amber-900 border-r border-amber-900/80 text-amber-50 transition-all duration-300 ease-in-out shadow-xl/40 z-50 lg:z-30 flex">
        <div class="flex flex-col w-full h-full overflow-hidden">
            <div class="px-6 py-5 border-b border-amber-800/80 flex items-center justify-between sticky top-0 bg-amber-900 z-10">
                <div class="flex items-center space-x-3">
                    <div class="h-9 w-9 rounded-2xl bg-amber-500 flex items-center justify-center text-xs font-semibold shadow-lg shadow-amber-500/40">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold tracking-wide sidebar-label">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-[11px] text-amber-100/80 sidebar-label">My Account</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button id="mobile-sidebar-close"
                            type="button"
                            class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg border border-amber-700/60 text-amber-100 hover:bg-amber-800/60 transition-colors"
                            title="Close sidebar"
                            aria-label="Close sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <button id="sidebar-collapse-toggle"
                            type="button"
                            class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg border border-amber-700/60 text-amber-100 hover:bg-amber-800/60 transition-colors"
                            title="Collapse sidebar">
                        <span class="text-[12px]">«</span>
                    </button>
                </div>
            </div>
            <nav class="mt-4 px-3 text-[13px] space-y-1 flex-1 overflow-y-auto min-h-0">
                <p class="px-3 mb-2 text-[11px] font-medium uppercase tracking-[0.18em] text-amber-200/80 sidebar-section-label">
                    Dashboard
                </p>
                <a href="{{ route('account.overview') }}"
                   class="group flex items-center px-3 py-2.5 rounded-xl transition
                          {{ request()->routeIs('account.overview') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                        <i class="fas fa-chart-line text-xs"></i>
                    </span>
                    <span class="font-medium sidebar-label">Overview</span>
                </a>
                <a href="{{ route('account.orders') }}"
                   class="group flex items-center px-3 py-2.5 rounded-xl transition
                          {{ request()->routeIs('account.orders*') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                        <i class="fas fa-shopping-bag text-xs"></i>
                    </span>
                    <span class="font-medium sidebar-label">My Orders</span>
                </a>

                <p class="px-3 mt-4 mb-2 text-[11px] font-medium uppercase tracking-[0.18em] text-amber-200/80 sidebar-section-label">
                    Resources
                </p>
                <a href="{{ route('account.notifications') }}"
                   class="group flex items-center px-3 py-2.5 rounded-xl transition
                          {{ request()->routeIs('account.notifications*') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90 relative">
                        <i class="fas fa-bell text-xs"></i>
                        @php
                            $unreadCount = \App\Models\SystemNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 text-white text-[9px] flex items-center justify-center font-bold">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </span>
                    <span class="font-medium sidebar-label">Notifications</span>
                </a>

                <p class="px-3 mt-4 mb-2 text-[11px] font-medium uppercase tracking-[0.18em] text-amber-200/80 sidebar-section-label">
                    Shopping
                </p>
                <a href="{{ route('shop.index') }}"
                   class="group flex items-center px-3 py-2.5 rounded-xl transition text-amber-100/90 hover:bg-amber-800/60 hover:text-white">
                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                        <i class="fas fa-store text-xs"></i>
                    </span>
                    <span class="font-medium sidebar-label">Shop</span>
                </a>
                <a href="{{ route('account.cart.index') }}"
                   class="group flex items-center px-3 py-2.5 rounded-xl transition
                          {{ request()->routeIs('account.cart*') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                        <i class="fas fa-shopping-cart text-xs"></i>
                    </span>
                    <span class="font-medium sidebar-label">Cart</span>
                    @if(isset($cartCount) && $cartCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center rounded-full bg-amber-500 text-white text-[10px] px-1.5 py-0.5">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            </nav>

            <div class="mt-auto px-3 py-4 border-t border-amber-800/80 text-[12px] space-y-2 bg-amber-950/40 sticky bottom-0">
                <a href="{{ route('home') }}"
                   class="flex items-center justify-between px-2 py-1.5 rounded-lg bg-amber-800/80 hover:bg-amber-700 text-amber-50 text-[11px]">
                    <span class="sidebar-label">Back to Store</span>
                    <span class="text-[10px]">↗</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-between px-2 py-1.5 rounded-lg border border-amber-700 text-amber-100 text-[11px] hover:bg-amber-800/60">
                        <span class="sidebar-label">Logout</span>
                        <span class="text-[10px]">⎋</span>
                    </button>
                </form>

                <a href="https://mathiasodhiambo.netlify.app"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="w-full text-left text-[11px] text-amber-300 hover:text-amber-100 hover:underline mt-1 block">
                    © {{ date('Y') }} Otto Investments
                </a>
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-h-screen w-full max-w-full overflow-x-hidden">
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-slate-200 px-3 md:px-6 py-3">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-3 relative">
                    <button id="sidebar-toggle"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-100 focus:outline-none lg:hidden"
                            aria-label="Toggle sidebar">
                        <span class="sr-only">Toggle sidebar</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="ml-2 md:ml-4">
                        <h1 class="text-sm md:text-base font-semibold text-slate-900 tracking-tight">
                            @yield('page_title', 'My Account')
                        </h1>
                        <p class="text-[11px] text-slate-400 hidden md:block">Customer Account - Manage your orders and profile</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if(isset($dataRefreshedAt) || true)
                        <div class="data-refresh-indicator hidden md:flex">
                            <span class="pulse"></span>
                            <span>Last updated: {{ now()->format('M d, H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
            @hasSection('breadcrumbs')
                <nav class="breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('account.overview') }}">Dashboard</a>
                    @yield('breadcrumbs')
                </nav>
            @endif
        </header>

            <div class="flex items-center space-x-2 md:space-x-3">
                {{-- Notifications Bell --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="relative flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 focus:outline-none shadow-sm transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        @php
                            $unreadCount = \App\Models\SystemNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-red-500 text-white text-[10px] flex items-center justify-center font-bold border-2 border-white">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>
                    {{-- Notification Dropdown --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-80 md:w-96 rounded-lg bg-white border border-slate-200 shadow-xl z-50 max-h-96 overflow-hidden">
                        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-900">Notifications</h3>
                            @if($unreadCount > 0)
                                <form action="{{ route('account.notifications.read-all') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-amber-600 hover:text-amber-700 font-medium">
                                        Mark all as read
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="overflow-y-auto max-h-80">
                            @php
                                $recentNotifications = \App\Models\SystemNotification::where('user_id', auth()->id())
                                    ->latest()
                                    ->take(5)
                                    ->get();
                            @endphp
                            @forelse($recentNotifications as $notification)
                                <a href="{{ route('account.notifications') }}" 
                                   class="block px-4 py-3 border-b border-slate-100 hover:bg-slate-50 transition-colors {{ !$notification->read_at ? 'bg-amber-50/50' : '' }}">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <div class="w-2 h-2 rounded-full {{ !$notification->read_at ? 'bg-amber-500' : 'bg-transparent' }}"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-900 truncate">{{ $notification->title }}</p>
                                            <p class="text-xs text-slate-600 mt-1 line-clamp-2">{{ $notification->message }}</p>
                                            <p class="text-xs text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-8 text-center">
                                    <p class="text-sm text-slate-500">No notifications</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-3 border-t border-slate-200 text-center">
                            <a href="{{ route('account.notifications') }}" class="text-xs text-amber-600 hover:text-amber-700 font-semibold">
                                View all notifications →
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('home') }}"
                   class="flex items-center space-x-2 rounded-full border border-slate-200 bg-white px-2 py-1.5 text-[11px] shadow-sm hover:bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    <span class="hidden sm:inline">Store</span>
                </a>
                
                {{-- Profile Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="flex items-center space-x-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm hover:bg-slate-50 focus:outline-none">
                        <div class="h-7 w-7 rounded-full bg-amber-500 flex items-center justify-center text-xs font-semibold text-white">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="hidden sm:flex flex-col items-start text-[11px] leading-tight">
                            <span class="font-semibold text-slate-900">{{ auth()->user()->name ?? 'Customer' }}</span>
                            <span class="text-slate-500">{{ auth()->user()->email ?? '' }}</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" 
                             class="h-4 w-4 text-slate-500 transition-transform"
                             :class="{ 'rotate-180': open }"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-56 rounded-lg bg-white border border-slate-200 shadow-lg z-50 py-1">
                        <a href="{{ route('account.profile') }}"
                           class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition
                                  {{ request()->routeIs('account.profile*') ? 'bg-slate-50 font-medium' : '' }}">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-user text-xs text-slate-500 w-4"></i>
                                <span>Profile Details</span>
                            </div>
                        </a>
                        <a href="{{ route('account.addresses') }}"
                           class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition
                                  {{ request()->routeIs('account.addresses') ? 'bg-slate-50 font-medium' : '' }}">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-map-marker-alt text-xs text-slate-500 w-4"></i>
                                <span>Addresses</span>
                            </div>
                        </a>
                        <a href="{{ route('wishlist.index') }}"
                           class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-heart text-xs text-slate-500 w-4"></i>
                                <span>Wishlist</span>
                            </div>
                        </a>
                        <div class="border-t border-slate-200 my-1"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-sign-out-alt text-xs w-4"></i>
                                    <span>Logout</span>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 w-full max-w-full overflow-x-hidden">
            <div class="account-content-wrapper w-full max-w-full p-4 md:p-6">
                {{-- Success/Error Messages --}}
                @if(session('success'))
                    <div class="mb-4 sm:mb-6 bg-emerald-50 border-2 border-emerald-200 rounded-xl px-4 py-3 text-sm text-emerald-700 shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 sm:mb-6 bg-red-50 border-2 border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 sm:mb-6 bg-red-50 border-2 border-red-200 rounded-xl px-4 py-3 shadow-sm">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-red-800 mb-2">Please correct the following errors:</h3>
                                <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer class="px-4 md:px-6 py-3 text-[11px] text-slate-500 border-t border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-6xl flex items-center justify-between">
                <a href="https://mathiasodhiambo.netlify.app" target="_blank" rel="noopener noreferrer" class="hover:underline">
                    © {{ date('Y') }} Otto Investments
                </a>
                <span class="hidden sm:inline text-slate-600">Customer account dashboard</span>
            </div>
        </footer>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('account-sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            const mobileSidebarClose = document.getElementById('mobile-sidebar-close');
            const mobileBackdrop = document.getElementById('mobile-sidebar-backdrop');
            const collapseToggle = document.getElementById('sidebar-collapse-toggle');
            const body = document.body;

            const openSidebar = () => {
                body.classList.add('mobile-sidebar-open');
                if (mobileBackdrop) {
                    mobileBackdrop.classList.remove('opacity-0', 'pointer-events-none');
                    mobileBackdrop.classList.add('opacity-100');
                }
            };

            const closeSidebar = () => {
                body.classList.remove('mobile-sidebar-open');
                if (mobileBackdrop) {
                    mobileBackdrop.classList.add('opacity-0', 'pointer-events-none');
                    mobileBackdrop.classList.remove('opacity-100');
                }
            };

            if (toggle) {
                toggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (window.innerWidth < 1024) {
                        if (body.classList.contains('mobile-sidebar-open')) {
                            closeSidebar();
                        } else {
                            openSidebar();
                        }
                    }
                });
            }

            if (mobileSidebarClose) {
                mobileSidebarClose.addEventListener('click', closeSidebar);
            }

            if (mobileBackdrop) {
                mobileBackdrop.addEventListener('click', closeSidebar);
            }

            // Close sidebar when clicking on a link (mobile only)
            if (sidebar) {
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 1024) {
                            closeSidebar();
                        }
                    });
                });
            }

            // Close sidebar on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && body.classList.contains('mobile-sidebar-open')) {
                    closeSidebar();
                }
            });

            // Handle window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth >= 1024) {
                        body.classList.remove('mobile-sidebar-open');
                        if (mobileBackdrop) {
                            mobileBackdrop.classList.add('opacity-0', 'pointer-events-none');
                            mobileBackdrop.classList.remove('opacity-100');
                        }
                    }
                }, 100);
            });

            if (collapseToggle) {
                collapseToggle.addEventListener('click', function () {
                    const isCollapsed = document.body.classList.toggle('account-sidebar-collapsed');
                    collapseToggle.querySelector('span').textContent = isCollapsed ? '»' : '«';
                });
            }

            // Handle delete confirmations with SweetAlert
            document.querySelectorAll('form[data-confirm]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const message = this.getAttribute('data-confirm');
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: message || 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, proceed',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });

            // Show flash messages with SweetAlert
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            @endif

            // Keyboard Shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + P: Print
                if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
                
                // Ctrl/Cmd + F: Focus search (if search input exists)
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    const searchInput = document.getElementById('search-input');
                    if (searchInput && !e.target.matches('input, textarea')) {
                        e.preventDefault();
                        searchInput.focus();
                        searchInput.select();
                    }
                }
                
                // Escape: Close modals/dropdowns
                if (e.key === 'Escape') {
                    // Close sidebar on mobile
                    if (window.innerWidth < 1024 && body.classList.contains('mobile-sidebar-open')) {
                        closeSidebar();
                    }
                }
            });

            // Show keyboard shortcuts hint on first visit
            if (!localStorage.getItem('account-shortcuts-shown')) {
                setTimeout(function() {
                    Swal.fire({
                        title: 'Keyboard Shortcuts',
                        html: `
                            <div class="text-left space-y-2 text-sm">
                                <p><kbd class="px-2 py-1 bg-slate-100 rounded">Ctrl/Cmd + P</kbd> - Print page</p>
                                <p><kbd class="px-2 py-1 bg-slate-100 rounded">Ctrl/Cmd + F</kbd> - Focus search</p>
                                <p><kbd class="px-2 py-1 bg-slate-100 rounded">Esc</kbd> - Close menus</p>
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonText: 'Got it!',
                        confirmButtonColor: '#f59e0b',
                    });
                    localStorage.setItem('account-shortcuts-shown', 'true');
                }, 2000);
            }
        })();
    </script>
</body>
</html>
