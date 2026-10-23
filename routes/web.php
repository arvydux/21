<?php

use App\Http\Controllers\OnlineOrdersController;
use App\Models\OrderNumbers;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::view('kasa', 'kasa')
        // ->middleware(['auth', 'verified'])
        ->name('kasa');

    Route::view('menu', 'menu')
        // ->middleware(['auth', 'verified'])
        ->name('menu');

    Route::view('kitchen', 'kitchen')
        // ->middleware(['auth', 'verified'])
        ->name('kitchen');

    Route::view('orders', 'orders')
        // ->middleware(['auth', 'verified'])
        ->name('orders');

    Route::get('online', [OnlineOrdersController::class, 'show'])->name('online-orders');
    Route::get('online/data', [OnlineOrdersController::class, 'data'])->name('online-orders.data');
    Route::post('online/subscribe', [OnlineOrdersController::class, 'subscribe'])->name('online-orders.subscribe');
    Route::post('online/unsubscribe', [OnlineOrdersController::class, 'unsubscribe'])->name('online-orders.unsubscribe');

    Route::view('settings', 'settings')
        // ->middleware(['auth', 'verified'])
        ->name('settings');

    Route::get('/get-unready-orders', function () {
        return OrderNumbers::where('is_ready', false)->get();
    });

    Route::get('/get-ready-orders', function () {
        return OrderNumbers::where('is_ready', true)->where('is_taken', false)->orderBy('updated_at', 'desc')->get();
    });
});

require __DIR__.'/auth.php';
