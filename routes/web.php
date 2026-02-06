<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController as FrontProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;

// Frontend
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/new-arrivals', [HomeController::class, 'newArrivals'])->name('frontend.new_arrivals');

Route::get('/shop', [FrontProductController::class, 'index'])->name('shop.index');
Route::get('/category/{slug}', [FrontProductController::class, 'category'])->name('frontend.category');
Route::get('/product/{slug}', [FrontProductController::class, 'show'])->name('product.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Customer account (only for non-admin users)
Route::middleware(['auth', 'customer'])->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'profile'])->name('profile');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
    Route::delete('/addresses/{id}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');
});

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

    // Password reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Static pages
Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('pages.contact.submit');
Route::get('/policies', [PageController::class, 'policies'])->name('pages.policies');

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');
    Route::get('/dashboard/report', [DashboardController::class, 'report'])->name('dashboard.report');

    Route::resource('products', AdminProductController::class);
    Route::get('/products/export', [AdminProductController::class, 'export'])->name('products.export');
    Route::get('/products/report', [AdminProductController::class, 'report'])->name('products.report');
    
    Route::resource('categories', AdminCategoryController::class);
    Route::get('/categories/export', [AdminCategoryController::class, 'export'])->name('categories.export');
    Route::get('/categories/report', [AdminCategoryController::class, 'report'])->name('categories.report');
    
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
    Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/report', [AdminOrderController::class, 'report'])->name('orders.report');
    
    Route::resource('users', AdminUserController::class)->only(['index', 'show']);
    Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');
    Route::get('/users/report', [AdminUserController::class, 'report'])->name('users.report');
    
    Route::resource('admins', \App\Http\Controllers\Admin\AdminController::class);

    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/password', [\App\Http\Controllers\Admin\SettingsController::class, 'updatePassword'])->name('settings.password');
    
    // Redirect old change-password route to settings
    Route::get('/profile/change-password', function() {
        return redirect()->route('admin.settings');
    })->name('profile.change-password');

    Route::resource('support-tickets', SupportTicketController::class)->only(['index', 'show', 'update']);
    Route::get('/support-tickets/export', [SupportTicketController::class, 'export'])->name('support-tickets.export');
    Route::get('/support-tickets/report', [SupportTicketController::class, 'report'])->name('support-tickets.report');

    // Partnership Management
    Route::resource('partners', \App\Http\Controllers\Admin\PartnerController::class);
    
    // Financial Management
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FinancialController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\FinancialController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\FinancialController::class, 'store'])->name('store');
        Route::get('/{financial}', [\App\Http\Controllers\Admin\FinancialController::class, 'show'])->name('show');
        Route::post('/{financial}/approve', [\App\Http\Controllers\Admin\FinancialController::class, 'approve'])->name('approve');
        Route::post('/{financial}/reject', [\App\Http\Controllers\Admin\FinancialController::class, 'reject'])->name('reject');
        Route::post('/{financial}/archive', [\App\Http\Controllers\Admin\FinancialController::class, 'archive'])->name('archive');
        
        // Contributions
        Route::get('/contributions', [\App\Http\Controllers\Admin\FinancialController::class, 'contributions'])->name('contributions');
        Route::post('/contributions', [\App\Http\Controllers\Admin\FinancialController::class, 'storeContribution'])->name('contributions.store');
        Route::post('/contributions/{contribution}/approve', [\App\Http\Controllers\Admin\FinancialController::class, 'approveContribution'])->name('contributions.approve');
    });

    // Documents
    Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class);
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('documents.download');
    Route::post('/documents/{document}/archive', [\App\Http\Controllers\Admin\DocumentController::class, 'archive'])->name('documents.archive');

    // Activity Logs
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
});

// Partner Dashboard (read-only financial access)
Route::prefix('partner')->name('partner.')->middleware(['auth', 'partner'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Partner\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/financial-records', [\App\Http\Controllers\Partner\DashboardController::class, 'financialRecords'])->name('financial-records');
    Route::get('/contributions', [\App\Http\Controllers\Partner\DashboardController::class, 'contributions'])->name('contributions');
});

