<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return ["message" => "hello"];
// });
Route::apiResource('product', ProductController::class);

Route::post('/neworder', [OrderController::class, 'newOrder']);
Route::post('/order', [OrderController::class, 'order']);
Route::post('/cart', [OrderController::class, 'cart']);
Route::get('/order', [OrderController::class, 'index']);
Route::put('/order', [OrderController::class, 'update']);
Route::delete('/order', [OrderController::class, 'destroy']);
Route::post('/order/delete', [OrderController::class, 'deleteProduct']);

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');