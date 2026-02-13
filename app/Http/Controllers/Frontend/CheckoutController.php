<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;

class CheckoutController extends Controller
{
    public function index()
    {
        // Ensure user is authenticated and is a customer (not partner or admin)
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to proceed to checkout.');
        }

        $user = auth()->user();
        
        // Redirect partners and admins to their respective dashboards
        if ($user->is_partner ?? false) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Partners cannot use the customer checkout. Please use the Partner Console.');
        }
        
        if ($user->is_admin ?? false) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admins cannot use the customer checkout. Please use the Admin Panel.');
        }

        $sessionId = session('cart_session_id');
        $userId = $user->id;

        $query = Cart::with('product')->where('user_id', $userId);
        $cartItems = $query->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * ($item->product->price ?? 0);
        });
        
        $shipping = 500; // Fixed shipping cost (can be made configurable)
        $total = $subtotal + $shipping;

        // Get saved addresses
        $addresses = $user->addresses;

        return view('frontend.checkout', compact('cartItems', 'subtotal', 'shipping', 'total', 'addresses'));
    }

    public function store(Request $request)
    {
        // Ensure user is authenticated and is a customer
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to complete checkout.');
        }

        $user = auth()->user();
        
        // Redirect partners and admins
        if ($user->is_partner ?? false) {
            return redirect()->route('partner.dashboard')
                ->with('error', 'Partners cannot use the customer checkout.');
        }
        
        if ($user->is_admin ?? false) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admins cannot use the customer checkout.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:mpesa,paybill,card,cod',
            'notes' => 'nullable|string|max:1000',
        ]);

        $sessionId = session('cart_session_id');
        $userId = $user->id;

        $query = Cart::with('product');
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $cartItems = $query->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate stock availability before processing
        foreach ($cartItems as $item) {
            if (!$item->product || $item->product->status !== 'active') {
                return back()->withErrors(['error' => "Product '{$item->product->name}' is no longer available."])->withInput();
            }
            if ($item->product->stock < $item->quantity) {
                return back()->withErrors(['error' => "Insufficient stock for '{$item->product->name}'. Only {$item->product->stock} available."])->withInput();
            }
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * ($item->product->price ?? 0);
        });
        
        $shipping = 500; // Fixed shipping cost
        $total = $subtotal + $shipping;

        // Use database transaction to ensure data consistency
        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $request->input('payment_method', 'cod'),
                'payment_status' => $request->input('payment_method') === 'cod' ? 'pending' : 'pending',
                'shipping_address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'notes' => $request->input('notes'),
            ]);

            // Create order items and deduct stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price ?? 0,
                ]);

                // Deduct stock
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock -= $item->quantity;
                    $product->save();
                }
            }

            // Clear cart
            Cart::where('session_id', $sessionId)
                ->orWhere('user_id', $userId)
                ->delete();

            DB::commit();

            // Send order confirmation email
            try {
                Mail::to($request->email)->send(new OrderConfirmationMail($order));
            } catch (\Exception $e) {
                Log::warning('Failed to send order confirmation email: ' . $e->getMessage());
            }

            return redirect()
                ->route('account.orders')
                ->with('success', "Order placed successfully! Order #{$order->order_number}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'error' => $e->getTraceAsString(),
            ]);

            return back()
                ->withErrors(['error' => 'An error occurred while processing your order. Please try again.'])
                ->withInput();
        }
    }
}

