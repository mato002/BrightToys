<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        // In a real app you would load cart items similarly to CartController
        return view('frontend.checkout');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:mpesa,paybill,card,cod',
        ]);

        // Basic example checkout flow
        $sessionId = session('cart_session_id');
        $cartItems = Cart::with('product')->where('session_id', $sessionId)->get();
        $total = $cartItems->sum(function ($item) {
            return $item->quantity * ($item->product->price ?? 0);
        });

        $order = Order::create([
            'user_id' => auth()->id(),
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->input('payment_method', 'cod'),
            'shipping_address' => $request->input('address'),
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price ?? 0,
            ]);
        }

        Cart::where('session_id', $sessionId)->delete();

        return redirect()->route('home')->with('success', 'Order placed successfully.');
    }
}

