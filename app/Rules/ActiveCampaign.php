<?php

namespace App\Rules;

use App\Models\Campaign;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveCampaign implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $campaign = Campaign::query()->find($value);

        if (! $campaign || ! $campaign->isPublic()) {
            $fail(__('public.campaigns.invalid_campaign'));
        }
    }
}
