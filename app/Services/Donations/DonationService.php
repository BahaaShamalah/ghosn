<?php

namespace App\Services\Donations;

use App\Models\Donation;
use App\Services\Donors\DonorEmailService;
use App\Services\Donors\DonorService;
use App\Services\Campaigns\CampaignService;
use App\Services\Payments\DTOs\PaymentResultData;
use App\Services\Payments\DTOs\WebhookResultData;
use App\Services\Payments\Gateways\PayPalBusinessGateway;
use App\Services\Payments\PaymentGatewayManager;
use App\Support\DonationSettings;
use App\Support\PaymentSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DonationService
{
    public function __construct(
        private readonly DonationSettings $donationSettings,
        private readonly PaymentSettings $paymentSettings,
        private readonly PaymentGatewayManager $gateways,
        private readonly CampaignService $campaigns,
        private readonly DonorService $donors,
        private readonly DonorEmailService $donorEmails,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?string $ipAddress = null): Donation
    {
        $method = (string) $data['payment_method'];
        $gateway = match ($method) {
            Donation::METHOD_STRIPE_CARD => Donation::GATEWAY_STRIPE,
            Donation::METHOD_PAYPAL => Donation::GATEWAY_PAYPAL,
            default => Donation::GATEWAY_BANK,
        };

        $donation = Donation::query()->create([
            'reference' => $this->generateReference(),
            'campaign_id' => $data['campaign_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? $this->donationSettings->currency(),
            'payment_method' => $method,
            'gateway' => $gateway,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => $data['donor_name'],
            'donor_email' => $data['donor_email'],
            'donor_phone' => $data['donor_phone'] ?? null,
            'message' => $data['message'] ?? null,
            'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
            'locale' => $data['locale'] ?? app()->getLocale(),
            'ip_address' => $ipAddress,
        ]);

        $this->donors->syncFromDonation($donation);
        $this->donorEmails->afterDonationCreated($donation->fresh(['donor']));

        return $donation;
    }

    public function initiateGatewayCheckout(Donation $donation): PaymentResultData
    {
        return $this->gateways->createCheckout($donation);
    }

    public function completeGatewayReturn(string $gateway, string $gatewayReference): ?Donation
    {
        $donation = Donation::query()
            ->where('gateway', $gateway)
            ->where('gateway_reference', $gatewayReference)
            ->first();

        if (! $donation) {
            return null;
        }

        if ($donation->isPaid()) {
            return $donation;
        }

        $result = $this->gateways->verifyReturn($gateway, $gatewayReference, [
            'donation' => $donation,
        ]);

        if ($result->success && $result->status === 'paid') {
            return $this->markPaid(
                $donation,
                $result->gatewayTransactionId,
                $result->gatewayReference,
                $this->paypalCaptureMetadata($result),
            );
        }

        Log::warning('Gateway return did not confirm payment.', [
            'gateway' => $gateway,
            'donation_id' => $donation->id,
            'reference' => $donation->reference,
            'gateway_reference' => $gatewayReference,
            'result_status' => $result->status,
            'error' => $result->error,
        ]);

        return $donation->fresh();
    }

    public function processWebhook(string $gateway, string $payload, array $headers): WebhookResultData
    {
        $result = $this->gateways->handleWebhook($gateway, $payload, $headers);

        if ($result->accepted && $result->processed && $result->donationId) {
            $donation = Donation::query()->find($result->donationId);

            if ($donation && ! $donation->isPaid()) {
                $this->markPaid(
                    $donation,
                    $result->context['transaction_id'] ?? null,
                    $result->context['gateway_reference'] ?? $donation->gateway_reference,
                );
            }
        }

        return $result;
    }

    public function findByReference(string $reference): ?Donation
    {
        return Donation::query()->where('reference', $reference)->first();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{order_id: string, donation_id: int, reference: string}
     */
    public function createPayPalOrder(array $data, ?string $ipAddress = null): array
    {
        $donation = $this->create([
            ...$data,
            'payment_method' => Donation::METHOD_PAYPAL,
            'currency' => $this->donationSettings->currency(),
            'locale' => $data['locale'] ?? app()->getLocale(),
        ], $ipAddress);

        $gateway = app(PayPalBusinessGateway::class);
        $order = $gateway->createJsSdkOrder($donation);

        Log::info('PayPal JS SDK order ready for approval.', [
            'donation_id' => $donation->id,
            'reference' => $donation->reference,
            'order_id' => $order['order_id'],
        ]);

        return [
            'orderID' => $order['order_id'],
            'order_id' => $order['order_id'],
            'donation_id' => $donation->id,
            'reference' => $donation->reference,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function capturePayPalOrder(Donation $donation, string $orderId): Donation
    {
        $orderId = trim($orderId);

        return DB::transaction(function () use ($donation, $orderId): Donation {
            /** @var Donation $locked */
            $locked = Donation::query()->lockForUpdate()->findOrFail($donation->id);

            if ($locked->isPaid()) {
                Log::info('PayPal capture skipped: donation already paid.', [
                    'donation_id' => $locked->id,
                    'reference' => $locked->reference,
                    'order_id' => $orderId,
                ]);

                return $locked;
            }

            if ($locked->payment_method !== Donation::METHOD_PAYPAL) {
                throw new \InvalidArgumentException('Invalid donation state for PayPal capture.');
            }

            if (! in_array($locked->status, [Donation::STATUS_PENDING, Donation::STATUS_FAILED], true)) {
                throw new \InvalidArgumentException('Invalid donation state for PayPal capture.');
            }

            if ($locked->status === Donation::STATUS_FAILED) {
                $locked->update(['status' => Donation::STATUS_PENDING]);
                $locked->refresh();
            }

            if (
                filled($locked->gateway_reference)
                && strcasecmp((string) $locked->gateway_reference, $orderId) !== 0
            ) {
                throw new \InvalidArgumentException('PayPal order does not belong to this donation.');
            }

            $gateway = app(PayPalBusinessGateway::class);
            $result = $gateway->captureOrderForDonation($locked, $orderId);

            if ($result->success && $result->status === 'paid') {
                $paid = $this->markPaid(
                    $locked,
                    $result->gatewayTransactionId,
                    $result->gatewayReference ?? $orderId,
                    $this->paypalCaptureMetadata($result),
                );

                $paid->refresh();

                if (! $paid->isPaid()) {
                    Log::error('PayPal donation not marked paid after capture.', [
                        'donation_id' => $paid->id,
                        'reference' => $paid->reference,
                        'order_id' => $orderId,
                    ]);

                    throw new \RuntimeException('PayPal payment could not be confirmed.');
                }

                Log::info('PayPal donation updated to paid.', [
                    'donation_id' => $paid->id,
                    'reference' => $paid->reference,
                    'order_id' => $orderId,
                    'capture_id' => $paid->gateway_transaction_id,
                ]);

                return $paid;
            }

            Log::warning('PayPal capture did not complete donation.', [
                'donation_id' => $locked->id,
                'reference' => $locked->reference,
                'order_id' => $orderId,
                'result_status' => $result->status,
                'error' => $result->error,
            ]);

            throw new \RuntimeException($result->error ?? 'PayPal capture failed.');
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function paypalCaptureMetadata(PaymentResultData $result): array
    {
        return array_filter([
            'paypal_capture' => 'js_sdk',
            'paypal_capture_id' => $result->gatewayTransactionId,
            'paypal_order_id' => $result->gatewayReference,
            'paypal_capture_response' => $result->rawResponse,
        ], static fn ($value) => $value !== null);
    }

    public function markPaid(
        Donation $donation,
        ?string $transactionId = null,
        ?string $gatewayReference = null,
        ?array $metadata = null,
    ): Donation {
        if ($donation->isPaid()) {
            return $donation;
        }

        $updates = [
            'status' => Donation::STATUS_PAID,
            'gateway_transaction_id' => $transactionId ?? $donation->gateway_transaction_id,
            'gateway_reference' => $gatewayReference ?? $donation->gateway_reference,
            'paid_at' => now(),
        ];

        if ($metadata !== null) {
            $updates['metadata'] = array_merge($donation->metadata ?? [], $metadata);
        }

        $donation->update($updates);

        $fresh = $donation->fresh(['donor', 'campaign']);
        $this->campaigns->applyPaidDonation($fresh);
        $this->donorEmails->afterDonationPaid($fresh);

        return $fresh;
    }

    public function markBankTransferConfirmed(Donation $donation): Donation
    {
        if (! $donation->canManuallyConfirm()) {
            return $donation;
        }

        return $this->markPaid($donation);
    }

    public function markFailed(Donation $donation): void
    {
        if ($donation->isPaid()) {
            return;
        }

        $donation->update(['status' => Donation::STATUS_FAILED]);
    }

    public function markCancelled(Donation $donation): void
    {
        if ($donation->isPaid()) {
            return;
        }

        $donation->update(['status' => Donation::STATUS_CANCELLED]);
    }

    private function generateReference(): string
    {
        $prefix = config('payments.reference_prefix', config('donations.reference_prefix', 'GHOSN'));

        do {
            $reference = $prefix.'-'.strtoupper(Str::random(8));
        } while (Donation::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
