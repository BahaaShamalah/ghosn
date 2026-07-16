<?php

namespace App\Support;

use App\Models\Donation;

class DonationPaymentMethods
{
    public const BANK = Donation::METHOD_BANK;

    public const STRIPE = Donation::METHOD_STRIPE_CARD;

    public const PAYPAL = Donation::METHOD_PAYPAL;

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return array_values(config('donations.payment_methods', [
            self::BANK,
            self::STRIPE,
            self::PAYPAL,
        ]));
    }

    /**
     * Methods allowed through POST /donate (standard form checkout).
     *
     * @return list<string>
     */
    public static function forStoreCheckout(DonationSettings $settings): array
    {
        $methods = [];

        if ($settings->stripeEnabled()) {
            $methods[] = self::STRIPE;
        }

        if ($settings->bankTransferEnabled() && $settings->hasBankDetails()) {
            $methods[] = self::BANK;
        }

        return $methods;
    }

    public static function normalize(?string $method): ?string
    {
        if ($method === null || $method === '') {
            return null;
        }

        $method = strtolower(trim($method));

        return match ($method) {
            'paypal' => self::PAYPAL,
            default => $method,
        };
    }

    public static function isPayPal(?string $method): bool
    {
        return self::normalize($method) === self::PAYPAL;
    }
}
