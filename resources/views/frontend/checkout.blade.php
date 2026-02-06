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

    <div class="container mx-auto px-4 lg:px-8 py-8">

        @if($errors->any())
            <div class="mb-6 text-sm text-red-700 bg-red-100 border border-red-200 px-4 py-3 rounded-2xl">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6 items-start">
            {{-- Main checkout column --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Step indicator --}}
                <div class="flex items-center justify-between text-xs font-medium text-slate-500 mb-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500 text-white text-[11px]">1</span>
                        <span>Delivery Details</span>
                    </div>
                    <div class="flex-1 h-px bg-slate-200 mx-4 hidden md:block"></div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-200 text-slate-600 text-[11px]">2</span>
                        <span>Payment</span>
                    </div>
                </div>

                <form action="{{ route('checkout.store') }}" method="POST" class="space-y-6 bg-white border border-slate-200 rounded-2xl p-6 md:p-8 text-sm shadow-sm">
                    @csrf
                    
                    <div class="border-b border-slate-100 pb-4 mb-4">
                        <h2 class="text-lg font-bold text-slate-900 mb-1">Delivery Information</h2>
                        <p class="text-xs text-slate-500">Where should we send your toys?</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
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
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-2 text-slate-700">Shipping Address *</label>
                        <textarea name="address" rows="3" required
                                  placeholder="Street address, Building name, Apartment/Unit number, City, County"
                                  class="border border-slate-200 rounded-lg w-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">{{ old('address') }}</textarea>
                        <p class="text-xs text-slate-500 mt-1">Please provide complete address details for accurate delivery.</p>
                    </div>

                    {{-- Payment section --}}
                    <div class="border-t border-slate-100 pt-4 mt-4">
                        <h2 class="text-lg font-bold text-slate-900 mb-1">Payment Method</h2>
                        <p class="text-xs text-slate-500 mb-4">Choose how you'd like to pay</p>

                        <div class="grid md:grid-cols-2 gap-3 mb-4">
                            {{-- Mpesa (STK / Sim toolkit) --}}
                            <button type="button"
                                    data-payment-tab="mpesa"
                                    class="payment-tab flex items-center gap-3 border border-slate-200 rounded-xl px-4 py-3 text-left bg-white hover:border-amber-500 hover:bg-amber-50 transition-colors {{ old('payment_method', 'mpesa') === 'mpesa' ? 'ring-2 ring-amber-500 border-amber-500 bg-amber-50' : '' }}">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">
                                    M
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-slate-900">Mpesa</p>
                                    <p class="text-[11px] text-slate-500">Pay via Mpesa STK push</p>
                                </div>
                            </button>

                            {{-- Paybill --}}
                            <button type="button"
                                    data-payment-tab="paybill"
                                    class="payment-tab flex items-center gap-3 border border-slate-200 rounded-xl px-4 py-3 text-left bg-white hover:border-amber-500 hover:bg-amber-50 transition-colors {{ old('payment_method') === 'paybill' ? 'ring-2 ring-amber-500 border-amber-500 bg-amber-50' : '' }}">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-sky-100 text-sky-700 text-xs font-bold">
                                    P
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-slate-900">Paybill</p>
                                    <p class="text-[11px] text-slate-500">Use our business paybill number</p>
                                </div>
                            </button>

                            {{-- Card --}}
                            <button type="button"
                                    data-payment-tab="card"
                                    class="payment-tab flex items-center gap-3 border border-slate-200 rounded-xl px-4 py-3 text-left bg-white hover:border-amber-500 hover:bg-amber-50 transition-colors {{ old('payment_method') === 'card' ? 'ring-2 ring-amber-500 border-amber-500 bg-amber-50' : '' }}">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">
                                    💳
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-slate-900">Card</p>
                                    <p class="text-[11px] text-slate-500">Pay securely with your card</p>
                                </div>
                            </button>

                            {{-- Cash on Delivery --}}
                            <button type="button"
                                    data-payment-tab="cod"
                                    class="payment-tab flex items-center gap-3 border border-slate-200 rounded-xl px-4 py-3 text-left bg-white hover:border-amber-500 hover:bg-amber-50 transition-colors {{ old('payment_method') === 'cod' ? 'ring-2 ring-amber-500 border-amber-500 bg-amber-50' : '' }}">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                    💵
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-slate-900">Cash on Delivery</p>
                                    <p class="text-[11px] text-slate-500">Pay when the toys arrive</p>
                                </div>
                            </button>
                        </div>

                        {{-- Hidden field that will be updated by JS --}}
                        <input type="hidden" name="payment_method" id="payment_method_input" value="{{ old('payment_method', 'mpesa') }}">

                        {{-- Payment method detail panels (re-usable partials) --}}
                        <div class="space-y-4">
                            <div data-payment-panel="mpesa" class="payment-panel border border-emerald-100 bg-emerald-50/60 rounded-xl p-4 text-xs {{ old('payment_method', 'mpesa') === 'mpesa' ? '' : 'hidden' }}">
                                @include('frontend.partials.payments.mpesa')
                            </div>

                            <div data-payment-panel="paybill" class="payment-panel border border-sky-100 bg-sky-50/60 rounded-xl p-4 text-xs {{ old('payment_method') === 'paybill' ? '' : 'hidden' }}">
                                @include('frontend.partials.payments.paybill')
                            </div>

                            <div data-payment-panel="card" class="payment-panel border border-indigo-100 bg-indigo-50/60 rounded-xl p-4 text-xs {{ old('payment_method') === 'card' ? '' : 'hidden' }}">
                                @include('frontend.partials.payments.card')
                            </div>

                            <div data-payment-panel="cod" class="payment-panel border border-slate-100 bg-slate-50 rounded-xl p-4 text-xs {{ old('payment_method') === 'cod' ? '' : 'hidden' }}">
                                <p class="font-semibold text-slate-900 mb-1">Cash on Delivery</p>
                                <p class="text-[11px] text-slate-600 mb-1">You will pay the rider when your order is delivered.</p>
                                <p class="text-[11px] text-slate-500">Please ensure your phone is reachable for delivery confirmation.</p>
                            </div>
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

            {{-- Order summary column (placeholder for now) --}}
            <aside class="bg-white border border-slate-200 rounded-2xl p-5 text-sm shadow-sm h-full">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Order Summary</h2>
                <p class="text-xs text-slate-500 mb-3">A quick overview of the toys in your cart.</p>

                {{-- TODO: replace with real cart summary if available --}}
                <div class="space-y-2 border-t border-slate-100 pt-3 mt-3 text-xs text-slate-600">
                    <div class="flex justify-between">
                        <span>Items total</span>
                        <span>—</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Delivery</span>
                        <span>—</span>
                    </div>
                    <div class="flex justify-between font-semibold text-slate-900 pt-2 border-t border-dashed border-slate-200 mt-2">
                        <span>Amount due</span>
                        <span>—</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-3">
                        Exact totals will be shown once cart integration is complete.
                    </p>
                </div>
            </aside>
        </div>
    </div>

    {{-- Simple inline JS for payment tab behaviour (no framework dependency) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.payment-tab');
            const panels = document.querySelectorAll('.payment-panel');
            const methodInput = document.getElementById('payment_method_input');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const method = tab.getAttribute('data-payment-tab');
                    methodInput.value = method;

                    tabs.forEach(t => t.classList.remove('ring-2', 'ring-amber-500', 'border-amber-500', 'bg-amber-50'));
                    tab.classList.add('ring-2', 'ring-amber-500', 'border-amber-500', 'bg-amber-50');

                    panels.forEach(panel => {
                        panel.classList.toggle('hidden', panel.getAttribute('data-payment-panel') !== method);
                    });
                });
            });
        });
    </script>
@endsection

