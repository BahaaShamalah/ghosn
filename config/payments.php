<?php

return [

    'gateways' => [
        'stripe' => 'stripe',
        'paypal' => 'paypal',
        'bank_transfer' => 'bank_transfer',
    ],

    'methods' => [
        'bank_transfer' => ['gateway' => 'bank_transfer', 'label' => 'Bank Transfer'],
        'stripe_card' => ['gateway' => 'stripe', 'label' => 'Card'],
        'paypal_business' => ['gateway' => 'paypal', 'label' => 'PayPal'],
    ],

    'statuses' => [
        'pending',
        'paid',
        'failed',
        'cancelled',
        'refunded',
    ],

    'default_currency' => 'USD',

    'min_amount' => 5,

    'max_amount' => 50000,

    'reference_prefix' => 'GHOSN',

    /**
     * Compliant default wording for payment providers — not "donation".
     */
    'stripe_product_name' => 'GHOSN Relief Support Contribution',

    'stripe_product_description' => 'Community support payment for GHOSN Relief Team activities.',

    'paypal_item_name' => 'GHOSN Relief Support Contribution',

    'paypal_item_description' => 'Community support payment for GHOSN Relief Team activities.',

    'paypal' => [
        'sandbox_base' => 'https://api-m.sandbox.paypal.com',
        'live_base' => 'https://api-m.paypal.com',
    ],

];
