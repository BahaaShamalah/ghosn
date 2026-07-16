{{ __('emails.contact.admin_alert.heading', locale: $locale) }}

{{ __('emails.contact.admin_alert.intro', locale: $locale) }}

{{ $contactMessage->name }} <{{ $contactMessage->email }}>

@if (filled($contactMessage->subject))
{{ __('emails.contact.admin_alert.field_subject', locale: $locale) }}: {{ $contactMessage->subject }}
@endif

{{ $contactMessage->message }}

{{ $adminUrl }}
