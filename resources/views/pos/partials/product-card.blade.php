<div class="border-2 border-slate-200 rounded-xl p-3 hover:border-emerald-400 hover:shadow-lg transition-all bg-white pos-product-card">
    <button type="button" onclick="window.posAddToCart && window.posAddToCart({{ $product->id }}, 1)" class="w-full text-left block">
        <div class="aspect-square rounded-lg bg-slate-100 mb-2 overflow-hidden flex items-center justify-center">
            @if($product->image_url)
                <img src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : asset('images/toys/' . $product->image_url) }}"
                     alt="{{ $product->name }}" class="w-full h-full object-cover">
            @else
                <span class="text-slate-400 text-xs">No image</span>
            @endif
        </div>
        <p class="text-sm font-semibold text-slate-900 truncate" title="{{ $product->name }}">{{ $product->name }}</p>
        @if($product->sku)
            <p class="text-[10px] text-slate-500">{{ $product->sku }}</p>
        @endif
        <p class="text-base font-bold text-emerald-600 mt-0.5">Ksh {{ number_format($product->price, 0) }}</p>
        <p class="text-[10px] text-slate-500">Stock: {{ $product->stock ?? 0 }}</p>
        <span class="mt-2 inline-block w-full py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition-colors text-center">
            Add to sale
        </span>
    </button>
</div>
