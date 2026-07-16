{{ __('emails.bank_transfer.heading', locale: $locale) }}

{{ __('emails.bank_transfer.greeting', ['name' => $donorName], $locale) }}

{{ __('emails.common.reference', locale: $locale) }}: {{ $donation->reference }}
{{ __('emails.common.amount', locale: $locale) }}: {{ $donation->formattedAmount() }}

@foreach ($bankDetails as $label => $value)
@if ($value)
{{ $label }}: {{ $value }}
@endif
@endforeach

{{ $instructions }}
