<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach (range(1, 15) as $_) {
            $user = $users->random();

            $order = Order::create([
                'user_id' => $user->id,
                'total' => 0,
                'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'completed', 'cancelled']),
                'payment_method' => fake()->randomElement(['mpesa', 'card', 'cash_on_delivery']),
                'shipping_address' => fake()->address(),
            ]);

            $itemsCount = rand(1, 4);
            $total = 0;

            foreach (range(1, $itemsCount) as $_i) {
                $product = $products->random();
                $qty = rand(1, 3);
                $price = $product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                ]);

                $total += $qty * $price;
            }

            $order->update(['total' => $total]);
        }
    }
}

