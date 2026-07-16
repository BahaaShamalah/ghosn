<?php

use App\Http\Controllers\Webhooks\PayPalWebhookController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/stripe', StripeWebhookController::class)
    ->name('webhooks.stripe')
    ->withoutMiddleware([ValidateCsrfToken::class]);

Route::post('/webhooks/paypal', PayPalWebhookController::class)
    ->name('webhooks.paypal')
    ->withoutMiddleware([ValidateCsrfToken::class]);
