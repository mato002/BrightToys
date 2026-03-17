<?php

namespace App\Services\Pos;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PosProductService
{
    /**
     * Search active products by name or SKU for POS.
     */
    public function search(string $query, int $perPage = 24): LengthAwarePaginator
    {
        $q = Product::query()
            ->where('status', 'active')
            ->with('category');

        if ($query !== '') {
            $q->where(function ($qb) use ($query) {
                $qb->where('name', 'like', '%' . $query . '%')
                    ->orWhere('sku', 'like', '%' . $query . '%');
            });
        }

        return $q->orderBy('name')->paginate($perPage);
    }

    /**
     * Get a single product by ID if active and in stock.
     */
    public function findForPos(int $productId): ?Product
    {
        return Product::where('id', $productId)
            ->where('status', 'active')
            ->with('category')
            ->first();
    }

    /**
     * Get featured/active products for quick-add grid (no search).
     */
    public function getProductsForGrid(int $perPage = 24): LengthAwarePaginator
    {
        return Product::query()
            ->where('status', 'active')
            ->with('category')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Find product by SKU or barcode (exact match) for POS.
     */
    public function findBySku(string $sku): ?Product
    {
        if (trim($sku) === '') {
            return null;
        }
        return Product::where('status', 'active')
            ->where(function ($q) use ($sku) {
                $q->where('sku', $sku)->orWhere('sku', 'like', '%' . $sku . '%');
            })
            ->with('category')
            ->first();
    }

    /**
     * Check if product has sufficient stock.
     */
    public function hasStock(Product $product, int $quantity): bool
    {
        return ($product->stock ?? 0) >= $quantity;
    }
}
