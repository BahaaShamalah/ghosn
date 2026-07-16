@component('emails.layouts.ghosn', compact('locale', 'dir', 'logoUrl', 'footerText', 'contactEmail', 'facebookUrl', 'instagramHandle'))
    <h1 style="margin:0 0 8px;font-size:22px;color:#0C5A2E;">{{ $emailSubject }}</h1>
    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#2d4a38;">
        {{ __('emails.custom.greeting', ['name' => $donorName], $locale) }}
    </p>
    <div style="font-size:15px;line-height:1.7;color:#2d4a38;">{!! nl2br(e($messageBody)) !!}</div>

    @if (! empty($imageUrls))
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:24px 0 0;">
            @foreach ($imageUrls as $imageUrl)
                <tr>
                    <td style="padding:8px 0;text-align:center;">
                        <img src="{{ $imageUrl }}" alt="" style="display:block;max-width:100%;width:100%;height:auto;border-radius:12px;border:1px solid rgba(12,90,46,0.12);">
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    @if (! empty($youtubeVideos))
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:24px 0 0;">
            @foreach ($youtubeVideos as $video)
                <tr>
                    <td style="padding:8px 0;text-align:center;">
                        <a href="{{ $video['watch_url'] }}" style="text-decoration:none;display:inline-block;">
                            <img src="{{ $video['thumbnail_url'] }}" alt="{{ __('emails.custom.watch_video', locale: $locale) }}" style="display:block;max-width:100%;height:auto;border-radius:12px;border:1px solid rgba(12,90,46,0.12);">
                            <span style="display:inline-block;margin-top:8px;font-size:13px;font-weight:600;color:#0C5A2E;">{{ __('emails.custom.watch_video', locale: $locale) }}</span>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

    @if ($ctaText && $ctaUrl)
        <p style="margin:24px 0 0;text-align:center;">
            <a href="{{ e($ctaUrl) }}" style="display:inline-block;background:#0C5A2E;color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:12px 24px;border-radius:999px;">{{ e($ctaText) }}</a>
        </p>
    @endif
@endcomponent
