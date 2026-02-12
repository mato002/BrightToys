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
                overflow-y: auto;
                overflow-x: hidden;
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

        .admin-table-scroll {
            /* Allow both directions so long/wide tables are fully reachable on small screens */
            max-height: 70vh;
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }

        /* Keep footer visible at the bottom on all screen sizes */
        .admin-main {
            padding-bottom: 3.5rem; /* space so content isn't hidden behind footer */
        }

        .admin-footer {
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

        /* Hide nav group content when sidebar is collapsed */
        body.admin-sidebar-collapsed .nav-group-content {
            display: none !important;
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
           class="hidden lg:flex fixed inset-y-0 left-0 w-64 bg-emerald-900 border-r border-emerald-900/80 text-emerald-50 transition-all duration-200 ease-in-out shadow-xl/40 z-20">
        <div class="flex flex-col w-full h-full overflow-hidden">
            <div class="px-6 py-5 border-b border-emerald-800/80 flex items-center justify-between sticky top-0 bg-emerald-900 z-10">
                <div class="flex items-center space-x-3">
                    <div class="h-9 w-9 rounded-2xl bg-emerald-500 flex items-center justify-center text-xs font-semibold shadow-lg shadow-emerald-500/40">
                        OI
                    </div>
                    <div>
                        <p class="text-sm font-semibold tracking-wide sidebar-label">Otto Investments Admin</p>
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
            <nav class="mt-4 px-3 text-[13px] space-y-1 flex-1 overflow-y-auto min-h-0" style="max-height: calc(100vh - 200px);">
                @php
                    $user = auth()->user();
                    $isSuperAdmin   = $user->isSuperAdmin();
                    $hasStoreAdmin  = $user->hasAdminRole('store_admin');
                    $hasFinanceAdmin = $user->hasAdminRole('finance_admin');
                    $hasChairman    = $user->hasAdminRole('chairman');
                    $hasTreasurer   = $user->hasAdminRole('treasurer');

                    // Permission-based navigation flags so users only see what they can actually access.
                    // We always allow Super Admin, Finance Admin and Chairman to see key partnership menus,
                    // even if granular permissions have not been explicitly seeded.
                    $canViewPartners   = $user->hasPermission('contributions.approve')
                        || $user->hasPermission('contributions.create')
                        || $isSuperAdmin
                        || $hasFinanceAdmin
                        || $hasChairman
                        || $hasTreasurer;

                    $canViewProjects   = $user->hasPermission('projects.create')
                        || $user->hasPermission('projects.update')
                        || $user->hasPermission('projects.activate')
                        || $isSuperAdmin
                        || $hasFinanceAdmin
                        || $hasChairman
                        || $hasTreasurer;

                    $canViewFinancial  = $user->hasPermission('financial.records.view')
                        || $isSuperAdmin
                        || $hasFinanceAdmin
                        || $hasChairman
                        || $hasTreasurer;

                    // Documents are managed by Super Admin, Finance Admin and Chairman, but we also
                    // honour explicit permission flags if you configure them.
                    $canViewDocuments  = $user->hasPermission('documents.upload')
                        || $user->hasPermission('documents.approve')
                        || $isSuperAdmin
                        || $hasFinanceAdmin
                        || $hasChairman
                        || $hasTreasurer;

                    $canViewLoans      = $user->hasPermission('loans.view')
                        || $isSuperAdmin
                        || $hasFinanceAdmin
                        || $hasChairman
                        || $hasTreasurer;
                    $canManagePenalties = $user->isSuperAdmin() || $hasFinanceAdmin || $hasChairman || $hasTreasurer;
                    $canManageVoting   = $hasChairman || $isSuperAdmin;
                    $canViewMembers    = $hasChairman || $isSuperAdmin; // chairman leads membership
                    $canViewActivity   = $isSuperAdmin || $hasFinanceAdmin || $hasChairman; // activity logs are audit-level

                    $hasPartnershipNav = $canViewMembers || $canViewPartners || $canViewProjects || $canViewFinancial || $canViewDocuments || $canViewLoans || $canManagePenalties || $canManageVoting || $canViewActivity;
                @endphp

                {{-- Overview Section --}}
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-emerald-200/80 hover:bg-emerald-800/60 hover:text-emerald-50 transition sidebar-section-label" data-group="overview">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Overview</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="overview">
                        <a href="{{ route('admin.dashboard') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 13h6V4H4v9zm0 7h6v-5H4v5zm10 0h6V11h-6v9zm0-16v4h6V4h-6z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Dashboard</span>
                        </a>
                    </div>
                </div>

                {{-- Store Management Section --}}
                @if($isSuperAdmin || $hasStoreAdmin)
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-emerald-200/80 hover:bg-emerald-800/60 hover:text-emerald-50 transition sidebar-section-label" data-group="store">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Store Management</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="store">
                        <a href="{{ route('admin.products.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.products.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 20a8 8 0 0 1 16 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Customers</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Admins Section --}}
                @if($isSuperAdmin || $hasFinanceAdmin)
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-emerald-200/80 hover:bg-emerald-800/60 hover:text-emerald-50 transition sidebar-section-label" data-group="admins">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Administration</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="admins">
                        <a href="{{ route('admin.admins.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.admins.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2L4 5v6c0 5.55 3.84 10.74 8 12 4.16-1.26 8-6.45 8-12V5l-8-3z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Admins</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Notifications & Transparency --}}
                @if($isSuperAdmin || $hasFinanceAdmin || $hasChairman)
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-emerald-200/80 hover:bg-emerald-800/60 hover:text-emerald-50 transition sidebar-section-label" data-group="notifications">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Notifications</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="notifications">
                        <a href="{{ route('admin.notifications.center') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.notifications.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Center</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Partnership Section --}}
                @if($hasPartnershipNav)
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-emerald-200/80 hover:bg-emerald-800/60 hover:text-emerald-50 transition sidebar-section-label" data-group="partnership">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Partnership</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="partnership">
                        @if($canViewMembers)
                            <a href="{{ route('admin.members.index') }}"
                               class="group flex items-center px-3 py-2.5 rounded-xl transition
                                      {{ request()->routeIs('admin.members.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4 20a8 8 0 0 1 16 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="font-medium sidebar-label">Members</span>
                            </a>
                        @endif
                        @if($canViewPartners)
                            <a href="{{ route('admin.partners.index') }}"
                               class="group flex items-center px-3 py-2.5 rounded-xl transition
                                      {{ request()->routeIs('admin.partners.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M9 11l3 3L22 4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="font-medium sidebar-label">Partners</span>
                            </a>
                        @endif
                        @if($canViewProjects)
                            <a href="{{ route('admin.projects.index') }}"
                               class="group flex items-center px-3 py-2.5 rounded-xl transition
                                      {{ request()->routeIs('admin.projects.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M4 7l8-4 8 4-8 4-8-4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4 17l8 4 8-4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M20 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="font-medium sidebar-label">Projects</span>
                            </a>
                        @endif
                        @if($canViewLoans)
                            <a href="{{ route('admin.loans.index') }}"
                               class="group flex items-center px-3 py-2.5 rounded-xl transition
                                      {{ request()->routeIs('admin.loans.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M4 6h16v12H4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4 10h16" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="font-medium sidebar-label">Loans</span>
                            </a>
                        @endif
                        @if($canViewFinancial || $hasChairman || $hasFinanceAdmin || $hasTreasurer)
                            <a href="{{ route('admin.payment-reminders.index') }}"
                               class="group flex items-center px-3 py-2.5 rounded-xl transition
                                      {{ request()->routeIs('admin.payment-reminders.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <circle cx="12" cy="12" r="10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 6v6l4 2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="font-medium sidebar-label">Payment Reminders</span>
                            </a>
                            @if($isSuperAdmin || $hasFinanceAdmin || $hasChairman)
                                <a href="{{ route('admin.penalty-rates.index') }}"
                                   class="group flex items-center px-3 py-2.5 rounded-xl transition
                                          {{ request()->routeIs('admin.penalty-rates.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <span class="font-medium sidebar-label">Penalty Rates</span>
                                </a>
                            @endif
                        @endif
                        @if($canManagePenalties)
                            <a href="{{ route('admin.penalties.index') }}"
                               class="group flex items-center px-3 py-2.5 rounded-xl transition
                                      {{ request()->routeIs('admin.penalties.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M4 6h16v12H4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M4 10h16" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="font-medium sidebar-label">Penalties</span>
                            </a>
                        @endif
                        @if($canManageVoting)
                            <a href="{{ route('admin.voting-topics.index') }}"
                               class="group flex items-center px-3 py-2.5 rounded-xl transition
                                      {{ request()->routeIs('admin.voting-topics.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <circle cx="12" cy="12" r="10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8 12h8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 8v8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="font-medium sidebar-label">Voting Topics</span>
                            </a>
                        @endif
                        @if($canViewActivity)
                            <a href="{{ route('admin.activity-logs.index') }}"
                               class="group flex items-center px-3 py-2.5 rounded-xl transition
                                      {{ request()->routeIs('admin.activity-logs.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                                <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <circle cx="12" cy="12" r="10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 6v6l4 2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="font-medium sidebar-label">Activity Logs</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Documents Section (own menu with sub-items) --}}
                @if($canViewDocuments)
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-emerald-200/80 hover:bg-emerald-800/60 hover:text-emerald-50 transition sidebar-section-label" data-group="documents">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Documents</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="documents">
                        <a href="{{ route('admin.documents.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.documents.index') || request()->routeIs('admin.documents.show') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">All Documents</span>
                        </a>
                        @if($isSuperAdmin || $hasFinanceAdmin || $hasChairman || $hasTreasurer)
                        <a href="{{ route('admin.documents.create') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.documents.create') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 4v16M4 12h16" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Upload Document</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Accounting Section --}}
                @if($isSuperAdmin || $hasFinanceAdmin || $hasChairman || $hasTreasurer)
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-emerald-200/80 hover:bg-emerald-800/60 hover:text-emerald-50 transition sidebar-section-label" data-group="accounting">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Accounting</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="accounting">
                        <a href="{{ route('admin.accounting.financial-overview') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.financial-overview') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 19V5l6 4 6-4 4 2v12" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 19h16" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Financial Overview</span>
                        </a>
                        <a href="{{ route('admin.accounting.dashboard') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.dashboard') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Books of Account</span>
                        </a>
                        <a href="{{ route('admin.accounting.journal.create') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.journal.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 4v16M4 12h16" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Journal Entry</span>
                        </a>
                        <a href="{{ route('admin.accounting.posted-entries.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.posted-entries.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 7h18M3 12h18M3 17h18" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Posted Entries</span>
                        </a>
                        <a href="{{ route('admin.accounting.chart-of-accounts.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.chart-of-accounts.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 3h18v18H3zM3 9h18M9 3v18" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Chart of Accounts</span>
                        </a>
                        <a href="{{ route('admin.accounting.expenses.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.expenses.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Company Expenses</span>
                        </a>
                        <a href="{{ route('admin.accounting.ledger.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.ledger.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 7h18M3 12h18M3 17h18" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">General Ledger</span>
                        </a>
                        <a href="{{ route('admin.accounting.reports.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.reports.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 3h18v18H3zM3 9h18M9 3v18" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Accruals & Reports</span>
                        </a>
                        <a href="{{ route('admin.accounting.budget.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.budget.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 3h18v18H3zM3 9h18M9 3v18" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Budget Reports</span>
                        </a>
                        <a href="{{ route('admin.accounting.assets.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.assets.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 7l8-4 8 4-8 4-8-4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 17l8 4 8-4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M20 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Company Assets</span>
                        </a>
                        <a href="{{ route('admin.accounting.reconciliation.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.reconciliation.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M8 7h8M8 12h8M8 17h8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Reconciliation</span>
                        </a>
                        <a href="{{ route('admin.loans.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.loans.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 6h16v12H4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 10h16" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Loans Management</span>
                        </a>
                        <a href="{{ route('admin.accounting.payroll.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.accounting.payroll.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Employee Payroll</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Support Section --}}
                @if($isSuperAdmin || $hasStoreAdmin)
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-emerald-200/80 hover:bg-emerald-800/60 hover:text-emerald-50 transition sidebar-section-label" data-group="support">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Support</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="support">
                        <a href="{{ route('admin.support-tickets.index') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.support-tickets.*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 6h16l-2 9H6L4 6z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9 11l3 3 3-3" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Support & messages</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Account Section --}}
                <div class="nav-group mb-2">
                    <button type="button" class="nav-group-toggle w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-emerald-200/80 hover:bg-emerald-800/60 hover:text-emerald-50 transition sidebar-section-label" data-group="account">
                        <span class="text-[11px] font-medium uppercase tracking-[0.18em]">Account</span>
                        <svg class="nav-chevron h-3 w-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="nav-group-content hidden pl-2 mt-1 space-y-1" data-content="account">
                        <a href="{{ route('admin.profile') }}"
                           class="group flex items-center px-3 py-2.5 rounded-xl transition
                                  {{ request()->routeIs('admin.profile*') && !request()->routeIs('admin.settings*') ? 'bg-emerald-800/80 text-emerald-50' : 'text-emerald-100/90 hover:bg-emerald-800/60 hover:text-white' }}">
                            <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-800/80 text-[11px] text-emerald-100 group-hover:bg-emerald-700/90">
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M19.4 14a1.8 1.8 0 0 0 .36 1.98l.03.03a2 2 0 1 1-2.83 2.83l-.03-.03A1.8 1.8 0 0 0 15 19.4a1.8 1.8 0 0 0-1 .33 1.8 1.8 0 0 0-.8 1.52V22a2 2 0 1 1-4 0v-.75a1.8 1.8 0 0 0-.8-1.52 1.8 1.8 0 0 0-1-.33 1.8 1.8 0 0 0-1.98.36l-.03.03a2 2 0 1 1-2.83-2.83l.03-.03A1.8 1.8 0 0 0 4.6 14a1.8 1.8 0 0 0-.33-1 1.8 1.8 0 0 0-1.52-.8H2a2 2 0 1 1 0-4h.75a1.8 1.8 0 0 0 1.52-.8 1.8 1.8 0 0 0 .33-1A1.8 1.8 0 0 0 4 4.6a1.8 1.8 0 0 0-1.98-.36l-.03.03a2 2 0 1 1 2.83-2.83l.03.03A1.8 1.8 0 0 0 9 4.6a1.8 1.8 0 0 0 1-.33 1.8 1.8 0 0 0 .8-1.52V2a2 2 0 1 1 4 0v.75a1.8 1.8 0 0 0 .8 1.52 1.8 1.8 0 0 0 1 .33 1.8 1.8 0 0 0 1.98-.36l.03-.03a2 2 0 1 1 2.83 2.83l-.03.03A1.8 1.8 0 0 0 19.4 10c0 .36.12.71.33 1a1.8 1.8 0 0 0 1.52.8H22a2 2 0 1 1 0 4h-.75a1.8 1.8 0 0 0-1.52.8 1.8 1.8 0 0 0-.33 1z" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="font-medium sidebar-label">Settings</span>
                        </a>
                    </div>
                </div>
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
                    © {{ date('Y') }} Otto Investments Admin
                </button>
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-h-screen">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200 px-3 md:px-6 py-3 flex items-center justify-between">
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
                    <p class="text-[11px] text-slate-400 hidden md:block">Otto Investments admin control panel</p>
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

        <main class="flex-1 p-3 md:p-6 admin-main">
            @yield('content')
        </main>

        <footer class="px-4 md:px-6 py-3 text-[11px] text-slate-500 border-t border-slate-200 bg-white/80 backdrop-blur admin-footer">
            <div class="mx-auto max-w-6xl flex items-center justify-between">
                <a href="https://odhiambo.netlify.app" target="_blank" class="hover:underline">
                    © {{ date('Y') }} Otto Investments Admin
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
                toggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('hidden');
                });

                // Hide sidebar when clicking anywhere outside on small screens
                document.addEventListener('click', function (e) {
                    if (window.innerWidth < 1024 &&
                        !sidebar.classList.contains('hidden') &&
                        !sidebar.contains(e.target) &&
                        !toggle.contains(e.target)) {
                        sidebar.classList.add('hidden');
                    }
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
                const hasActiveLink = content.querySelector('a.bg-emerald-800\\/80');
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

