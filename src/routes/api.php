<?php

use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Products
    Route::get('/products', [ProductController::class, 'index'])
        ->name('api.v1.products.index');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])
        ->name('api.v1.orders.index');
    Route::post('/orders', [OrderController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('api.v1.orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])
        ->name('api.v1.orders.show');
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])
        ->name('api.v1.orders.status');
});