@extends('layouts.admin')

@section('page_title', 'Order #'.$order->id)

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">Order #{{ $order->id }}</h1>
            <p class="text-xs text-slate-500">Order details and management.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('admin.orders.index') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50">
                Back to list
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid md:grid-cols-3 gap-6 text-sm">
        <div class="md:col-span-2 bg-white border border-slate-100 rounded-xl p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-900 mb-4">Order Items</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-slate-50 text-[11px] text-slate-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-3 py-2 text-left">Product</th>
                        <th class="px-3 py-2 text-right">Quantity</th>
                        <th class="px-3 py-2 text-right">Unit Price</th>
                        <th class="px-3 py-2 text-right">Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-t border-slate-100">
                            <td class="px-3 py-2">
                                <div class="text-xs font-medium text-slate-900">{{ $item->product->name ?? 'Product Deleted' }}</div>
                                @if($item->product && $item->product->sku)
                                    <div class="text-[10px] text-slate-500">SKU: {{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right text-xs text-slate-700">{{ $item->quantity }}</td>
                            <td class="px-3 py-2 text-right text-xs text-slate-700">Ksh {{ number_format($item->price, 0) }}</td>
                            <td class="px-3 py-2 text-right text-xs font-semibold text-slate-900">
                                Ksh {{ number_format($item->price * $item->quantity, 0) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50">
                    <tr>
                        <td colspan="3" class="px-3 py-2 text-right text-xs font-semibold text-slate-700">Total:</td>
                        <td class="px-3 py-2 text-right text-sm font-bold text-slate-900">
                            Ksh {{ number_format($order->total, 0) }}
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm space-y-3">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">Order Information</h2>
                
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Customer</span>
                    @if($order->user)
                        <a href="{{ route('admin.users.show', $order->user) }}"
                           class="text-xs font-medium text-amber-600 hover:text-amber-700 hover:underline">
                            {{ $order->user->name }}
                        </a>
                        <div class="text-[10px] text-slate-500 mt-0.5">{{ $order->user->email }}</div>
                    @else
                        <span class="text-xs text-slate-600">Guest</span>
                    @endif
                </div>

                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Status</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium
                        @class([
                            'bg-amber-50 text-amber-700 border border-amber-100' => $order->status === 'pending',
                            'bg-blue-50 text-blue-700 border border-blue-100' => $order->status === 'processing',
                            'bg-indigo-50 text-indigo-700 border border-indigo-100' => $order->status === 'shipped',
                            'bg-purple-50 text-purple-700 border border-purple-100' => $order->status === 'delivered',
                            'bg-emerald-50 text-emerald-700 border border-emerald-100' => $order->status === 'completed',
                            'bg-red-50 text-red-700 border border-red-100' => $order->status === 'cancelled',
                        ])">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Total Amount</span>
                    <span class="text-base font-bold text-slate-900">Ksh {{ number_format($order->total, 0) }}</span>
                </div>

                @if($order->payment_method)
                    <div>
                        <span class="text-xs text-slate-500 uppercase tracking-wide block mb-1">Payment Method</span>
                        <span class="text-xs text-slate-700">{{ ucfirst($order->payment_method) }}</span>
                    </div>
                @endif

                <div class="pt-2 border-t border-slate-100">
                    <p class="text-[11px] text-slate-500">
                        <span class="block">Created: {{ $order->created_at->format('M d, Y H:i') }}</span>
                        <span class="block mt-1">Updated: {{ $order->updated_at->format('M d, Y H:i') }}</span>
                    </p>
                </div>
            </div>

            @if($order->shipping_address)
                <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm">
                    <h3 class="text-xs font-semibold text-slate-900 mb-2">Shipping Address</h3>
                    <p class="text-xs text-slate-700 whitespace-pre-line leading-relaxed">{{ $order->shipping_address }}</p>
                </div>
            @endif

            <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm">
                <h3 class="text-xs font-semibold text-slate-900 mb-3">Update Status</h3>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <select name="status"
                            class="w-full border border-slate-200 rounded px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-400">
                        @foreach(['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected($order->status === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                            class="w-full bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2 rounded shadow-sm">
                        Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
