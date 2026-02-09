@extends('layouts.app')

@section('title', 'Home - BrightToys')

@section('content')
    {{-- Promotional Hero Banners (like Toyworld) --}}
    <section class="relative bg-gradient-to-b from-sky-100 via-pink-50 to-amber-50 overflow-hidden">
        <div class="container mx-auto px-4 lg:px-8 py-6">
            <div id="hero-carousel" class="relative rounded-3xl overflow-hidden shadow-2xl">
                {{-- Banner 1 --}}
                <div class="hero-slide active bg-gradient-to-r from-amber-500 via-pink-500 to-purple-500 p-8 md:p-16 lg:p-20">
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div class="text-white">
                            <div class="inline-flex items-center gap-2 text-xs font-semibold bg-white/20 backdrop-blur px-3 py-1 rounded-full mb-4">
                                <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                                NEW ARRIVALS
                </div>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold mb-4">
                                Build the thrill with<br>
                                <span class="text-yellow-200">LEGO Formula 1</span>
                </h1>
                            <p class="text-sm sm:text-base text-white/90 mb-6 max-w-lg">
                                Discover exciting new toys that spark creativity and imagination. Shop the latest collections now!
                </p>
                    <a href="{{ route('frontend.new_arrivals') }}"
                               class="inline-flex items-center justify-center bg-white text-amber-600 hover:bg-amber-50 text-sm font-bold px-6 py-3 rounded-full shadow-lg transition-all hover:scale-105">
                                Shop Now →
                    </a>
                </div>
                        <div class="hidden md:block">
                            <div class="bg-white/10 backdrop-blur rounded-2xl p-8 text-center">
                                <div class="text-6xl mb-4">🧩</div>
                                <p class="text-white font-semibold">New Collection</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Banner 2 --}}
                <div class="hero-slide hidden bg-gradient-to-r from-pink-500 via-rose-500 to-red-500 p-8 md:p-16 lg:p-20">
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div class="text-white">
                            <div class="inline-flex items-center gap-2 text-xs font-semibold bg-white/20 backdrop-blur px-3 py-1 rounded-full mb-4">
                                <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                                SPECIAL OFFER
                            </div>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold mb-4">
                                Toys for Every Age,<br>
                                <span class="text-yellow-200">Joy for Every Stage</span>
                            </h1>
                            <p class="text-sm sm:text-base text-white/90 mb-6 max-w-lg">
                                From baby's first toy to teen favorites, find the perfect gift for every milestone.
                            </p>
                            <a href="{{ route('shop.index') }}"
                               class="inline-flex items-center justify-center bg-white text-pink-600 hover:bg-pink-50 text-sm font-bold px-6 py-3 rounded-full shadow-lg transition-all hover:scale-105">
                                Shop Now →
                            </a>
                        </div>
                        <div class="hidden md:block">
                            <div class="bg-white/10 backdrop-blur rounded-2xl p-8 text-center">
                                <div class="mb-4"><i class="fas fa-gift text-6xl text-yellow-300"></i></div>
                                <p class="text-white font-semibold">Perfect Gifts</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Banner 3 --}}
                <div class="hero-slide hidden bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 p-8 md:p-16 lg:p-20">
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <div class="text-white">
                            <div class="inline-flex items-center gap-2 text-xs font-semibold bg-white/20 backdrop-blur px-3 py-1 rounded-full mb-4">
                                <span class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></span>
                                CREATING MEMORIES
                            </div>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold mb-4">
                                Creating Happy<br>
                                <span class="text-yellow-200">Childhood Memories</span>
                            </h1>
                            <p class="text-sm sm:text-base text-white/90 mb-6 max-w-lg">
                                Quality toys that inspire play, learning, and unforgettable moments for your little ones.
                            </p>
                            <a href="{{ route('shop.index') }}"
                               class="inline-flex items-center justify-center bg-white text-blue-600 hover:bg-blue-50 text-sm font-bold px-6 py-3 rounded-full shadow-lg transition-all hover:scale-105">
                                Explore Toys →
                            </a>
            </div>
                        <div class="hidden md:block">
                            <div class="bg-white/10 backdrop-blur rounded-2xl p-8 text-center">
                                <div class="mb-4"><i class="fas fa-star text-6xl text-yellow-300"></i></div>
                                <p class="text-white font-semibold">Quality Toys</p>
                    </div>
                        </div>
                    </div>
                </div>

                {{-- Carousel Controls --}}
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                    <button onclick="showSlide(0)" class="carousel-dot active w-2.5 h-2.5 rounded-full bg-white/80 hover:bg-white transition-all"></button>
                    <button onclick="showSlide(1)" class="carousel-dot w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition-all"></button>
                    <button onclick="showSlide(2)" class="carousel-dot w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition-all"></button>
                </div>
            </div>
        </div>
    </section>

    {{-- Trust Badges & Stats (like StoryToys) --}}
    <section class="bg-white border-b border-slate-200">
        <div class="container mx-auto px-4 lg:px-8 py-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-amber-600 mb-1">500+</div>
                    <p class="text-xs md:text-sm text-slate-600">Fun Toys Available</p>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-pink-600 mb-1">10K+</div>
                    <p class="text-xs md:text-sm text-slate-600">Happy Customers</p>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-blue-600 mb-1">50+</div>
                    <p class="text-xs md:text-sm text-slate-600">Top Brands</p>
                    </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-emerald-600 mb-1">24/7</div>
                    <p class="text-xs md:text-sm text-slate-600">Customer Support</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Top brands --}}
    <section class="bg-gradient-to-b from-white to-slate-50/50 py-12">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 mb-2">Top Brands</h2>
                <p class="text-sm text-slate-600 max-w-2xl mx-auto">Discover trusted toy brands loved by kids and parents worldwide</p>
            </div>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-4">
                @foreach(['Lego','Barbie','Pokemon','Ravensburger','VTech','Playmobil'] as $brand)
                    <div class="group flex items-center justify-center rounded-2xl border-2 border-slate-200 bg-white py-6 px-4 text-slate-700 font-semibold text-sm hover:border-amber-400 hover:bg-amber-50 hover:shadow-lg hover:-translate-y-1 transition-all duration-200 cursor-pointer">
                        <span class="group-hover:text-amber-600 transition-colors">{{ $brand }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Shop by Age / Categories --}}
    <section class="bg-white py-12">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 mb-2">Shop by Age</h2>
                <p class="text-sm text-slate-600 max-w-2xl mx-auto">Pick toys that match your child's stage and interests</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4">
                @forelse(($categories ?? []) as $cat)
                    <a href="{{ route('frontend.category', ['slug' => $cat->slug]) }}"
                       class="group relative overflow-hidden rounded-2xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 px-4 py-6 text-center shadow-sm hover:shadow-xl hover:border-amber-400 hover:-translate-y-1 transition-all duration-200">
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-50/0 to-pink-50/0 group-hover:from-amber-50/50 group-hover:to-pink-50/50 transition-all duration-200"></div>
                        <p class="relative text-sm font-semibold text-slate-900 group-hover:text-amber-600 transition-colors leading-tight">{{ $cat->name }}</p>
                    </a>
                @empty
                    @php
                        $fallbackCategories = [
                            'Baby & Toddler (0-3 yrs)',
                            'Pre-school (3-5 yrs)',
                            'Primary (6-8 yrs)',
                            'Pre-teen (9-12 yrs)',
                        ];
                    @endphp
                    @foreach($fallbackCategories as $cat)
                        <a href="{{ route('shop.index') }}"
                           class="group relative overflow-hidden rounded-2xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 px-4 py-6 text-center shadow-sm hover:shadow-xl hover:border-amber-400 hover:-translate-y-1 transition-all duration-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-50/0 to-pink-50/0 group-hover:from-amber-50/50 group-hover:to-pink-50/50 transition-all duration-200"></div>
                            <p class="relative text-sm font-semibold text-slate-900 group-hover:text-amber-600 transition-colors leading-tight">{{ $cat }}</p>
                        </a>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    {{-- Popular categories --}}
    <section class="bg-gradient-to-b from-slate-50 to-white py-12">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 mb-2">Popular Categories</h2>
                    <p class="text-sm text-slate-600">Explore our most loved toy collections</p>
                </div>
                <a href="{{ route('shop.index') }}" class="hidden md:flex items-center gap-2 text-sm font-semibold text-amber-600 hover:text-amber-700 hover:gap-3 transition-all">
                    View all
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @php
                    $popularCategories = [
                        'Lego & Construction',
                        'Vehicles & Action Figures',
                        'Dolls & Dollhouses',
                        'Soft Toys',
                        'Games & Puzzles',
                        'Arts & Crafts',
                    ];
                @endphp
                @foreach($popularCategories as $label)
                    <a href="{{ route('shop.index') }}"
                       class="group relative overflow-hidden rounded-2xl bg-white border-2 border-slate-200 px-4 py-5 shadow-md hover:shadow-xl hover:border-amber-400 hover:-translate-y-1 transition-all duration-200 text-center">
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-50/0 via-pink-50/0 to-sky-50/0 group-hover:from-amber-50/30 group-hover:via-pink-50/30 group-hover:to-sky-50/30 transition-all duration-200"></div>
                        <p class="relative text-xs font-semibold text-slate-900 group-hover:text-amber-600 transition-colors leading-tight">
                            {{ $label }}
                        </p>
                    </a>
                @endforeach
            </div>
            <div class="mt-6 text-center md:hidden">
                <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-600 hover:text-amber-700">
                    View all categories
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Best sellers --}}
    <section class="bg-white py-12">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 mb-2">Best Sellers</h2>
                    <p class="text-sm text-slate-600">Top-rated toys loved by families</p>
                </div>
                <a href="{{ route('shop.index') }}" class="hidden md:flex items-center gap-2 text-sm font-semibold text-amber-600 hover:text-amber-700 hover:gap-3 transition-all">
                    View all
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-5">
                @forelse(($trending ?? []) as $product)
                    <div class="group border-2 border-slate-200 rounded-2xl bg-white overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-2 hover:border-amber-400 transition-all duration-200">
                        <div class="relative bg-slate-100 overflow-hidden">
                            @if($product->image_url)
                                <img
                                    src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}"
                                    alt="{{ $product->name }}"
                                    class="h-44 md:h-48 lg:h-52 w-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                            @else
                                <div class="h-44 md:h-48 lg:h-52 flex items-center justify-center text-[11px] text-slate-400 bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100">
                                    Product image
                                </div>
                            @endif
                            <div class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-amber-500 text-white text-[10px] font-bold px-2.5 py-1 shadow-lg">
                                <i class="fas fa-star text-[9px]"></i> Trending
                            </div>
                        </div>
                        <div class="p-4 space-y-2">
                            <p class="font-semibold text-sm text-slate-900 truncate leading-tight">{{ $product->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ $product->category->name ?? 'Category' }}</p>
                            <p class="font-bold text-amber-600 text-base">
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
                                            class="w-full bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-3 py-2 rounded-lg shadow-sm transition-colors">
                                        Add to cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    @foreach(range(1,5) as $i)
                        <div class="group border-2 border-slate-200 rounded-2xl bg-white overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-2 hover:border-amber-400 transition-all duration-200">
                            <div class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 overflow-hidden">
                                <div class="h-44 md:h-48 lg:h-52 flex items-center justify-center text-[11px] text-slate-500">
                                    Product image
                                </div>
                                <div class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-amber-500 text-white text-[10px] font-bold px-2.5 py-1 shadow-lg">
                                    <i class="fas fa-star text-[9px]"></i> Trending
                                </div>
                            </div>
                            <div class="p-4 space-y-2">
                                <p class="font-semibold text-sm text-slate-900 truncate">Sample Product {{ $i }}</p>
                                <p class="text-[11px] text-slate-500">Category name</p>
                                <p class="font-bold text-amber-600 text-base">Ksh {{ 500 * $i }}</p>
                                <div class="pt-2 border-t border-slate-100">
                                    <span class="block text-center text-xs font-semibold text-slate-700 py-1.5">View details</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforelse
            </div>
            <div class="mt-6 text-center md:hidden">
                <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-600 hover:text-amber-700">
                    View all products
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Customer Testimonials (like StoryToys) --}}
    <section class="bg-gradient-to-b from-amber-50 via-pink-50 to-sky-50 py-16">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 mb-2">What Parents Say</h2>
                <p class="text-sm text-slate-600 max-w-2xl mx-auto">Real feedback from families who love shopping with us</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
                    <div class="flex items-center gap-1 mb-4">
                        @foreach(range(1,5) as $i)
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endforeach
                    </div>
                    <p class="text-sm text-slate-700 mb-4 italic">
                        "My daughter loves the toys from BrightToys! The quality is amazing and the age recommendations are spot on. Shopping here gives me confidence that I'm choosing the right toys for her development."
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center text-white font-semibold">
                            S
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 text-sm">Sarah M.</p>
                            <p class="text-xs text-slate-500">Parent, Nairobi</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
                    <div class="flex items-center gap-1 mb-4">
                        @foreach(range(1,5) as $i)
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endforeach
                    </div>
                    <p class="text-sm text-slate-700 mb-4 italic">
                        "If I could give this store 1M+ stars I would! My son is always so excited when new toys arrive. The educational toys section is fantastic - he's learning while having fun!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-pink-500 flex items-center justify-center text-white font-semibold">
                            J
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 text-sm">James K.</p>
                            <p class="text-xs text-slate-500">Parent, Mombasa</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
                    <div class="flex items-center gap-1 mb-4">
                        @foreach(range(1,5) as $i)
                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endforeach
                    </div>
                    <p class="text-sm text-slate-700 mb-4 italic">
                        "My almost three year old is learning so much from the toys we got here. The selection is incredible and the customer service is top-notch. Highly recommend!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                            A
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900 text-sm">Amina W.</p>
                            <p class="text-xs text-slate-500">Parent, Kisumu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Shop the Latest --}}
    <section class="bg-gradient-to-b from-slate-50 to-white py-12">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Shop the Latest</h2>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full border border-emerald-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            New · Just dropped
                        </span>
                    </div>
                    <p class="text-sm text-slate-600">Fresh arrivals and limited‑time deals picked for you</p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-5">
                @forelse(($latest ?? []) as $product)
                    <div class="group border-2 border-slate-200 rounded-2xl bg-white overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-2 hover:border-emerald-400 transition-all duration-200">
                        <div class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 overflow-hidden">
                            @if($product->image_url)
                                <img
                                    src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}"
                                    alt="{{ $product->name }}"
                                    class="h-44 md:h-48 lg:h-52 w-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                            @else
                                <div class="h-44 md:h-48 lg:h-52 flex items-center justify-center text-[11px] text-slate-400 bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100">
                                    Product image
                                </div>
                            @endif
                            <div class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 shadow-lg">
                                <i class="fas fa-sparkles text-[9px]"></i> New
                            </div>
                        </div>
                        <div class="p-4 space-y-2">
                            <p class="font-semibold text-sm text-slate-900 truncate leading-tight">{{ $product->name }}</p>
                            <p class="font-bold text-amber-600 text-base">
                                Ksh {{ number_format($product->price, 0) }}
                            </p>
                            <a href="{{ route('product.show', $product->slug) }}"
                               class="block w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-3 py-2.5 rounded-lg shadow-sm transition-colors text-center">
                                View product
                            </a>
                        </div>
                    </div>
                @empty
                    @foreach(range(1,10) as $i)
                        <div class="group border-2 border-slate-200 rounded-2xl bg-white overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-2 hover:border-emerald-400 transition-all duration-200">
                            <div class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 overflow-hidden">
                                <div class="h-44 md:h-48 lg:h-52 flex items-center justify-center text-[11px] text-slate-500">
                                    Product image
                                </div>
                                <div class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 shadow-lg">
                                    <i class="fas fa-sparkles text-[9px]"></i> New
                                </div>
                            </div>
                            <div class="p-4 space-y-2">
                                <p class="font-semibold text-sm text-slate-900 truncate">Latest Product {{ $i }}</p>
                                <p class="font-bold text-amber-600 text-base">Ksh {{ 400 * $i }}</p>
                                <button class="w-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-3 py-2.5 rounded-lg shadow-sm transition-colors">
                                    View product
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    {{-- Newsletter Signup (like all reference sites) --}}
    <section class="bg-gradient-to-r from-amber-500 via-pink-500 to-purple-500 py-16">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-2xl mx-auto text-center text-white">
                <h2 class="text-2xl md:text-3xl font-bold mb-3">Subscribe and get 10% OFF</h2>
                <p class="text-sm md:text-base text-white/90 mb-6">
                    Stay updated with our latest toys, exclusive deals, and special offers. Join our newsletter today!
                </p>
                <form action="#" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    @csrf
                    <input type="email" 
                           name="email" 
                           placeholder="Enter your email address" 
                           required
                           class="flex-1 px-4 py-3 rounded-lg text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-white/50">
                    <button type="submit"
                            class="bg-white text-amber-600 hover:bg-amber-50 font-bold px-6 py-3 rounded-lg shadow-lg transition-all hover:scale-105">
                        Subscribe
                    </button>
                </form>
                <p class="text-xs text-white/80 mt-4">
                    By signing up, you agree to receive updates and our Privacy Policy
                </p>
            </div>
        </div>
    </section>

    {{-- Contact Information (like Toyworld & Toyzoona) --}}
    <section class="bg-white py-12 border-t border-slate-200">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 mb-2">Contact Us</h2>
                <p class="text-sm text-slate-600">We're here to help! Reach out to us anytime</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="text-center p-6 bg-slate-50 rounded-2xl">
                    <div class="mb-3"><i class="fas fa-phone text-4xl text-amber-600"></i></div>
                    <h3 class="font-semibold text-slate-900 mb-2">Phone</h3>
                    <p class="text-sm text-slate-600">(+254) 747900900</p>
                </div>
                <div class="text-center p-6 bg-slate-50 rounded-2xl">
                    <div class="mb-3"><i class="fas fa-envelope text-4xl text-pink-600"></i></div>
                    <h3 class="font-semibold text-slate-900 mb-2">Email</h3>
                    <p class="text-sm text-slate-600">info@brighttoys.com</p>
                </div>
                <div class="text-center p-6 bg-slate-50 rounded-2xl">
                    <div class="mb-3"><i class="fas fa-map-marker-alt text-4xl text-sky-600"></i></div>
                    <h3 class="font-semibold text-slate-900 mb-2">Location</h3>
                    <p class="text-sm text-slate-600">Nairobi, Kenya</p>
                </div>
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
                slide.classList.toggle('hidden', i !== index);
                slide.classList.toggle('active', i === index);
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
                dot.classList.toggle('bg-white/80', i === index);
                dot.classList.toggle('bg-white/40', i !== index);
            });
            currentSlide = index;
        }

        // Auto-rotate carousel
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 5000);

        // Initialize
        showSlide(0);
    </script>
@endsection
