<?php

namespace App\Services\Payments\Gateways;

use App\Models\Donation;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\DTOs\PaymentRequestData;
use App\Services\Payments\DTOs\PaymentResultData;
use App\Services\Payments\DTOs\WebhookResultData;
use App\Services\Payments\PaymentGatewayEventLogger;
use App\Support\PaymentSettings;
use Illuminate\Support\Facades\Http;

class PayPalBusinessGateway implements PaymentGatewayInterface
{
    public function __construct(
        private readonly PaymentSettings $settings,
        private readonly PaymentGatewayEventLogger $events,
    ) {}

    public function gateway(): string
    {
        return 'paypal';
    }

    public function isConfigured(): bool
    {
        return $this->settings->paypalEnabled();
    }

    public function createJsSdkOrder(Donation $donation): array
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('PayPal is not configured.');
        }

        $token = $this->accessToken();
        $amount = number_format((float) $donation->amount, 2, '.', '');
        $currency = strtoupper($donation->currency);

        $response = Http::withToken($token)
            ->acceptJson()
            ->post($this->settings->paypalApiBase().'/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'application_context' => [
                    'brand_name' => 'GHOSN Relief Team',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                ],
                'purchase_units' => [[
                    'reference_id' => $donation->reference,
                    'custom_id' => (string) $donation->id,
                    'description' => $this->settings->paypalItemDescription(),
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => $amount,
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => $currency,
                                'value' => $amount,
                            ],
                        ],
                    ],
                    'items' => [[
                        'name' => $this->settings->paypalItemName(),
                        'description' => $this->settings->paypalItemDescription(),
                        'quantity' => '1',
                        'unit_amount' => [
                            'currency_code' => $currency,
                            'value' => $amount,
                        ],
                    ]],
                ]],
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('PayPal order creation failed.');
        }

        $order = $response->json();
        $orderId = (string) ($order['id'] ?? '');

        if ($orderId === '') {
            throw new \RuntimeException('PayPal order ID missing.');
        }

        $donation->update([
            'gateway' => $this->gateway(),
            'gateway_reference' => $orderId,
            'metadata' => array_merge($donation->metadata ?? [], [
                'paypal_item_name' => $this->settings->paypalItemName(),
                'paypal_flow' => 'js_sdk',
            ]),
        ]);

        $this->events->logSafe('info', 'PayPal order created', [
            'gateway' => $this->gateway(),
            'donation_id' => $donation->id,
            'reference' => $donation->reference,
            'order_id' => $orderId,
            'order_status' => (string) ($order['status'] ?? 'CREATED'),
        ]);

        return [
            'order_id' => $orderId,
            'status' => (string) ($order['status'] ?? 'CREATED'),
        ];
    }

    public function createCheckout(PaymentRequestData $request): PaymentResultData
    {
        if (! $this->isConfigured()) {
            return PaymentResultData::failed($this->gateway(), 'PayPal is not configured.');
        }

        $token = $this->accessToken();

        $response = Http::withToken($token)
            ->acceptJson()
            ->post($this->settings->paypalApiBase().'/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'application_context' => [
                    'brand_name' => 'GHOSN Relief Team',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                    'return_url' => $request->successUrl,
                    'cancel_url' => $request->cancelUrl,
                ],
                'purchase_units' => [[
                    'reference_id' => $request->reference,
                    'custom_id' => (string) $request->donationId,
                    'description' => $this->settings->paypalItemDescription(),
                    'amount' => [
                        'currency_code' => strtoupper($request->currency),
                        'value' => number_format($request->amount, 2, '.', ''),
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => strtoupper($request->currency),
                                'value' => number_format($request->amount, 2, '.', ''),
                            ],
                        ],
                    ],
                    'items' => [[
                        'name' => $this->settings->paypalItemName(),
                        'description' => $this->settings->paypalItemDescription(),
                        'quantity' => '1',
                        'unit_amount' => [
                            'currency_code' => strtoupper($request->currency),
                            'value' => number_format($request->amount, 2, '.', ''),
                        ],
                    ]],
                ]],
            ]);

        if (! $response->successful()) {
            return PaymentResultData::failed($this->gateway(), 'PayPal order creation failed.');
        }

        $order = $response->json();
        $orderId = (string) ($order['id'] ?? '');
        $approveUrl = collect($order['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        if ($orderId === '' || ! $approveUrl) {
            return PaymentResultData::failed($this->gateway(), 'PayPal approval URL missing.');
        }

        Donation::query()->whereKey($request->donationId)->update([
            'gateway' => $this->gateway(),
            'gateway_reference' => $orderId,
            'metadata' => array_merge($request->metadata, [
                'paypal_item_name' => $this->settings->paypalItemName(),
            ]),
        ]);

        return new PaymentResultData(
            success: true,
            gateway: $this->gateway(),
            status: 'pending',
            checkoutUrl: $approveUrl,
            gatewayReference: $orderId,
        );
    }

    public function verifyPayment(string $gatewayReference, array $context = []): PaymentResultData
    {
        $donation = isset($context['donation']) && $context['donation'] instanceof Donation
            ? $context['donation']
            : null;

        if ($donation === null) {
            return $this->captureOrderByReference($gatewayReference);
        }

        return $this->captureOrderForDonation($donation, $gatewayReference);
    }

    public function captureOrderForDonation(Donation $donation, string $orderId): PaymentResultData
    {
        if (! $this->isConfigured()) {
            return PaymentResultData::failed($this->gateway(), 'PayPal is not configured.');
        }

        $orderId = trim($orderId);
        $token = $this->accessToken();

        $this->events->logSafe('info', 'PayPal capture requested', [
            'gateway' => $this->gateway(),
            'donation_id' => $donation->id,
            'reference' => $donation->reference,
            'order_id' => $orderId,
        ]);

        $order = $this->captureOrFetchOrder($token, $orderId);

        if ($order === null) {
            $this->events->logSafe('warning', 'PayPal capture failed', [
                'gateway' => $this->gateway(),
                'donation_id' => $donation->id,
                'order_id' => $orderId,
            ]);

            return PaymentResultData::failed($this->gateway(), 'PayPal capture failed.');
        }

        if (! $this->orderBelongsToDonation($order, $donation, $orderId)) {
            $this->events->logSafe('warning', 'PayPal capture rejected: order does not belong to donation', [
                'gateway' => $this->gateway(),
                'donation_id' => $donation->id,
                'order_id' => $orderId,
            ]);

            return PaymentResultData::failed($this->gateway(), 'PayPal order does not belong to this donation.');
        }

        $paymentState = $this->resolvePaymentState($order);
        $paid = $paymentState['paid'];
        $transactionId = $paymentState['capture_id'];

        $this->events->logSafe('info', 'PayPal capture response status', [
            'gateway' => $this->gateway(),
            'donation_id' => $donation->id,
            'order_id' => $orderId,
            'order_status' => $paymentState['order_status'],
            'capture_status' => $paymentState['capture_status'],
            'capture_id' => $transactionId !== '' ? $transactionId : null,
            'paid' => $paid,
        ]);

        if ($paid && ! $this->capturedAmountMatchesDonation($order, $donation)) {
            return PaymentResultData::failed($this->gateway(), 'PayPal capture amount mismatch.');
        }

        return new PaymentResultData(
            success: $paid,
            gateway: $this->gateway(),
            status: $paid ? 'paid' : 'pending',
            gatewayReference: $orderId,
            gatewayTransactionId: $transactionId !== '' ? $transactionId : null,
            error: $paid ? null : 'PayPal payment is not completed.',
            rawResponse: $this->sanitizePayPalResponse($order),
        );
    }

    public function findDonationByPayPalOrderId(string $orderId): ?Donation
    {
        $orderId = trim($orderId);

        if ($orderId === '') {
            return null;
        }

        return Donation::query()
            ->where('gateway', $this->gateway())
            ->where('gateway_reference', $orderId)
            ->first();
    }

    /**
     * @return array{paid: bool, capture_id: string, order_status: string, capture_status: string}
     */
    private function resolvePaymentState(array $order): array
    {
        $orderStatus = strtoupper((string) ($order['status'] ?? ''));
        $capture = $this->extractCapture($order);
        $captureId = is_array($capture) ? (string) ($capture['id'] ?? '') : '';
        $captureStatus = is_array($capture) ? strtoupper((string) ($capture['status'] ?? '')) : '';
        $paid = ($orderStatus === 'COMPLETED' || $captureStatus === 'COMPLETED') && $captureId !== '';

        return [
            'paid' => $paid,
            'capture_id' => $captureId,
            'order_status' => $orderStatus,
            'capture_status' => $captureStatus,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function captureOrFetchOrder(string $token, string $orderId): ?array
    {
        $captureResponse = Http::withToken($token)
            ->acceptJson()
            ->withHeaders(['Prefer' => 'return=representation'])
            ->withBody('{}', 'application/json')
            ->post($this->settings->paypalApiBase().'/v2/checkout/orders/'.$orderId.'/capture');

        if ($captureResponse->successful()) {
            $order = $captureResponse->json();

            return is_array($order) ? $order : null;
        }

        if ($this->isRecoverableCaptureFailure($captureResponse)) {
            return $this->fetchPayPalOrder($token, $orderId);
        }

        $existingOrder = $this->fetchPayPalOrder($token, $orderId);

        if ($existingOrder !== null && $this->resolvePaymentState($existingOrder)['paid']) {
            $this->events->logSafe('info', 'PayPal order already completed after capture error', [
                'gateway' => $this->gateway(),
                'order_id' => $orderId,
                'order_status' => $existingOrder['status'] ?? null,
            ]);

            return $existingOrder;
        }

        $this->events->logSafe('warning', 'PayPal capture API error', [
            'gateway' => $this->gateway(),
            'order_id' => $orderId,
            'http_status' => $captureResponse->status(),
            'paypal_error' => $this->summarizePayPalError($captureResponse),
        ]);

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchPayPalOrder(string $token, string $orderId): ?array
    {
        $orderResponse = Http::withToken($token)
            ->acceptJson()
            ->get($this->settings->paypalApiBase().'/v2/checkout/orders/'.$orderId);

        if (! $orderResponse->successful()) {
            $this->events->logSafe('warning', 'PayPal order lookup failed', [
                'gateway' => $this->gateway(),
                'order_id' => $orderId,
                'http_status' => $orderResponse->status(),
                'paypal_error' => $this->summarizePayPalError($orderResponse),
            ]);

            return null;
        }

        $order = $orderResponse->json();

        return is_array($order) ? $order : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function summarizePayPalError(\Illuminate\Http\Client\Response $response): array
    {
        $body = $response->json();

        if (! is_array($body)) {
            return ['http_status' => $response->status()];
        }

        return array_filter([
            'name' => $body['name'] ?? null,
            'message' => $body['message'] ?? null,
            'debug_id' => $body['debug_id'] ?? null,
            'details' => $body['details'] ?? null,
        ]);
    }

    private function isRecoverableCaptureFailure(\Illuminate\Http\Client\Response $response): bool
    {
        if (! in_array($response->status(), [400, 422, 409], true)) {
            return false;
        }

        $body = $response->json();

        if (! is_array($body)) {
            return false;
        }

        $encoded = json_encode($body);

        if (! is_string($encoded)) {
            return false;
        }

        foreach ([
            'ORDER_ALREADY_CAPTURED',
            'ORDER_ALREADY_COMPLETED',
            'CAPTURE_ALREADY_COMPLETED',
        ] as $issue) {
            if (str_contains($encoded, $issue)) {
                return true;
            }
        }

        return false;
    }

    private function captureOrderByReference(string $orderId): PaymentResultData
    {
        if (! $this->isConfigured()) {
            return PaymentResultData::failed($this->gateway(), 'PayPal is not configured.');
        }

        $orderId = trim($orderId);
        $token = $this->accessToken();
        $order = $this->captureOrFetchOrder($token, $orderId);

        if ($order === null) {
            return PaymentResultData::failed($this->gateway(), 'PayPal capture failed.');
        }

        $paymentState = $this->resolvePaymentState($order);
        $transactionId = $paymentState['capture_id'];
        $paid = $paymentState['paid'];

        return new PaymentResultData(
            success: $paid,
            gateway: $this->gateway(),
            status: $paid ? 'paid' : 'pending',
            gatewayReference: $orderId,
            gatewayTransactionId: $transactionId !== '' ? $transactionId : null,
            rawResponse: $this->sanitizePayPalResponse($order),
        );
    }

    /**
     * @param  array<string, mixed>  $order
     */
    private function orderBelongsToDonation(array $order, Donation $donation, string $orderId): bool
    {
        $storedReference = (string) ($donation->gateway_reference ?? '');

        if ($storedReference !== '' && strcasecmp($storedReference, $orderId) !== 0) {
            return false;
        }

        $customId = (string) ($order['purchase_units'][0]['custom_id'] ?? '');

        return $customId === '' || (int) $customId === (int) $donation->id;
    }

    /**
     * @param  array<string, mixed>  $order
     * @return array<string, mixed>|null
     */
    private function extractCapture(array $order): ?array
    {
        foreach ($order['purchase_units'] ?? [] as $unit) {
            if (! is_array($unit)) {
                continue;
            }

            $captures = $unit['payments']['captures'] ?? [];

            if (! is_array($captures)) {
                continue;
            }

            foreach ($captures as $capture) {
                if (is_array($capture) && filled($capture['id'] ?? null)) {
                    return $capture;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $order
     * @return array<string, mixed>
     */
    private function sanitizePayPalResponse(array $order): array
    {
        $sanitized = json_decode(json_encode($order) ?: '[]', true);

        if (! is_array($sanitized)) {
            return ['truncated' => true];
        }

        unset($sanitized['payer'], $sanitized['payment_source']);

        $encoded = json_encode($sanitized);

        if ($encoded === false || strlen($encoded) > 12000) {
            return [
                'id' => $sanitized['id'] ?? null,
                'status' => $sanitized['status'] ?? null,
                'truncated' => true,
            ];
        }

        return $sanitized;
    }

    private function isAlreadyCapturedResponse(\Illuminate\Http\Client\Response $response): bool
    {
        return $this->isRecoverableCaptureFailure($response);
    }

    /**
     * @param  array<string, mixed>  $order
     */
    public function capturedAmountMatchesDonation(array $order, Donation $donation): bool
    {
        $capture = $this->extractCapture($order);
        $capturedValue = is_array($capture)
            ? ($capture['amount']['value'] ?? null)
            : ($order['purchase_units'][0]['amount']['value'] ?? null);

        if ($capturedValue === null) {
            return false;
        }

        $expected = number_format((float) $donation->amount, 2, '.', '');
        $actual = number_format((float) $capturedValue, 2, '.', '');

        return $expected === $actual
            && strtoupper((string) (is_array($capture) ? ($capture['amount']['currency_code'] ?? $donation->currency) : ($order['purchase_units'][0]['amount']['currency_code'] ?? $donation->currency))) === strtoupper($donation->currency);
    }

    public function handleWebhook(string $payload, array $headers): WebhookResultData
    {
        if (! filled($this->settings->paypalWebhookId())) {
            return new WebhookResultData(
                accepted: true,
                processed: false,
                duplicate: false,
                eventType: 'webhook_not_configured',
            );
        }

        $event = json_decode($payload, true);

        if (! is_array($event)) {
            return new WebhookResultData(accepted: false, processed: false, duplicate: false);
        }

        $eventId = (string) ($event['id'] ?? $headers['paypal-transmission-id'] ?? '');
        $eventType = (string) ($event['event_type'] ?? '');

        if ($eventId === '' || ! $this->verifyWebhookSignature($payload, $headers, $event)) {
            $this->events->logSafe('warning', 'PayPal webhook signature rejected', [
                'gateway' => $this->gateway(),
                'event_type' => $eventType,
            ]);

            return new WebhookResultData(accepted: false, processed: false, duplicate: false);
        }

        if ($this->events->isDuplicate($this->gateway(), $eventId)) {
            return new WebhookResultData(
                accepted: true,
                processed: false,
                duplicate: true,
                eventId: $eventId,
                eventType: $eventType,
            );
        }

        $donationId = null;
        $processed = false;
        $transactionId = null;
        $gatewayReference = null;

        if (in_array($eventType, ['PAYMENT.CAPTURE.COMPLETED', 'CHECKOUT.ORDER.APPROVED'], true)) {
            $resource = $event['resource'] ?? [];
            $gatewayReference = (string) ($resource['supplementary_data']['related_ids']['order_id'] ?? $resource['id'] ?? '');
            $transactionId = (string) ($resource['id'] ?? '');
            $customId = (string) ($resource['custom_id'] ?? $event['resource']['purchase_units'][0]['custom_id'] ?? '');

            $donation = $customId !== ''
                ? Donation::query()->find((int) $customId)
                : Donation::query()->where('gateway_reference', $gatewayReference)->first();

            $donationId = $donation?->id;
            $processed = $donation !== null && $eventType === 'PAYMENT.CAPTURE.COMPLETED';
        }

        $this->events->record(
            gateway: $this->gateway(),
            eventId: $eventId,
            eventType: $eventType,
            donationId: $donationId,
            payload: ['event_type' => $eventType],
            status: $processed ? 'processed' : 'ignored',
        );

        return new WebhookResultData(
            accepted: true,
            processed: $processed,
            duplicate: false,
            donationId: $donationId,
            eventId: $eventId,
            eventType: $eventType,
            context: [
                'transaction_id' => $transactionId,
                'gateway_reference' => $gatewayReference,
            ],
        );
    }

    private function accessToken(): string
    {
        $response = Http::asForm()
            ->withBasicAuth($this->settings->paypalClientId(), $this->settings->paypalClientSecret())
            ->post($this->settings->paypalApiBase().'/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('PayPal authentication failed.');
        }

        return (string) $response->json('access_token');
    }

    /**
     * @param  array<string, mixed>  $event
     * @param  array<string, string|null>  $headers
     */
    private function verifyWebhookSignature(string $payload, array $headers, array $event): bool
    {
        $webhookId = $this->settings->paypalWebhookId();

        if (! filled($webhookId)) {
            return false;
        }

        $normalized = [];
        foreach ($headers as $key => $value) {
            $normalized[strtolower((string) $key)] = $value;
        }

        $response = Http::withToken($this->accessToken())
            ->acceptJson()
            ->post($this->settings->paypalApiBase().'/v1/notifications/verify-webhook-signature', [
                'auth_algo' => $normalized['paypal-auth-algo'] ?? '',
                'cert_url' => $normalized['paypal-cert-url'] ?? '',
                'transmission_id' => $normalized['paypal-transmission-id'] ?? '',
                'transmission_sig' => $normalized['paypal-transmission-sig'] ?? '',
                'transmission_time' => $normalized['paypal-transmission-time'] ?? '',
                'webhook_id' => $webhookId,
                'webhook_event' => $event,
            ]);

        return $response->successful() && $response->json('verification_status') === 'SUCCESS';
    }
}
