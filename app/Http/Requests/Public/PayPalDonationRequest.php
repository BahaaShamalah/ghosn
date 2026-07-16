<?php

namespace App\Http\Requests\Public;

use App\Rules\ActiveCampaign;
use App\Support\DonationPaymentMethods;
use App\Support\DonationSettings;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class PayPalDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $settings = app(DonationSettings::class);

        return $settings->enabled() && $settings->paypalEnabled();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_anonymous' => $this->boolean('is_anonymous'),
            'amount' => $this->filled('amount') ? (float) $this->input('amount') : null,
            'campaign_id' => $this->filled('campaign_id') ? (int) $this->input('campaign_id') : null,
            'payment_method' => DonationPaymentMethods::normalize($this->input('payment_method')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function donorRules(): array
    {
        /** @var DonationSettings $settings */
        $settings = app(DonationSettings::class);

        return [
            'amount' => ['required', 'numeric', 'min:'.$settings->minAmount(), 'max:'.$settings->maxAmount()],
            'donor_name' => ['required', 'string', 'max:120'],
            'donor_email' => ['required', 'email', 'max:255'],
            'donor_phone' => ['nullable', 'string', 'max:40'],
            'message' => ['nullable', 'string', 'max:1000'],
            'is_anonymous' => ['sometimes', 'boolean'],
            'campaign_id' => ['nullable', 'integer', new ActiveCampaign],
            'website' => ['nullable', 'max:0'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
