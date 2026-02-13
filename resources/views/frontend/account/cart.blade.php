@extends('layouts.account')

@section('title', 'My Cart')
@section('page_title', 'Shopping Cart')

@section('content')
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 tracking-tight">Shopping Cart</h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1">Review your items before checkout</p>
        </div>

        @if($items->isEmpty())
            <div class="bg-white border-2 border-dashed border-slate-300 rounded-xl p-12 text-center">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 md:h-20 md:w-20 text-slate-300 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Your cart is empty</h3>
                <p class="text-sm text-slate-500 mb-6 max-w-md mx-auto">Start adding products to your cart to see them here!</p>
                <a href="{{ route('shop.index') }}" 
                   class="inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold px-6 py-3 rounded-lg shadow-lg shadow-amber-500/30 transition-all hover:shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Continue Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Cart Items --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($items as $item)
                        <div class="bg-white border-2 border-slate-200 rounded-xl p-5 shadow-sm hover:shadow-lg transition-all">
                            <div class="flex flex-col md:flex-row gap-4">
                                {{-- Product Image --}}
                                <div class="w-full md:w-32 h-32 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0 border-2 border-slate-200">
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
                                    <h3 class="text-lg font-bold text-slate-900 mb-2">
                                        <a href="{{ route('product.show', $item->product->slug ?? '#') }}" 
                                           class="hover:text-amber-600 transition-colors">
                                            {{ $item->product->name ?? 'Product' }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-slate-600 mb-3">
                                        <span class="font-semibold">Ksh {{ number_format($item->product->price ?? 0, 0) }}</span> each
                                    </p>
                                    
                                    {{-- Quantity Controls --}}
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            <label class="text-xs font-semibold text-slate-700">Quantity:</label>
                                            <input type="number" 
                                                   name="quantity" 
                                                   value="{{ $item->quantity }}"
                                                   min="1" 
                                                   max="{{ $item->product->stock ?? 100 }}"
                                                   class="w-20 border-2 border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-amber-700 bg-amber-50 border-2 border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M20 6L9 17l-5-5"/>
                                                </svg>
                                                Update
                                            </button>
                                        </form>

                                        <div class="flex items-center gap-4">
                                            <p class="text-base font-bold text-slate-900">
                                                Ksh {{ number_format(($item->product->price ?? 0) * $item->quantity, 0) }}
                                            </p>
                                            <form action="{{ route('cart.remove', $item->id) }}" 
                                                  method="POST"
                                                  data-confirm="Remove this item from cart?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-red-700 bg-red-50 border-2 border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white border-2 border-slate-200 rounded-xl p-5 md:p-6 shadow-sm sticky top-6">
                        <h2 class="text-lg font-bold text-slate-900 mb-4">Order Summary</h2>
                        
                        <div class="space-y-3 mb-5">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-700">Subtotal</span>
                                <span class="text-base font-semibold text-slate-900">Ksh {{ number_format($total, 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs text-slate-500">
                                <span>Shipping</span>
                                <span>Calculated at checkout</span>
                            </div>
                            <div class="flex justify-between items-center text-xs text-slate-500">
                                <span>Taxes</span>
                                <span>Calculated at checkout</span>
                            </div>
                            <hr class="border-slate-200">
                            <div class="flex justify-between items-center">
                                <span class="text-base font-bold text-slate-900">Total</span>
                                <span class="text-xl font-bold text-amber-600">Ksh {{ number_format($total, 0) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('checkout.index') }}"
                           class="block w-full text-center bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-5 py-3 rounded-lg shadow-md hover:shadow-lg transition-all">
                            <span class="inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Proceed to Checkout
                            </span>
                        </a>

                        <a href="{{ route('shop.index') }}"
                           class="block w-full text-center mt-3 text-sm text-slate-600 hover:text-slate-900 font-medium">
                            ← Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
