<?php

namespace App\Services\Pos;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class PosCartService
{
    protected string $sessionKey = 'pos_cart';

    public function getCart(): array
    {
        return Session::get($this->sessionKey, []);
    }

    public function add(int $productId, int $quantity = 1): array
    {
        $cart = $this->getCart();
        $product = Product::where('id', $productId)->where('status', 'active')->first();

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found or inactive.'];
        }

        $available = $product->stock ?? 0;
        $currentQty = (int) ($cart[$productId] ?? 0);
        $newQty = min($currentQty + $quantity, $available);

        if ($newQty <= 0) {
            return ['success' => false, 'message' => 'Insufficient stock. Available: ' . $available];
        }

        $cart[$productId] = $newQty;
        Session::put($this->sessionKey, $cart);

        return ['success' => true, 'cart' => $this->getCartWithDetails()];
    }

    public function update(int $productId, int $quantity): array
    {
        $cart = $this->getCart();
        $product = Product::where('id', $productId)->where('status', 'active')->first();

        if (!$product) {
            unset($cart[$productId]);
            Session::put($this->sessionKey, $cart);
            return ['success' => true, 'cart' => $this->getCartWithDetails()];
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = min($quantity, $product->stock ?? 0);
            if ($cart[$productId] <= 0) {
                unset($cart[$productId]);
            }
        }

        Session::put($this->sessionKey, $cart);
        return ['success' => true, 'cart' => $this->getCartWithDetails()];
    }

    public function remove(int $productId): array
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        Session::put($this->sessionKey, $cart);
        return ['success' => true, 'cart' => $this->getCartWithDetails()];
    }

    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }

    /**
     * Cart items with product details and line totals for display/checkout.
     */
    public function getCartWithDetails(): array
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return ['items' => [], 'subtotal' => 0, 'item_count' => 0];
        }

        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $qty) {
            $product = $products->get($productId);
            if (!$product || $product->status !== 'active') {
                continue;
            }
            $qty = (int) $qty;
            $maxQty = min($qty, $product->stock ?? 0);
            if ($maxQty <= 0) {
                continue;
            }
            $lineTotal = $maxQty * $product->price;
            $items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'quantity' => $maxQty,
                'line_total' => $lineTotal,
                'stock' => $product->stock,
            ];
            $subtotal += $lineTotal;
        }

        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'item_count' => array_sum(array_column($items, 'quantity')),
        ];
    }

    public function isEmpty(): bool
    {
        return empty($this->getCart());
    }
}
