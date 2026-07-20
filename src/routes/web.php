<?php

use App\Http\Controllers\Crm\HomeController;
use App\Http\Controllers\Crm\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/crm')->name('crm.')->middleware('auth.vk')->group(function (): void {
    Route::get('/', HomeController::class)->name('dashboard');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
});
