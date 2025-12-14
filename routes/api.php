<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| Auth (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Protected (JWT)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::apiResource('orders', OrderController::class);
    Route::get('payments', [PaymentController::class, 'index']);
    Route::get('orders/{order}/payments', [PaymentController::class, 'listForOrder']);
    Route::post('orders/{order}/payments', [PaymentController::class, 'process']);
    Route::delete('orders/{order}', [OrderController::class, 'destroy']);


    Route::get('/ping', function () {
        return response()->json(['ok' => true]);
    });
});
