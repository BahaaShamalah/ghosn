@component('emails.layouts.ghosn', compact('locale', 'dir', 'logoUrl', 'footerText', 'contactEmail', 'facebookUrl', 'instagramHandle'))
    @if (! empty($heading))
        <h1 style="margin:0 0 8px;font-size:22px;color:#0C5A2E;">{{ $heading }}</h1>
    @endif

    <div style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#2d4a38;white-space:pre-line;">{{ $bodyText }}</div>

    @if (! empty($showSummary))
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#F5F0E4;border-radius:16px;margin-bottom:4px;">
            <tr>
                <td style="padding:16px 20px;">
                    <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:rgba(12,90,46,0.6);">{{ __('emails.volunteers.summary_name', locale: $locale) }}</p>
                    <p style="margin:0 0 14px;font-size:16px;font-weight:700;color:#0C5A2E;">{{ $application->name }}</p>
                    <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:rgba(12,90,46,0.6);">{{ __('emails.volunteers.summary_email', locale: $locale) }}</p>
                    <p style="margin:0 0 14px;font-size:15px;color:#2d4a38;direction:ltr;">{{ $application->email }}</p>
                    @if ($application->phone)
                        <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:rgba(12,90,46,0.6);">{{ __('emails.volunteers.summary_phone', locale: $locale) }}</p>
                        <p style="margin:0 0 14px;font-size:15px;color:#2d4a38;direction:ltr;">{{ $application->phone }}</p>
                    @endif
                    <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:rgba(12,90,46,0.6);">{{ __('emails.volunteers.summary_area', locale: $locale) }}</p>
                    <p style="margin:0;font-size:15px;color:#2d4a38;">{{ $application->localizedArea($locale) }}</p>
                </td>
            </tr>
        </table>
    @endif
@endcomponent
