<?php

namespace App\Http\Requests\Public;

use App\Support\DonationSettings;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * PayPal capture-order expects a minimal JSON payload (donor data is stored at create-order):
 *
 * {
 *   "order_id": "<PayPal order ID from onApprove data.orderID>",
 *   "donation_id": 123,
 *   "reference": "GHOSN-XXXXXXXX"
 * }
 *
 * Either donation_id or reference is required. order_id / orderID is required.
 */
class PayPalCaptureOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $settings = app(DonationSettings::class);

        return $settings->enabled() && $settings->paypalEnabled();
    }

    protected function prepareForValidation(): void
    {
        $orderId = $this->input('order_id') ?? $this->input('orderID');

        $this->merge([
            'order_id' => is_string($orderId) ? trim($orderId) : (is_scalar($orderId) ? trim((string) $orderId) : null),
            'donation_id' => $this->filled('donation_id') ? (int) $this->input('donation_id') : null,
            'reference' => $this->filled('reference') ? trim((string) $this->input('reference')) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'string', 'max:64'],
            'donation_id' => ['nullable', 'integer', 'min:1'],
            'reference' => ['nullable', 'string', 'max:64'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->filled('donation_id') && ! $this->filled('reference')) {
                $validator->errors()->add('donation_id', 'The donation id or reference field is required.');
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
