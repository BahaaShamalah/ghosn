<?php

namespace App\Services\Payments;

use App\Models\Donation;
use App\Models\PaymentGatewayEvent;
use Illuminate\Support\Facades\Log;

class PaymentGatewayEventLogger
{
    public function isDuplicate(string $gateway, string $eventId): bool
    {
        return PaymentGatewayEvent::query()
            ->where('gateway', $gateway)
            ->where('event_id', $eventId)
            ->exists();
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    public function record(
        string $gateway,
        string $eventId,
        ?string $eventType = null,
        ?int $donationId = null,
        ?array $payload = null,
        string $status = 'processed',
    ): PaymentGatewayEvent {
        return PaymentGatewayEvent::query()->create([
            'gateway' => $gateway,
            'event_id' => $eventId,
            'event_type' => $eventType,
            'donation_id' => $donationId,
            'status' => $status,
            'payload' => $this->sanitizePayload($payload),
            'processed_at' => now(),
        ]);
    }

    public function logSafe(string $level, string $message, array $context = []): void
    {
        unset($context['payload'], $context['raw_body']);

        Log::log($level, $message, $context);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     * @return array<string, mixed>|null
     */
    private function sanitizePayload(?array $payload): ?array
    {
        if ($payload === null) {
            return null;
        }

        $json = json_encode($payload);

        if ($json === false || strlen($json) > 8000) {
            return ['truncated' => true, 'keys' => array_keys($payload)];
        }

        return $payload;
    }
}
