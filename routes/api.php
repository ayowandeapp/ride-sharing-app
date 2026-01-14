<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TripController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/login/verify', [LoginController::class, 'verify']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/driver', [DriverController::class, 'show']);
    Route::post('/driver', [DriverController::class, 'update']);

    Route::post('/trip', [TripController::class, 'store']);
    Route::get('/trip/{trip}', [TripController::class, 'show']);
    Route::post('/trip/{trip}/accept', [TripController::class, 'accept']);
    Route::post('/trip/{trip}/start', [TripController::class, 'start']);
    Route::post('/trip/{trip}/end', [TripController::class, 'end']);
    Route::post('/trip/{trip}/location', [TripController::class, 'location']);

    Route::get('/trip/{trip}/checkout', [TransactionController::class, 'checkout'])->name('checkout');

    Route::get('/trip/{trip}/paystack/checkout', [PaystackController::class, 'initialize'])->name('paystack.checkout');
    Route::get('/paystack-checkout/verify', [PaystackController::class, 'verify']);

});

//add a custom middleware to protect this routes

Route::post('/paystack/webhook', [PaystackController::class, 'webhook']);

Route::get('/checkout/success', [TransactionController::class, 'success'])->name('checkout-success');

Route::get('/checkout/cancel', [TransactionController::class, 'cancel'])->name('checkout-cancel');

Route::get('/paystack-checkout/callback', [PaystackController::class, 'callback']);
