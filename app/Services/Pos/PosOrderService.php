<?php

namespace App\Services\Pos;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosOrderService
{
    public const PAYMENT_CASH = 'cash';
    public const PAYMENT_CARD = 'card';
    public const PAYMENT_MOBILE = 'mobile';

    public function __construct(
        protected PosCartService $cartService,
        protected PosProductService $productService
    ) {}

    /**
     * Valid payment methods for POS.
     */
    public static function paymentMethods(): array
    {
        return [
            self::PAYMENT_CASH => 'Cash',
            self::PAYMENT_CARD => 'Card',
            self::PAYMENT_MOBILE => 'Mobile (M-Pesa etc.)',
        ];
    }

    /**
     * Create POS order: validate cart, create order + items, deduct stock, clear cart.
     *
     * @param string $paymentMethod One of PAYMENT_CASH, PAYMENT_CARD, PAYMENT_MOBILE
     * @param int|null $userId Optional staff/cashier user ID
     * @param float|null $total Override total (e.g. subtotal + tax - discount); null = use cart subtotal
     * @param float|null $taxAmount Optional tax amount for notes
     * @param float|null $discountAmount Optional discount amount (stored on order)
     * @return Order
     * @throws ValidationException
     */
    public function createOrder(string $paymentMethod, ?int $userId = null, ?float $total = null, ?float $taxAmount = null, ?float $discountAmount = null): Order
    {
        $cart = $this->cartService->getCartWithDetails();
        if (empty($cart['items'])) {
            throw ValidationException::withMessages(['cart' => ['Cart is empty.']]);
        }

        $validMethods = array_keys(self::paymentMethods());
        if (!in_array($paymentMethod, $validMethods, true)) {
            throw ValidationException::withMessages(['payment_method' => ['Invalid payment method.']]);
        }

        try {
            DB::beginTransaction();

            $orderTotal = $total !== null && $total >= 0
                ? (float) $total
                : (float) $cart['subtotal'];
            $discount = $discountAmount !== null && $discountAmount >= 0 ? (float) $discountAmount : 0;

            $order = Order::create([
                'user_id' => $userId,
                'total' => $orderTotal,
                'discount_amount' => $discount,
                'status' => 'completed',
                'payment_method' => $paymentMethod,
                'payment_status' => 'paid',
                'shipping_address' => null,
                'phone' => null,
                'notes' => 'POS sale' . ($taxAmount !== null && $taxAmount > 0 ? ' (tax: ' . $taxAmount . ')' : ''),
                'source' => Order::SOURCE_POS,
            ]);

            foreach ($cart['items'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    DB::rollBack();
                    throw ValidationException::withMessages(['cart' => ["Product #{$item['product_id']} not found."]]);
                }
                if (($product->stock ?? 0) < $item['quantity']) {
                    DB::rollBack();
                    throw ValidationException::withMessages([
                        'cart' => ["Insufficient stock for {$product->name}. Available: {$product->stock}"],
                    ]);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            $this->cartService->clear();
            DB::commit();

            return $order->fresh(['items.product']);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['cart' => ['Failed to create order: ' . $e->getMessage()]]);
        }
    }
}
