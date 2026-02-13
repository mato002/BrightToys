@extends('layouts.account')

@section('title', 'My Wishlist')
@section('page_title', 'My Wishlist')

@section('content')
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">My Wishlist</h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Products you've saved for later</p>
        </div>

        @forelse($wishlistItems as $item)
            <div class="bg-white border-2 border-slate-200 rounded-xl p-5 md:p-6 shadow-sm hover:shadow-lg transition-all">
                <div class="flex flex-col md:flex-row gap-5">
                    {{-- Product Image --}}
                    <div class="w-full md:w-40 h-40 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0 border-2 border-slate-200">
                        @if($item->product && $item->product->image_url)
                            <img src="{{ str_starts_with($item->product->image_url, 'http') ? $item->product->image_url : asset('images/toys/' . $item->product->image_url) }}" 
                                 alt="{{ $item->product->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-slate-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Product Details --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                            <a href="{{ route('product.show', $item->product->slug ?? '#') }}" 
                               class="hover:text-amber-600 transition-colors">
                                {{ $item->product->name ?? 'Product' }}
                            </a>
                        </h3>
                        <p class="text-2xl font-bold text-amber-600 mb-3">
                            Ksh {{ number_format($item->product->price ?? 0, 0) }}
                        </p>
                        @if($item->product && $item->product->description)
                            <p class="text-sm text-slate-600 mb-4 line-clamp-2">
                                {{ Str::limit($item->product->description, 120) }}
                            </p>
                        @endif
                        
                        {{-- Stock Status --}}
                        @if($item->product)
                            <div class="mb-4">
                                @if($item->product->status === 'active' && $item->product->stock > 0)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        In Stock ({{ $item->product->stock }} available)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="15" y1="9" x2="9" y2="15"/>
                                            <line x1="9" y1="9" x2="15" y2="15"/>
                                        </svg>
                                        Out of Stock
                                    </span>
                                @endif
                            </div>
                        @endif
                        
                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap gap-2">
                            @if($item->product && $item->product->status === 'active' && $item->product->stock > 0)
                                <form action="{{ route('cart.add', $item->product->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" 
                                            class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="9" cy="21" r="1"/>
                                            <circle cx="20" cy="21" r="1"/>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                        </svg>
                                        Add to Cart
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('wishlist.remove', $item->product->id) }}" 
                                  method="POST" 
                                  class="inline"
                                  data-confirm="Remove from wishlist?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-red-700 bg-red-50 border-2 border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 6L9 17l-5-5"/>
                                    </svg>
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border-2 border-dashed border-slate-300 rounded-xl p-12 text-center">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 md:h-20 md:w-20 text-slate-300 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Your wishlist is empty</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-md mx-auto">Start adding products you love to your wishlist for easy access later!</p>
                <a href="{{ route('shop.index') }}" 
                   class="inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow-lg shadow-amber-500/30 transition-all hover:shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Browse Store
                </a>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($wishlistItems->hasPages())
            <div class="mt-6">
                {{ $wishlistItems->links() }}
            </div>
        @endif
    </div>
@endsection
