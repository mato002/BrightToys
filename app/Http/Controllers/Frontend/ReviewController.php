<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $user = auth()->user();
        
        // Check if user has already reviewed this product
        if ($user) {
            $existingReview = Review::where('product_id', $product->id)
                ->where('user_id', $user->id)
                ->first();
            
            if ($existingReview) {
                return back()->withErrors(['error' => 'You have already reviewed this product.']);
            }
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:1000',
            'name' => 'nullable|string|max:255', // For guests
            'email' => 'nullable|email|max:255', // For guests
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // If user is logged in, use their info
        if ($user) {
            $validated['user_id'] = $user->id;
            $validated['name'] = $user->name;
            $validated['email'] = $user->email;
        } else {
            // For guests, name and email are required
            if (empty($validated['name']) || empty($validated['email'])) {
                return back()->withErrors(['error' => 'Please login or provide your name and email to leave a review.']);
            }
        }

        $validated['product_id'] = $product->id;
        $validated['status'] = 'pending'; // Requires admin approval

        $review = Review::create(array_diff_key($validated, array_flip(['images'])));

        if ($user && $request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('reviews/' . $review->id, 'public');
                $review->images()->create(['path' => $path, 'sort_order' => $index]);
            }
        }

        return back()->with('success', 'Thank you for your review! It will be published after admin approval.');
    }

    public function helpful(Request $request, Review $review)
    {
        $review->increment('helpful_count');
        
        return response()->json([
            'success' => true,
            'helpful_count' => $review->helpful_count,
        ]);
    }
}
