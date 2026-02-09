<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = auth()->user()->wishlist()->with('product')->latest()->paginate(20);
        
        return view('frontend.account.wishlist', compact('wishlistItems'));
    }

    public function toggle(Product $product)
    {
        $user = auth()->user();
        
        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            $message = 'Removed from wishlist';
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            $message = 'Added to wishlist';
        }

        return back()->with('success', $message);
    }

    public function remove(Product $product)
    {
        $user = auth()->user();
        
        Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->delete();

        return redirect()->route('wishlist.index')
            ->with('success', 'Removed from wishlist');
    }
}
