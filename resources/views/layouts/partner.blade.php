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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Fixed sidebar layout + collapse behaviour */
        @media (min-width: 1024px) {
            body.partner-has-sidebar {
                padding-left: 16rem; /* 64 * 0.25rem */
            }
            body.partner-sidebar-collapsed.partner-has-sidebar {
                padding-left: 4.5rem; /* collapsed width */
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

        /* Mobile sidebar styles */
        @media (max-width: 1023px) {
            #partner-sidebar {
                position: fixed;
                z-index: 50;
                display: flex !important;
            }
            #partner-sidebar.hidden {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased partner-has-sidebar">
    {{-- Mobile overlay --}}
    <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-40 lg:hidden"></div>
    
    {{-- Sidebar --}}
    <aside id="partner-sidebar" class="hidden lg:flex fixed inset-y-0 left-0 w-64 bg-amber-900 border-r border-amber-900/80 text-amber-50 transition-all duration-200 ease-in-out shadow-xl/40 z-50 lg:z-20">
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
                <button id="sidebar-collapse-toggle" type="button" class="flex items-center justify-center w-8 h-8 rounded-lg border border-amber-700/60 text-amber-100 hover:bg-amber-800/60 transition-colors" title="Collapse sidebar">
                    <span class="text-[12px]">«</span>
                </button>
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
                        <a href="{{ route('partner.activity') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.activity*') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 6v6l4 2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Activity</span>
                        </a>
                        <a href="{{ route('partner.reports') }}" class="group flex items-center px-3 py-2.5 rounded-xl transition {{ request()->routeIs('partner.reports') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M16 18l-4-4-4 4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 14V4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 10h-4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v-6z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M3 10h4a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H3v-6z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Reports</span>
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
                <button type="button" onclick="window.open('https://odhiambo.netlify.app','_blank')" class="w-full text-left text-[11px] text-amber-300 hover:text-amber-100 hover:underline mt-1">
                    © {{ date('Y') }} BrightToys Partner
                </button>
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
                    <p class="text-[11px] text-slate-400 hidden md:block">BrightToys partner overview</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 relative">
                <div class="hidden md:flex flex-col items-end text-[11px] leading-tight">
                    <span class="font-semibold text-slate-900">{{ auth()->user()->name ?? 'Partner' }}</span>
                    <span class="text-slate-500">{{ auth()->user()->email ?? '' }}</span>
                </div>
                <button id="profile-toggle" type="button" class="flex items-center space-x-2 rounded-full border border-slate-200 bg-white px-2 py-1.5 text-[11px] shadow-sm hover:bg-slate-50 relative z-40">
                    <div class="w-7 h-7 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-semibold shadow-lg shadow-amber-500/40">
                        {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <div id="profile-menu" class="hidden absolute right-0 top-full mt-2 w-48 rounded-lg border border-slate-200 bg-white shadow-lg shadow-slate-200/80 text-[12px] py-1 z-50">
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
                <a href="https://odhiambo.netlify.app" target="_blank" class="hover:underline">
                    © {{ date('Y') }} BrightToys Partner
                </a>
                <span class="hidden sm:inline text-slate-600">Investment overview dashboard</span>
            </div>
        </footer>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('partner-sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');
            const profileToggle = document.getElementById('profile-toggle');
            const profileMenu = document.getElementById('profile-menu');
            const collapseToggle = document.getElementById('sidebar-collapse-toggle');

            function showSidebar() {
                if (window.innerWidth < 1024) {
                    sidebar.classList.remove('hidden');
                    if (overlay) overlay.classList.remove('hidden');
                }
            }

            function hideSidebar() {
                if (window.innerWidth < 1024) {
                    sidebar.classList.add('hidden');
                    if (overlay) overlay.classList.add('hidden');
                }
            }

            if (sidebar && toggle) {
                toggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    // On mobile, toggle the sidebar
                    if (window.innerWidth < 1024) {
                        if (sidebar.classList.contains('hidden')) {
                            showSidebar();
                        } else {
                            hideSidebar();
                        }
                    }
                });

                // Hide sidebar when clicking on overlay
                if (overlay) {
                    overlay.addEventListener('click', function() {
                        hideSidebar();
                    });
                }

                // Hide sidebar when clicking outside on small screens
                document.addEventListener('click', function (e) {
                    if (window.innerWidth < 1024 &&
                        !sidebar.classList.contains('hidden') &&
                        !sidebar.contains(e.target) &&
                        !toggle.contains(e.target) &&
                        overlay &&
                        !overlay.contains(e.target)) {
                        hideSidebar();
                    }
                });

                // Handle window resize - ensure sidebar is hidden on mobile by default
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        if (window.innerWidth < 1024) {
                            hideSidebar();
                        } else {
                            sidebar.classList.remove('hidden');
                            if (overlay) overlay.classList.add('hidden');
                        }
                    }, 100);
                });
            }

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
                    profileMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', function (e) {
                    if (!profileMenu.classList.contains('hidden') &&
                        !profileMenu.contains(e.target) &&
                        !profileToggle.contains(e.target)) {
                        profileMenu.classList.add('hidden');
                    }
                });
            }

            // Navigation group toggle functionality
            document.querySelectorAll('.nav-group-toggle').forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const groupName = this.getAttribute('data-group');
                    const content = document.querySelector(`.nav-group-content[data-content="${groupName}"]`);
                    const isExpanded = content && content.classList.contains('expanded');
                    
                    if (content) {
                        if (isExpanded) {
                            content.classList.remove('expanded');
                            setTimeout(function() {
                                content.classList.add('hidden');
                            }, 300);
                            this.classList.remove('active');
                        } else {
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
