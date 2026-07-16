<?php

namespace App\Http\Requests\Public;

use App\Models\Donation;
use App\Rules\ActiveCampaign;
use App\Support\DonationPaymentMethods;
use App\Support\DonationSettings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return app(DonationSettings::class)->enabled();
    }

    protected function prepareForValidation(): void
    {
        $method = DonationPaymentMethods::normalize($this->input('payment_method'));

        $this->merge([
            'is_anonymous' => $this->boolean('is_anonymous'),
            'amount' => $this->filled('amount') ? (float) $this->input('amount') : null,
            'campaign_id' => $this->filled('campaign_id') ? (int) $this->input('campaign_id') : null,
            'payment_method' => $method,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var DonationSettings $settings */
        $settings = app(DonationSettings::class);

        return [
            'amount' => ['required', 'numeric', 'min:'.$settings->minAmount(), 'max:'.$settings->maxAmount()],
            'payment_method' => [
                'required',
                'string',
                Rule::in(DonationPaymentMethods::forStoreCheckout($settings)),
                Rule::notIn([Donation::METHOD_PAYPAL, 'paypal']),
            ],
            'donor_name' => ['required', 'string', 'max:120'],
            'donor_email' => ['required', 'email', 'max:255'],
            'donor_phone' => ['nullable', 'string', 'max:40'],
            'message' => ['nullable', 'string', 'max:1000'],
            'is_anonymous' => ['sometimes', 'boolean'],
            'campaign_id' => ['nullable', 'integer', new ActiveCampaign],
            'website' => ['nullable', 'max:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'website.max' => __('public.donate.spam_detected'),
            'payment_method.in' => __('public.donate.payment_method_invalid'),
            'payment_method.not_in' => __('public.donate.paypal_use_button'),
        ];
    }
}
