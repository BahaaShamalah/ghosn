{{ __('emails.payment_confirmed.heading', locale: $locale) }}

{{ __('emails.payment_confirmed.greeting', ['name' => $donorName], $locale) }}

{{ __('emails.common.reference', locale: $locale) }}: {{ $donation->reference }}
{{ __('emails.common.amount', locale: $locale) }}: {{ $donation->formattedAmount() }}
@if ($campaignName)
{{ __('emails.common.campaign', locale: $locale) }}: {{ $campaignName }}
@endif

{{ __('emails.payment_confirmed.thank_you', locale: $locale) }}
