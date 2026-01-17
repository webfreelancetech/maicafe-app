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

// E-commerce Routes
Route::get('/', [EcommerceController::class, 'index'])->name('home');
Route::get('/menu', [EcommerceController::class, 'menu'])->name('menu');
Route::get('/product/{slug}', [EcommerceController::class, 'product'])->name('product');
Route::get('/stores', [EcommerceController::class, 'stores'])->name('stores');
Route::get('/cart', [EcommerceController::class, 'cart'])->name('cart');

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
    Route::resource('stores', StoreController::class)->only(['index']);
    Route::resource('customers', CustomerController::class)->only(['index']);
    Route::resource('coupons', CouponController::class);
    Route::resource('banners', BannerController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
});
