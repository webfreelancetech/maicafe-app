<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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
    
    // Legacy route for backward compatibility
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
