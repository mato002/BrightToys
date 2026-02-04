<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category');

        // Search by name or description
        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category slug
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->get('category'));
            });
        }

        // Price range filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->get('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->get('max_price'));
        }

        // Sorting
        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                // featured / default sorting: newest first
                $query->latest();
                break;
        }

        $products = $query->paginate(16)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('frontend.shop', compact('products', 'categories'));
    }

    public function show(string $slug)
    {
        $product = Product::query()->where('slug', $slug)->with('category')->firstOrFail();
        $related = Product::query()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('frontend.product', compact('product', 'related'));
    }

    public function category(string $slug)
    {
        $category = Category::query()->where('slug', $slug)->firstOrFail();
        $productsQuery = $category->products()->newQuery();

        // Search within category
        if ($search = request('q')) {
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Price range within category
        if (request()->filled('min_price')) {
            $productsQuery->where('price', '>=', (float) request('min_price'));
        }

        if (request()->filled('max_price')) {
            $productsQuery->where('price', '<=', (float) request('max_price'));
        }

        // Sorting
        switch (request('sort')) {
            case 'price_asc':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'newest':
                $productsQuery->latest();
                break;
            default:
                $productsQuery->latest();
                break;
        }

        $products = $productsQuery->paginate(16)->withQueryString();

        return view('frontend.category', compact('category', 'products'));
    }
}

