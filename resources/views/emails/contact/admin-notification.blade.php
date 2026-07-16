@component('emails.layouts.ghosn', compact('locale', 'dir', 'logoUrl', 'footerText', 'contactEmail', 'facebookUrl', 'instagramHandle'))
    <h1 style="margin:0 0 8px;font-size:22px;color:#0C5A2E;">{{ __('emails.contact.admin_alert.heading', locale: $locale) }}</h1>
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#2d4a38;">{{ __('emails.contact.admin_alert.intro', locale: $locale) }}</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#F5F0E4;border-radius:16px;margin-bottom:20px;">
        <tr>
            <td style="padding:16px 20px;">
                <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;color:rgba(12,90,46,0.6);">{{ __('emails.contact.admin_alert.from', locale: $locale) }}</p>
                <p style="margin:0 0 14px;font-size:16px;font-weight:700;color:#0C5A2E;">{{ $contactMessage->name }}</p>

                <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;color:rgba(12,90,46,0.6);">{{ __('emails.contact.admin_alert.email', locale: $locale) }}</p>
                <p style="margin:0 0 14px;font-size:15px;color:#2d4a38;direction:ltr;">{{ $contactMessage->email }}</p>

                @if (filled($contactMessage->subject))
                    <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;color:rgba(12,90,46,0.6);">{{ __('emails.contact.admin_alert.field_subject', locale: $locale) }}</p>
                    <p style="margin:0 0 14px;font-size:15px;color:#2d4a38;">{{ $contactMessage->subject }}</p>
                @endif

                <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;color:rgba(12,90,46,0.6);">{{ __('emails.contact.admin_alert.message', locale: $locale) }}</p>
                <p style="margin:0;font-size:15px;line-height:1.7;color:#2d4a38;white-space:pre-line;">{{ $contactMessage->message }}</p>
            </td>
        </tr>
    </table>

    <p style="margin:0;">
        <a href="{{ $adminUrl }}" style="display:inline-block;background:#0C5A2E;color:#ffffff;text-decoration:none;padding:12px 22px;border-radius:999px;font-size:14px;font-weight:700;">
            {{ __('emails.contact.admin_alert.view_in_admin', locale: $locale) }}
        </a>
    </p>
@endcomponent
