<?php

namespace App\Services\Payments\Contracts;

use App\Services\Payments\DTOs\PaymentRequestData;
use App\Services\Payments\DTOs\PaymentResultData;
use App\Services\Payments\DTOs\WebhookResultData;

interface PaymentGatewayInterface
{
    public function gateway(): string;

    public function isConfigured(): bool;

    public function createCheckout(PaymentRequestData $request): PaymentResultData;

    public function verifyPayment(string $gatewayReference, array $context = []): PaymentResultData;

  /**
     * @param  array<string, string|null>  $headers
     */
    public function handleWebhook(string $payload, array $headers): WebhookResultData;
}
