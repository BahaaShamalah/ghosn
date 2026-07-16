<?php

use App\Http\Controllers\Admin\Media\MediaController;
use App\Http\Controllers\Admin\Media\MediaPickerController;

Route::get('media', [MediaController::class, 'index'])->name('media.index');
Route::post('media', [MediaController::class, 'store'])->name('media.store');
Route::delete('media/{medium}', [MediaController::class, 'destroy'])->name('media.destroy');

Route::get('media/picker', [MediaPickerController::class, 'index'])->name('media.picker');
Route::post('media/picker', [MediaPickerController::class, 'store'])->name('media.picker.store');
