<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected function currentSessionId(): string
    {
        return session()->remember('cart_session_id', function () {
            return Str::uuid()->toString();
        });
    }

    protected function getCartCount(?string $sessionId = null, ?int $userId = null): int
    {
        $sessionId = $sessionId ?? $this->currentSessionId();
        $userId = $userId ?? auth()->id();

        $query = Cart::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->sum('quantity');
    }

    public function index()
    {
        $sessionId = $this->currentSessionId();
        $userId = auth()->id();

        $query = Cart::with('product');
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $items = $query->get();

        $total = $items->sum(function ($item) {
            return $item->quantity * ($item->product->price ?? 0);
        });

        return view('frontend.cart', compact('items', 'total'));
    }

    public function accountIndex()
    {
        $sessionId = $this->currentSessionId();
        $userId = auth()->id();

        $query = Cart::with('product');
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $items = $query->get();

        $total = $items->sum(function ($item) {
            return $item->quantity * ($item->product->price ?? 0);
        });

        return view('frontend.account.cart', compact('items', 'total'));
    }

    public function add(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1|max:100',
        ]);

        $product = Product::findOrFail($id);
        
        // Check if product is active
        if ($product->status !== 'active') {
            return back()->withErrors(['error' => 'This product is not available.']);
        }

        // Check stock availability
        $quantity = (int)$request->input('quantity', 1);
        if ($product->stock < $quantity) {
            return back()->withErrors(['error' => 'Insufficient stock. Only ' . $product->stock . ' available.']);
        }

        $sessionId = $this->currentSessionId();
        $userId = auth()->id();

        // Build query conditions
        $query = Cart::where('product_id', $product->id);
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $cartItem = $query->first();

        if (!$cartItem) {
            $cartItem = new Cart();
            $cartItem->product_id = $product->id;
            $cartItem->user_id = $userId;
            $cartItem->session_id = $sessionId;
            $cartItem->quantity = 0;
        }

        $newQuantity = $cartItem->quantity + $quantity;
        
        // Check if total quantity exceeds stock
        if ($product->stock < $newQuantity) {
            return back()->withErrors(['error' => 'Cannot add more items. Stock limit reached.']);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        // Get updated cart count
        $cartCount = $this->getCartCount($sessionId, $userId);

        return back()->with([
            'success' => 'Product added to cart successfully!',
            'cart_count' => $cartCount
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $sessionId = $this->currentSessionId();
        $userId = auth()->id();

        $query = Cart::with('product')->where('id', $id);
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $cartItem = $query->firstOrFail();
        $newQuantity = max(1, (int)$request->input('quantity', 1));

        // Check stock availability
        if ($cartItem->product && $cartItem->product->stock < $newQuantity) {
            return back()->withErrors(['error' => 'Insufficient stock. Only ' . $cartItem->product->stock . ' available.']);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        // Check if request came from account section
        $referer = request()->header('referer', '');
        $isAccountContext = str_contains($referer, '/account/cart') || (str_contains($referer, '/account') && !str_contains($referer, '/account/orders'));
        
        if ($isAccountContext) {
            return redirect()->route('account.cart.index')->with('success', 'Cart updated successfully.');
        }
        
        return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
    }

    public function remove(int $id)
    {
        $sessionId = $this->currentSessionId();
        $userId = auth()->id();

        $query = Cart::where('id', $id);
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $query->delete();

        // Check if request came from account section
        $referer = request()->header('referer', '');
        $isAccountContext = str_contains($referer, '/account/cart') || (str_contains($referer, '/account') && !str_contains($referer, '/account/orders'));
        
        if ($isAccountContext) {
            return redirect()->route('account.cart.index')->with('success', 'Item removed from cart.');
        }
        
        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }
}

