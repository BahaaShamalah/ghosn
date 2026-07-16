<?php

namespace App\Mail\Concerns;

use App\Support\EmailSettings;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;

trait GhosnBrandedMail
{
    public ?int $emailLogId = null;

    protected function brandedEnvelope(string $subject): Envelope
    {
        $settings = app(EmailSettings::class);

        return new Envelope(
            from: new Address($settings->fromEmail(), $settings->fromName()),
            subject: $subject,
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function brandingData(string $locale): array
    {
        $settings = app(EmailSettings::class);
        $isRtl = $locale === 'ar';

        return [
            'locale' => $locale,
            'dir' => $isRtl ? 'rtl' : 'ltr',
            'logoUrl' => $settings->logoUrl(),
            'footerText' => $settings->footerText($locale) ?: __('emails.footer_default', locale: $locale),
            'contactEmail' => (string) app(\App\Services\Settings\SettingsService::class)->get('contact.email', ''),
            'facebookUrl' => (string) app(\App\Services\Settings\SettingsService::class)->get('social.facebook_url', ''),
            'instagramHandle' => (string) app(\App\Services\Settings\SettingsService::class)->get('social.instagram_handle', ''),
        ];
    }
}
