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
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\MemberOnboardingController;

// Frontend
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/new-arrivals', [HomeController::class, 'newArrivals'])->name('frontend.new_arrivals');

Route::get('/shop', [FrontProductController::class, 'index'])->name('shop.index');
Route::get('/category/{slug}', [FrontProductController::class, 'category'])->name('frontend.category');
Route::get('/product/{slug}', [FrontProductController::class, 'show'])->name('product.show');

// Reviews
Route::post('/product/{product}/review', [\App\Http\Controllers\Frontend\ReviewController::class, 'store'])->name('review.store')->middleware('auth');
Route::post('/review/{review}/helpful', [\App\Http\Controllers\Frontend\ReviewController::class, 'helpful'])->name('review.helpful');

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
    Route::get('/orders/{order}/track', [AccountController::class, 'trackOrder'])->name('orders.track');
    Route::get('/orders/{order}/invoice', [AccountController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{order}/cancel', [AccountController::class, 'cancelOrder'])->name('orders.cancel');
    Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
    Route::delete('/addresses/{id}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');
});

// Wishlist (requires authentication)
Route::middleware('auth')->prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Frontend\WishlistController::class, 'index'])->name('index');
    Route::post('/toggle/{product}', [\App\Http\Controllers\Frontend\WishlistController::class, 'toggle'])->name('toggle');
    Route::delete('/remove/{product}', [\App\Http\Controllers\Frontend\WishlistController::class, 'remove'])->name('remove');
});

// Auth
// Login should be accessible even when already authenticated (to allow switching accounts),
// so it is defined outside the 'guest' middleware group.
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Registration should also be accessible even when already authenticated
// (for creating new accounts explicitly), so keep it outside the guest group.
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Password reset remains guest-only.
Route::middleware('guest')->group(function () {
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

// Member onboarding (public, token-based)
Route::middleware('guest')->group(function () {
    Route::get('/onboarding/{token}', [MemberOnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding/{token}', [MemberOnboardingController::class, 'submit'])->name('onboarding.submit');
});

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
    Route::resource('members', \App\Http\Controllers\Admin\MemberController::class)->only(['index', 'create', 'store', 'show']);

    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/password', [\App\Http\Controllers\Admin\SettingsController::class, 'updatePassword'])->name('settings.password');
    
    // Role Management (only for super admin and finance admin)
    Route::post('/settings/users/{user}/roles', [\App\Http\Controllers\Admin\SettingsController::class, 'assignRoles'])->name('settings.assign-roles');
    Route::delete('/settings/users/{user}/roles/{role}', [\App\Http\Controllers\Admin\SettingsController::class, 'removeRole'])->name('settings.remove-role');
    
    // Redirect old change-password route to settings
    Route::get('/profile/change-password', function() {
        return redirect()->route('admin.settings');
    })->name('profile.change-password');

    Route::resource('support-tickets', SupportTicketController::class)->only(['index', 'show', 'update']);
    Route::get('/support-tickets/export', [SupportTicketController::class, 'export'])->name('support-tickets.export');
    Route::get('/support-tickets/report', [SupportTicketController::class, 'report'])->name('support-tickets.report');

    // Reviews Management
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Partnership Management
    Route::resource('partners', \App\Http\Controllers\Admin\PartnerController::class);
    
    // Projects Management
    Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class);
    Route::post('/projects/{project}/activate', [\App\Http\Controllers\Admin\ProjectController::class, 'activate'])
        ->name('projects.activate');

    // Project Assets (per-project investments like land, stock, equipment)
    Route::resource('project-assets', \App\Http\Controllers\Admin\ProjectAssetController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);

    // Loans Management
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::post('/loans/{loan}/repayments', [LoanController::class, 'storeRepayment'])->name('loans.repayments.store');
    Route::post('/loans/{loan}/repayments/{repayment}/reconcile', [LoanController::class, 'reconcileRepayment'])->name('loans.repayments.reconcile');
    
    // Financial Management
    Route::prefix('financial')->name('financial.')->group(function () {
        // Financial Records (Expenses, etc.)
        Route::get('/', [\App\Http\Controllers\Admin\FinancialRecordController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\FinancialRecordController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\FinancialRecordController::class, 'store'])->name('store');
        
        // Contributions - MUST come before {financialRecord} route to avoid route conflicts
        Route::get('/contributions', [\App\Http\Controllers\Admin\PartnerContributionController::class, 'index'])->name('contributions');
        Route::get('/contributions/create', [\App\Http\Controllers\Admin\PartnerContributionController::class, 'create'])->name('contributions.create');
        Route::post('/contributions', [\App\Http\Controllers\Admin\PartnerContributionController::class, 'store'])->name('contributions.store');
        Route::get('/contributions/{contribution}', [\App\Http\Controllers\Admin\PartnerContributionController::class, 'show'])->name('contributions.show');
        Route::post('/contributions/{contribution}/approve', [\App\Http\Controllers\Admin\PartnerContributionController::class, 'approve'])->name('contributions.approve');
        Route::post('/contributions/{contribution}/reject', [\App\Http\Controllers\Admin\PartnerContributionController::class, 'reject'])->name('contributions.reject');
        Route::post('/contributions/{contribution}/archive', [\App\Http\Controllers\Admin\PartnerContributionController::class, 'archive'])->name('contributions.archive');
        
        // Financial Records with parameters - MUST come after specific routes
        Route::get('/{financialRecord}', [\App\Http\Controllers\Admin\FinancialRecordController::class, 'show'])->name('show');
        Route::post('/{financialRecord}/approve', [\App\Http\Controllers\Admin\FinancialRecordController::class, 'approve'])->name('approve');
        Route::post('/{financialRecord}/reject', [\App\Http\Controllers\Admin\FinancialRecordController::class, 'reject'])->name('reject');
        Route::post('/{financialRecord}/archive', [\App\Http\Controllers\Admin\FinancialRecordController::class, 'archive'])->name('archive');
    });

    // Documents
    Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class);
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Admin\DocumentController::class, 'download'])->name('documents.download');
    Route::post('/documents/{document}/archive', [\App\Http\Controllers\Admin\DocumentController::class, 'archive'])->name('documents.archive');

    // Activity Logs
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{activityLog}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
});

