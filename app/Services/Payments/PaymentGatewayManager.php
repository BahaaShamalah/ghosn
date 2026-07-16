<?php

namespace App\Services\Payments;

use App\Models\Donation;
use App\Support\DonationPaymentMethods;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\DTOs\PaymentRequestData;
use App\Services\Payments\DTOs\PaymentResultData;
use App\Services\Payments\DTOs\WebhookResultData;
use App\Services\Payments\Gateways\PayPalBusinessGateway;
use App\Services\Payments\Gateways\StripeGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    public function __construct(
        private readonly StripeGateway $stripe,
        private readonly PayPalBusinessGateway $paypal,
        private readonly PaymentGatewayEventLogger $events,
    ) {}

    public function gatewayForMethod(string $paymentMethod): ?PaymentGatewayInterface
    {
        $gateway = config("payments.methods.{$paymentMethod}.gateway");

        return match ($gateway) {
            'stripe' => $this->stripe->isConfigured() ? $this->stripe : null,
            'paypal' => $this->paypal->isConfigured() ? $this->paypal : null,
            default => null,
        };
    }

    public function gateway(string $name): PaymentGatewayInterface
    {
        return match ($name) {
            'stripe' => $this->stripe,
            'paypal' => $this->paypal,
            default => throw new InvalidArgumentException("Unknown gateway [{$name}]."),
        };
    }

    public function createCheckout(Donation $donation): PaymentResultData
    {
        $gateway = $this->gatewayForMethod($donation->payment_method);

        if (! $gateway) {
            return PaymentResultData::failed('unknown', 'Payment method is not available.');
        }

        return $gateway->createCheckout(new PaymentRequestData(
            donationId: $donation->id,
            reference: $donation->reference,
            amount: (float) $donation->amount,
            currency: $donation->currency,
            donorEmail: $donation->donor_email,
            successUrl: $this->successUrl($donation, $gateway->gateway()),
            cancelUrl: route('donate.cancel', ['reference' => $donation->reference]),
            metadata: [
                'payment_method' => $donation->payment_method,
            ],
        ));
    }

    public function verifyReturn(string $gatewayName, string $gatewayReference, array $context = []): PaymentResultData
    {
        return $this->gateway($gatewayName)->verifyPayment($gatewayReference, $context);
    }

    /**
     * @param  array<string, string|null>  $headers
     */
    public function handleWebhook(string $gatewayName, string $payload, array $headers): WebhookResultData
    {
        return $this->gateway($gatewayName)->handleWebhook($payload, $headers);
    }

    public function isGatewayPayment(Donation $donation): bool
    {
        return in_array($donation->payment_method, [DonationPaymentMethods::STRIPE, DonationPaymentMethods::PAYPAL], true);
    }

    private function successUrl(Donation $donation, string $gateway): string
    {
        return match ($gateway) {
            'stripe' => route('donate.success', ['gateway' => 'stripe', 'session_id' => '{CHECKOUT_SESSION_ID}']),
            'paypal' => route('donate.success', ['gateway' => 'paypal']),
            default => route('donate.success', ['reference' => $donation->reference]),
        };
    }
}
