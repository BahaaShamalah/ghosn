<?php

namespace App\Services\Payments\Gateways;

use App\Models\Donation;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\DTOs\PaymentRequestData;
use App\Services\Payments\DTOs\PaymentResultData;
use App\Services\Payments\DTOs\WebhookResultData;
use App\Services\Payments\PaymentGatewayEventLogger;
use App\Support\PaymentSettings;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct(
        private readonly PaymentSettings $settings,
        private readonly PaymentGatewayEventLogger $events,
    ) {}

    public function gateway(): string
    {
        return 'stripe';
    }

    public function isConfigured(): bool
    {
        return $this->settings->stripeEnabled();
    }

    public function createCheckout(PaymentRequestData $request): PaymentResultData
    {
        if (! $this->isConfigured()) {
            return PaymentResultData::failed($this->gateway(), 'Stripe is not configured.');
        }

        Stripe::setApiKey($this->settings->stripeSecret());

        $session = Session::create([
            'mode' => 'payment',
            'customer_email' => $request->donorEmail,
            'client_reference_id' => $request->reference,
            'metadata' => array_merge($request->metadata, [
                'donation_id' => (string) $request->donationId,
                'reference' => $request->reference,
            ]),
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower($request->currency),
                    'unit_amount' => (int) round($request->amount * 100),
                    'product_data' => [
                        'name' => $this->settings->stripeProductName(),
                        'description' => $this->settings->stripeProductDescription().' Ref: '.$request->reference,
                    ],
                ],
            ]],
            'success_url' => $request->successUrl,
            'cancel_url' => $request->cancelUrl,
        ]);

        Donation::query()->whereKey($request->donationId)->update([
            'gateway' => $this->gateway(),
            'gateway_reference' => $session->id,
        ]);

        return new PaymentResultData(
            success: true,
            gateway: $this->gateway(),
            status: 'pending',
            checkoutUrl: $session->url,
            gatewayReference: $session->id,
        );
    }

    public function verifyPayment(string $gatewayReference, array $context = []): PaymentResultData
    {
        if (! $this->isConfigured()) {
            return PaymentResultData::failed($this->gateway(), 'Stripe is not configured.');
        }

        Stripe::setApiKey($this->settings->stripeSecret());

        $session = Session::retrieve($gatewayReference);
        $paid = $session->payment_status === 'paid';

        return new PaymentResultData(
            success: $paid,
            gateway: $this->gateway(),
            status: $paid ? 'paid' : 'pending',
            gatewayReference: $session->id,
            gatewayTransactionId: $session->payment_intent ? (string) $session->payment_intent : null,
        );
    }

    public function handleWebhook(string $payload, array $headers): WebhookResultData
    {
        $signature = $headers['stripe-signature'] ?? $headers['Stripe-Signature'] ?? null;
        $secret = $this->settings->stripeWebhookSecret();

        if (! filled($secret) || ! filled($signature)) {
            return new WebhookResultData(accepted: false, processed: false, duplicate: false);
        }

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (\Throwable $exception) {
            $this->events->logSafe('warning', 'Stripe webhook signature rejected', [
                'gateway' => $this->gateway(),
                'error' => $exception->getMessage(),
            ]);

            return new WebhookResultData(accepted: false, processed: false, duplicate: false);
        }

        if ($this->events->isDuplicate($this->gateway(), $event->id)) {
            return new WebhookResultData(
                accepted: true,
                processed: false,
                duplicate: true,
                eventId: $event->id,
                eventType: $event->type,
            );
        }

        $donationId = null;
        $processed = false;

        if ($event->type === 'checkout.session.completed') {
            /** @var \Stripe\Checkout\Session $session */
            $session = $event->data->object;
            $donation = Donation::query()->where('gateway_reference', $session->id)->first();
            $donationId = $donation?->id;
            $processed = $donation !== null && $session->payment_status === 'paid';
        }

        $this->events->record(
            gateway: $this->gateway(),
            eventId: $event->id,
            eventType: $event->type,
            donationId: $donationId,
            payload: ['type' => $event->type],
            status: $processed ? 'processed' : 'ignored',
        );

        return new WebhookResultData(
            accepted: true,
            processed: $processed,
            duplicate: false,
            donationId: $donationId,
            eventId: $event->id,
            eventType: $event->type,
            context: [
                'transaction_id' => $event->data->object->payment_intent ?? null,
                'gateway_reference' => $event->data->object->id ?? null,
            ],
        );
    }
}
