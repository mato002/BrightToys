@extends('layouts.app')

@section('title', 'Shop - Otto Investments')

@section('content')
    {{-- Hero section --}}
    <section class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 overflow-hidden py-12">
        {{-- Background image with overlay - Colorful toy display --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-25" 
             style="background-image: url('https://images.pexels.com/photos/160715/pexels-photo-160715.jpeg?auto=compress&cs=tinysrgb&w=1920');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-pink-100/70 via-amber-50/70 to-sky-100/70"></div>
        
        <div class="container mx-auto px-4 lg:px-8 text-center relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">Discover Amazing Toys</h1>
            <p class="text-base md:text-lg text-slate-600 max-w-2xl mx-auto">
                Explore our complete collection of fun, safe, and educational toys for kids of all ages
            </p>
        </div>
    </section>

    <div class="container mx-auto px-4 lg:px-8 py-8 grid md:grid-cols-4 gap-8">
        <form method="GET" class="contents">
            {{-- Sidebar filters --}}
            <aside class="md:col-span-1 space-y-8">
                <div>
                    <h3 class="font-semibold mb-2 text-xs text-slate-600 uppercase tracking-[0.16em]">Search</h3>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Search products..."
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                </div>

                <div>
                    <h3 class="font-semibold mb-2 text-xs text-slate-600 uppercase tracking-[0.16em]">Categories</h3>
                    <select name="category"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                        <option value="">All categories</option>
                        @foreach(($categories ?? []) as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <h3 class="font-semibold mb-2 text-xs text-slate-600 uppercase tracking-[0.16em]">Price range (Ksh)</h3>
                    <div class="flex gap-2">
                        <input type="number"
                               name="min_price"
                               value="{{ request('min_price') }}"
                               placeholder="Min"
                               class="w-1/2 border border-slate-200 rounded-lg px-3 py-2.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                        <input type="number"
                               name="max_price"
                               value="{{ request('max_price') }}"
                               placeholder="Max"
                               class="w-1/2 border border-slate-200 rounded-lg px-3 py-2.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-full shadow-sm shadow-amber-500/30">
                        Apply filters
                    </button>
                    @if(request()->hasAny(['q','category','min_price','max_price','sort']))
                        <a href="{{ route('shop.index') }}"
                           class="text-xs text-slate-500 hover:text-slate-700 self-center">
                            Clear
                        </a>
                    @endif
                </div>
            </aside>

            {{-- Product grid --}}
            <section class="md:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-xl font-semibold text-slate-900">All products</h1>
                    <select name="sort"
                            onchange="this.form.submit()"
                            class="border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                        <option value="">Sort by: Featured</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                        <option value="newest" @selected(request('sort') === 'newest')>Newest</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 text-sm">
                    @forelse(($products ?? []) as $product)
                        <div class="group border border-slate-200 rounded-2xl bg-white overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
                            <div class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100">
                                @if($product->image_url)
                                    <img
                                        src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}"
                                        alt="{{ $product->name }}"
                                        class="h-40 md:h-44 w-full object-cover"
                                    >
                                @else
                                    <div class="h-40 md:h-44 flex items-center justify-center text-[11px] text-slate-500">
                                        Product image
                                    </div>
                                @endif
                                @if($loop->first)
                                    <div class="absolute top-2 left-2 inline-flex items-center rounded-full bg-amber-500 text-[10px] font-medium px-2 py-0.5 text-white">
                                        Featured
                                    </div>
                                @endif
                            </div>
                            <div class="p-3.5 space-y-1.5">
                                <p class="font-medium text-[13px] text-slate-900 truncate">{{ $product->name }}</p>
                                <p class="text-[11px] text-slate-500">{{ $product->category->name ?? 'Category' }}</p>
                                <p class="font-semibold text-amber-600 text-[13px]">
                                    Ksh {{ number_format($product->price, 0) }}
                                </p>
                                <div class="flex justify-between items-center text-[11px] mt-2">
                                    <a href="{{ route('product.show', $product->slug) }}"
                                       class="font-medium text-slate-900 hover:text-amber-600">View details</a>
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-amber-600 hover:text-amber-700 font-medium">
                                            Add to cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-full text-sm text-gray-500">
                            No products found.
                        </p>
                    @endforelse
                </div>

                @if(isset($products))
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                @endif
            </section>
        </form>
    </div>
@endsection

