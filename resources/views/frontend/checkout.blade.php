@extends('layouts.app')

@section('title', 'Checkout - BrightToys')

@section('content')
    {{-- Hero section --}}
    <section class="relative bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 overflow-hidden py-8">
        {{-- Background image with overlay - Bright colorful toys --}}
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20" 
             style="background-image: url('https://images.pexels.com/photos/160715/pexels-photo-160715.jpeg?auto=compress&cs=tinysrgb&w=1920');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-br from-pink-100/80 via-amber-50/80 to-sky-100/80"></div>
        
        <div class="container mx-auto px-4 lg:px-8 text-center relative z-10">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-2">Complete Your Order</h1>
            <p class="text-sm text-slate-600">Just a few details to get your toys on their way!</p>
        </div>
    </section>

    <div class="container mx-auto px-4 lg:px-8 py-8 max-w-3xl">

        @if($errors->any())
            <div class="mb-4 text-sm text-red-700 bg-red-100 border border-red-200 px-4 py-2 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" class="space-y-5 bg-white border border-slate-200 rounded-2xl p-6 md:p-8 text-sm shadow-sm">
            @csrf
            
            <div class="border-b border-slate-100 pb-4 mb-4">
                <h2 class="text-lg font-bold text-slate-900 mb-1">Delivery Information</h2>
                <p class="text-xs text-slate-500">Where should we send your toys?</p>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-2 text-slate-700">Full Name *</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required
                       placeholder="Enter your full name"
                       class="border border-slate-200 rounded-lg w-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-2 text-slate-700">Email Address *</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required
                       placeholder="your.email@example.com"
                       class="border border-slate-200 rounded-lg w-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-2 text-slate-700">Shipping Address *</label>
                <textarea name="address" rows="4" required
                          placeholder="Street address, Building name, Apartment/Unit number, City, County"
                          class="border border-slate-200 rounded-lg w-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">{{ old('address') }}</textarea>
                <p class="text-xs text-slate-500 mt-1">Please provide complete address details for accurate delivery.</p>
            </div>

            <div class="border-t border-slate-100 pt-4 mt-4">
                <h2 class="text-lg font-bold text-slate-900 mb-1">Payment Method</h2>
                <p class="text-xs text-slate-500 mb-4">Choose how you'd like to pay</p>
                
                <div class="border border-slate-200 rounded-lg p-4 bg-slate-50">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="payment_method" value="cod" checked
                               class="w-4 h-4 text-amber-500 focus:ring-amber-500">
                        <div>
                            <p class="font-semibold text-slate-900">Cash on Delivery</p>
                            <p class="text-xs text-slate-500">Pay when you receive your order</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-3.5 rounded-lg shadow-sm shadow-amber-500/30 transition-colors text-base">
                    Place Order & Complete Purchase
                </button>
                <p class="text-xs text-slate-500 text-center mt-3">
                    By placing this order, you agree to our <a href="{{ route('pages.policies') }}" class="text-amber-600 hover:underline">Terms & Conditions</a>
                </p>
            </div>
        </form>
    </div>
@endsection

