@extends('layouts.admin')

@section('title', 'Point of Sale')

@section('content')
<div class="pos-checkout min-h-[calc(100vh-6rem)] flex flex-col lg:flex-row gap-3 p-3 md:p-4" id="pos-app">
    {{-- Top bar: Barcode + Search --}}
    <div class="flex-shrink-0 flex flex-wrap items-center gap-3 p-3 bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="flex-1 min-w-[180px] flex items-center gap-2">
            <label for="pos-barcode" class="text-xs font-semibold text-slate-600 whitespace-nowrap">Barcode / SKU</label>
            <input type="text" id="pos-barcode" autocomplete="off" placeholder="Scan or type SKU..."
                   class="flex-1 border-2 border-slate-200 rounded-lg px-4 py-2.5 text-base focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 pos-input">
        </div>
        <div class="flex-1 min-w-[200px] flex items-center gap-2 relative" id="pos-search-wrap">
            <label for="pos-search" class="text-xs font-semibold text-slate-600 whitespace-nowrap">Search</label>
            <input type="search" id="pos-search" autocomplete="off" placeholder="Product name or SKU..."
                   class="flex-1 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <div id="pos-search-results" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg z-30 max-h-64 overflow-auto"></div>
        </div>
        <a href="{{ route('admin.pos.index') }}" class="px-4 py-2.5 text-slate-600 hover:text-emerald-600 text-sm font-medium whitespace-nowrap">Clear all</a>
    </div>

    <div class="flex-1 flex flex-col lg:flex-row gap-3 min-h-0">
        {{-- Products grid --}}
        <div class="flex-1 flex flex-col min-h-0 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-2 border-b border-slate-100 text-xs text-slate-500 flex justify-between items-center">
                <span>Products</span>
                <span class="hidden sm:inline">Click or scan to add • F2: Barcode • F9: Checkout</span>
            </div>
            <div class="flex-1 overflow-auto p-3">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 md:gap-3" id="pos-product-grid">
                    @foreach($products as $product)
                        @include('pos.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                @if($products->hasPages())
                    <div class="mt-4 flex justify-center gap-2 flex-wrap">
                        @if($products->onFirstPage())
                            <span class="px-3 py-2 bg-slate-100 text-slate-400 rounded-lg text-sm">Previous</span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-slate-50">Previous</a>
                        @endif
                        <span class="px-3 py-2 text-sm text-slate-600">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>
                        @if($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-slate-50">Next</a>
                        @else
                            <span class="px-3 py-2 bg-slate-100 text-slate-400 rounded-lg text-sm">Next</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Cart panel --}}
        <div class="w-full lg:w-[380px] xl:w-[420px] flex flex-col bg-white rounded-xl border border-slate-200 shadow-sm min-h-[320px] flex-shrink-0">
            <div class="p-3 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <h2 class="font-semibold text-slate-900">Current Sale</h2>
                <span class="text-sm text-slate-500" id="pos-cart-count">{{ ($cart['item_count'] ?? 0) }} item(s)</span>
            </div>
            <div class="flex-1 overflow-auto p-3" id="pos-cart-items">
                @include('pos.partials.cart-items', ['cart' => $cart])
            </div>
            <div class="p-3 border-t border-slate-200 space-y-2 bg-slate-50/50">
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <label class="text-slate-600 flex items-center gap-1">Tax %</label>
                    <input type="number" id="pos-tax-rate" value="0" min="0" max="100" step="0.01"
                           class="border border-slate-200 rounded-lg px-2 py-1.5 text-right text-sm pos-calc-input">
                    <label class="text-slate-600 flex items-center gap-1">Discount (Ksh)</label>
                    <input type="number" id="pos-discount" value="0" min="0" step="1"
                           class="border border-slate-200 rounded-lg px-2 py-1.5 text-right text-sm pos-calc-input">
                </div>
                <div class="space-y-1 text-sm pt-1 border-t border-slate-200">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span id="pos-cart-subtotal">Ksh {{ number_format($cart['subtotal'] ?? 0, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600" id="pos-tax-row">
                        <span>Tax</span>
                        <span id="pos-tax-amount">Ksh 0</span>
                    </div>
                    <div class="flex justify-between text-slate-600" id="pos-discount-row">
                        <span>Discount</span>
                        <span id="pos-discount-amount">Ksh 0</span>
                    </div>
                    <div class="flex justify-between font-bold text-base text-slate-900 pt-1">
                        <span>Total</span>
                        <span id="pos-cart-total">Ksh {{ number_format($cart['subtotal'] ?? 0, 0) }}</span>
                    </div>
                </div>
                <button type="button" id="pos-checkout-btn"
                        class="w-full py-3.5 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-base shadow-md shadow-emerald-600/20">
                    Complete Sale (F9)
                </button>
            </div>
        </div>
    </div>

    {{-- Checkout modal --}}
    <div id="pos-checkout-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50" aria-hidden="true">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6" id="pos-checkout-modal-inner" role="dialog" aria-labelledby="pos-modal-title">
            <h3 id="pos-modal-title" class="text-xl font-semibold text-slate-900 mb-4">Select payment method</h3>
            <form id="pos-checkout-form" class="space-y-3">
                @foreach($paymentMethods as $value => $label)
                    <label class="flex items-center gap-3 p-4 border-2 border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 transition-colors">
                        <input type="radio" name="payment_method" value="{{ $value }}" {{ $loop->first ? 'checked' : '' }} class="w-5 h-5 text-emerald-600">
                        <span class="font-medium text-slate-900">{{ $label }}</span>
                    </label>
                @endforeach
                <div class="flex gap-3 pt-4">
                    <button type="button" id="pos-checkout-cancel" class="flex-1 py-3 border-2 border-slate-200 rounded-xl font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" id="pos-checkout-submit" class="flex-1 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700">
                        Pay Ksh <span id="pos-checkout-total">{{ number_format($cart['subtotal'] ?? 0, 0) }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Success toast --}}
    <div id="pos-success-toast" class="fixed bottom-4 right-4 z-50 hidden px-5 py-4 bg-emerald-600 text-white rounded-xl shadow-xl items-center gap-3" role="status">
        <span id="pos-success-message"></span>
        <a id="pos-success-link" href="#" class="underline font-semibold hover:text-emerald-100">View order</a>
    </div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';
    var csrf = '{{ csrf_token() }}';
    var urls = {
        cartAdd: '{{ route("admin.pos.cart.add") }}',
        cartUpdate: '{{ route("admin.pos.cart.update") }}',
        cartRemove: '{{ url("admin/pos/cart/remove") }}',
        cartGet: '{{ route("admin.pos.cart.get") }}',
        cartAddBySku: '{{ route("admin.pos.cart.add-by-sku") }}',
        productBySku: '{{ route("admin.pos.products.by-sku") }}',
        searchProducts: '{{ route("admin.pos.products.search") }}',
        checkout: '{{ route("admin.pos.checkout") }}',
        orderShow: '{{ url("admin/orders") }}'
    };

    var searchTimeout = null;
    var currentCart = { items: [], subtotal: 0, item_count: 0 };

    function escapeHtml(s) {
        var div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    function getSubtotal() { return currentCart.subtotal || 0; }
    function getTaxRate() { return parseFloat(document.getElementById('pos-tax-rate').value) || 0; }
    function getDiscount() { return parseFloat(document.getElementById('pos-discount').value) || 0; }
    function getTaxAmount() { return (getSubtotal() * getTaxRate() / 100); }
    function getTotal() { return Math.max(0, getSubtotal() + getTaxAmount() - getDiscount()); }

    function updateTotalsUI() {
        var subtotal = getSubtotal();
        var taxRate = getTaxRate();
        var taxAmt = getTaxAmount();
        var discount = getDiscount();
        var total = getTotal();
        document.getElementById('pos-cart-subtotal').textContent = 'Ksh ' + Math.round(subtotal).toLocaleString();
        document.getElementById('pos-tax-amount').textContent = 'Ksh ' + Math.round(taxAmt).toLocaleString();
        document.getElementById('pos-discount-amount').textContent = 'Ksh ' + Math.round(discount).toLocaleString();
        document.getElementById('pos-cart-total').textContent = 'Ksh ' + Math.round(total).toLocaleString();
        var checkoutTotal = document.getElementById('pos-checkout-total');
        if (checkoutTotal) checkoutTotal.textContent = Math.round(total).toLocaleString();
    }

    function updateCartUI(cart) {
        currentCart = cart;
        var countEl = document.getElementById('pos-cart-count');
        var container = document.getElementById('pos-cart-items');
        var checkoutBtn = document.getElementById('pos-checkout-btn');
        var itemCount = cart.item_count || 0;
        if (countEl) countEl.textContent = itemCount + ' item(s)';
        if (checkoutBtn) checkoutBtn.disabled = itemCount === 0;

        if (!cart.items || cart.items.length === 0) {
            if (container) container.innerHTML = '<p class="text-sm text-slate-500 text-center py-10">Cart is empty. Scan barcode or add products.</p>';
            updateTotalsUI();
            return;
        }

        var html = '<ul class="space-y-2">';
        cart.items.forEach(function(item) {
            var stock = item.stock || 999;
            html += '<li class="flex items-center gap-2 text-sm border-b border-slate-100 pb-2" data-product-id="' + item.product_id + '">';
            html += '<div class="flex-1 min-w-0">';
            html += '<p class="font-medium text-slate-900 truncate">' + escapeHtml(item.name) + '</p>';
            html += '<p class="text-xs text-slate-500">Ksh ' + Math.round(item.price).toLocaleString() + ' × <span class="qty">' + item.quantity + '</span></p>';
            html += '</div>';
            html += '<div class="flex items-center gap-0.5 flex-shrink-0">';
            html += '<button type="button" class="pos-qty-btn w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 font-bold text-slate-600" data-product-id="' + item.product_id + '" data-delta="-1">−</button>';
            html += '<input type="number" min="1" max="' + stock + '" value="' + item.quantity + '" class="w-12 text-center border border-slate-200 rounded-lg px-1 py-1.5 text-sm pos-qty-input" data-product-id="' + item.product_id + '">';
            html += '<button type="button" class="pos-qty-btn w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 font-bold text-slate-600" data-product-id="' + item.product_id + '" data-delta="1">+</button>';
            html += '<button type="button" onclick="window.posRemoveItem(' + item.product_id + ')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Remove"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>';
            html += '</div></li>';
        });
        html += '</ul>';
        if (container) container.innerHTML = html;

        container.querySelectorAll('.pos-qty-input').forEach(function(input) {
            input.addEventListener('change', function() {
                var id = parseInt(this.dataset.productId, 10);
                var qty = parseInt(this.value, 10) || 0;
                window.posUpdateQuantity(id, qty);
            });
        });
        container.querySelectorAll('.pos-qty-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = parseInt(this.dataset.productId, 10);
                var delta = parseInt(this.dataset.delta, 10);
                var input = container.querySelector('.pos-qty-input[data-product-id="' + id + '"]');
                if (!input) return;
                var qty = Math.max(0, parseInt(input.value, 10) + delta);
                window.posUpdateQuantity(id, qty);
            });
        });
        updateTotalsUI();
    }

    function fetchCart(callback) {
        fetch(urls.cartGet, { headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.cart) updateCartUI(data.cart);
                if (callback) callback(data.cart);
            });
    }

    window.posAddToCart = function(productId, quantity) {
        quantity = quantity || 1;
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('product_id', productId);
        fd.append('quantity', quantity);
        fetch(urls.cartAdd, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) updateCartUI(data.cart);
                else if (data.message) alert(data.message);
            });
    };

    window.posUpdateQuantity = function(productId, quantity) {
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('product_id', productId);
        fd.append('quantity', quantity);
        fetch(urls.cartUpdate, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) updateCartUI(data.cart);
            });
    };

    window.posRemoveItem = function(productId) {
        fetch(urls.cartRemove + '/' + productId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) updateCartUI(data.cart);
            });
    };

    function addBySku(sku, quantity) {
        quantity = quantity || 1;
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('sku', sku);
        fd.append('quantity', quantity);
        fetch(urls.cartAddBySku, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) updateCartUI(data.cart);
                else alert(data.message || 'Product not found');
            });
    }

    var barcodeInput = document.getElementById('pos-barcode');
    var searchInput = document.getElementById('pos-search');
    var searchResults = document.getElementById('pos-search-results');

    if (barcodeInput) {
        barcodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var sku = this.value.trim();
                if (sku) {
                    addBySku(sku, 1);
                    this.value = '';
                }
            }
        });
        barcodeInput.addEventListener('blur', function() { setTimeout(function() { barcodeInput.focus(); }, 100); });
        barcodeInput.focus();
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = this.value.trim();
            clearTimeout(searchTimeout);
            if (q.length < 2) {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
                return;
            }
            searchTimeout = setTimeout(function() {
                fetch(urls.searchProducts + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (!data.products || data.products.length === 0) {
                            searchResults.innerHTML = '<div class="p-3 text-sm text-slate-500">No products found</div>';
                        } else {
                            searchResults.innerHTML = data.products.slice(0, 8).map(function(p) {
                                return '<button type="button" class="pos-search-item w-full text-left px-4 py-2.5 hover:bg-slate-50 border-b border-slate-100 last:border-0 flex justify-between items-center" data-id="' + p.id + '">' +
                                    '<span class="font-medium text-slate-900 truncate">' + escapeHtml(p.name) + '</span>' +
                                    '<span class="text-emerald-600 font-semibold text-sm ml-2">Ksh ' + Math.round(p.price).toLocaleString() + '</span></button>';
                            }).join('');
                            searchResults.querySelectorAll('.pos-search-item').forEach(function(btn) {
                                btn.addEventListener('click', function() {
                                    window.posAddToCart(parseInt(this.dataset.id, 10), 1);
                                    searchResults.classList.add('hidden');
                                    searchInput.value = '';
                                    searchInput.blur();
                                });
                            });
                        }
                        searchResults.classList.remove('hidden');
                    });
            }, 250);
        });
        searchInput.addEventListener('blur', function() {
            setTimeout(function() { searchResults.classList.add('hidden'); }, 200);
        });
    }

    document.getElementById('pos-tax-rate').addEventListener('input', updateTotalsUI);
    document.getElementById('pos-discount').addEventListener('input', updateTotalsUI);

    var modal = document.getElementById('pos-checkout-modal');
    var form = document.getElementById('pos-checkout-form');
    var cancelBtn = document.getElementById('pos-checkout-cancel');
    var toast = document.getElementById('pos-success-toast');

    document.getElementById('pos-checkout-btn').addEventListener('click', function() {
        if (this.disabled) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    cancelBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var method = form.querySelector('input[name="payment_method"]:checked');
        var paymentMethod = method ? method.value : 'cash';
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('payment_method', paymentMethod);
        fd.append('total', String(getTotal()));
        fd.append('tax_amount', String(getTaxAmount()));
        fd.append('discount_amount', String(getDiscount()));
        fetch(urls.checkout, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    updateCartUI(data.cart);
                    document.getElementById('pos-success-message').textContent = 'Sale complete! Order #' + data.order.order_number;
                    document.getElementById('pos-success-link').href = urls.orderShow + '/' + data.order.id;
                    toast.classList.remove('hidden');
                    toast.classList.add('flex');
                    setTimeout(function() {
                        toast.classList.add('hidden');
                        toast.classList.remove('flex');
                    }, 6000);
                } else {
                    alert(data.message || (data.errors && data.errors.cart ? data.errors.cart[0] : 'Checkout failed'));
                }
            });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2') { e.preventDefault(); if (barcodeInput) barcodeInput.focus(); }
        if (e.key === 'F9') { e.preventDefault(); var btn = document.getElementById('pos-checkout-btn'); if (btn && !btn.disabled) btn.click(); }
    });

    document.getElementById('pos-cart-items').addEventListener('change', function(e) {
        if (e.target.classList.contains('pos-qty-input')) {
            var id = parseInt(e.target.dataset.productId, 10);
            var qty = parseInt(e.target.value, 10) || 0;
            window.posUpdateQuantity(id, qty);
        }
    });
    document.getElementById('pos-cart-items').addEventListener('click', function(e) {
        var btn = e.target.closest('.pos-qty-btn');
        if (!btn) return;
        var id = parseInt(btn.dataset.productId, 10);
        var delta = parseInt(btn.dataset.delta, 10);
        var input = this.querySelector('.pos-qty-input[data-product-id="' + id + '"]');
        if (!input) return;
        var qty = Math.max(0, parseInt(input.value, 10) + delta);
        window.posUpdateQuantity(id, qty);
    });
})();
</script>
@endpush
@endsection
