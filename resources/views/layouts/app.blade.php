<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Otto Investments - Investment Management System')</title>
    
    {{-- SEO Meta Tags --}}
    <meta name="description" content="@yield('meta_description', 'Otto Investments - Investment management and partnership system. Track contributions, projects, and financial records.')">
    <meta name="keywords" content="@yield('meta_keywords', 'investments, partnerships, financial management, contributions, projects')">
    <meta name="author" content="Otto Investments">
    <meta name="robots" content="index, follow">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'Otto Investments - Investment Management System')">
    <meta property="og:description" content="@yield('og_description', 'Investment management and partnership system. Track contributions, projects, and financial records.')">
    <meta property="og:image" content="@yield('og_image', asset('images/toys/default.jpg'))">
    
    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('twitter_title', 'Otto Investments - Investment Management System')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Investment management and partnership system.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/toys/default.jpg'))">
    
    @stack('meta')
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ url()->current() }}">
    
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Structured Data (JSON-LD) --}}
    @stack('structured_data')
</head>
<body class="bg-gray-50 text-gray-900">
    
    {{-- Header --}}
    <header id="main-header" class="bg-white shadow-md sticky top-0 z-50 transition-all duration-300">
        <div class="container mx-auto px-4 lg:px-8">
            {{-- Top Row: Logo, Search, Actions --}}
            <div class="flex items-center justify-between py-4 gap-4">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex-shrink-0">
                    <span class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-amber-600 via-pink-600 to-purple-600 bg-clip-text text-transparent">
                        Otto Investments
                    </span>
                </a>

                {{-- Search Bar (Desktop) --}}
                <div class="hidden lg:flex flex-1 max-w-2xl mx-8">
                    <form action="{{ route('shop.index') }}" method="GET" class="w-full">
                        <div class="relative">
                            <input type="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search for toys, brands, categories..." 
                                   class="w-full px-4 py-2.5 pr-12 border-2 border-slate-300 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all">
                            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-amber-500 hover:bg-amber-600 text-white p-2 rounded-full transition-colors">
                                <i class="fas fa-search text-sm"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 md:gap-4">
                    {{-- Wishlist --}}
                    @auth
                        <a href="{{ route('wishlist.index') }}" class="hidden md:flex items-center gap-1.5 text-sm text-slate-700 hover:text-amber-600 transition-colors">
                            <i class="far fa-heart text-lg"></i>
                            <span class="hidden lg:inline">Wishlist</span>
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-500 text-white text-xs font-semibold">
                                {{ auth()->user()->wishlist()->count() }}
                            </span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden md:flex items-center gap-1.5 text-sm text-slate-700 hover:text-amber-600 transition-colors">
                            <i class="far fa-heart text-lg"></i>
                            <span class="hidden lg:inline">Wishlist</span>
                        </a>
                    @endauth

                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative flex items-center gap-1.5 text-sm text-slate-700 hover:text-amber-600 transition-colors">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span class="hidden lg:inline">Cart</span>
                        <span id="cart-count" class="absolute -top-2 -right-2 md:relative md:top-0 md:right-0 inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-500 text-white text-xs font-bold">
                            {{ $cartCount ?? 0 }}
                        </span>
                    </a>

                    {{-- User Menu --}}
                    @auth
                        @php($user = auth()->user())
                        <div class="relative group">
                            <button class="flex items-center gap-2 text-sm text-slate-700 hover:text-amber-600 transition-colors">
                                <i class="far fa-user text-lg"></i>
                                <span class="hidden lg:inline">Account</span>
                                <i class="fas fa-chevron-down text-xs hidden lg:inline"></i>
                            </button>
                            <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-xl border-2 border-slate-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <div class="py-2">
                                    @if($user->is_partner ?? false)
                                        <a href="{{ route('partner.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">Partner Dashboard</a>
                                        <p class="px-4 py-2 text-xs text-slate-500">Partner Account</p>
                                    @elseif($user->is_admin ?? false)
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">Admin Dashboard</a>
                                        <p class="px-4 py-2 text-xs text-slate-500">Admin Account</p>
                                    @else
                                        <a href="{{ route('account.overview') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">My Account</a>
                                        <a href="{{ route('account.orders') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">My Orders</a>
                                        <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">Wishlist</a>
                                    @endif
                                    <hr class="my-1 border-slate-200">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center gap-1.5 text-sm font-semibold text-slate-700 hover:text-amber-600 transition-colors">
                            <i class="far fa-user text-lg"></i>
                            <span class="hidden lg:inline">Login</span>
                        </a>
                    @endauth

                    {{-- Mobile Menu Toggle --}}
                    <button id="mobile-menu-toggle" class="lg:hidden text-slate-700 hover:text-amber-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            {{-- Bottom Row: Navigation & Mobile Search --}}
            <div class="border-t border-slate-200">
                {{-- Desktop Navigation --}}
                <nav class="hidden lg:flex items-center justify-center space-x-8 py-3">
                    <a href="{{ route('shop.index') }}" class="text-sm font-semibold text-slate-700 hover:text-amber-600 transition-colors py-2 border-b-2 border-transparent hover:border-amber-600">All Toys</a>
                    <div class="relative group">
                        <a href="{{ route('shop.index') }}" class="text-sm font-semibold text-slate-700 hover:text-amber-600 transition-colors py-2 flex items-center gap-1">
                            By Age
                            <i class="fas fa-chevron-down text-xs"></i>
                        </a>
                        <div class="absolute left-0 top-full mt-2 w-48 bg-white rounded-lg shadow-xl border-2 border-slate-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <div class="py-2">
                                <a href="{{ route('shop.index') }}?age=0-3" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">0-3 Years</a>
                                <a href="{{ route('shop.index') }}?age=4-7" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">4-7 Years</a>
                                <a href="{{ route('shop.index') }}?age=8-12" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">8-12 Years</a>
                                <a href="{{ route('shop.index') }}?age=13+" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">13+ Years</a>
                            </div>
                        </div>
                    </div>
                    <div class="relative group">
                        <a href="{{ route('shop.index') }}" class="text-sm font-semibold text-slate-700 hover:text-amber-600 transition-colors py-2 flex items-center gap-1">
                            By Brand
                            <i class="fas fa-chevron-down text-xs"></i>
                        </a>
                        <div class="absolute left-0 top-full mt-2 w-48 bg-white rounded-lg shadow-xl border-2 border-slate-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <div class="py-2">
                                <a href="{{ route('shop.index') }}?brand=lego" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">LEGO</a>
                                <a href="{{ route('shop.index') }}?brand=barbie" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">Barbie</a>
                                <a href="{{ route('shop.index') }}?brand=pokemon" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">Pokemon</a>
                                <a href="{{ route('shop.index') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-600">View All Brands</a>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('pages.about') }}" class="text-sm font-semibold text-slate-700 hover:text-amber-600 transition-colors py-2 border-b-2 border-transparent hover:border-amber-600">About Us</a>
                    <a href="{{ route('pages.contact') }}" class="text-sm font-semibold text-slate-700 hover:text-amber-600 transition-colors py-2 border-b-2 border-transparent hover:border-amber-600">Contact Us</a>
                </nav>

                {{-- Mobile Search --}}
                <div id="mobile-search" class="lg:hidden pb-3 hidden">
                    <form action="{{ route('shop.index') }}" method="GET" class="w-full">
                        <div class="relative">
                            <input type="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search for toys..." 
                                   class="w-full px-4 py-2.5 pr-12 border-2 border-slate-300 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-amber-500 hover:bg-amber-600 text-white p-2 rounded-full">
                                <i class="fas fa-search text-sm"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Mobile Menu Overlay --}}
        <div id="mobile-menu" class="lg:hidden fixed inset-0 bg-black/50 z-50 hidden">
            <div class="bg-white w-80 h-full shadow-xl overflow-y-auto">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <span class="font-bold text-lg">Menu</span>
                    <button id="mobile-menu-close" class="text-slate-600 hover:text-amber-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <nav class="p-4 space-y-2">
                    <a href="{{ route('shop.index') }}" class="block py-3 px-4 text-slate-700 hover:bg-amber-50 hover:text-amber-600 rounded-lg font-semibold">All Toys</a>
                    <a href="{{ route('shop.index') }}" class="block py-3 px-4 text-slate-700 hover:bg-amber-50 hover:text-amber-600 rounded-lg">By Age</a>
                    <a href="{{ route('shop.index') }}" class="block py-3 px-4 text-slate-700 hover:bg-amber-50 hover:text-amber-600 rounded-lg">By Brand</a>
                    <a href="{{ route('pages.about') }}" class="block py-3 px-4 text-slate-700 hover:bg-amber-50 hover:text-amber-600 rounded-lg">About Us</a>
                    <a href="{{ route('pages.contact') }}" class="block py-3 px-4 text-slate-700 hover:bg-amber-50 hover:text-amber-600 rounded-lg">Contact Us</a>
                    <hr class="my-2 border-slate-200">
                    @auth
                        <a href="{{ route('wishlist.index') }}" class="block py-3 px-4 text-slate-700 hover:bg-amber-50 hover:text-amber-600 rounded-lg">Wishlist</a>
                        <a href="{{ route('account.orders') }}" class="block py-3 px-4 text-slate-700 hover:bg-amber-50 hover:text-amber-600 rounded-lg">My Orders</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-toggle')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        document.getElementById('mobile-menu-close')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.add('hidden');
        });

        // Sticky header shadow on scroll
        window.addEventListener('scroll', function() {
            const header = document.getElementById('main-header');
            if (window.scrollY > 10) {
                header.classList.add('shadow-lg');
            } else {
                header.classList.remove('shadow-lg');
            }
        });
    </script>

    {{-- Page content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-slate-200 mt-16 pt-12 pb-8">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 md:gap-10 mb-10">
                {{-- Company Info --}}
                <div class="col-span-2 md:col-span-1">
                    <h3 class="text-xl font-bold mb-4 text-white">Otto Investments</h3>
                    <p class="text-sm text-slate-400 mb-4 leading-relaxed">
                        Your trusted partner for premium toys and games. Creating happy childhood memories, one toy at a time.
                    </p>
                    <div class="flex items-center gap-4">
                        <a href="https://facebook.com" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-slate-800 hover:bg-amber-600 flex items-center justify-center transition-colors">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-slate-800 hover:bg-amber-600 flex items-center justify-center transition-colors">
                            <i class="fab fa-instagram text-sm"></i>
                        </a>
                        <a href="https://twitter.com" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-slate-800 hover:bg-amber-600 flex items-center justify-center transition-colors">
                            <i class="fab fa-twitter text-sm"></i>
                        </a>
                        <a href="https://tiktok.com" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-slate-800 hover:bg-amber-600 flex items-center justify-center transition-colors">
                            <i class="fab fa-tiktok text-sm"></i>
                        </a>
                    </div>
                </div>

                {{-- Shop --}}
                <div>
                    <h3 class="text-base font-bold mb-4 text-white">Shop</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('shop.index') }}" class="text-slate-400 hover:text-amber-400 transition-colors">All Toys</a></li>
                        <li><a href="{{ route('shop.index') }}?age=0-3" class="text-slate-400 hover:text-amber-400 transition-colors">Age 0–3 Years</a></li>
                        <li><a href="{{ route('shop.index') }}?age=4-7" class="text-slate-400 hover:text-amber-400 transition-colors">Age 4–7 Years</a></li>
                        <li><a href="{{ route('shop.index') }}?age=8-12" class="text-slate-400 hover:text-amber-400 transition-colors">Age 8–12 Years</a></li>
                        <li><a href="{{ route('frontend.new_arrivals') }}" class="text-slate-400 hover:text-amber-400 transition-colors">New Arrivals</a></li>
                        <li><a href="{{ route('shop.index') }}" class="text-slate-400 hover:text-amber-400 transition-colors">Best Sellers</a></li>
                    </ul>
                </div>

                {{-- Customer Service --}}
                <div>
                    <h3 class="text-base font-bold mb-4 text-white">Customer Service</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('account.orders') }}" class="text-slate-400 hover:text-amber-400 transition-colors">Track My Order</a></li>
                        <li><a href="{{ route('cart.index') }}" class="text-slate-400 hover:text-amber-400 transition-colors">Shopping Cart</a></li>
                        <li><a href="{{ route('account.overview') }}" class="text-slate-400 hover:text-amber-400 transition-colors">My Account</a></li>
                        <li><a href="{{ route('pages.contact') }}" class="text-slate-400 hover:text-amber-400 transition-colors">Help & Support</a></li>
                        <li><a href="{{ route('pages.contact') }}" class="text-slate-400 hover:text-amber-400 transition-colors">Contact Us</a></li>
                        <li><a href="{{ route('pages.policies') }}" class="text-slate-400 hover:text-amber-400 transition-colors">FAQs</a></li>
                    </ul>
                </div>

                {{-- About & Policies --}}
                <div>
                    <h3 class="text-base font-bold mb-4 text-white">About</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('pages.about') }}" class="text-slate-400 hover:text-amber-400 transition-colors">Our Story</a></li>
                        <li><a href="{{ route('pages.about') }}" class="text-slate-400 hover:text-amber-400 transition-colors">How We Choose Toys</a></li>
                        <li><a href="{{ route('pages.policies') }}" class="text-slate-400 hover:text-amber-400 transition-colors">Terms & Conditions</a></li>
                        <li><a href="{{ route('pages.policies') }}" class="text-slate-400 hover:text-amber-400 transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('pages.policies') }}" class="text-slate-400 hover:text-amber-400 transition-colors">Shipping & Returns</a></li>
                        <li><a href="{{ route('pages.policies') }}" class="text-slate-400 hover:text-amber-400 transition-colors">Refund Policy</a></li>
                    </ul>
                </div>

                {{-- Contact Info --}}
                <div class="col-span-2 md:col-span-1">
                    <h3 class="text-base font-bold mb-4 text-white">Get in Touch</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-phone text-amber-400 mt-1"></i>
                            <div>
                                <p class="text-slate-400">Phone</p>
                                <a href="tel:+254747900900" class="text-white hover:text-amber-400 transition-colors">(+254) 747900900</a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-envelope text-amber-400 mt-1"></i>
                            <div>
                                <p class="text-slate-400">Email</p>
                                <a href="mailto:info@ottoinvestments.com" class="text-white hover:text-amber-400 transition-colors">info@ottoinvestments.com</a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-amber-400 mt-1"></i>
                            <div>
                                <p class="text-slate-400">Location</p>
                                <p class="text-white">Nairobi, Kenya</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-slate-800 pt-8 mt-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-xs md:text-sm text-slate-400">
                    <p>© {{ date('Y') }} Otto Investments. All rights reserved.</p>
                    <div class="flex items-center gap-6">
                        <a href="{{ route('pages.policies') }}" class="hover:text-amber-400 transition-colors">Terms</a>
                        <a href="{{ route('pages.policies') }}" class="hover:text-amber-400 transition-colors">Privacy</a>
                        <a href="{{ route('pages.policies') }}" class="hover:text-amber-400 transition-colors">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- SweetAlert2 for notifications --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Handle flash messages
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
            
            // Update cart count if provided
            @if(session('cart_count'))
                document.getElementById('cart-count').textContent = {{ session('cart_count') }};
            @endif
        @endif

        @if(session('error') || $errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') ?? $errors->first() }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        @endif

        // Handle AJAX cart add forms
        document.addEventListener('DOMContentLoaded', function() {
            // Find all cart add forms
            const cartForms = document.querySelectorAll('form[action*="cart/add"]');
            
            cartForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    const submitButton = form.querySelector('button[type="submit"]');
                    const originalText = submitButton.innerHTML;
                    
                    // Disable button and show loading
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token')
                        },
                        redirect: 'follow'
                    })
                    .then(response => {
                        // Check if response is a redirect
                        if (response.redirected) {
                            // Get the redirect URL and fetch it to get the flash message
                            return fetch(response.url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                        }
                        return response;
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Re-enable button
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                        
                        // Parse the HTML to extract cart count if available
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newCartCount = doc.getElementById('cart-count')?.textContent;
                        
                        // Update cart count if found
                        const cartCountElement = document.getElementById('cart-count');
                        if (newCartCount && cartCountElement) {
                            cartCountElement.textContent = newCartCount;
                        }
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Added to Cart!',
                            text: 'Product added successfully. Continue shopping!',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            showCancelButton: true,
                            cancelButtonText: 'View Cart',
                            cancelButtonColor: '#f59e0b'
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                window.location.href = '{{ route('cart.index') }}';
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to add product to cart. Please try again.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>

