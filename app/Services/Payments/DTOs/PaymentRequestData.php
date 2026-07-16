<?php

namespace App\Services\Payments\DTOs;

final readonly class PaymentRequestData
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public int $donationId,
        public string $reference,
        public float $amount,
        public string $currency,
        public string $donorEmail,
        public string $successUrl,
        public string $cancelUrl,
        public array $metadata = [],
    ) {}
}
