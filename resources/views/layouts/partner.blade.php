@php
    $user = auth()->user();
    $partner = $user?->partner;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Partner Console')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin-enhancements.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Mobile Sidebar Overlay */
        #partner-sidebar {
            transition: transform 0.3s ease-in-out;
        }

        @media (max-width: 1023px) {
            #partner-sidebar {
                transform: translateX(-100%);
                z-index: 50;
            }
            body.mobile-sidebar-open #partner-sidebar {
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
            body.partner-has-sidebar {
                padding-left: 16rem; /* 64 * 0.25rem */
            }
            body.partner-sidebar-collapsed.partner-has-sidebar {
                padding-left: 4.5rem; /* collapsed width */
            }
            #partner-sidebar {
                transform: translateX(0) !important;
            }
        }

        @media (min-width: 1024px) {
            #partner-sidebar {
                scrollbar-width: thin;
                scrollbar-color: rgba(217, 119, 6, 0.7) rgba(120, 53, 15, 0.4);
            }
            #partner-sidebar::-webkit-scrollbar {
                width: 8px;
            }
            #partner-sidebar::-webkit-scrollbar-track {
                background: rgba(120, 53, 15, 0.4);
            }
            #partner-sidebar::-webkit-scrollbar-thumb {
                background: rgba(217, 119, 6, 0.7);
                border-radius: 9999px;
            }
            #partner-sidebar::-webkit-scrollbar-thumb:hover {
                background: rgba(217, 119, 6, 0.9);
            }
            #partner-sidebar nav {
                scrollbar-width: thin;
                scrollbar-color: rgba(217, 119, 6, 0.7) rgba(120, 53, 15, 0.4);
                overflow-y: auto;
                overflow-x: hidden;
            }
            #partner-sidebar nav::-webkit-scrollbar {
                width: 6px;
            }
            #partner-sidebar nav::-webkit-scrollbar-track {
                background: transparent;
            }
            #partner-sidebar nav::-webkit-scrollbar-thumb {
                background: rgba(217, 119, 6, 0.5);
                border-radius: 9999px;
            }
            #partner-sidebar nav::-webkit-scrollbar-thumb:hover {
                background: rgba(217, 119, 6, 0.7);
            }
        }

        .partner-table-scroll {
            max-height: 70vh;
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
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
            .responsive-table td:last-child {
                border-bottom: none;
            }
        }

        /* Mobile Header Adjustments */
        @media (max-width: 768px) {
            header {
                padding: 0.75rem 1rem;
                position: relative;
                z-index: 30;
            }

            /* Ensure profile menu is positioned correctly on mobile */
            .flex.items-center.space-x-3.relative {
                position: relative;
                z-index: 50;
            }
        }

        /* Keep footer visible at the bottom on all screen sizes */
        .partner-main {
            padding-bottom: 3.5rem; /* space so content isn't hidden behind footer */
        }

        .partner-footer {
            position: sticky;
            bottom: 0;
            z-index: 20;
        }

        /* Navigation group styles */
        .nav-group-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.2s ease-out;
            opacity: 0;
        }

        .nav-group-content:not(.hidden) {
            display: block;
        }

        .nav-group-content.expanded {
            max-height: 1000px;
            opacity: 1;
        }

        .nav-chevron {
            transition: transform 0.3s ease;
        }

        .nav-group-toggle.active .nav-chevron {
            transform: rotate(180deg);
        }

        /* Profile dropdown mobile improvements */
        @media (max-width: 768px) {
            #profile-menu {
                right: 0.5rem;
                width: calc(100vw - 1rem);
                max-width: 16rem;
            }
        }

        /* Touch-friendly buttons */
        .nav-group-toggle,
        #profile-toggle {
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
            cursor: pointer;
        }

        /* Profile chevron rotation */
        #profile-toggle.active #profile-chevron {
            transform: rotate(180deg);
        }

        /* Better mobile touch targets */
        @media (max-width: 768px) {
            .nav-group-toggle {
                min-height: 44px; /* iOS recommended touch target */
                padding: 0.75rem;
            }
            
            #profile-toggle {
                min-height: 44px;
                min-width: 44px;
            }

            .nav-group-content a {
                min-height: 44px;
                padding: 0.75rem;
            }

            #profile-menu a,
            #profile-menu button {
                min-height: 44px;
                padding: 0.75rem 1rem;
            }
        }

        /* Sidebar collapse styles */
        @media (min-width: 1024px) {
            #partner-sidebar.collapsed {
                width: 4.5rem;
            }
            #partner-sidebar.collapsed .sidebar-label,
            #partner-sidebar.collapsed .sidebar-section-label {
                display: none;
            }
            #partner-sidebar.collapsed .nav-group-content {
                display: none !important;
            }
        }

    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased partner-has-sidebar">
    {{-- Mobile Sidebar Backdrop --}}
    <div id="mobile-sidebar-backdrop" class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300"></div>
    
    {{-- Sidebar --}}
    <aside id="partner-sidebar" class="fixed inset-y-0 left-0 w-64 bg-amber-900 border-r border-amber-900/80 text-amber-50 transition-all duration-300 ease-in-out shadow-xl/40 z-50 lg:z-20 flex">
        <div class="flex flex-col w-full h-full overflow-hidden">
            <div class="px-6 py-5 border-b border-amber-800/80 flex items-center justify-between sticky top-0 bg-amber-900 z-10">
                <div class="flex items-center space-x-3">
                    <div class="h-9 w-9 rounded-2xl bg-amber-500 flex items-center justify-center text-xs font-semibold shadow-lg shadow-amber-500/40">
                        PC
                    </div>
                    <div>
                        <p class="text-sm font-semibold tracking-wide sidebar-label">Partner Console</p>
                        <p class="text-[11px] text-amber-100/80 sidebar-label">{{ $partner->name ?? $user?->name }}</p>
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
                    <button id="sidebar-collapse-toggle" type="button" class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg border border-amber-700/60 text-amber-100 hover:bg-amber-800/60 transition-colors" title="Collapse sidebar">
                        <span class="text-[12px]">«</span>
                    </button>
                </div>
            </div>

            <nav class="mt-4 px-3 text-[13px] space-y-1 flex-1 overflow-y-auto min-h-0" style="max-height: calc(100vh - 200px);">
                {{-- Overview Section --}}
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-amber-200/80 hover:bg-amber-800/60 hover:text-amber-50 transition sidebar-section-label" data-group="overview">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Overview</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="overview">
                        <a href="{{ route('partner.dashboard') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.dashboard') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 13h6V4H4v9zm0 7h6v-5H4v5zm10 0h6V11h-6v9zm0-16v4h6V4h-6z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Dashboard</span>
                        </a>
                    </div>
                </div>

                {{-- Projects Section --}}
                <div class="nav-group mb-2">
                    <a href="{{ route('partner.projects.index') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.projects.*') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-200/80 hover:bg-amber-800/60 hover:text-amber-50' }}">
                        <div class="flex items-center">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 7l8-4 8 4-8 4-8-4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 17l8 4 8-4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M20 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Projects</span>
                        </div>
                        @if(request()->routeIs('partner.projects.*'))
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @endif
                    </a>
                </div>

                {{-- My Money Section --}}
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-amber-200/80 hover:bg-amber-800/60 hover:text-amber-50 transition sidebar-section-label" data-group="my-money">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">My Money</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="my-money">
                        <a href="{{ route('partner.contributions') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.contributions*') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">My Contributions</span>
                        </a>
                        <a href="{{ route('partner.earnings') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.earnings') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">My Earnings</span>
                        </a>
                    </div>
                </div>

                {{-- Company Financials Section --}}
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-amber-200/80 hover:bg-amber-800/60 hover:text-amber-50 transition sidebar-section-label" data-group="company-financials">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Company Financials</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="company-financials">
                        <a href="{{ route('partner.financial-records', ['type' => 'expense']) }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.financial-records*') && request('type') === 'expense' ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Company Expenses</span>
                        </a>
                        <a href="{{ route('partner.financial-records', ['type' => 'revenue']) }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.financial-records*') && request('type') === 'revenue' ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Sales Overview</span>
                        </a>
                    </div>
                </div>

                {{-- Accounting Section --}}
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-amber-200/80 hover:bg-amber-800/60 hover:text-amber-50 transition sidebar-section-label" data-group="accounting">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Accounting</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="accounting">
                        <a href="{{ route('partner.accounting.dashboard') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.accounting.dashboard') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M9 12l2 2 4-4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" stroke-width="1.6"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Dashboard</span>
                        </a>
                        <a href="{{ route('partner.accounting.financial-overview') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.accounting.financial-overview') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Financial Overview</span>
                        </a>
                        <a href="{{ route('partner.accounting.chart-of-accounts') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.accounting.chart-of-accounts') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 7h16M4 12h16M4 17h16" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Chart of Accounts</span>
                        </a>
                        <a href="{{ route('partner.accounting.posted-entries') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.accounting.posted-entries') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Journal Entries</span>
                        </a>
                        <a href="{{ route('partner.accounting.ledger') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.accounting.ledger') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">General Ledger</span>
                        </a>
                        <a href="{{ route('partner.accounting.expenses') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.accounting.expenses') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M17 9V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2m2 4h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2zm7-5a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Expenses</span>
                        </a>
                        <a href="{{ route('partner.accounting.reports') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.accounting.reports') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M14 2v6h6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16 13H8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16 17H8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 9H8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Financial Reports</span>
                        </a>
                        <a href="{{ route('partner.accounting.budget') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.accounting.budget') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 3v18h18" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18 7c0 1.66-1.34 3-3 3s-3-1.34-3-3 1.34-3 3-3 3 1.34 3 3z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18 14c0 1.66-1.34 3-3 3s-3-1.34-3-3 1.34-3 3-3 3 1.34 3 3z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Budget</span>
                        </a>
                        <a href="{{ route('partner.accounting.assets') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.accounting.assets') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 22V12h6v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Assets</span>
                        </a>
                        <a href="{{ route('partner.accounting.reconciliation') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.accounting.reconciliation') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M9 11l3 3L22 4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Reconciliation</span>
                        </a>
                        <a href="{{ route('partner.accounting.payroll') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.accounting.payroll') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="9" cy="7" r="4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Payroll</span>
                        </a>
                    </div>
                </div>

                {{-- Resources Section --}}
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-amber-200/80 hover:bg-amber-800/60 hover:text-amber-50 transition sidebar-section-label" data-group="resources">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Resources</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="resources">
                        <a href="{{ route('partner.documents') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.documents*') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Documents</span>
                        </a>
                        <a href="{{ route('partner.voting.index') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.voting.*') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 12h8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 8v8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Voting</span>
                        </a>
                        <a href="{{ route('partner.activity') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.activity*') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 6v6l4 2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Activity</span>
                        </a>
                        <a href="{{ route('partner.notifications') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.notifications') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Notifications</span>
                        </a>
                    </div>
                </div>

                {{-- Account Section --}}
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-amber-200/80 hover:bg-amber-800/60 hover:text-amber-50 transition sidebar-section-label" data-group="account">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Account</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="account">
                        <a href="{{ route('partner.profile') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.profile*') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 20a8 8 0 0 1 16 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Profile</span>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="mt-auto px-3 py-4 border-t border-amber-800/80 text-[12px] space-y-2 bg-amber-950/40 sticky bottom-0">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-2 py-1.5 rounded-lg bg-amber-800/80 hover:bg-amber-700 text-amber-50 text-[11px]">
                    <span class="sidebar-label">View website</span>
                    <span class="text-[10px]">↗</span>
                </a>
                <a href="https://mathiasodhiambo.netlify.app" target="_blank" rel="noopener noreferrer" class="w-full text-left text-[11px] text-amber-300 hover:text-amber-100 hover:underline mt-1 block">
                    © {{ date('Y') }} Otto Investments Partner
                </a>
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-h-screen">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200 px-3 md:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3 relative">
                <button id="sidebar-toggle" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-100 focus:outline-none lg:hidden">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-sm md:text-base font-semibold text-slate-900 tracking-tight">
                        @yield('page_title', 'Partner Dashboard')
                    </h1>
                    <p class="text-[11px] text-slate-400 hidden md:block">Otto Investments partner overview</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 relative">
                <div class="hidden md:flex flex-col items-end text-[11px] leading-tight">
                    <span class="font-semibold text-slate-900">{{ auth()->user()->name ?? 'Partner' }}</span>
                    <span class="text-slate-500">{{ auth()->user()->email ?? '' }}</span>
                </div>
                <button id="profile-toggle" type="button" class="flex items-center space-x-2 rounded-full border border-slate-200 bg-white px-2 py-1.5 text-[11px] shadow-sm hover:bg-slate-50 relative z-50 touch-manipulation">
                    <div class="w-7 h-7 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-semibold shadow-lg shadow-amber-500/40">
                        {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500 transition-transform duration-200" id="profile-chevron" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div id="profile-menu" class="hidden absolute right-0 top-full mt-2 w-48 sm:w-56 rounded-lg border border-slate-200 bg-white shadow-xl shadow-slate-200/80 text-[12px] py-1 z-[60]">
                    <div class="px-3 py-2 border-b border-slate-100">
                        <p class="font-semibold text-slate-900 truncate">{{ auth()->user()->name ?? 'Partner' }}</p>
                        <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <a href="{{ route('partner.profile') }}" class="flex items-center px-3 py-2 text-slate-700 hover:bg-slate-50">
                        <span class="mr-2 text-[13px]">👤</span>
                        <span>My Profile</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center px-3 py-2 text-red-500 hover:bg-red-50 hover:text-red-600">
                            <span class="mr-2 text-[13px]">⎋</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 p-3 md:p-6 partner-main">
            @yield('partner_content')
        </main>

        <footer class="px-4 md:px-6 py-3 text-[11px] text-slate-500 border-t border-slate-200 bg-white/80 backdrop-blur partner-footer">
            <div class="mx-auto max-w-6xl flex items-center justify-between">
                <a href="https://mathiasodhiambo.netlify.app" target="_blank" rel="noopener noreferrer" class="hover:underline">
                    © {{ date('Y') }} Otto Investments Partner
                </a>
                <span class="hidden sm:inline text-slate-600">Investment overview dashboard</span>
            </div>
        </footer>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('partner-sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            const mobileSidebarClose = document.getElementById('mobile-sidebar-close');
            const mobileBackdrop = document.getElementById('mobile-sidebar-backdrop');
            const profileToggle = document.getElementById('profile-toggle');
            const profileMenu = document.getElementById('profile-menu');
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

            // Handle window resize - ensure sidebar state is correct
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
                collapseToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const body = document.body;
                    const isCurrentlyCollapsed = body.classList.contains('partner-sidebar-collapsed');
                    
                    // Toggle the collapsed class
                    if (isCurrentlyCollapsed) {
                        body.classList.remove('partner-sidebar-collapsed');
                        sidebar.classList.remove('collapsed');
                    } else {
                        body.classList.add('partner-sidebar-collapsed');
                        sidebar.classList.add('collapsed');
                    }
                    
                    // Update button text
                    const span = collapseToggle.querySelector('span');
                    if (span) {
                        span.textContent = body.classList.contains('partner-sidebar-collapsed') ? '»' : '«';
                    }
                    
                    // Update button title
                    collapseToggle.setAttribute('title', body.classList.contains('partner-sidebar-collapsed') ? 'Expand sidebar' : 'Collapse sidebar');
                });
            }

            if (profileToggle && profileMenu) {
                profileToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    const isHidden = profileMenu.classList.contains('hidden');
                    
                    // Close all other dropdowns first
                    document.querySelectorAll('.nav-group-content.expanded').forEach(function(content) {
                        content.classList.remove('expanded');
                        setTimeout(function() {
                            content.classList.add('hidden');
                        }, 300);
                    });
                    document.querySelectorAll('.nav-group-toggle.active').forEach(function(toggle) {
                        toggle.classList.remove('active');
                    });
                    
                    if (isHidden) {
                        profileMenu.classList.remove('hidden');
                        profileMenu.style.display = 'block';
                        profileToggle.classList.add('active');
                    } else {
                        profileMenu.classList.add('hidden');
                        profileToggle.classList.remove('active');
                    }
                });

                // Close profile menu when clicking outside or on links
                profileMenu.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A' || e.target.closest('a') || e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                        setTimeout(function() {
                            profileMenu.classList.add('hidden');
                            profileToggle.classList.remove('active');
                        }, 100);
                    }
                });

                document.addEventListener('click', function (e) {
                    if (!profileMenu.classList.contains('hidden') &&
                        !profileMenu.contains(e.target) &&
                        !profileToggle.contains(e.target)) {
                        profileMenu.classList.add('hidden');
                        profileToggle.classList.remove('active');
                    }
                });

                // Close profile menu on escape key
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && !profileMenu.classList.contains('hidden')) {
                        profileMenu.classList.add('hidden');
                        profileToggle.classList.remove('active');
                    }
                });
            }

            // Navigation group toggle functionality
            document.querySelectorAll('.nav-group-toggle').forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Close profile menu if open
                    if (profileMenu && !profileMenu.classList.contains('hidden')) {
                        profileMenu.classList.add('hidden');
                        if (profileToggle) profileToggle.classList.remove('active');
                    }
                    
                    const groupName = this.getAttribute('data-group');
                    const content = document.querySelector(`.nav-group-content[data-content="${groupName}"]`);
                    const isExpanded = content && content.classList.contains('expanded');
                    
                    if (content) {
                        if (isExpanded) {
                            // Collapse this group
                            content.classList.remove('expanded');
                            setTimeout(function() {
                                content.classList.add('hidden');
                            }, 300);
                            this.classList.remove('active');
                        } else {
                            // Expand this group
                            content.classList.remove('hidden');
                            // Use setTimeout to ensure the transition works
                            setTimeout(function() {
                                content.classList.add('expanded');
                            }, 10);
                            this.classList.add('active');
                        }
                    }
                });
            });

            // Ensure navigation groups work on mobile touch
            if (sidebar) {
                sidebar.addEventListener('touchstart', function(e) {
                    // Allow touch events to propagate for navigation toggles
                    if (e.target.closest('.nav-group-toggle')) {
                        e.stopPropagation();
                    }
                }, { passive: true });
            }

            // Auto-expand groups that contain active routes on page load
            document.querySelectorAll('.nav-group-content').forEach(function(content) {
                const hasActiveLink = content.querySelector('a.bg-amber-800\\/80');
                if (hasActiveLink) {
                    const groupName = content.getAttribute('data-content');
                    const toggle = document.querySelector(`.nav-group-toggle[data-group="${groupName}"]`);
                    if (toggle && content) {
                        content.classList.remove('hidden');
                        setTimeout(function() {
                            content.classList.add('expanded');
                        }, 10);
                        toggle.classList.add('active');
                    }
                }
            });

            // SweetAlert flash messages
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: @json(session('success')),
                    timer: 2200,
                    showConfirmButton: false,
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: @json(session('error')),
                });
            @endif

            // SweetAlert delete confirmations
            document.querySelectorAll('form[data-confirm]').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const message = form.getAttribute('data-confirm') || 'Are you sure?';

                    Swal.fire({
                        title: 'Are you sure?',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, delete it',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
