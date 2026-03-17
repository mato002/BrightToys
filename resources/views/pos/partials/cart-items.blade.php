@if(!empty($cart['items']))
    <ul class="space-y-2">
        @foreach($cart['items'] as $item)
            <li class="flex items-center gap-2 text-sm border-b border-slate-100 pb-2" data-product-id="{{ $item['product_id'] }}">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-slate-900 truncate">{{ $item['name'] }}</p>
                    <p class="text-xs text-slate-500">Ksh {{ number_format($item['price'], 0) }} × <span class="qty">{{ $item['quantity'] }}</span></p>
                </div>
                <div class="flex items-center gap-0.5 flex-shrink-0">
                    <button type="button" class="pos-qty-btn w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 font-bold text-slate-600" data-product-id="{{ $item['product_id'] }}" data-delta="-1">−</button>
                    <input type="number" min="1" max="{{ $item['stock'] }}" value="{{ $item['quantity'] }}"
                           class="w-12 text-center border border-slate-200 rounded-lg px-1 py-1.5 text-sm pos-qty-input"
                           data-product-id="{{ $item['product_id'] }}">
                    <button type="button" class="pos-qty-btn w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 font-bold text-slate-600" data-product-id="{{ $item['product_id'] }}" data-delta="1">+</button>
                    <button type="button" onclick="window.posRemoveItem({{ $item['product_id'] }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Remove">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </li>
        @endforeach
    </ul>
@else
    <p class="text-sm text-slate-500 text-center py-10">Cart is empty. Scan barcode or add products.</p>
@endif
