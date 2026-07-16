<?php

use App\Http\Controllers\Public\OfficialPageController;
use App\Support\ReservedSlug;
use Illuminate\Support\Facades\Route;

Route::get('/{slug}', [OfficialPageController::class, 'show'])
    ->name('pages.show')
    ->where('slug', ReservedSlug::routePattern());
