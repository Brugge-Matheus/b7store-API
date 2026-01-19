<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('banner')->group(function() {
    Route::get('/', [BannerController::class, 'index']);
});

Route::prefix('product')->group(function() {
    Route::get('/', [ProductController::class, 'index'])->name('product.index');
    Route::get('/{id}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/{id}/related', [ProductController::class, 'related'])->name('product.related');
});

Route::prefix('category')->group(function() {
    Route::get('/{slug}/metadata', [CategoryController::class, 'metadata'])->name('category.metadata');
});

Route::prefix('cart')->group(function() {
    Route::get('/mount',  [CartController::class, 'mount'])->name('cart.mount');
    Route::get('/shipping', [CartController::class, 'shipping'])->name('cart.shipping');

    Route::middleware('auth:sanctum')->post('/finish', [CartController::class, 'finish'])->name('cart.finish'); 
});

Route::prefix('user')->group(function() {  
    Route::middleware('throttle:5,1')->group(function() {
        Route::post('/register', [UserController::class, 'register'])->name('user.register');
        Route::post('/login', [UserController::class, 'login'])
        ->name('user.login');
    });

    Route::middleware('auth:sanctum')->group(function() {
        Route::get('/addresses', [UserController::class, 'getAddresses'])->name('user.address');
        Route::post('/addresses', [UserController::class, 'addresses'])->name('user.address.store');;
    });
});

Route::middleware('auth:sanctum')->prefix('order')->group(function() {
    Route::get('/', [OrderController::class, 'index'])->name('order.index');
    Route::get('/{id}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/{id}/session', [OrderController::class, 'stripeSession'])->name('order.stripeSession');
});

Route::get('/ping', function() {
    return response()->json([
        'pong' => true
    ]);
});

Route::fallback(function() {
    return response()->json([
        'error' => true,
        'message' => 'Rota não encontrada no sistema!'
    ]);
});