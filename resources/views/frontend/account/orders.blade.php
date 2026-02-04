@extends('layouts.account')

@section('title', 'My Orders')
@section('page_title', 'My Orders')

@section('content')
    <div class="space-y-4">
            <div class="bg-white border rounded-lg p-5">
                <h1 class="text-lg font-semibold mb-4">My Orders</h1>

                <form method="GET" class="mb-4 grid md:grid-cols-4 gap-3 text-xs">
                    <div>
                        <label class="block text-[11px] font-semibold mb-1 text-gray-600">Status</label>
                        <select name="status"
                                class="border rounded px-3 py-1.5 text-xs w-full">
                            <option value="">All statuses</option>
                            @foreach(['pending','processing','shipped','completed','cancelled'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1 text-gray-600">From date</label>
                        <input type="date"
                               name="from_date"
                               value="{{ request('from_date') }}"
                               class="border rounded px-3 py-1.5 text-xs w-full">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1 text-gray-600">To date</label>
                        <input type="date"
                               name="to_date"
                               value="{{ request('to_date') }}"
                               class="border rounded px-3 py-1.5 text-xs w-full">
                    </div>
                    <div class="flex items-end gap-2">
                        <button class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-1.5 rounded">
                            Filter
                        </button>
                        @if(request()->hasAny(['status','from_date','to_date']) && (request('status') || request('from_date') || request('to_date')))
                            <a href="{{ route('account.orders') }}"
                               class="text-xs text-gray-500 hover:text-gray-700">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>

                @forelse($orders as $order)
                    <div class="border border-slate-200 rounded-xl mb-4 p-5 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-3 pb-4 border-b border-slate-100">
                            <div>
                                <p class="font-bold text-slate-900 text-base">Order #{{ $order->id }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-amber-600 text-lg">
                                    Ksh {{ number_format($order->total, 0) }}
                                </p>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium mt-1
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

                        <div class="space-y-2">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between py-2 text-sm">
                                    <div class="flex items-center gap-3">
                                        @if($item->product && $item->product->image_url)
                                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0">
                                                <img src="{{ str_starts_with($item->product->image_url, 'http') ? $item->product->image_url : asset('images/toys/' . $item->product->image_url) }}" 
                                                     alt="{{ $item->product->name }}"
                                                     class="w-full h-full object-cover">
                                            </div>
                                        @endif
                                        <span class="text-slate-700">
                                            <span class="font-medium">{{ $item->product->name ?? 'Product' }}</span>
                                            <span class="text-slate-500"> × {{ $item->quantity }}</span>
                                        </span>
                                    </div>
                                    <span class="font-semibold text-slate-900">Ksh {{ number_format($item->price * $item->quantity, 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white border border-slate-200 rounded-xl">
                        <p class="text-4xl mb-3">📦</p>
                        <p class="text-base font-semibold text-slate-900 mb-2">No orders yet</p>
                        <p class="text-sm text-slate-500 mb-4">Start shopping to see your orders here!</p>
                        <a href="{{ route('shop.index') }}" 
                           class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                            Browse Toys
                        </a>
                    </div>
                @endforelse

                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            </div>
    </div>
@endsection

