<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::query()->take(8)->get();
        $trending = Product::query()->where('featured', true)->take(5)->get();
        $latest = Product::query()->latest()->take(10)->get();

        return view('frontend.home', compact('categories', 'trending', 'latest'));
    }

    public function newArrivals()
    {
        $products = Product::query()->latest()->paginate(16);

        return view('frontend.shop', compact('products'));
    }
}

