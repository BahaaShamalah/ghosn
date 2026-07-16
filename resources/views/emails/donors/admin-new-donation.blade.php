@component('emails.layouts.ghosn', compact('locale', 'dir', 'logoUrl', 'footerText', 'contactEmail', 'facebookUrl', 'instagramHandle'))
    <h1 style="margin:0 0 8px;font-size:22px;color:#0C5A2E;">{{ __('emails.admin_alert.heading', locale: $locale) }}</h1>
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#2d4a38;">{{ __('emails.admin_alert.intro', locale: $locale) }}</p>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#F5F0E4;border-radius:16px;">
        <tr>
            <td style="padding:16px 20px;">
                <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;color:rgba(12,90,46,0.6);">{{ __('emails.common.reference', locale: $locale) }}</p>
                <p style="margin:0 0 12px;font-size:16px;font-weight:700;color:#0C5A2E;direction:ltr;">{{ $donation->reference }}</p>
                <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;color:rgba(12,90,46,0.6);">{{ __('emails.common.amount', locale: $locale) }}</p>
                <p style="margin:0 0 12px;font-size:18px;font-weight:700;color:#0C5A2E;direction:ltr;">{{ $donation->formattedAmount() }}</p>
                <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;color:rgba(12,90,46,0.6);">{{ __('emails.admin_alert.donor', locale: $locale) }}</p>
                <p style="margin:0 0 12px;font-size:15px;color:#2d4a38;">{{ $donorLabel }} &lt;{{ $donation->donor_email }}&gt;</p>
                <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;color:rgba(12,90,46,0.6);">{{ __('emails.common.status', locale: $locale) }}</p>
                <p style="margin:0;font-size:15px;color:#2d4a38;">{{ $statusLabel }}</p>
            </td>
        </tr>
    </table>
@endcomponent
