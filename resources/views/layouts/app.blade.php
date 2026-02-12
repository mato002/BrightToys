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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Structured Data (JSON-LD) --}}
    @stack('structured_data')
</head>
<body class="bg-gray-50 text-gray-900">
    {{-- Top bar --}}
    <div class="bg-black text-white text-xs py-2">
        <div class="container mx-auto flex justify-between items-center px-4">
            <span>(+254) 747900900</span>
            <div class="space-x-4">
                <a href="{{ route('shop.index') }}" class="hover:underline">Gift Cards</a>
                <a href="{{ route('account.orders') }}" class="hover:underline">Track My Order</a>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="bg-white shadow-sm">
        <div class="container mx-auto flex items-center justify-between py-4 px-4">
            <a href="{{ route('home') }}" class="text-2xl font-bold tracking-wide">
                Otto Investments
            </a>

            {{-- Main nav --}}
            <nav class="hidden md:flex space-x-6 text-sm font-medium">
                <a href="{{ route('shop.index') }}" class="hover:text-amber-600">All Toys</a>
                <a href="{{ route('shop.index') }}" class="hover:text-amber-600">By Age</a>
                <a href="{{ route('shop.index') }}" class="hover:text-amber-600">By Brand</a>
                <a href="{{ route('pages.about') }}" class="hover:text-amber-600">About Us</a>
                <a href="{{ route('pages.contact') }}" class="hover:text-amber-600">Contact Us</a>
            </nav>

            {{-- Icons --}}
            <div class="flex items-center space-x-4 text-sm">
                @auth
                    <a href="{{ route('wishlist.index') }}" class="hover:text-amber-600">
                        Wishlist ({{ auth()->user()->wishlist()->count() }})
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-amber-600">Wishlist</a>
                @endauth
                <a href="{{ route('cart.index') }}" class="relative hover:text-amber-600">
                    Cart
                    <span id="cart-count" class="ml-1 inline-flex items-center justify-center rounded-full bg-amber-500 text-white text-xs px-2 py-0.5">
                        {{ $cartCount ?? 0 }}
                    </span>
                </a>

                @auth
                    @php($user = auth()->user())
                    @if($user->is_partner ?? false)
                        <a href="{{ route('partner.dashboard') }}" class="text-xs font-semibold hover:text-amber-600">
                            Partner
                        </a>
                    @elseif($user->is_admin ?? false)
                        <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold hover:text-amber-600">
                            Admin
                        </a>
                    @else
                        <a href="{{ route('account.profile') }}" class="text-xs font-semibold hover:text-amber-600">
                            My Account
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-600">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-semibold hover:text-amber-600">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Page content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-200 mt-10 pt-10 pb-6">
        <div class="container mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 px-4 text-sm">
            <div>
                <h3 class="font-semibold mb-3">Shop toys</h3>
                <ul class="space-y-1">
                    <li><a href="{{ route('shop.index') }}" class="hover:underline">All toys</a></li>
                    <li><a href="{{ route('shop.index') }}" class="hover:underline">Age 0–3 years</a></li>
                    <li><a href="{{ route('shop.index') }}" class="hover:underline">Age 4–7 years</a></li>
                    <li><a href="{{ route('shop.index') }}" class="hover:underline">Age 8–12 years</a></li>
                    <li><a href="{{ route('frontend.new_arrivals') }}" class="hover:underline">New arrivals</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold mb-3">For parents</h3>
                <ul class="space-y-1">
                    <li><a href="{{ route('account.orders') }}" class="hover:underline">Track my order</a></li>
                    <li><a href="{{ route('cart.index') }}" class="hover:underline">View cart</a></li>
                    <li><a href="{{ route('account.profile') }}" class="hover:underline">My account</a></li>
                    <li><a href="{{ route('pages.contact') }}" class="hover:underline">Help & support</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold mb-3">About Otto Investments</h3>
                <ul class="space-y-1">
                    <li><a href="{{ route('pages.about') }}" class="hover:underline">Our story</a></li>
                    <li><a href="{{ route('pages.about') }}" class="hover:underline">How we choose toys</a></li>
                    <li><a href="{{ route('pages.contact') }}" class="hover:underline">Contact us</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-semibold mb-3">Policies</h3>
                <ul class="space-y-1">
                    <li><a href="{{ route('pages.policies') }}" class="hover:underline">Terms & Conditions</a></li>
                    <li><a href="{{ route('pages.policies') }}" class="hover:underline">Privacy & data</a></li>
                    <li><a href="{{ route('pages.policies') }}" class="hover:underline">Shipping & returns</a></li>
                </ul>
            </div>
        </div>

        <div class="container mx-auto mt-8 px-4 flex flex-col md:flex-row items-center justify-between text-xs text-gray-400">
            <p>© {{ date('Y') }} Otto Investments. All rights reserved.</p>
            <div class="flex space-x-3 mt-2 md:mt-0">
                <a href="https://twitter.com" target="_blank" rel="noopener" class="hover:text-white">Twitter</a>
                <a href="https://facebook.com" target="_blank" rel="noopener" class="hover:text-white">Facebook</a>
                <a href="https://instagram.com" target="_blank" rel="noopener" class="hover:text-white">Instagram</a>
                <a href="https://tiktok.com" target="_blank" rel="noopener" class="hover:text-white">Tiktok</a>
                <a href="https://pinterest.com" target="_blank" rel="noopener" class="hover:text-white">Pinterest</a>
            </div>
        </div>
    </footer>

</body>
</html>

