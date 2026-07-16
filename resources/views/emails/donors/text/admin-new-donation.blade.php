{{ __('emails.admin_alert.heading', locale: $locale) }}

{{ __('emails.admin_alert.intro', locale: $locale) }}

{{ __('emails.common.reference', locale: $locale) }}: {{ $donation->reference }}
{{ __('emails.common.amount', locale: $locale) }}: {{ $donation->formattedAmount() }}
{{ __('emails.admin_alert.donor', locale: $locale) }}: {{ $donorLabel }} <{{ $donation->donor_email }}>
{{ __('emails.common.status', locale: $locale) }}: {{ $statusLabel }}
