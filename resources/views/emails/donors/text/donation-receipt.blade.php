{{ __('emails.donation_receipt.heading', locale: $locale) }}

{{ __('emails.donation_receipt.greeting', ['name' => $donorName], $locale) }}

{{ __('emails.common.reference', locale: $locale) }}: {{ $donation->reference }}
{{ __('emails.common.amount', locale: $locale) }}: {{ $donation->formattedAmount() }}
{{ __('emails.common.status', locale: $locale) }}: {{ $statusLabel }}
@if ($campaignName)
{{ __('emails.common.campaign', locale: $locale) }}: {{ $campaignName }}
@endif

{{ __('emails.donation_receipt.thank_you', locale: $locale) }}
