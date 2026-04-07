<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartItemsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-admin', [AuthController::class, 'registerAdmin']);
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)->only(['index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('orders')->group(function () {
        Route::get('/my', [OrderController::class, 'myOrders']); // cleaner naming
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{id}', [OrderController::class, 'show']);
    });

    Route::prefix('cart-items')->group(function () {
        Route::get('/{id}', [CartItemsController::class, 'show']);
        Route::post('/', [CartItemsController::class, 'store']);
        Route::patch('/{id}/increment', [CartItemsController::class, 'increment']);
        Route::patch('/{id}/decrement', [CartItemsController::class, 'decrement']);
        Route::delete('/{id}', [CartItemsController::class, 'destroy']);
    });

});

// Admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::patch('/{id}', [OrderController::class, 'update']);
    });

    Route::prefix('cart-items')->group(function () {
        Route::get('/', [CartItemsController::class, 'index']);
    });

    Route::apiResource('coupons', CouponsController::class)->only(['index', 'destroy', 'store']);

    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::apiResource('categories', CategoryController::class)->except(['index']);

});

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();
//     return response()->json(['message' => 'Email verified successfully!']);
// })->middleware(['signed'])->name('verification.verify');
















Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid verification link.'], 403);
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    return response()->json(['message' => 'Email verified successfully!']);
})->middleware(['signed'])->name('verification.verify');