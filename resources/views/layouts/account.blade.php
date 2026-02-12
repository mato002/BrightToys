<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Account - Otto Investments')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Fixed sidebar layout + collapse behaviour */
        @media (min-width: 1024px) {
            body.account-has-sidebar {
                padding-left: 16rem; /* 64 * 0.25rem */
            }
            body.account-sidebar-collapsed.account-has-sidebar {
                padding-left: 4.5rem; /* collapsed width */
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
    </style>
</head>
<body class="bg-slate-50 text-slate-900 flex min-h-screen antialiased account-has-sidebar">
    {{-- Sidebar --}}
    <aside id="account-sidebar"
           class="hidden lg:flex fixed inset-y-0 left-0 w-64 bg-amber-900 border-r border-amber-900/80 text-amber-50 transition-all duration-200 ease-in-out shadow-xl/40 z-30">
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
                <button id="sidebar-collapse-toggle"
                        type="button"
                        class="flex items-center justify-center w-8 h-8 rounded-lg border border-amber-700/60 text-amber-100 hover:bg-amber-800/60 transition-colors"
                        title="Collapse sidebar">
                    <span class="text-[12px]">«</span>
                </button>
            </div>
            <nav class="mt-4 px-3 text-[13px] space-y-1 flex-1 overflow-y-auto min-h-0">
                <p class="px-3 mb-2 text-[11px] font-medium uppercase tracking-[0.18em] text-amber-200/80 sidebar-section-label">
                    Account
                </p>
                <a href="{{ route('account.profile') }}"
                   class="group flex items-center px-3 py-2.5 rounded-xl transition
                          {{ request()->routeIs('account.profile') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 20a8 8 0 0 1 16 0" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="font-medium sidebar-label">Profile Overview</span>
                </a>
                <a href="{{ route('account.orders') }}"
                   class="group flex items-center px-3 py-2.5 rounded-xl transition
                          {{ request()->routeIs('account.orders') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M7 3h10a1 1 0 0 1 1 1v16l-3-2-3 2-3-2-3 2V4a1 1 0 0 1 1-1z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 8h6M9 12h4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="font-medium sidebar-label">My Orders</span>
                </a>
                <a href="{{ route('account.addresses') }}"
                   class="group flex items-center px-3 py-2.5 rounded-xl transition
                          {{ request()->routeIs('account.addresses') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 2L2 7l10 5 10-5-10-5z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="font-medium sidebar-label">Addresses</span>
                </a>
                <a href="{{ route('account.loans') }}"
                   class="group flex items-center px-3 py-2.5 rounded-xl transition
                          {{ request()->routeIs('account.loans*') ? 'bg-amber-800/80 text-amber-50' : 'text-amber-100/90 hover:bg-amber-800/60 hover:text-white' }}">
                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </span>
                    <span class="font-medium sidebar-label">Group Loans</span>
                </a>

                <p class="px-3 mt-4 mb-2 text-[11px] font-medium uppercase tracking-[0.18em] text-amber-200/80 sidebar-section-label">
                    Shopping
                </p>
                <a href="{{ route('shop.index') }}"
                   class="group flex items-center px-3 py-2.5 rounded-xl transition text-amber-100/90 hover:bg-amber-800/60 hover:text-white">
                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M4 7l8-4 8 4-8 4-8-4z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 17l8 4 8-4" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20 7v10" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="font-medium sidebar-label">Shop</span>
                </a>
                <a href="{{ route('cart.index') }}"
                   class="group flex items-center px-3 py-2.5 rounded-xl transition text-amber-100/90 hover:bg-amber-800/60 hover:text-white">
                    <span class="mr-3 flex h-6 w-6 items-center justify-center rounded-lg bg-amber-800/80 text-[11px] text-amber-100 group-hover:bg-amber-700/90">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-8 2a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
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

                <button type="button"
                        onclick="window.open('https://odhiambo.netlify.app','_blank')"
                        class="w-full text-left text-[11px] text-amber-300 hover:text-amber-100 hover:underline mt-1">
                    © {{ date('Y') }} Otto Investments
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
                        @yield('page_title', 'My Account')
                    </h1>
                    <p class="text-[11px] text-slate-400 hidden md:block">Manage your account and orders</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="hidden md:flex flex-col items-end text-[11px] leading-tight">
                    <span class="font-semibold text-slate-900">{{ auth()->user()->name ?? 'Customer' }}</span>
                    <span class="text-slate-500">{{ auth()->user()->email ?? '' }}</span>
                </div>
                <a href="{{ route('home') }}"
                   class="flex items-center space-x-2 rounded-full border border-slate-200 bg-white px-2 py-1.5 text-[11px] shadow-sm hover:bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    <span class="hidden sm:inline">Store</span>
                </a>
            </div>
        </header>

        <main class="flex-1 p-3 md:p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="px-4 md:px-6 py-3 text-[11px] text-slate-500 border-t border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto max-w-6xl flex items-center justify-between">
                <a href="https://odhiambo.netlify.app" target="_blank" class="hover:underline">
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
            const collapseToggle = document.getElementById('sidebar-collapse-toggle');

            if (sidebar && toggle) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.toggle('hidden');
                });
            }

            if (collapseToggle) {
                collapseToggle.addEventListener('click', function () {
                    const isCollapsed = document.body.classList.toggle('account-sidebar-collapsed');
                    collapseToggle.querySelector('span').textContent = isCollapsed ? '»' : '«';
                });
            }
        })();
    </script>
</body>
</html>
