@extends('layouts.account')

@section('title', 'Shop')
@section('page_title', 'Shop')

@section('breadcrumbs')
    <span class="breadcrumb-separator">/</span>
    <span class="text-slate-700">Shop</span>
@endsection

@section('content')
    <div class="grid md:grid-cols-4 gap-6">
        <form method="GET" action="{{ route('account.shop') }}" class="contents">
            {{-- Sidebar filters --}}
            <aside class="md:col-span-1 space-y-6">
                <div>
                    <h3 class="font-semibold mb-2 text-xs text-slate-600 uppercase tracking-wide">Search</h3>
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Search products..."
                           class="w-full border-2 border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                </div>
                <div>
                    <h3 class="font-semibold mb-2 text-xs text-slate-600 uppercase tracking-wide">Categories</h3>
                    <select name="category"
                            class="w-full border-2 border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">All categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <h3 class="font-semibold mb-2 text-xs text-slate-600 uppercase tracking-wide">Price range (Ksh)</h3>
                    <div class="flex gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                               class="flex-1 border-2 border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max"
                               class="flex-1 border-2 border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                        Apply filters
                    </button>
                    @if(request()->hasAny(['q','category','min_price','max_price','sort']))
                        <a href="{{ route('account.shop') }}" class="text-sm text-slate-500 hover:text-amber-600 self-center">Clear</a>
                    @endif
                </div>
            </aside>

            {{-- Product grid --}}
            <section class="md:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">All products</h2>
                    <select name="sort" onchange="this.form.submit()"
                            class="border-2 border-slate-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Sort: Featured</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                        <option value="newest" @selected(request('sort') === 'newest')>Newest</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    @forelse($products ?? [] as $product)
                        <div class="group border-2 border-slate-200 rounded-xl bg-white overflow-hidden shadow-sm hover:shadow-md hover:border-amber-200 transition-all">
                            <div class="relative bg-slate-100">
                                @if($product->image_url)
                                    <img src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}"
                                         alt="{{ $product->name }}"
                                         class="h-36 md:h-40 w-full object-cover">
                                @else
                                    <div class="h-36 md:h-40 flex items-center justify-center text-slate-400 text-xs">No image</div>
                                @endif
                                @if($product->featured ?? false)
                                    <span class="absolute top-2 left-2 rounded-full bg-amber-500 text-[10px] font-medium px-2 py-0.5 text-white">Featured</span>
                                @endif
                            </div>
                            <div class="p-3 space-y-1">
                                <p class="font-semibold text-slate-900 truncate text-[13px]">{{ $product->name }}</p>
                                <p class="text-xs text-slate-500">{{ $product->category->name ?? 'Category' }}</p>
                                <p class="font-bold text-amber-600">Ksh {{ number_format($product->price, 0) }}</p>
                                <div class="flex justify-between items-center mt-2 gap-2">
                                    <a href="{{ route('product.show', $product->slug) }}" class="text-xs font-medium text-slate-700 hover:text-amber-600">View</a>
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="text-xs font-medium text-amber-600 hover:text-amber-700">Add to cart</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-full text-sm text-slate-500">No products found.</p>
                    @endforelse
                </div>

                @if(isset($products) && $products->hasPages())
                    <div class="mt-6">{{ $products->links() }}</div>
                @endif
            </section>
        </form>
    </div>
@endsection
