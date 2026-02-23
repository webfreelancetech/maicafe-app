<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcommerceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\AddonGroupController;
use App\Http\Controllers\Kitchen\KitchenController;
use App\Http\Controllers\Kitchen\AuthController as KitchenAuthController;
use App\Http\Controllers\Counter\CounterController;
use App\Http\Controllers\Counter\AuthController as CounterAuthController;
use App\Http\Controllers\OrderDisplayController;
use App\Http\Controllers\LegalController;

// Legal Pages (Public)
Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-and-conditions', [LegalController::class, 'termsAndConditions'])->name('terms-and-conditions');

// E-commerce Routes
Route::get('/', [EcommerceController::class, 'index'])->name('home');
Route::get('/menu', [EcommerceController::class, 'menu'])->name('menu');
Route::get('/product/{slug}', [EcommerceController::class, 'product'])->name('product');
Route::get('/stores', [EcommerceController::class, 'stores'])->name('stores');
Route::get('/cart', [EcommerceController::class, 'cart'])->name('cart');

// Public Order Display (for pickup counter screens)
Route::get('/order-status', [OrderDisplayController::class, 'index'])->name('order-display');
Route::get('/order-status/data', [OrderDisplayController::class, 'data'])->name('order-display.data');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('orders-kitchen', [OrderController::class, 'kitchen'])->name('orders.kitchen');
    Route::get('orders-kitchen/data', [OrderController::class, 'kitchenData'])->name('orders.kitchenData');
    Route::resource('stores', StoreController::class)->only(['index']);
    Route::resource('customers', CustomerController::class)->only(['index', 'show']);
    Route::resource('coupons', CouponController::class);
    Route::resource('banners', BannerController::class);
    Route::post('banners/update-order', [BannerController::class, 'updateOrder'])->name('banners.updateOrder');
    Route::resource('addons', AddonGroupController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/email', [SettingController::class, 'updateEmail'])->name('settings.email.update');
    Route::post('settings/email/test', [SettingController::class, 'testEmail'])->name('settings.email.test');
});

// Kitchen Routes
Route::prefix('kitchen')->name('kitchen.')->group(function () {
    // Auth routes (public)
    Route::get('/login', [KitchenAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [KitchenAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [KitchenAuthController::class, 'logout'])->name('logout');
    
    // Protected routes (kitchen staff only)
    Route::middleware('kitchen')->group(function () {
        Route::get('/', [KitchenController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [KitchenController::class, 'orders'])->name('orders');
        Route::get('/orders/data', [KitchenController::class, 'ordersData'])->name('orders.data');
        Route::get('/orders/{order}', [KitchenController::class, 'orderDetail'])->name('orders.show');
        Route::post('/orders/{order}/status', [KitchenController::class, 'updateStatus'])->name('orders.updateStatus');
    });
});

// Counter Routes
Route::prefix('counter')->name('counter.')->group(function () {
    // Auth routes (public)
    Route::get('/login', [CounterAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CounterAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [CounterAuthController::class, 'logout'])->name('logout');
    
    // Protected routes (counter staff only)
    Route::middleware('counter')->group(function () {
        Route::get('/', [CounterController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/data', [CounterController::class, 'dashboardData'])->name('dashboard.data');
        Route::get('/orders', [CounterController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [CounterController::class, 'orderDetail'])->name('orders.show');
        Route::post('/orders/{order}/confirm-payment', [CounterController::class, 'confirmPayment'])->name('orders.confirmPayment');
        Route::post('/orders/{order}/cancel', [CounterController::class, 'cancelOrder'])->name('orders.cancel');
        
        // Counter Sale / POS
        Route::get('/sale', [CounterController::class, 'newSale'])->name('sale');
        Route::get('/sale/products', [CounterController::class, 'getProducts'])->name('sale.products');
        Route::get('/sale/product/{id}', [CounterController::class, 'getProductDetails'])->name('sale.product');
        Route::post('/sale/create', [CounterController::class, 'createSale'])->name('sale.create');
    });
});
