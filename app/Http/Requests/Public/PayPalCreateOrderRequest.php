<?php

namespace App\Http\Requests\Public;

use App\Support\DonationPaymentMethods;
use Illuminate\Validation\Rule;

class PayPalCreateOrderRequest extends PayPalDonationRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...$this->donorRules(),
            'payment_method' => ['required', 'string', Rule::in([DonationPaymentMethods::PAYPAL])],
        ];
    }
}
