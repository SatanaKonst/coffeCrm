<?php

use App\Http\Controllers\Crm\HomeController;
use App\Http\Controllers\Crm\OrderController;
use App\Http\Controllers\Crm\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/crm')->name('crm.')->middleware('auth.vk')->group(function (): void {
    Route::get('/', HomeController::class)->name('dashboard');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create/{product}', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
});
