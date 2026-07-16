<?php

return [

    'amount_presets' => [25, 50, 100, 250, 500],

    'currencies' => [
        'USD' => ['symbol' => '$', 'label_en' => 'US Dollar', 'label_ar' => 'دولار أمريكي'],
        'EUR' => ['symbol' => '€', 'label_en' => 'Euro', 'label_ar' => 'يورو'],
        'GBP' => ['symbol' => '£', 'label_en' => 'British Pound', 'label_ar' => 'جنيه إسترليني'],
    ],

    'default_currency' => 'USD',

    'min_amount' => 5,

    'max_amount' => 50000,

    'reference_prefix' => 'GHOSN',

    'statuses' => [
        'pending',
        'paid',
        'failed',
        'cancelled',
        'refunded',
    ],

    'payment_methods' => [
        'bank_transfer' => 'bank_transfer',
        'stripe_card' => 'stripe_card',
        'paypal_business' => 'paypal_business',
    ],

];
