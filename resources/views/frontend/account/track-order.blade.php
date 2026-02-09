@extends('layouts.account')

@section('title', 'Track Order')
@section('page_title', 'Track Order #' . $order->order_number)

@section('content')
    <div class="space-y-6">
        <div class="bg-white border rounded-lg p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Order #{{ $order->order_number }}</h2>
                    <p class="text-xs text-slate-500 mt-1">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        @if($order->status === 'completed') bg-emerald-100 text-emerald-700
                        @elseif($order->status === 'shipped') bg-blue-100 text-blue-700
                        @elseif($order->status === 'processing') bg-amber-100 text-amber-700
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                        @else bg-slate-100 text-slate-700
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            @if($order->tracking_number)
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <p class="text-sm font-semibold text-amber-900 mb-1">Tracking Number</p>
                    <p class="text-lg font-bold text-amber-700">{{ $order->tracking_number }}</p>
                    <p class="text-xs text-amber-600 mt-2">Use this number to track your order with the shipping carrier.</p>
                </div>
            @endif

            {{-- Order Status Timeline --}}
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Order Status</h3>
                <div class="relative">
                    <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>
                    <div class="space-y-4">
                        @php
                            $statuses = [
                                'pending' => ['icon' => '⏳', 'label' => 'Order Placed', 'description' => 'Your order has been received'],
                                'processing' => ['icon' => '📦', 'label' => 'Processing', 'description' => 'We\'re preparing your order'],
                                'shipped' => ['icon' => '🚚', 'label' => 'Shipped', 'description' => 'Your order is on the way'],
                                'completed' => ['icon' => '✅', 'label' => 'Delivered', 'description' => 'Order has been delivered'],
                                'cancelled' => ['icon' => '❌', 'label' => 'Cancelled', 'description' => 'Order has been cancelled'],
                            ];
                            $currentStatusIndex = array_search($order->status, array_keys($statuses));
                        @endphp

                        @foreach($statuses as $status => $info)
                            @if($status === 'cancelled' && $order->status !== 'cancelled')
                                @continue
                            @endif
                            @php
                                $isActive = array_search($status, array_keys($statuses)) <= $currentStatusIndex;
                            @endphp
                            <div class="relative flex items-start gap-4">
                                <div class="relative z-10 flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm
                                        @if($isActive) bg-amber-500 text-white @else bg-slate-200 text-slate-400 @endif">
                                        {{ $info['icon'] }}
                                    </div>
                                </div>
                                <div class="flex-1 pt-1">
                                    <p class="text-sm font-semibold @if($isActive) text-slate-900 @else text-slate-400 @endif">
                                        {{ $info['label'] }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $info['description'] }}</p>
                                    @if($isActive && $status === $order->status)
                                        <p class="text-xs text-amber-600 mt-1 font-medium">Current Status</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-4">Order Items</h3>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-lg">
                            @if($item->product && $item->product->image_url)
                                <img src="{{ str_starts_with($item->product->image_url, 'http') ? $item->product->image_url : asset('images/toys/' . $item->product->image_url) }}" 
                                     alt="{{ $item->product->name }}"
                                     class="w-16 h-16 object-cover rounded-lg">
                            @endif
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-900">{{ $item->product->name ?? 'Product' }}</p>
                                <p class="text-xs text-slate-500">Quantity: {{ $item->quantity }} × KES {{ number_format($item->price, 2) }}</p>
                            </div>
                            <p class="text-sm font-semibold text-slate-900">KES {{ number_format($item->price * $item->quantity, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Shipping Information --}}
            <div class="bg-slate-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Shipping Information</h3>
                <div class="space-y-2 text-sm">
                    <p class="text-slate-700"><strong>Address:</strong> {{ $order->shipping_address }}</p>
                    @if($order->phone)
                        <p class="text-slate-700"><strong>Phone:</strong> {{ $order->phone }}</p>
                    @endif
                    <p class="text-slate-700"><strong>Payment Method:</strong> {{ strtoupper($order->payment_method ?? 'N/A') }}</p>
                    <p class="text-slate-700"><strong>Total Amount:</strong> <span class="text-amber-600 font-semibold">KES {{ number_format($order->total, 2) }}</span></p>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('account.orders') }}" class="text-sm text-slate-600 hover:text-slate-900 font-semibold px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                    Back to Orders
                </a>
                @if(in_array($order->status, ['pending', 'processing']))
                    <form action="{{ route('account.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-semibold px-4 py-2 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                            Cancel Order
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
