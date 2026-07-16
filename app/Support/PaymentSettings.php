<?php



namespace App\Support;



use App\Services\Settings\SettingsService;



class PaymentSettings

{

    public function __construct(

        private readonly SettingsService $settings,

    ) {}



    public function currency(): string

    {

        $currency = strtoupper((string) $this->settings->get(

            'payments.currency',

            $this->settings->get('donations.currency', config('payments.default_currency', 'USD')),

        ));



        return array_key_exists($currency, config('donations.currencies', []))

            ? $currency

            : config('payments.default_currency', 'USD');

    }



    public function minAmount(): float

    {

        return (float) $this->settings->get(

            'payments.min_amount',

            $this->settings->get('donations.min_amount', config('payments.min_amount', 5)),

        );

    }



    public function maxAmount(): float

    {

        return (float) $this->settings->get('payments.max_amount', config('payments.max_amount', 50000));

    }



    public function receiptEmail(): string

    {

        return (string) $this->settings->get(

            'payments.receipt_email',

            $this->settings->get('contact.email', ''),

        );

    }



    public function stripeAdminEnabled(): bool

    {

        return (bool) $this->settings->get('payments.stripe_enabled', false);

    }



    public function stripeEnvConfigured(): bool

    {

        return filled($this->stripeSecret()) && filled($this->stripeWebhookSecret());

    }



    public function stripeEnabled(): bool

    {

        return $this->stripeAdminEnabled() && filled($this->stripeSecret());

    }



    public function stripePublicKey(): string

    {

        return (string) config('services.stripe.key', '');

    }



    public function stripeSecret(): string

    {

        return (string) config('services.stripe.secret', '');

    }



    public function stripeWebhookSecret(): string

    {

        return (string) config('services.stripe.webhook_secret', '');

    }



    public function stripeProductName(): string

    {

        return (string) $this->settings->get(

            'payments.stripe_product_name',

            config('payments.stripe_product_name'),

        );

    }



    public function stripeProductDescription(): string

    {

        return (string) $this->settings->get(

            'payments.stripe_product_description',

            config('payments.stripe_product_description'),

        );

    }



    public function paypalAdminEnabled(): bool

    {

        return (bool) $this->settings->get('payments.paypal_enabled', false);

    }



    public function paypalEnvConfigured(): bool

    {

        return filled($this->paypalClientId())

            && filled($this->paypalClientSecret());

    }

    public function paypalWebhookConfigured(): bool

    {

        return filled($this->paypalWebhookId());

    }



    public function paypalEnabled(): bool

    {

        return $this->paypalAdminEnabled()

            && filled($this->paypalClientId())

            && filled($this->paypalClientSecret());

    }



    public function paypalMode(): string

    {

        $mode = strtolower((string) ($this->settings->get('payments.paypal_mode') ?: config('services.paypal.mode', 'sandbox')));



        return in_array($mode, ['sandbox', 'live'], true) ? $mode : 'sandbox';

    }



    public function paypalClientId(): string

    {

        return (string) config('services.paypal.client_id', '');

    }



    public function paypalClientSecret(): string

    {

        return (string) config('services.paypal.client_secret', '');

    }



    public function paypalWebhookId(): string

    {

        return (string) config('services.paypal.webhook_id', '');

    }



    public function paypalItemName(): string

    {

        return (string) $this->settings->get(

            'payments.paypal_item_name',

            config('payments.paypal_item_name'),

        );

    }



    public function paypalItemDescription(): string

    {

        return (string) $this->settings->get(

            'payments.paypal_item_description',

            config('payments.paypal_item_description'),

        );

    }



    public function paypalApiBase(): string

    {

        return $this->paypalMode() === 'live'

            ? config('payments.paypal.live_base')

            : config('payments.paypal.sandbox_base');

    }

}


