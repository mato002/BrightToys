<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Wishlist;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share cart count with all views
        View::composer(['layouts.app', 'layouts.account'], function ($view) {
            $sessionId = session()->remember('cart_session_id', function () {
                return Str::uuid()->toString();
            });
            $userId = auth()->id();

            $cartCount = Cart::when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }, function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
            })->sum('quantity');

            $view->with('cartCount', $cartCount ?? 0);
        });

        // Share wishlist count with account layout (authenticated users only)
        View::composer('layouts.account', function ($view) {
            $wishlistCount = 0;
            if (auth()->check()) {
                $wishlistCount = Wishlist::where('user_id', auth()->id())->count();
            }
            $view->with('wishlistCount', $wishlistCount);
        });

        // Cache frequently accessed data
        \Illuminate\Support\Facades\Cache::remember('categories_menu', 3600, function () {
            return \App\Models\Category::orderBy('name')->get();
        });
    }
}
