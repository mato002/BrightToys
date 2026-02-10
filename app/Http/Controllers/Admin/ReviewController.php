<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected function checkStoreAdminPermission()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasAdminRole('store_admin')) {
            abort(403, 'You do not have permission to access this resource.');
        }
    }

    public function index()
    {
        $this->checkStoreAdminPermission();
        
        $query = Review::with(['product', 'user']);

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $this->checkStoreAdminPermission();
        
        $review->update(['status' => 'approved']);

        return back()->with('success', 'Review approved successfully.');
    }

    public function reject(Review $review)
    {
        $this->checkStoreAdminPermission();
        
        $review->update(['status' => 'rejected']);

        return back()->with('success', 'Review rejected.');
    }

    public function destroy(Review $review)
    {
        $this->checkStoreAdminPermission();
        
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
