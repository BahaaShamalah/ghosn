<?php



namespace App\Support;



use App\Services\Settings\SettingsService;



class DonationSettings

{

    public function __construct(

        private readonly SettingsService $settings,

        private readonly PaymentSettings $payments,

    ) {}



    public function enabled(): bool

    {

        return (bool) $this->settings->get('donations.enabled', true);

    }



    public function currency(): string

    {

        return $this->payments->currency();

    }



    public function stripeEnabled(): bool

    {

        return $this->enabled() && $this->payments->stripeEnabled();

    }



    public function paypalEnabled(): bool

    {

        return $this->enabled() && $this->payments->paypalEnabled();

    }



    public function bankTransferEnabled(): bool

    {

        return $this->enabled()

            && (bool) $this->settings->get('donations.bank_transfer_enabled', true);

    }



    /**

     * @return array<string, mixed>

     */

    public function bankDetails(): array

    {

        return [

            'bank_name_en' => (string) $this->settings->get('donations.bank_name_en', ''),

            'bank_name_ar' => (string) $this->settings->get('donations.bank_name_ar', ''),

            'account_holder_en' => (string) $this->settings->get('donations.account_holder_en', 'GHOSN Relief Team'),

            'account_holder_ar' => (string) $this->settings->get('donations.account_holder_ar', 'فريق غُصن للإغاثة'),

            'iban' => (string) $this->settings->get('donations.iban', ''),

            'account_number' => (string) $this->settings->get('donations.account_number', ''),

            'swift' => (string) $this->settings->get('donations.swift', ''),

            'instructions_en' => (string) $this->settings->get('donations.instructions_en', 'Please include your payment reference in the transfer description.'),

            'instructions_ar' => (string) $this->settings->get('donations.instructions_ar', 'يرجى ذكر رقم مرجع الدفع في وصف التحويل.'),

        ];

    }



    public function hasBankDetails(): bool

    {

        $bank = $this->bankDetails();



        return filled($bank['iban']) || filled($bank['account_number']);

    }



    /**

     * @return list<int|float>

     */

    public function amountPresets(): array

    {

        return config('donations.amount_presets', [25, 50, 100, 250, 500]);

    }



    public function minAmount(): float

    {

        return $this->payments->minAmount();

    }



    public function maxAmount(): float

    {

        return $this->payments->maxAmount();

    }

}

