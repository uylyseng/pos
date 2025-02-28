<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExchangeRateController;
use App\Http\Controllers\Api\DiscountController;
use App\Livewire\Pos\PosTerminal;
use App\Livewire\Pos\OrdersList;
use App\Livewire\OrderHistory\OrderHistory;
use App\Livewire\OrderHistory\Show as OrderShow;
use Illuminate\Support\Facades\Auth;

// Add redirect for old order-history URL to new orders URL
Route::redirect('/order-history', '/orders')->middleware('auth');

// POS routes
Route::middleware(['auth'])->group(function () {
    Route::get('/pos', PosTerminal::class)->name('pos');
    // Route::get('/pos/orders', OrdersList::class)->name('pos.orders');

    // Order history route
    Route::get('/orders', OrderHistory::class)->name('orders.index');
    Route::get('/orders/{order}', OrderShow::class)->name('orders.show');
    Route::get('/orders/{order}/receipt', [App\Http\Controllers\OrderController::class, 'receipt'])->name('orders.receipt');

    // Add a simple logout route
    Route::post('/logout', function() {
        Auth::logout();
        return redirect('/login');
    });

    // Receipt routes - moved inside auth middleware group
    Route::get('/receipts/view/{filename}', [App\Http\Controllers\ReceiptController::class, 'view'])
        ->name('receipts.view');

    Route::get('/receipts/generate/{orderId}', [App\Http\Controllers\ReceiptController::class, 'generate'])
        ->name('receipts.generate');
});

// // Define login route if not already defined
// Route::get('/login', function() {
//     return view('auth.login');
// })->middleware('guest')->name('login');
