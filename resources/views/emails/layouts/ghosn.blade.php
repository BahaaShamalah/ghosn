@php
    $locale = $locale ?? 'en';
    $dir = $dir ?? ($locale === 'ar' ? 'rtl' : 'ltr');
    $primary = '#0C5A2E';
    $secondary = '#46A45B';
    $background = '#FAF8F1';
    $cream = '#F5F0E4';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subjectLine ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background-color:{{ $background }};font-family:{{ $locale === 'ar' ? 'Cairo, Arial, sans-serif' : 'Montserrat, Arial, sans-serif' }};color:#1a3d2a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:{{ $background }};padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;">
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            <img src="{{ $logoUrl }}" alt="GHOSN" width="120" style="display:block;max-width:120px;height:auto;border:0;">
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#ffffff;border-radius:24px;border:1px solid rgba(12,90,46,0.12);box-shadow:0 8px 32px rgba(12,90,46,0.08);padding:32px 28px;">
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 12px 8px;text-align:center;font-size:12px;line-height:1.6;color:rgba(26,61,42,0.65);">
                            @if (! empty($footerText))
                                <p style="margin:0 0 12px;">{{ $footerText }}</p>
                            @endif
                            @if (! empty($contactEmail))
                                <p style="margin:0 0 8px;">
                                    <a href="mailto:{{ $contactEmail }}" style="color:{{ $primary }};text-decoration:none;">{{ $contactEmail }}</a>
                                </p>
                            @endif
                            <p style="margin:0;">
                                @if (! empty($facebookUrl))
                                    <a href="{{ $facebookUrl }}" style="color:{{ $secondary }};text-decoration:none;margin:0 8px;">Facebook</a>
                                @endif
                                @if (! empty($instagramHandle))
                                    <span style="color:{{ $secondary }};">{{ '@'.$instagramHandle }}</span>
                                @endif
                            </p>
                            <p style="margin:16px 0 0;font-size:11px;color:rgba(26,61,42,0.45);">
                                {{ __('emails.unsubscribe_placeholder', locale: $locale) }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
