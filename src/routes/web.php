<?php

use App\Http\Controllers\Crm\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/crm')->name('crm.')->middleware('auth.vk')->group(function (): void {
    Route::get('/', HomeController::class)->name('dashboard');
});
