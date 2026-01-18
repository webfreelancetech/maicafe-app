<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    // Registration
    Route::post('/register', [AuthController::class, 'register']);
    
    // Login
    Route::post('/login', [AuthController::class, 'login']);
    
    // Forgot Password
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    
    // Reset Password
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authentication Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    });
    
    // User profile routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });
    
    // Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{slug}', [CategoryController::class, 'show']);
    });

    // Products
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{slug}', [ProductController::class, 'show']);
    });

    // Banners
    Route::prefix('banners')->group(function () {
        Route::get('/', [BannerController::class, 'index']);
        Route::get('/{id}', [BannerController::class, 'show']);
    });

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);              // Get cart
        Route::post('/items', [CartController::class, 'addItem']);      // Add item to cart
        Route::put('/items/{itemId}', [CartController::class, 'updateItem']);   // Update item quantity
        Route::delete('/items/{itemId}', [CartController::class, 'removeItem']); // Remove item
        Route::delete('/clear', [CartController::class, 'clear']);      // Clear cart
        Route::get('/count', [CartController::class, 'count']);         // Get cart count
        Route::post('/coupon', [CartController::class, 'applyCoupon']); // Apply coupon
        Route::delete('/coupon', [CartController::class, 'removeCoupon']); // Remove coupon
        Route::put('/notes', [CartController::class, 'updateNotes']);   // Update notes
        Route::put('/store', [CartController::class, 'setStore']);      // Set store
    });

    // Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);             // Get user orders
        Route::post('/', [OrderController::class, 'store']);            // Create order (legacy - direct items)
        Route::post('/checkout', [OrderController::class, 'checkout']); // Checkout from cart
        Route::get('/by-token', [OrderController::class, 'getByToken']); // Get by token
        Route::get('/{id}', [OrderController::class, 'show']);          // Get order details
        Route::get('/{id}/track', [OrderController::class, 'track']);   // Track order status
        Route::post('/{id}/cancel', [OrderController::class, 'cancel']); // Cancel order
        Route::post('/{id}/reorder', [OrderController::class, 'reorder']); // Reorder items to cart
        Route::post('/{id}/payment/confirm', [OrderController::class, 'confirmPayment']); // Confirm payment
    });

    // Wishlist
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);          // Get user's wishlist
        Route::post('/', [WishlistController::class, 'store']);         // Add product to wishlist
        Route::post('/toggle', [WishlistController::class, 'toggle']);  // Toggle product in wishlist
        Route::get('/count', [WishlistController::class, 'count']);     // Get wishlist item count
        Route::post('/check', [WishlistController::class, 'checkMultiple']); // Check multiple products
        Route::delete('/clear', [WishlistController::class, 'clear']);  // Clear entire wishlist
        Route::get('/check/{productId}', [WishlistController::class, 'check']); // Check if product is in wishlist
        Route::delete('/{productId}', [WishlistController::class, 'destroy']); // Remove product from wishlist
    });
    
    // Legacy route for backward compatibility
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
