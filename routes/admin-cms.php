<?php

use App\Http\Controllers\Admin\Cms\CategoryController;
use App\Http\Controllers\Admin\Cms\ContentPageController;
use App\Http\Controllers\Admin\Cms\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('posts', PostController::class)->except(['show']);
Route::get('posts/{post}/preview', [PostController::class, 'preview'])->name('posts.preview');

Route::post('content-pages/bulk', [ContentPageController::class, 'bulk'])->name('content-pages.bulk');
Route::post('content-pages/{content_page}/duplicate', [ContentPageController::class, 'duplicate'])->name('content-pages.duplicate');
Route::resource('content-pages', ContentPageController::class)->except(['show'])->parameters([
    'content-pages' => 'content_page',
]);
Route::get('content-pages/{content_page}/preview', [ContentPageController::class, 'preview'])->name('content-pages.preview');

Route::resource('categories', CategoryController::class)->except(['show']);
