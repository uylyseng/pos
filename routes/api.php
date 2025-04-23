<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Discount;
use App\Models\ExchangeRate;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\DiscountController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('api')->group(function() {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/exchange-rates/current', [ExchangeRateController::class, 'current']);
    Route::get('/discounts/applicable', [DiscountController::class, 'applicable']);
});

Route::post('/orders', [OrderController::class, 'store']);

Route::get('/discounts/applicable', function (Request $request) {
    $amount = $request->query('amount', 0);

    $discount = Discount::active()
        ->minPurchase($amount)
        ->orderBy('amount', 'desc')
        ->first();

    return response()->json([
        'discount' => $discount
    ]);
});

Route::get('/exchange-rates/current', function () {
    $exchangeRate = ExchangeRate::getCurrentRate(1, 2); // Assuming 1=USD, 2=KHR
    return response()->json([
        'rate' => $exchangeRate ? $exchangeRate->rate : 4100 // Default fallback rate
    ]);
});
