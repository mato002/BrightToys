@extends('layouts.app')

@section('title', 'Home - Otto Investments | Premium Toys & Games')

@section('content')
    {{-- HERO SECTION - Full Width with Product Images --}}
    <section class="relative overflow-hidden">
        <div id="hero-carousel" class="relative">
            {{-- Banner 1 - LEGO Formula 1 --}}
            <div class="hero-slide active relative h-[500px] md:h-[600px] lg:h-[700px] overflow-hidden">
                {{-- Background Image with Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600/90 via-purple-600/80 to-pink-600/70">
                    <img src="https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?auto=format&fit=crop&w=1920&h=1080&q=80" 
                         alt="LEGO toys background" 
                         class="absolute inset-0 w-full h-full object-cover opacity-30">
                </div>
                {{-- Product Image on Right --}}
                <div class="absolute right-0 top-0 bottom-0 w-full md:w-1/2 lg:w-2/5">
                    <div class="h-full bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-700 flex items-center justify-center relative overflow-hidden">
                        {{-- LEGO/Construction toys image from Unsplash --}}
                        <img src="https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?auto=format&fit=crop&w=800&h=800&q=80" 
                             alt="LEGO Formula 1" 
                             class="absolute inset-0 w-full h-full object-cover opacity-50">
                        {{-- Animated background pattern --}}
                        <div class="absolute inset-0 opacity-20">
                            <div class="absolute top-20 right-20 w-64 h-64 bg-yellow-400 rounded-full blur-3xl animate-pulse"></div>
                            <div class="absolute bottom-20 left-20 w-48 h-48 bg-pink-400 rounded-full blur-3xl animate-pulse delay-300"></div>
                        </div>
                        {{-- Product representation with Font Awesome icon --}}
                        <div class="relative z-10 text-center">
                            <i class="fas fa-car text-9xl md:text-[12rem] lg:text-[14rem] text-white/90 mb-4 transform hover:scale-110 transition-transform duration-500 drop-shadow-2xl"></i>
                            <div class="text-white/90 text-base font-bold">LEGO Formula 1</div>
                        </div>
                    </div>
                </div>
                
                {{-- Text Content Overlay on Left --}}
                <div class="relative z-10 h-full flex items-center">
                    <div class="container mx-auto px-4 lg:px-8">
                        <div class="max-w-2xl">
                            <div class="inline-flex items-center gap-2 text-xs font-bold bg-white/25 backdrop-blur-sm px-4 py-2 rounded-full mb-6 border border-white/30">
                                <span class="h-2 w-2 rounded-full bg-white animate-pulse"></span>
                                <span class="text-white">NEW ARRIVALS</span>
                            </div>
                            <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-6 leading-[1.1] tracking-tight">
                                Build the thrill with<br>
                                <span class="text-yellow-300 drop-shadow-2xl">LEGO Formula 1</span>
                            </h1>
                            <p class="text-lg md:text-xl text-white/95 mb-8 max-w-xl leading-relaxed font-medium">
                                Discover exciting new toys that spark creativity and imagination. Shop the latest collections now!
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="{{ route('frontend.new_arrivals') }}"
                                   class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white text-base font-bold px-8 py-4 rounded-lg shadow-2xl transition-all hover:scale-105 hover:shadow-blue-500/50">
                                    Shop Now
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                                <a href="{{ route('shop.index') }}"
                                   class="inline-flex items-center justify-center bg-white/10 backdrop-blur-md text-white border-2 border-white/40 hover:bg-white/20 text-base font-bold px-8 py-4 rounded-lg transition-all">
                                    Browse All
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Banner 2 - Toys for Every Age --}}
            <div class="hero-slide hidden relative h-[500px] md:h-[600px] lg:h-[700px] overflow-hidden">
                {{-- Background Image with Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-r from-pink-600/90 via-rose-600/80 to-red-600/70">
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=1920&h=1080&q=80" 
                         alt="Diverse toys background" 
                         class="absolute inset-0 w-full h-full object-cover opacity-30">
                </div>
                {{-- Product Image on Right --}}
                <div class="absolute right-0 top-0 bottom-0 w-full md:w-1/2 lg:w-2/5">
                    <div class="h-full bg-gradient-to-br from-pink-500 via-rose-600 to-red-700 flex items-center justify-center relative overflow-hidden">
                        {{-- Diverse toys/gifts image from Unsplash --}}
                        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=800&h=800&q=80" 
                             alt="Perfect Gifts" 
                             class="absolute inset-0 w-full h-full object-cover opacity-50">
                        {{-- Animated background pattern --}}
                        <div class="absolute inset-0 opacity-20">
                            <div class="absolute top-20 right-20 w-64 h-64 bg-yellow-400 rounded-full blur-3xl animate-pulse"></div>
                            <div class="absolute bottom-20 left-20 w-48 h-48 bg-orange-400 rounded-full blur-3xl animate-pulse delay-300"></div>
                        </div>
                        {{-- Product representation with Font Awesome icon --}}
                        <div class="relative z-10 text-center">
                            <i class="fas fa-gift text-9xl md:text-[12rem] lg:text-[14rem] text-white/90 mb-4 transform hover:scale-110 transition-transform duration-500 drop-shadow-2xl"></i>
                            <div class="text-white/90 text-base font-bold">Perfect Gifts</div>
                        </div>
                    </div>
                </div>
                
                {{-- Text Content Overlay on Left --}}
                <div class="relative z-10 h-full flex items-center">
                    <div class="container mx-auto px-4 lg:px-8">
                        <div class="max-w-2xl">
                            <div class="inline-flex items-center gap-2 text-xs font-bold bg-white/25 backdrop-blur-sm px-4 py-2 rounded-full mb-6 border border-white/30">
                                <span class="h-2 w-2 rounded-full bg-white animate-pulse"></span>
                                <span class="text-white">SPECIAL OFFER</span>
                            </div>
                            <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-6 leading-[1.1] tracking-tight">
                                Toys for Every Age,<br>
                                <span class="text-yellow-300 drop-shadow-2xl">Joy for Every Stage</span>
                            </h1>
                            <p class="text-lg md:text-xl text-white/95 mb-8 max-w-xl leading-relaxed font-medium">
                                From baby's first toy to teen favorites, find the perfect gift for every milestone.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="{{ route('shop.index') }}"
                                   class="inline-flex items-center justify-center bg-pink-600 hover:bg-pink-700 text-white text-base font-bold px-8 py-4 rounded-lg shadow-2xl transition-all hover:scale-105 hover:shadow-pink-500/50">
                                    Shop Now
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                                <a href="{{ route('shop.index') }}"
                                   class="inline-flex items-center justify-center bg-white/10 backdrop-blur-md text-white border-2 border-white/40 hover:bg-white/20 text-base font-bold px-8 py-4 rounded-lg transition-all">
                                    Explore Categories
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Banner 3 - Creating Memories --}}
            <div class="hero-slide hidden relative h-[500px] md:h-[600px] lg:h-[700px] overflow-hidden">
                {{-- Background Image with Gradient Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/90 via-purple-600/80 to-blue-600/70">
                    <img src="https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=1920&h=1080&q=80" 
                         alt="Children playing background" 
                         class="absolute inset-0 w-full h-full object-cover opacity-30">
                </div>
                {{-- Product Image on Right --}}
                <div class="absolute right-0 top-0 bottom-0 w-full md:w-1/2 lg:w-2/5">
                    <div class="h-full bg-gradient-to-br from-indigo-500 via-purple-600 to-blue-700 flex items-center justify-center relative overflow-hidden">
                        {{-- Children playing/happy memories image from Unsplash --}}
                        <img src="https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=800&h=800&q=80" 
                             alt="Quality Toys" 
                             class="absolute inset-0 w-full h-full object-cover opacity-50">
                        {{-- Animated background pattern --}}
                        <div class="absolute inset-0 opacity-20">
                            <div class="absolute top-20 right-20 w-64 h-64 bg-yellow-400 rounded-full blur-3xl animate-pulse"></div>
                            <div class="absolute bottom-20 left-20 w-48 h-48 bg-pink-400 rounded-full blur-3xl animate-pulse delay-300"></div>
                        </div>
                        {{-- Product representation with Font Awesome icon --}}
                        <div class="relative z-10 text-center">
                            <i class="fas fa-star text-9xl md:text-[12rem] lg:text-[14rem] text-white/90 mb-4 transform hover:scale-110 transition-transform duration-500 drop-shadow-2xl"></i>
                            <div class="text-white/90 text-base font-bold">Quality Toys</div>
                        </div>
                    </div>
                </div>
                
                {{-- Text Content Overlay on Left --}}
                <div class="relative z-10 h-full flex items-center">
                    <div class="container mx-auto px-4 lg:px-8">
                        <div class="max-w-2xl">
                            <div class="inline-flex items-center gap-2 text-xs font-bold bg-white/25 backdrop-blur-sm px-4 py-2 rounded-full mb-6 border border-white/30">
                                <span class="h-2 w-2 rounded-full bg-white animate-pulse"></span>
                                <span class="text-white">CREATING MEMORIES</span>
                            </div>
                            <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white mb-6 leading-[1.1] tracking-tight">
                                Creating Happy<br>
                                <span class="text-yellow-300 drop-shadow-2xl">Childhood Memories</span>
                            </h1>
                            <p class="text-lg md:text-xl text-white/95 mb-8 max-w-xl leading-relaxed font-medium">
                                Quality toys that inspire play, learning, and unforgettable moments for your little ones.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="{{ route('shop.index') }}"
                                   class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white text-base font-bold px-8 py-4 rounded-lg shadow-2xl transition-all hover:scale-105 hover:shadow-indigo-500/50">
                                    Explore Toys
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                                <a href="{{ route('pages.about') }}"
                                   class="inline-flex items-center justify-center bg-white/10 backdrop-blur-md text-white border-2 border-white/40 hover:bg-white/20 text-base font-bold px-8 py-4 rounded-lg transition-all">
                                    Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Carousel Controls --}}
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-3 z-20">
                <button onclick="showSlide(0)" class="carousel-dot active w-3 h-3 rounded-full bg-white hover:bg-white/80 transition-all shadow-lg"></button>
                <button onclick="showSlide(1)" class="carousel-dot w-3 h-3 rounded-full bg-white/60 hover:bg-white/80 transition-all"></button>
                <button onclick="showSlide(2)" class="carousel-dot w-3 h-3 rounded-full bg-white/60 hover:bg-white/80 transition-all"></button>
            </div>
        </div>
    </section>

    {{-- TRUST BADGES & STATISTICS --}}
    <section class="bg-white border-b-2 border-slate-200">
        <div class="container mx-auto px-4 lg:px-8 py-8 md:py-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 text-center">
                <div class="group hover:scale-105 transition-transform duration-200">
                    <div class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-amber-600 to-amber-400 bg-clip-text text-transparent mb-2">500+</div>
                    <p class="text-xs md:text-sm text-slate-600 font-medium">Fun Toys Available</p>
                </div>
                <div class="group hover:scale-105 transition-transform duration-200">
                    <div class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-pink-600 to-pink-400 bg-clip-text text-transparent mb-2">10K+</div>
                    <p class="text-xs md:text-sm text-slate-600 font-medium">Happy Customers</p>
                </div>
                <div class="group hover:scale-105 transition-transform duration-200">
                    <div class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-blue-400 bg-clip-text text-transparent mb-2">50+</div>
                    <p class="text-xs md:text-sm text-slate-600 font-medium">Top Brands</p>
                </div>
                <div class="group hover:scale-105 transition-transform duration-200">
                    <div class="text-4xl md:text-5xl font-bold bg-gradient-to-r from-emerald-600 to-emerald-400 bg-clip-text text-transparent mb-2">24/7</div>
                    <p class="text-xs md:text-sm text-slate-600 font-medium">Customer Support</p>
                </div>
            </div>
        </div>
    </section>

    {{-- WHY CHOOSE US / TRUST SECTION --}}
    <section class="bg-gradient-to-b from-slate-50 to-white py-12 md:py-16">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-10 md:mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">Why Choose Us</h2>
                <p class="text-sm md:text-base text-slate-600 max-w-2xl mx-auto">We're committed to bringing you the best toys with exceptional service</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                <div class="bg-white rounded-2xl p-6 md:p-8 shadow-lg border-2 border-slate-100 hover:border-amber-400 hover:shadow-xl transition-all duration-300 text-center group">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-amber-100 to-amber-200 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-shield-alt text-3xl text-amber-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Quality Guaranteed</h3>
                    <p class="text-sm text-slate-600">All products are carefully selected and tested for safety and durability</p>
                </div>
                <div class="bg-white rounded-2xl p-6 md:p-8 shadow-lg border-2 border-slate-100 hover:border-pink-400 hover:shadow-xl transition-all duration-300 text-center group">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-pink-100 to-pink-200 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-shipping-fast text-3xl text-pink-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Fast Delivery</h3>
                    <p class="text-sm text-slate-600">Quick and reliable shipping to get your toys to you as soon as possible</p>
                </div>
                <div class="bg-white rounded-2xl p-6 md:p-8 shadow-lg border-2 border-slate-100 hover:border-blue-400 hover:shadow-xl transition-all duration-300 text-center group">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-lock text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Secure Payment</h3>
                    <p class="text-sm text-slate-600">Your transactions are protected with industry-leading security</p>
                </div>
                <div class="bg-white rounded-2xl p-6 md:p-8 shadow-lg border-2 border-slate-100 hover:border-emerald-400 hover:shadow-xl transition-all duration-300 text-center group">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headset text-3xl text-emerald-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">24/7 Support</h3>
                    <p class="text-sm text-slate-600">Our friendly team is always ready to help you with any questions</p>
                </div>
            </div>
        </div>
    </section>

    {{-- TOP BRANDS --}}
    <section class="bg-white py-12 md:py-16">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">Top Brands</h2>
                <p class="text-sm md:text-base text-slate-600 max-w-2xl mx-auto">Discover trusted toy brands loved by kids and parents worldwide</p>
            </div>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-4 md:gap-6">
                @foreach(['Lego','Barbie','Pokemon','Ravensburger','VTech','Playmobil'] as $brand)
                    <div class="group flex items-center justify-center rounded-2xl border-2 border-slate-200 bg-white py-6 md:py-8 px-4 text-slate-700 font-bold text-sm md:text-base hover:border-amber-400 hover:bg-gradient-to-br hover:from-amber-50 hover:to-pink-50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                        <span class="group-hover:text-amber-600 transition-colors">{{ $brand }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- SHOP BY AGE / CATEGORIES --}}
    <section class="bg-gradient-to-b from-slate-50 to-white py-12 md:py-16">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">Shop by Age</h2>
                <p class="text-sm md:text-base text-slate-600 max-w-2xl mx-auto">Pick toys that match your child's stage and interests</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 md:gap-6">
                @forelse(($categories ?? []) as $cat)
                    <a href="{{ route('frontend.category', ['slug' => $cat->slug]) }}"
                       class="group relative overflow-hidden rounded-2xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 px-6 py-8 text-center shadow-md hover:shadow-2xl hover:border-amber-400 hover:-translate-y-2 transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-50/0 via-pink-50/0 to-sky-50/0 group-hover:from-amber-50/60 group-hover:via-pink-50/60 group-hover:to-sky-50/60 transition-all duration-300"></div>
                        <div class="relative">
                            <i class="fas fa-bullseye text-4xl md:text-5xl mb-3 group-hover:scale-110 transition-transform duration-300 text-amber-600"></i>
                            <p class="text-sm md:text-base font-bold text-slate-900 group-hover:text-amber-600 transition-colors leading-tight">{{ $cat->name }}</p>
                        </div>
                    </a>
                @empty
                    @php
                        $fallbackCategories = [
                            ['name' => 'Baby & Toddler', 'age' => '0-3 yrs', 'icon' => 'fa-baby'],
                            ['name' => 'Pre-school', 'age' => '3-5 yrs', 'icon' => 'fa-child'],
                            ['name' => 'Primary', 'age' => '6-8 yrs', 'icon' => 'fa-gamepad'],
                            ['name' => 'Pre-teen', 'age' => '9-12 yrs', 'icon' => 'fa-rocket'],
                        ];
                    @endphp
                    @foreach($fallbackCategories as $cat)
                        <a href="{{ route('shop.index') }}"
                           class="group relative overflow-hidden rounded-2xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 px-6 py-8 text-center shadow-md hover:shadow-2xl hover:border-amber-400 hover:-translate-y-2 transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-50/0 via-pink-50/0 to-sky-50/0 group-hover:from-amber-50/60 group-hover:via-pink-50/60 group-hover:to-sky-50/60 transition-all duration-300"></div>
                            <div class="relative">
                                <i class="fas {{ $cat['icon'] }} text-4xl md:text-5xl mb-3 group-hover:scale-110 transition-transform duration-300 text-amber-600"></i>
                                <p class="text-sm md:text-base font-bold text-slate-900 group-hover:text-amber-600 transition-colors leading-tight">{{ $cat['name'] }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $cat['age'] }}</p>
                            </div>
                        </a>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    {{-- POPULAR CATEGORIES --}}
    <section class="bg-white py-12 md:py-16">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Popular Categories</h2>
                    <p class="text-sm md:text-base text-slate-600">Explore our most loved toy collections</p>
                </div>
                <a href="{{ route('shop.index') }}" class="hidden md:flex items-center gap-2 text-sm font-bold text-amber-600 hover:text-amber-700 hover:gap-3 transition-all">
                    View all
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-6">
                @php
                    $popularCategories = [
                        ['name' => 'Lego & Construction', 'icon' => 'fa-cubes'],
                        ['name' => 'Vehicles & Action', 'icon' => 'fa-car'],
                        ['name' => 'Dolls & Dollhouses', 'icon' => 'fa-female'],
                        ['name' => 'Soft Toys', 'icon' => 'fa-heart'],
                        ['name' => 'Games & Puzzles', 'icon' => 'fa-puzzle-piece'],
                        ['name' => 'Arts & Crafts', 'icon' => 'fa-palette'],
                    ];
                @endphp
                @foreach($popularCategories as $cat)
                    <a href="{{ route('shop.index') }}"
                       class="group relative overflow-hidden rounded-2xl bg-white border-2 border-slate-200 px-4 py-6 md:py-8 shadow-md hover:shadow-2xl hover:border-amber-400 hover:-translate-y-2 transition-all duration-300 text-center">
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-50/0 via-pink-50/0 to-sky-50/0 group-hover:from-amber-50/50 group-hover:via-pink-50/50 group-hover:to-sky-50/50 transition-all duration-300"></div>
                        <div class="relative">
                            <i class="fas {{ $cat['icon'] }} text-3xl md:text-4xl mb-2 group-hover:scale-110 transition-transform duration-300 text-amber-600"></i>
                            <p class="text-xs md:text-sm font-bold text-slate-900 group-hover:text-amber-600 transition-colors leading-tight">
                                {{ $cat['name'] }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8 text-center md:hidden">
                <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-amber-600 hover:text-amber-700">
                    View all categories
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- BEST SELLERS / TRENDING PRODUCTS --}}
    <section class="bg-gradient-to-b from-slate-50 to-white py-12 md:py-16">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Best Sellers</h2>
                    <p class="text-sm md:text-base text-slate-600">Top-rated toys loved by families</p>
                </div>
                <a href="{{ route('shop.index') }}" class="hidden md:flex items-center gap-2 text-sm font-bold text-amber-600 hover:text-amber-700 hover:gap-3 transition-all">
                    View all
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                @forelse(($trending ?? []) as $product)
                    <div class="group border-2 border-slate-200 rounded-2xl bg-white overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-2 hover:border-amber-400 transition-all duration-300">
                        <div class="relative bg-slate-100 overflow-hidden">
                            @if($product->image_url)
                                <img
                                    src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}"
                                    alt="{{ $product->name }}"
                                    class="h-48 md:h-56 lg:h-64 w-full object-cover group-hover:scale-110 transition-transform duration-500"
                                >
                            @else
                                <div class="h-48 md:h-56 lg:h-64 flex items-center justify-center text-xs text-slate-400 bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100">
                                    <div class="text-center">
                                        <i class="fas fa-gift text-4xl mb-2 text-amber-500"></i>
                                        <p>Product image</p>
                                    </div>
                                </div>
                            @endif
                            <div class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-amber-500 text-white text-xs font-bold px-3 py-1.5 shadow-lg">
                                <i class="fas fa-star text-xs"></i> Trending
                            </div>
                        </div>
                        <div class="p-4 space-y-2">
                            <p class="font-bold text-sm text-slate-900 truncate leading-tight">{{ $product->name }}</p>
                            <p class="text-xs text-slate-500">{{ $product->category->name ?? 'Category' }}</p>
                            <p class="font-bold text-amber-600 text-lg">
                                Ksh {{ number_format($product->price, 0) }}
                            </p>
                            <div class="flex flex-col gap-2 pt-2 border-t border-slate-100">
                                <a href="{{ route('product.show', $product->slug) }}"
                                   class="text-center text-xs font-semibold text-slate-700 hover:text-amber-600 transition-colors py-1.5">
                                    View details
                                </a>
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit"
                                            class="w-full bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-3 py-2.5 rounded-lg shadow-sm transition-all hover:shadow-md">
                                        Add to cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    @foreach(range(1,5) as $i)
                        <div class="group border-2 border-slate-200 rounded-2xl bg-white overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-2 hover:border-amber-400 transition-all duration-300">
                            <div class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 overflow-hidden">
                                <div class="h-48 md:h-56 lg:h-64 flex items-center justify-center text-xs text-slate-500">
                                    <div class="text-center">
                                        <i class="fas fa-gift text-4xl mb-2 text-amber-500"></i>
                                        <p>Product image</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-amber-500 text-white text-xs font-bold px-3 py-1.5 shadow-lg">
                                    <i class="fas fa-star text-xs"></i> Trending
                                </div>
                            </div>
                            <div class="p-4 space-y-2">
                                <p class="font-bold text-sm text-slate-900 truncate">Sample Product {{ $i }}</p>
                                <p class="text-xs text-slate-500">Category name</p>
                                <p class="font-bold text-amber-600 text-lg">Ksh {{ 500 * $i }}</p>
                                <div class="pt-2 border-t border-slate-100">
                                    <span class="block text-center text-xs font-semibold text-slate-700 py-1.5">View details</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforelse
            </div>
            <div class="mt-8 text-center md:hidden">
                <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-amber-600 hover:text-amber-700">
                    View all products
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- PROMOTIONAL BANNER / SPECIAL OFFERS --}}
    <section class="bg-gradient-to-r from-amber-500 via-pink-500 to-purple-600 py-12 md:py-16 my-12 md:my-16">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-4xl mx-auto text-center text-white">
                <div class="inline-flex items-center gap-2 text-xs font-bold bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full mb-4">
                    <span class="h-2 w-2 rounded-full bg-white animate-pulse"></span>
                    LIMITED TIME OFFER
                </div>
                <h2 class="text-3xl md:text-5xl font-extrabold mb-4">Up to 30% OFF Selected Toys</h2>
                <p class="text-base md:text-lg text-white/95 mb-6 max-w-2xl mx-auto">
                    Don't miss out on our biggest sale of the year! Shop now and save on premium toys.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('shop.index') }}"
                       class="inline-flex items-center justify-center bg-white text-amber-600 hover:bg-amber-50 text-sm font-bold px-8 py-3.5 rounded-full shadow-xl transition-all hover:scale-105">
                        Shop Sale →
                    </a>
                    <a href="{{ route('frontend.new_arrivals') }}"
                       class="inline-flex items-center justify-center bg-white/10 backdrop-blur-md text-white border-2 border-white/30 hover:bg-white/20 text-sm font-bold px-8 py-3.5 rounded-full transition-all">
                        View New Arrivals
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- CUSTOMER TESTIMONIALS --}}
    <section class="bg-white py-12 md:py-16">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-10 md:mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">What Parents Say</h2>
                <p class="text-sm md:text-base text-slate-600 max-w-2xl mx-auto">Real feedback from families who love shopping with us</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 md:gap-8">
                <div class="bg-gradient-to-br from-white to-slate-50 rounded-2xl p-6 md:p-8 shadow-xl border-2 border-slate-100 hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-center gap-1 mb-4">
                        @foreach(range(1,5) as $i)
                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endforeach
                    </div>
                    <p class="text-sm md:text-base text-slate-700 mb-6 italic leading-relaxed">
                        "Otto Investments has provided excellent investment opportunities and transparent financial management. The toys are high quality and my kids love them!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            S
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 text-sm md:text-base">Sarah M.</p>
                            <p class="text-xs text-slate-500">Parent, Nairobi</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-white to-slate-50 rounded-2xl p-6 md:p-8 shadow-xl border-2 border-slate-100 hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-center gap-1 mb-4">
                        @foreach(range(1,5) as $i)
                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endforeach
                    </div>
                    <p class="text-sm md:text-base text-slate-700 mb-6 italic leading-relaxed">
                        "If I could give this store 1M+ stars I would! My son is always so excited when new toys arrive. The educational toys section is fantastic - he's learning while having fun!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            J
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 text-sm md:text-base">James K.</p>
                            <p class="text-xs text-slate-500">Parent, Mombasa</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-white to-slate-50 rounded-2xl p-6 md:p-8 shadow-xl border-2 border-slate-100 hover:shadow-2xl transition-all duration-300">
                    <div class="flex items-center gap-1 mb-4">
                        @foreach(range(1,5) as $i)
                            <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endforeach
                    </div>
                    <p class="text-sm md:text-base text-slate-700 mb-6 italic leading-relaxed">
                        "My almost three year old is learning so much from the toys we got here. The selection is incredible and the customer service is top-notch. Highly recommend!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            A
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 text-sm md:text-base">Amina W.</p>
                            <p class="text-xs text-slate-500">Parent, Kisumu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SHOP THE LATEST --}}
    <section class="bg-gradient-to-b from-slate-50 to-white py-12 md:py-16">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-3xl md:text-4xl font-bold text-slate-900">Shop the Latest</h2>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1.5 rounded-full border-2 border-emerald-200">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            New · Just dropped
                        </span>
                    </div>
                    <p class="text-sm md:text-base text-slate-600">Fresh arrivals and limited‑time deals picked for you</p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                @forelse(($latest ?? []) as $product)
                    <div class="group border-2 border-slate-200 rounded-2xl bg-white overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-2 hover:border-emerald-400 transition-all duration-300">
                        <div class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 overflow-hidden">
                            @if($product->image_url)
                                <img
                                    src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}"
                                    alt="{{ $product->name }}"
                                    class="h-48 md:h-56 lg:h-64 w-full object-cover group-hover:scale-110 transition-transform duration-500"
                                >
                            @else
                                <div class="h-48 md:h-56 lg:h-64 flex items-center justify-center text-xs text-slate-400 bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100">
                                    <div class="text-center">
                                        <i class="fas fa-gift text-4xl mb-2 text-amber-500"></i>
                                        <p>Product image</p>
                                    </div>
                                </div>
                            @endif
                            <div class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-emerald-500 text-white text-xs font-bold px-3 py-1.5 shadow-lg">
                                <i class="fas fa-sparkles text-xs"></i> New
                            </div>
                        </div>
                        <div class="p-4 space-y-2">
                            <p class="font-bold text-sm text-slate-900 truncate leading-tight">{{ $product->name }}</p>
                            <p class="font-bold text-amber-600 text-lg">
                                Ksh {{ number_format($product->price, 0) }}
                            </p>
                            <a href="{{ route('product.show', $product->slug) }}"
                               class="block w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold px-3 py-2.5 rounded-lg shadow-sm transition-all hover:shadow-md text-center">
                                View product
                            </a>
                        </div>
                    </div>
                @empty
                    @foreach(range(1,10) as $i)
                        <div class="group border-2 border-slate-200 rounded-2xl bg-white overflow-hidden shadow-lg hover:shadow-2xl hover:-translate-y-2 hover:border-emerald-400 transition-all duration-300">
                            <div class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 overflow-hidden">
                                <div class="h-48 md:h-56 lg:h-64 flex items-center justify-center text-xs text-slate-500">
                                    <div class="text-center">
                                        <i class="fas fa-gift text-4xl mb-2 text-amber-500"></i>
                                        <p>Product image</p>
                                    </div>
                                </div>
                                <div class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-emerald-500 text-white text-xs font-bold px-3 py-1.5 shadow-lg">
                                    <i class="fas fa-sparkles text-xs"></i> New
                                </div>
                            </div>
                            <div class="p-4 space-y-2">
                                <p class="font-bold text-sm text-slate-900 truncate">Latest Product {{ $i }}</p>
                                <p class="font-bold text-amber-600 text-lg">Ksh {{ 400 * $i }}</p>
                                <button class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold px-3 py-2.5 rounded-lg shadow-sm transition-all hover:shadow-md">
                                    View product
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    {{-- NEWSLETTER SIGNUP --}}
    <section class="bg-gradient-to-r from-amber-500 via-pink-500 to-purple-600 py-16 md:py-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-3xl mx-auto text-center text-white">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">Subscribe and get 10% OFF</h2>
                <p class="text-base md:text-lg text-white/95 mb-8 max-w-2xl mx-auto">
                    Stay updated with our latest toys, exclusive deals, and special offers. Join our newsletter today!
                </p>
                <form action="#" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
                    @csrf
                    <input type="email" 
                           name="email" 
                           placeholder="Enter your email address" 
                           required
                           class="flex-1 px-5 py-3.5 rounded-full text-slate-900 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-white/50 shadow-lg">
                    <button type="submit"
                            class="bg-white text-amber-600 hover:bg-amber-50 font-bold px-8 py-3.5 rounded-full shadow-xl transition-all hover:scale-105 whitespace-nowrap">
                        Subscribe
                    </button>
                </form>
                <p class="text-xs md:text-sm text-white/80 mt-6">
                    By signing up, you agree to receive updates and our Privacy Policy
                </p>
            </div>
        </div>
    </section>

    <script>
        // Hero Carousel Functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.carousel-dot');

        function showSlide(index) {
            slides.forEach((slide, i) => {
                if (i === index) {
                    slide.classList.remove('hidden');
                    slide.classList.add('active');
                } else {
                    slide.classList.add('hidden');
                    slide.classList.remove('active');
                }
            });
            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.add('active', 'bg-white');
                    dot.classList.remove('bg-white/60');
                } else {
                    dot.classList.remove('active', 'bg-white');
                    dot.classList.add('bg-white/60');
                }
            });
            currentSlide = index;
        }

        // Auto-rotate carousel
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 6000);

        // Initialize
        showSlide(0);
    </script>
@endsection
