<?php

use App\Http\Controllers\Admin\AdminLocaleController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('locale/{locale}', [AdminLocaleController::class, 'switch'])
        ->name('locale.switch')
        ->where('locale', implode('|', config('locale.supported', ['en', 'ar'])));

    Route::middleware('guest')->group(function (): void {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('login.store');
    });

    Route::post('logout', [LoginController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');
});
