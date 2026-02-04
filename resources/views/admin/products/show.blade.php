@extends('layouts.admin')

@section('page_title', $product->name)

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg font-semibold">{{ $product->name }}</h1>
            <p class="text-xs text-slate-500">Detailed view of this product.</p>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <a href="{{ route('admin.products.edit', $product) }}"
               class="inline-flex items-center px-3 py-1.5 rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50">
                Edit Product
            </a>
            <a href="{{ route('admin.products.index') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-50">
                Back to list
            </a>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6 text-sm">
        <div class="md:col-span-2 bg-white border border-slate-100 rounded-xl p-4 shadow-sm space-y-4">
            @if($product->image_url)
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide block mb-2">Product Image</span>
                    <div class="w-full max-w-md rounded-lg overflow-hidden border border-slate-200">
                        <img src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}" 
                             alt="{{ $product->name }}"
                             class="w-full h-auto object-cover"
                             onerror="this.parentElement.innerHTML='<div class=\'w-full h-64 bg-slate-100 flex items-center justify-center text-slate-400 text-sm\'>Image not found</div>'">
                    </div>
                </div>
            @endif

            <div class="space-y-3">
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide">Name</span><br>
                    <span class="font-semibold text-slate-900 text-base">{{ $product->name }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide">SKU</span><br>
                    <span class="text-slate-800 text-sm">{{ $product->sku ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide">Slug</span><br>
                    <span class="text-slate-800 text-sm font-mono">{{ $product->slug ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide">Category</span><br>
                    <span class="text-slate-800 text-sm">{{ $product->category->name ?? 'Uncategorized' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide">Image URL</span><br>
                    <span class="text-slate-800 text-sm font-mono">{{ $product->image_url ?? 'No image' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide">Description</span><br>
                    <span class="text-slate-700 text-sm leading-relaxed">{{ $product->description ?: 'No description provided.' }}</span>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm space-y-3">
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide">Price</span><br>
                    <span class="text-lg font-semibold text-slate-900">
                        Ksh {{ number_format($product->price, 0) }}
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide">Stock</span><br>
                    <span class="text-sm font-semibold {{ $product->stock > 10 ? 'text-emerald-600' : ($product->stock > 0 ? 'text-amber-600' : 'text-red-600') }}">
                        {{ $product->stock }} units
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide">Status</span><br>
                    @if($product->status === 'active')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[11px]">
                            Active
                        </span>
                    @elseif($product->status === 'inactive')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-50 text-slate-700 border border-slate-100 text-[11px]">
                            Inactive
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-50 text-slate-600 border border-slate-100 text-[11px]">
                            Not Set
                        </span>
                    @endif
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase tracking-wide">Featured</span><br>
                    @if($product->featured)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-100 text-[11px]">
                            Featured
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-50 text-slate-600 border border-slate-100 text-[11px]">
                            Standard
                        </span>
                    @endif
                </div>
                <div class="pt-2 border-t border-slate-100">
                    <p class="text-[11px] text-slate-500">
                        <span class="block">Created: {{ $product->created_at->format('M d, Y H:i') }}</span>
                        <span class="block mt-1">Updated: {{ $product->updated_at->format('M d, Y H:i') }}</span>
                    </p>
                </div>
            </div>

            <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm text-xs">
                <h2 class="text-sm font-semibold text-slate-900 mb-2">Recent Orders with this product</h2>
                @php
                    $items = $product->orderItems->take(5);
                @endphp
                @forelse($items as $item)
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-50 last:border-0">
                        <div>
                            <p class="text-slate-700">Order #{{ $item->order_id }}</p>
                            <p class="text-[11px] text-slate-500">
                                Qty {{ $item->quantity }} · Ksh {{ number_format($item->price * $item->quantity, 0) }}
                            </p>
                        </div>
                        <a href="{{ route('admin.orders.show', $item->order) }}"
                           class="text-[11px] text-amber-600 hover:underline">
                            View
                        </a>
                    </div>
                @empty
                    <p class="text-[11px] text-slate-500">No orders yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