// Project Management (for users assigned to projects)
Route::prefix('project')->name('project.')->middleware(['auth'])->group(function () {
    Route::get('/{project}/dashboard', [\App\Http\Controllers\Project\ProjectDashboardController::class, 'index'])->name('dashboard');
});

// Partner Dashboard (read-only financial access)
Route::prefix('partner')->name('partner.')->middleware(['auth', 'partner'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Partner\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/financial-records', [\App\Http\Controllers\Partner\DashboardController::class, 'financialRecords'])->name('financial-records');
    Route::get('/contributions', [\App\Http\Controllers\Partner\DashboardController::class, 'contributions'])->name('contributions');
    Route::get('/contributions/create', [\App\Http\Controllers\Partner\DashboardController::class, 'createContribution'])->name('contributions.create');
    Route::post('/contributions', [\App\Http\Controllers\Partner\DashboardController::class, 'storeContribution'])->name('contributions.store');
    Route::get('/earnings', [\App\Http\Controllers\Partner\DashboardController::class, 'earnings'])->name('earnings');
    
    // Projects (viewing)
    Route::get('/projects', [\App\Http\Controllers\Partner\ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{project}', [\App\Http\Controllers\Partner\ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/redirect', [\App\Http\Controllers\Partner\ProjectController::class, 'redirect'])->name('projects.redirect');
    
    // Project Management (partners create/manage projects)
    Route::get('/projects-manage', [\App\Http\Controllers\Partner\ProjectManagementController::class, 'index'])->name('projects.manage');
    Route::get('/projects-manage/create', [\App\Http\Controllers\Partner\ProjectManagementController::class, 'create'])->name('projects.manage.create');
    Route::post('/projects-manage', [\App\Http\Controllers\Partner\ProjectManagementController::class, 'store'])->name('projects.manage.store');
    Route::get('/projects-manage/{project}/edit', [\App\Http\Controllers\Partner\ProjectManagementController::class, 'edit'])->name('projects.manage.edit');
    Route::put('/projects-manage/{project}', [\App\Http\Controllers\Partner\ProjectManagementController::class, 'update'])->name('projects.manage.update');
    Route::delete('/projects-manage/{project}', [\App\Http\Controllers\Partner\ProjectManagementController::class, 'destroy'])->name('projects.manage.destroy');
    
    // Project Performance & Finances (partners view their projects)
    Route::get('/projects/{project}/performance', [\App\Http\Controllers\Partner\ProjectPerformanceController::class, 'show'])->name('projects.performance');
    Route::get('/projects/{project}/finances', [\App\Http\Controllers\Partner\ProjectPerformanceController::class, 'finances'])->name('projects.finances');
    
    Route::get('/documents', [\App\Http\Controllers\Partner\DocumentController::class, 'index'])->name('documents');
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Partner\DocumentController::class, 'download'])->name('documents.download');
    Route::get('/activity', [\App\Http\Controllers\Partner\ActivityController::class, 'index'])->name('activity');
    Route::get('/profile', [\App\Http\Controllers\Partner\ProfileController::class, 'index'])->name('profile');
    Route::get('/reports', [\App\Http\Controllers\Partner\DashboardController::class, 'reports'])->name('reports');
});

