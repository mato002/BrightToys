<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Fixed sidebar layout + collapse behaviour */
        @media (min-width: 1024px) {
            body.admin-has-sidebar {
                padding-left: 16rem; /* 64 * 0.25rem */
            }
            body.admin-sidebar-collapsed.admin-has-sidebar {
                padding-left: 4.5rem; /* collapsed width */
            }
        }

        @media (min-width: 1024px) {
            #admin-sidebar {
                scrollbar-width: thin;
                scrollbar-color: rgba(16, 185, 129, 0.7) rgba(6, 78, 59, 0.4);
            }
            #admin-sidebar::-webkit-scrollbar {
                width: 8px;
            }
            #admin-sidebar::-webkit-scrollbar-track {
                background: rgba(6, 78, 59, 0.4);
            }
            #admin-sidebar::-webkit-scrollbar-thumb {
                background: rgba(16, 185, 129, 0.7);
                border-radius: 9999px;
            }
            #admin-sidebar::-webkit-scrollbar-thumb:hover {
                background: rgba(16, 185, 129, 0.9);
            }
            #admin-sidebar nav {
                scrollbar-width: thin;
                scrollbar-color: rgba(16, 185, 129, 0.7) rgba(6, 78, 59, 0.4);
            }
            #admin-sidebar nav::-webkit-scrollbar {
                width: 6px;
            }
            #admin-sidebar nav::-webkit-scrollbar-track {
                background: transparent;
            }
            #admin-sidebar nav::-webkit-scrollbar-thumb {
                background: rgba(16, 185, 129, 0.5);
                border-radius: 9999px;
            }
            #admin-sidebar nav::-webkit-scrollbar-thumb:hover {
                background: rgba(16, 185, 129, 0.7);
            }
        }

        /* Collapse label text but keep icons */
        body.admin-sidebar-collapsed #admin-sidebar .sidebar-label,
        body.admin-sidebar-collapsed #admin-sidebar .sidebar-section-label {
            display: none;
        }
        @media (min-width: 1024px) {
            body.admin-sidebar-collapsed #admin-sidebar {
                width: 4.5rem;
            }
            /* Adjust header when collapsed - center everything */
            body.admin-sidebar-collapsed #admin-sidebar > div > div:first-child {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
                justify-content: center;
                flex-direction: column;
                gap: 0.5rem;
            }
            /* Hide logo text container when collapsed */
            body.admin-sidebar-collapsed #admin-sidebar > div > div:first-child > div:first-child {
                justify-content: center;
            }
            /* Center the collapse button when sidebar is collapsed */
            body.admin-sidebar-collapsed #admin-sidebar #sidebar-collapse-toggle {
                position: relative;
                margin: 0;
                align-self: center;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 flex min-h-screen antialiased admin-has-sidebar">
    {{-- Sidebar --}}
    <aside id="admin-sidebar"
           class="hidden lg:flex fixed inset-y-0 left-0 w-64 bg-emerald-900 border-r border-emerald-900/80 text-emerald-50 transition-all duration-200 ease-in-out shadow-xl/40 z-30">
        <div class="flex flex-col w-full h-full overflow-hidden">
            <div class="px-6 py-5 border-b border-emerald-800/80 flex items-center justify-between sticky top-0 bg-emerald-900 z-10">
                <div class="flex items-center space-x-3">
                    <div class="h-9 w-9 rounded-2xl bg-emerald-500 flex items-center justify-center text-xs font-semibold shadow-lg shadow-emerald-500/40">
                        BT
                    </div>
                    <div>
                        <p class="text-sm font-semibold tracking-wide sidebar-label">BrightToys Admin</p>
                        <p class="text-[11px] text-emerald-100/80 sidebar-label">Control center</p>
                    </div>
                </div>
                <button id="sidebar-collapse-toggle"
                        type="button"
                        class="flex items-center justify-center w-8 h-8 rounded-lg border border-emerald-700/60 text-emerald-100 hover:bg-emerald-800/60 transition-colors"
                        title="Collapse sidebar">
                    <span class="text-[12px]">«</span>
                </button>
            </div>
            <nav class="mt-4 px-3 text-[13px] space-y-1 flex-1 overflow-y-auto min-h-0">
                <p class="px-3 mb-2 text-[11px] font-medium uppercase tracking-[0.18em] text-emerald-200/80 sidebar-section-label">
                    Overview
                </p>
            <a href="{{ route('admin.dashboard') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    {{-- Dashboard icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 13h6V4H4v9zm0 7h6v-5H4v5zm10 0h6V11h-6v9zm0-16v4h6V4h-6z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="font-medium sidebar-label">Dashboard</span>
            </a>
            
            @php
                $user = auth()->user();
                $isSuperAdmin = $user->isSuperAdmin();
                $hasStoreAdmin = $user->hasAdminRole('store_admin');
                $hasFinanceAdmin = $user->hasAdminRole('finance_admin');
            @endphp

            {{-- Store Admin Section: Products, Categories, Orders, Customers --}}
            @if($isSuperAdmin || $hasStoreAdmin)
            <a href="{{ route('admin.products.index') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.products.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    {{-- Box icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 7l8-4 8 4-8 4-8-4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 17l8 4 8-4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="font-medium sidebar-label">Products</span>
            </a>
            <a href="{{ route('admin.categories.index') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.categories.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    {{-- Folder icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="font-medium sidebar-label">Categories</span>
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.orders.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    {{-- Receipt icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M7 3h10a1 1 0 0 1 1 1v16l-3-2-3 2-3-2-3 2V4a1 1 0 0 1 1-1z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 8h6M9 12h4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="font-medium sidebar-label">Orders</span>
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.users.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    {{-- User icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 20a8 8 0 0 1 16 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="font-medium sidebar-label">Customers</span>
            </a>
            @endif

            {{-- Admins (Super Admin or Finance Admin can manage) --}}
            @if($isSuperAdmin || $hasFinanceAdmin)
            <a href="{{ route('admin.admins.index') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.admins.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    {{-- Shield/Admin icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 2L4 5v6c0 5.55 3.84 10.74 8 12 4.16-1.26 8-6.45 8-12V5l-8-3z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="font-medium sidebar-label">Admins</span>
            </a>
            @endif

            {{-- Finance Admin Section: Partners, Financial, Documents, Activity Logs --}}
            @if($isSuperAdmin || $hasFinanceAdmin)
            <p class="px-3 mt-4 mb-2 text-[11px] font-medium uppercase tracking-[0.18em] text-emerald-200/80 sidebar-section-label">
                Partnership
            </p>
            <a href="{{ route('admin.partners.index') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.partners.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    <i class="fas fa-handshake"></i>
                </span>
                <span class="font-medium sidebar-label">Partners</span>
            </a>
            <a href="{{ route('admin.financial.index') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.financial.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    <i class="fas fa-dollar-sign"></i>
                </span>
                <span class="font-medium sidebar-label">Financial</span>
            </a>
            <a href="{{ route('admin.documents.index') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.documents.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    <i class="fas fa-folder-open"></i>
                </span>
                <span class="font-medium sidebar-label">Documents</span>
            </a>
            <a href="{{ route('admin.activity-logs.index') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.activity-logs.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    <i class="fas fa-history"></i>
                </span>
                <span class="font-medium sidebar-label">Activity Logs</span>
            </a>
            @endif

            {{-- Support (Store Admin or Super Admin) --}}
            @if($isSuperAdmin || $hasStoreAdmin)
            <p class="px-3 mt-4 mb-2 text-[11px] font-medium uppercase tracking-[0.18em] text-emerald-200/80 sidebar-section-label">
                Support
            </p>
            <a href="{{ route('admin.support-tickets.index') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.support-tickets.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    {{-- Inbox icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 6h16l-2 9H6L4 6z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 11l3 3 3-3" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="font-medium sidebar-label">Support & messages</span>
            </a>
            @endif

            <p class="px-3 mt-4 mb-2 text-[11px] font-medium uppercase tracking-[0.18em] text-emerald-200/80 sidebar-section-label">
                Account
            </p>
            <a href="{{ route('admin.profile') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.profile*') && !request()->routeIs('admin.settings*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    {{-- User icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 20a8 8 0 0 1 16 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="font-medium sidebar-label">Profile</span>
            </a>
            <a href="{{ route('admin.settings') }}"
               class="group flex items-center px-3 py-2.5 rounded-xl transition
                      {{ request()->routeIs('admin.settings*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                    {{-- Settings icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19.4 14a1.8 1.8 0 0 0 .36 1.98l.03.03a2 2 0 1 1-2.83 2.83l-.03-.03A1.8 1.8 0 0 0 15 19.4a1.8 1.8 0 0 0-1 .33 1.8 1.8 0 0 0-.8 1.52V22a2 2 0 1 1-4 0v-.75a1.8 1.8 0 0 0-.8-1.52 1.8 1.8 0 0 0-1-.33 1.8 1.8 0 0 0-1.98.36l-.03.03a2 2 0 1 1-2.83-2.83l.03-.03A1.8 1.8 0 0 0 4.6 14a1.8 1.8 0 0 0-.33-1 1.8 1.8 0 0 0-1.52-.8H2a2 2 0 1 1 0-4h.75a1.8 1.8 0 0 0 1.52-.8 1.8 1.8 0 0 0 .33-1A1.8 1.8 0 0 0 4 4.6a1.8 1.8 0 0 0-1.98-.36l-.03.03a2 2 0 1 1 2.83-2.83l.03.03A1.8 1.8 0 0 0 9 4.6a1.8 1.8 0 0 0 1-.33 1.8 1.8 0 0 0 .8-1.52V2a2 2 0 1 1 4 0v.75a1.8 1.8 0 0 0 .8 1.52 1.8 1.8 0 0 0 1 .33 1.8 1.8 0 0 0 1.98-.36l.03-.03a2 2 0 1 1 2.83 2.83l-.03.03A1.8 1.8 0 0 0 19.4 10c0 .36.12.71.33 1a1.8 1.8 0 0 0 1.52.8H22a2 2 0 1 1 0 4h-.75a1.8 1.8 0 0 0-1.52.8 1.8 1.8 0 0 0-.33 1z" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="font-medium sidebar-label">Settings</span>
            </a>
            </nav>

            <div class="mt-auto px-3 py-4 border-t border-emerald-800/80 text-[12px] space-y-2 bg-emerald-950/40 sticky bottom-0">
                <a href="{{ route('home') }}"
                   target="_blank"
                   class="flex items-center justify-between px-2 py-1.5 rounded-lg bg-emerald-800/80 hover:bg-emerald-700 text-emerald-50 text-[11px]">
                    <span class="sidebar-label">View website</span>
                    <span class="text-[10px]">↗</span>
                </a>

                <button type="button"
                        onclick="window.open('https://odhiambo.netlify.app','_blank')"
                        class="w-full text-left text-[11px] text-emerald-300 hover:text-emerald-100 hover:underline mt-1">
                    © {{ date('Y') }} BrightToys Admin
                </button>
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-h-screen">
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-slate-200 px-3 md:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3 relative">
                <button id="sidebar-toggle"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-100 focus:outline-none lg:hidden">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div>
                    <h1 class="text-sm md:text-base font-semibold text-slate-900 tracking-tight">
                        @yield('page_title', 'Dashboard')
                    </h1>
                    <p class="text-[11px] text-slate-400 hidden md:block">BrightToys admin control panel</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="hidden md:flex flex-col items-end text-[11px] leading-tight">
                    <span class="font-semibold text-slate-900">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <span class="text-slate-500">{{ auth()->user()->email ?? '' }}</span>
                </div>
                <button id="profile-toggle"
                        type="button"
                        class="flex items-center space-x-2 rounded-full border border-slate-200 bg-white px-2 py-1.5 text-[11px] shadow-sm hover:bg-slate-50">
                    <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-semibold shadow-lg shadow-emerald-500/40">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div id="profile-menu"
                     class="hidden absolute right-0 top-10 mt-1 w-48 rounded-lg border border-slate-200 bg-white shadow-lg shadow-slate-200/80 text-[12px] py-1 z-30">
                    <div class="px-3 py-2 border-b border-slate-100">
                        <p class="font-semibold text-slate-900 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <a href="{{ route('admin.profile') }}"
                       class="flex items-center px-3 py-2 text-slate-700 hover:bg-slate-50">
                        <span class="mr-2 text-[13px]">👤</span>
                        <span>My Profile</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full text-left flex items-center px-3 py-2 text-red-500 hover:bg-red-50 hover:text-red-600">
                            <span class="mr-2 text-[13px]">⎋</span>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 p-3 md:p-6">
            @yield('content')
        </main>

        <footer class="px-4 md:px-6 py-3 text-[11px] text-slate-500 border-t border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-6xl flex items-center justify-between">
                <a href="https://odhiambo.netlify.app" target="_blank" class="hover:underline">
                    © {{ date('Y') }} BrightToys Admin
                </a>
                <span class="hidden sm:inline text-slate-600">Ecommerce operations dashboard</span>
            </div>
        </footer>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('admin-sidebar');
            const toggle = document.getElementById('sidebar-toggle');
            const profileToggle = document.getElementById('profile-toggle');
            const profileMenu = document.getElementById('profile-menu');
            const collapseToggle = document.getElementById('sidebar-collapse-toggle');

            if (sidebar && toggle) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.toggle('hidden');
                });
            }

            if (collapseToggle) {
                collapseToggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const body = document.body;
                    const isCurrentlyCollapsed = body.classList.contains('admin-sidebar-collapsed');
                    
                    // Toggle the collapsed class
                    if (isCurrentlyCollapsed) {
                        body.classList.remove('admin-sidebar-collapsed');
                    } else {
                        body.classList.add('admin-sidebar-collapsed');
                    }
                    
                    // Update button text
                    const span = collapseToggle.querySelector('span');
                    if (span) {
                        span.textContent = body.classList.contains('admin-sidebar-collapsed') ? '»' : '«';
                    }
                    
                    // Update button title
                    collapseToggle.setAttribute('title', body.classList.contains('admin-sidebar-collapsed') ? 'Expand sidebar' : 'Collapse sidebar');
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

