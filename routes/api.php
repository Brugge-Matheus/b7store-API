<?php

use App\Http\Controllers\BannerController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Nette\Utils\Json;

Route::get('/ping', function() {
    return response()->json([
        'pong' => true
    ]);
});

Route::apiResource('banner', BannerController::class);
Route::apiResource('product', ProductController::class);
