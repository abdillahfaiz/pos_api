<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('/product', ProductController::class)->middleware('auth:sanctum');
Route::apiResource('/order', [OrderController::class, 'store'])->middleware('auth:sanctum');
// Route::post('/product', [ProductController::class, 'store'])->middleware('auth:sanctum');
