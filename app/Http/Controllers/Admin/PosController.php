<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Pos\PosCartService;
use App\Services\Pos\PosOrderService;
use App\Services\Pos\PosProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function __construct(
        protected PosCartService $cartService,
        protected PosOrderService $orderService,
        protected PosProductService $productService
    ) {}

    /**
     * Main POS screen: product grid + cart.
     */
    public function index(Request $request)
    {
        $products = $this->productService->getProductsForGrid(24);
        $cart = $this->cartService->getCartWithDetails();
        $paymentMethods = PosOrderService::paymentMethods();

        return view('pos.index', compact('products', 'cart', 'paymentMethods'));
    }

    /**
     * Search products (AJAX or query).
     */
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        $products = $this->productService->search($query, 24);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'products' => $products->items(),
                'cart' => $this->cartService->getCartWithDetails(),
            ]);
        }

        $cart = $this->cartService->getCartWithDetails();
        $paymentMethods = PosOrderService::paymentMethods();

        return view('pos.index', compact('products', 'cart', 'paymentMethods'));
    }

    /**
     * Add product to POS cart.
     */
    public function addToCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:9999',
        ]);

        $result = $this->cartService->add(
            (int) $request->product_id,
            (int) ($request->quantity ?? 1)
        );

        return response()->json($result);
    }

    /**
     * Update quantity in POS cart.
     */
    public function updateCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:0|max:9999',
        ]);

        $result = $this->cartService->update(
            (int) $request->product_id,
            (int) $request->quantity
        );

        return response()->json($result);
    }

    /**
     * Remove item from POS cart.
     */
    public function removeFromCart(Request $request, int $productId): JsonResponse
    {
        $result = $this->cartService->remove($productId);
        return response()->json($result);
    }

    /**
     * Get current cart (for AJAX refresh).
     */
    public function getCart(): JsonResponse
    {
        return response()->json(['cart' => $this->cartService->getCartWithDetails()]);
    }

    /**
     * Find product by SKU/barcode (for barcode scanner).
     */
    public function productBySku(Request $request): JsonResponse
    {
        $sku = $request->get('sku', '');
        $product = $this->productService->findBySku($sku);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found for SKU/barcode.'], 404);
        }
        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'stock' => $product->stock,
            ],
        ]);
    }

    /**
     * Add to cart by SKU/barcode (for barcode scanner / quick add).
     */
    public function addBySku(Request $request): JsonResponse
    {
        $request->validate([
            'sku' => 'required|string|max:100',
            'quantity' => 'nullable|integer|min:1|max:9999',
        ]);
        $product = $this->productService->findBySku($request->sku);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found for this barcode/SKU.'], 404);
        }
        $result = $this->cartService->add($product->id, (int) ($request->quantity ?? 1));
        return response()->json($result);
    }

    /**
     * Complete POS checkout: create order, deduct stock, clear cart.
     */
    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,mobile',
            'total' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $order = $this->orderService->createOrder(
                $request->payment_method,
                auth()->id(),
                $request->input('total'),
                $request->input('tax_amount'),
                $request->input('discount_amount')
            );

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully.',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                ],
                'cart' => $this->cartService->getCartWithDetails(),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
