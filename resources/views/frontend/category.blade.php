@extends('layouts.app')

@section('title', ($category->name ?? 'Category') . ' - BrightToys')

@section('content')
    {{-- Hero section --}}
    <section class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 overflow-hidden py-10">
        {{-- Background image with overlay - Kids playing with toys --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-25" 
             style="background-image: url('https://images.pexels.com/photos/159711/books-bookstore-book-reading-159711.jpeg?auto=compress&cs=tinysrgb&w=1920');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-pink-100/70 via-amber-50/70 to-sky-100/70"></div>
        
        <div class="container mx-auto px-4 lg:px-8 text-center relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">
                {{ $category->name ?? 'Category' }}
            </h1>
            <p class="text-base md:text-lg text-slate-600 max-w-2xl mx-auto">
                Find the perfect toys in this category
            </p>
        </div>
    </section>

    <div class="container mx-auto px-4 lg:px-8 py-8">
        <div class="mb-4 text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:underline">Home</a>
            <span class="mx-1">/</span>
            <span>{{ $category->name ?? 'Category' }}</span>
        </div>

        <form method="GET" class="space-y-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-2">
                <div>
                    <h1 class="text-xl font-semibold text-slate-900">
                        {{ $category->name ?? 'Category' }}
                    </h1>
                    <p class="text-xs text-slate-500">
                        {{ $products->total() ?? 0 }} items
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 text-xs">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Search in this category..."
                           class="border border-slate-200 rounded-lg px-3 py-2.5 text-xs w-full sm:w-56 bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                    <div class="flex gap-2">
                        <input type="number"
                               name="min_price"
                               value="{{ request('min_price') }}"
                               placeholder="Min"
                               class="border border-slate-200 rounded-lg px-3 py-2.5 text-xs w-24 bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                        <input type="number"
                               name="max_price"
                               value="{{ request('max_price') }}"
                               placeholder="Max"
                               class="border border-slate-200 rounded-lg px-3 py-2.5 text-xs w-24 bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                    </div>
                    <select name="sort"
                            class="border border-slate-200 rounded-lg px-2.5 py-2 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                        <option value="">Sort: Featured</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                        <option value="newest" @selected(request('sort') === 'newest')>Newest</option>
                    </select>
                    <div class="flex items-center gap-2">
                        <button class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-full shadow-sm shadow-amber-500/30">
                            Apply
                        </button>
                        @if(request()->hasAny(['q','min_price','max_price','sort']))
                            <a href="{{ route('frontend.category', $category->slug) }}"
                               class="text-xs text-slate-500 hover:text-slate-700">
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 text-sm">
                @forelse($products ?? [] as $product)
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
                        </div>
                        <div class="p-3.5 space-y-1.5">
                            <p class="font-medium text-[13px] text-slate-900 truncate">{{ $product->name }}</p>
                            <p class="font-semibold text-amber-600 text-[13px]">Ksh {{ number_format($product->price, 0) }}</p>
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
                    <p class="text-sm text-slate-500 col-span-full">No products found in this category.</p>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </form>
    </div>
@endsection

