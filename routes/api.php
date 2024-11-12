<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});




Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('products')->group(function () {
        Route::get('products', [ProductController::class, 'getAll']);
        Route::get('search', [ProductController::class, 'search']);
    });

    Route::prefix('orders')->group(function () {
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders/{id}', [OrderController::class, 'getOrx`derDetails']);
    });
});
