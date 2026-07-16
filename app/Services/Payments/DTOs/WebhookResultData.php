<?php

namespace App\Services\Payments\DTOs;

final readonly class WebhookResultData
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public bool $accepted,
        public bool $processed,
        public bool $duplicate,
        public ?int $donationId = null,
        public ?string $eventId = null,
        public ?string $eventType = null,
        public array $context = [],
    ) {}
}
