@extends('layouts.account')

@section('title', 'Track Order')
@section('page_title', 'Track Order #' . $order->order_number)

@section('content')
    <div class="space-y-6">
        {{-- Order Header --}}
        <div class="bg-white border-2 border-slate-200 rounded-xl p-5 md:p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 pb-5 border-b-2 border-slate-200">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-slate-900 mb-2">Order #{{ $order->order_number }}</h1>
                    <p class="text-xs md:text-sm text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 inline mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                    </p>
                </div>
                <div class="text-left md:text-right">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                        @if($order->status === 'completed') bg-emerald-100 text-emerald-700 border-2 border-emerald-200
                        @elseif($order->status === 'delivered') bg-purple-100 text-purple-700 border-2 border-purple-200
                        @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-700 border-2 border-indigo-200
                        @elseif($order->status === 'processing') bg-amber-100 text-amber-700 border-2 border-amber-200
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-700 border-2 border-red-200
                        @else bg-slate-100 text-slate-700 border-2 border-slate-200
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            {{-- Tracking Number --}}
            @if($order->tracking_number)
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 border-2 border-amber-200 rounded-xl p-5 mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg bg-amber-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-1">Tracking Number</p>
                            <p class="text-xl font-bold text-amber-900 font-mono">{{ $order->tracking_number }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-amber-600 mt-3">Use this number to track your order with the shipping carrier.</p>
                </div>
            @endif

            {{-- Order Status Timeline --}}
            <div class="mb-6">
                <h3 class="text-base font-semibold text-slate-900 mb-5">Order Status Timeline</h3>
                <div class="relative">
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-slate-200"></div>
                    <div class="space-y-6">
                        @php
                            $statuses = [
                                'pending' => ['icon' => 'fa-clock', 'label' => 'Order Placed', 'description' => 'Your order has been received and is being processed'],
                                'processing' => ['icon' => 'fa-box', 'label' => 'Processing', 'description' => 'We\'re preparing your order for shipment'],
                                'shipped' => ['icon' => 'fa-truck', 'label' => 'Shipped', 'description' => 'Your order is on the way to you'],
                                'delivered' => ['icon' => 'fa-check-circle', 'label' => 'Delivered', 'description' => 'Your order has been delivered and is ready for collection'],
                                'completed' => ['icon' => 'fa-check-double', 'label' => 'Completed', 'description' => 'Order has been collected and completed'],
                                'cancelled' => ['icon' => 'fa-times-circle', 'label' => 'Cancelled', 'description' => 'Order has been cancelled'],
                            ];
                            $currentStatusIndex = array_search($order->status, array_keys($statuses));
                        @endphp

                        @foreach($statuses as $status => $info)
                            @if($status === 'cancelled' && $order->status !== 'cancelled')
                                @continue
                            @endif
                            @php
                                $isActive = array_search($status, array_keys($statuses)) <= $currentStatusIndex;
                                $isCurrent = $status === $order->status;
                            @endphp
                            <div class="relative flex items-start gap-4">
                                <div class="relative z-10 flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all
                                        @if($isActive) 
                                            bg-amber-600 text-white border-amber-600 shadow-lg shadow-amber-500/50
                                        @else 
                                            bg-white text-slate-400 border-slate-300
                                        @endif">
                                        <i class="fas {{ $info['icon'] }} text-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-1 pt-1">
                                    <p class="text-sm font-bold @if($isActive) text-slate-900 @else text-slate-400 @endif mb-1">
                                        {{ $info['label'] }}
                                    </p>
                                    <p class="text-xs text-slate-500">{{ $info['description'] }}</p>
                                    @if($isCurrent)
                                        <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            Current Status
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="bg-white border-2 border-slate-200 rounded-xl p-5 md:p-6 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900 mb-5">Order Items</h3>
            <div class="space-y-3">
                @foreach($order->items as $item)
                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                        @if($item->product && $item->product->image_url)
                            <div class="w-20 h-20 rounded-lg overflow-hidden bg-white border-2 border-slate-200 flex-shrink-0">
                                <img src="{{ str_starts_with($item->product->image_url, 'http') ? $item->product->image_url : asset('images/toys/' . $item->product->image_url) }}" 
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-20 h-20 rounded-lg bg-slate-200 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm md:text-base font-semibold text-slate-900 mb-1">{{ $item->product->name ?? 'Product' }}</p>
                            <p class="text-xs text-slate-600">
                                Quantity: <span class="font-semibold">{{ $item->quantity }}</span>
                                <span class="mx-2">•</span>
                                Unit Price: <span class="font-semibold">Ksh {{ number_format($item->price, 0) }}</span>
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-base md:text-lg font-bold text-slate-900">
                                Ksh {{ number_format($item->price * $item->quantity, 0) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Shipping Information --}}
        <div class="bg-white border-2 border-slate-200 rounded-xl p-5 md:p-6 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Shipping & Payment Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Shipping Address</p>
                        <p class="text-sm text-slate-900">{{ $order->shipping_address }}</p>
                    </div>
                    @if($order->phone)
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Phone Number</p>
                            <p class="text-sm text-slate-900">{{ $order->phone }}</p>
                        </div>
                    @endif
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Payment Method</p>
                        <p class="text-sm text-slate-900 font-semibold">{{ strtoupper($order->payment_method ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Total Amount</p>
                        <p class="text-2xl font-bold text-amber-600">Ksh {{ number_format($order->total, 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('account.orders') }}" 
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border-2 border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Back to Orders
            </a>
            <a href="{{ route('account.orders.invoice', $order) }}" 
               target="_blank"
               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-blue-700 bg-blue-50 border-2 border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Download Invoice
            </a>
            @if(in_array($order->status, ['pending', 'processing']))
                <form action="{{ route('account.orders.cancel', $order) }}" 
                      method="POST" 
                      class="inline"
                      data-confirm="Are you sure you want to cancel this order?">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-red-700 bg-red-50 border-2 border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                        Cancel Order
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
