@extends('layouts.account')

@section('title', 'My Wishlist')
@section('page_title', 'My Wishlist')

@section('content')
    <div class="space-y-4">
        <div class="bg-white border rounded-lg p-5">
            <h1 class="text-lg font-semibold mb-4">My Wishlist</h1>

            @if(session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @forelse($wishlistItems as $item)
                <div class="border border-slate-200 rounded-xl mb-4 p-5 bg-white shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-32 h-32 bg-slate-100 rounded-lg overflow-hidden flex-shrink-0">
                            @if($item->product && $item->product->image_url)
                                <img src="{{ str_starts_with($item->product->image_url, 'http') ? $item->product->image_url : asset('images/toys/' . $item->product->image_url) }}" 
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-xs text-slate-400">
                                    No Image
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="font-semibold text-slate-900 mb-2">
                                <a href="{{ route('product.show', $item->product->slug ?? '#') }}" class="hover:text-amber-600">
                                    {{ $item->product->name ?? 'Product' }}
                                </a>
                            </h3>
                            <p class="text-amber-600 font-bold text-lg mb-2">
                                KES {{ number_format($item->product->price ?? 0, 2) }}
                            </p>
                            @if($item->product && $item->product->description)
                                <p class="text-sm text-slate-600 mb-4 line-clamp-2">
                                    {{ Str::limit($item->product->description, 100) }}
                                </p>
                            @endif
                            
                            <div class="flex gap-2 flex-wrap">
                                @if($item->product && $item->product->status === 'active' && $item->product->stock > 0)
                                    <form action="{{ route('cart.add', $item->product->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                                            Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-500 px-4 py-2 border border-slate-200 rounded-lg">
                                        Out of Stock
                                    </span>
                                @endif
                                
                                <form action="{{ route('wishlist.remove', $item->product->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove from wishlist?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-semibold px-4 py-2 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-white border border-slate-200 rounded-xl">
                    <p class="text-4xl mb-3">❤️</p>
                    <p class="text-base font-semibold text-slate-900 mb-2">Your wishlist is empty</p>
                    <p class="text-sm text-slate-500 mb-4">Start adding products you love!</p>
                    <a href="{{ route('shop.index') }}" 
                       class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                        Browse Toys
                    </a>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $wishlistItems->links() }}
            </div>
        </div>
    </div>
@endsection
