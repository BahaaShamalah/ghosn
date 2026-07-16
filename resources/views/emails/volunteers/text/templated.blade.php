@if (! empty($heading))
{{ $heading }}

@endif
{{ $bodyText }}

@if (! empty($showSummary))
---
{{ __('emails.volunteers.summary_name', locale: $locale) }}: {{ $application->name }}
{{ __('emails.volunteers.summary_email', locale: $locale) }}: {{ $application->email }}
@if ($application->phone)
{{ __('emails.volunteers.summary_phone', locale: $locale) }}: {{ $application->phone }}
@endif
{{ __('emails.volunteers.summary_area', locale: $locale) }}: {{ $application->localizedArea($locale) }}
@endif
