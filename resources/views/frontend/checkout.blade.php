@extends('layouts.account')

@section('title', 'Checkout')
@section('page_title', 'Checkout')

@section('content')
    <div class="w-full space-y-6">

        @if($errors->any())
            <div class="mb-6 text-sm text-red-700 bg-red-100 border border-red-200 px-4 py-3 rounded-2xl">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6 items-start px-4 md:px-6">
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

                    @if(auth()->check() && $addresses->isNotEmpty())
                        <div class="mb-4">
                            <label class="block text-xs font-semibold mb-2 text-slate-700">Use Saved Address</label>
                            <select id="saved-address-select" class="border border-slate-200 rounded-lg w-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">
                                <option value="">Select a saved address or enter new one</option>
                                @foreach($addresses as $address)
                                    <option value="{{ $address->id }}" 
                                            data-name="{{ $address->name }}"
                                            data-phone="{{ $address->phone }}"
                                            data-address="{{ $address->address }}, {{ $address->city }}, {{ $address->county }}, {{ $address->country }}">
                                        {{ $address->name }} - {{ $address->city }}, {{ $address->county }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold mb-2 text-slate-700">Full Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name ?? '') }}" required
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
                        <label class="block text-xs font-semibold mb-2 text-slate-700">Phone Number *</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                               placeholder="e.g., 0712345678"
                               class="border border-slate-200 rounded-lg w-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">
                        <p class="text-xs text-slate-500 mt-1">We'll use this to contact you about your order.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-2 text-slate-700">Shipping Address *</label>
                        <textarea name="address" id="address" rows="3" required
                                  placeholder="Street address, Building name, Apartment/Unit number, City, County"
                                  class="border border-slate-200 rounded-lg w-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">{{ old('address') }}</textarea>
                        <p class="text-xs text-slate-500 mt-1">Please provide complete address details for accurate delivery.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-2 text-slate-700">Order Notes (Optional)</label>
                        <textarea name="notes" rows="2" 
                                  placeholder="Any special instructions for delivery..."
                                  class="border border-slate-200 rounded-lg w-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">{{ old('notes') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-2 text-slate-700">Coupon code (Optional)</label>
                        <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="Enter code"
                               class="border border-slate-200 rounded-lg w-full px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-400">
                        <p class="text-xs text-slate-500 mt-1">We'll apply the discount when you place the order.</p>
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

            {{-- Order summary column --}}
            <aside class="bg-white border border-slate-200 rounded-2xl p-5 text-sm shadow-sm h-full sticky top-4">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Order Summary</h2>
                
                @if(isset($cartItems) && $cartItems->count() > 0)
                    <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                        @foreach($cartItems as $item)
                            <div class="flex items-start gap-3 pb-3 border-b border-slate-100 last:border-0">
                                @if($item->product && $item->product->image_url)
                                    <img src="{{ asset('images/toys/' . $item->product->image_url) }}" 
                                         alt="{{ $item->product->name }}"
                                         class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <div class="w-16 h-16 bg-slate-100 rounded-lg flex items-center justify-center">
                                        <span class="text-xs text-slate-400">No Image</span>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-900 truncate">{{ $item->product->name ?? 'Product' }}</p>
                                    <p class="text-[11px] text-slate-500">Qty: {{ $item->quantity }}</p>
                                    <p class="text-xs font-semibold text-amber-600 mt-1">KES {{ number_format(($item->product->price ?? 0) * $item->quantity, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 border-t border-slate-200 pt-3 mt-3 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span>KES {{ number_format($subtotal ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Delivery</span>
                            <span>KES {{ number_format($shipping ?? 500, 2) }}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-slate-900 pt-2 border-t border-slate-200 mt-2 text-sm">
                            <span>Total</span>
                            <span class="text-amber-600">KES {{ number_format($total ?? 0, 2) }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-500">Your cart is empty.</p>
                @endif
            </aside>
        </div>
    </div>

    {{-- Simple inline JS for payment tab behaviour and address selection --}}
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

            // Handle saved address selection
            const addressSelect = document.getElementById('saved-address-select');
            if (addressSelect) {
                addressSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value) {
                        document.getElementById('name').value = selectedOption.dataset.name || '';
                        document.getElementById('phone').value = selectedOption.dataset.phone || '';
                        document.getElementById('address').value = selectedOption.dataset.address || '';
                    }
                });
            }
        });
    </script>
@endsection

