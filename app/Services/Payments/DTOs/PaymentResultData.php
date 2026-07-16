<?php

namespace App\Services\Payments\DTOs;

final readonly class PaymentResultData
{
    /**
     * @param  array<string, mixed>|null  $rawResponse
     */
    public function __construct(
        public bool $success,
        public string $gateway,
        public string $status,
        public ?string $checkoutUrl = null,
        public ?string $gatewayReference = null,
        public ?string $gatewayTransactionId = null,
        public ?string $error = null,
        public ?array $rawResponse = null,
    ) {}

    public static function failed(string $gateway, string $error): self
    {
        return new self(
            success: false,
            gateway: $gateway,
            status: 'failed',
            error: $error,
        );
    }
}
