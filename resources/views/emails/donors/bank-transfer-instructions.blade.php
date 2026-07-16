@component('emails.layouts.ghosn', compact('locale', 'dir', 'logoUrl', 'footerText', 'contactEmail', 'facebookUrl', 'instagramHandle'))
    <h1 style="margin:0 0 8px;font-size:22px;color:#0C5A2E;">{{ __('emails.bank_transfer.heading', locale: $locale) }}</h1>
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#2d4a38;">
        {{ __('emails.bank_transfer.greeting', ['name' => $donorName], $locale) }}
    </p>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#F5F0E4;border-radius:16px;margin-bottom:20px;">
        <tr>
            <td style="padding:16px 20px;">
                <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:rgba(12,90,46,0.6);">{{ __('emails.common.reference', locale: $locale) }}</p>
                <p style="margin:0 0 14px;font-size:16px;font-weight:700;color:#0C5A2E;direction:ltr;">{{ $donation->reference }}</p>
                <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:rgba(12,90,46,0.6);">{{ __('emails.common.amount', locale: $locale) }}</p>
                <p style="margin:0;font-size:20px;font-weight:700;color:#0C5A2E;direction:ltr;">{{ $donation->formattedAmount() }}</p>
            </td>
        </tr>
    </table>
    @if ($bankDetails['bank_name'] || $bankDetails['iban'] || $bankDetails['account_number'])
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:20px;border:1px solid rgba(12,90,46,0.12);border-radius:16px;">
            @foreach ($bankDetails as $label => $value)
                @if ($value)
                    <tr>
                        <td style="padding:10px 16px;border-bottom:1px solid rgba(12,90,46,0.08);font-size:12px;color:rgba(12,90,46,0.65);width:40%;">{{ $label }}</td>
                        <td style="padding:10px 16px;border-bottom:1px solid rgba(12,90,46,0.08);font-size:14px;color:#2d4a38;direction:ltr;">{{ $value }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
    @endif
    <p style="margin:0;font-size:15px;line-height:1.7;color:#2d4a38;">{{ $instructions }}</p>
@endcomponent
