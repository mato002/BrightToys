@extends('layouts.app')

@section('title', 'Cart - BrightToys')

@section('content')
    <div class="container mx-auto px-4 lg:px-8 py-8">
        <h1 class="text-xl font-semibold mb-1 text-slate-900">Your cart</h1>
        <p class="text-xs text-slate-500 mb-4">Review your items before you check out.</p>

        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-200 px-4 py-2 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 text-sm text-red-700 bg-red-100 border border-red-200 px-4 py-2 rounded">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($items->isEmpty())
            <p class="text-sm text-slate-600">Your cart is empty.</p>
            <a href="{{ route('shop.index') }}" class="mt-3 inline-flex items-center text-amber-600 text-sm hover:text-amber-700 hover:underline font-medium">
                Continue shopping
            </a>
        @else
            <div class="grid md:grid-cols-3 gap-6">
                <div class="md:col-span-2 space-y-4">
                    @foreach($items as $item)
                        <div class="flex items-center justify-between border border-slate-200 rounded-xl p-3.5 bg-white shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3">
                                <div class="w-20 h-20 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0">
                                    @if($item->product && $item->product->image_url)
                                        <img src="{{ str_starts_with($item->product->image_url, 'http') ? $item->product->image_url : asset('images/toys/' . $item->product->image_url) }}" 
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-[10px] text-slate-400">
                                            Toy
                                        </div>
                                    @endif
                                </div>
                                <div class="text-sm">
                                    <p class="font-medium text-slate-900">{{ $item->product->name ?? 'Product' }}</p>
                                    <p class="text-xs text-slate-500">
                                        Ksh {{ number_format($item->product->price ?? 0, 0) }} each
                                    </p>
                                    <p class="text-xs text-amber-600 font-medium mt-1">
                                        Qty: {{ $item->quantity }} × Ksh {{ number_format($item->product->price ?? 0, 0) }} = Ksh {{ number_format(($item->product->price ?? 0) * $item->quantity, 0) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-xs">
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-1">
                                    @csrf
                                    <input type="number" name="quantity" value="{{ $item->quantity }}"
                                           min="1" class="border border-slate-200 rounded-md px-2 py-1 w-16 text-[11px] focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                                    <button class="text-slate-700 hover:text-amber-600 font-medium">Update</button>
                                </form>

                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:text-red-600 font-medium">Remove</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border border-slate-200 rounded-2xl bg-white p-4 text-sm h-fit shadow-sm">
                    <h2 class="font-semibold mb-1 text-slate-900">Order summary</h2>
                    <p class="text-[11px] text-slate-500 mb-3">Shipping and taxes are calculated at checkout.</p>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-slate-700">Subtotal</span>
                        <span class="text-sm font-medium text-slate-900">Ksh {{ number_format($total, 0) }}</span>
                    </div>
                    <div class="flex justify-between mb-2 text-xs text-slate-500">
                        <span>Shipping</span>
                        <span>Calculated at checkout</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between font-semibold mb-4">
                        <span class="text-slate-900">Total</span>
                        <span class="text-slate-900">Ksh {{ number_format($total, 0) }}</span>
                    </div>
                    <a href="{{ route('checkout.index') }}"
                       class="block text-center bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

