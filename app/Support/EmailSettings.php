<?php

namespace App\Support;

use App\Services\Settings\SettingsService;

class EmailSettings
{
    public function __construct(
        private readonly SettingsService $settings,
        private readonly PaymentSettings $payments,
    ) {}

    public function fromName(): string
    {
        return (string) $this->settings->get(
            'email.from_name',
            $this->settings->get('site.name_en', 'GHOSN Relief Team'),
        );
    }

    public function fromEmail(): string
    {
        $from = (string) $this->settings->get('email.from_email', '');

        if (filled($from)) {
            return $from;
        }

        return (string) config('mail.from.address', 'noreply@example.com');
    }

    public function adminNotificationEmail(): string
    {
        return (string) $this->settings->get(
            'email.admin_notification_email',
            $this->payments->receiptEmail(),
        );
    }

    public function donorReceiptsEnabled(): bool
    {
        return (bool) $this->settings->get('email.donor_receipts_enabled', true);
    }

    public function contactInboxEmail(): string
    {
        $inbox = (string) $this->settings->get('contact.inbox_email', '');

        if (filled($inbox)) {
            return $inbox;
        }

        $admin = $this->adminNotificationEmail();

        if (filled($admin)) {
            return $admin;
        }

        return (string) $this->settings->get('contact.email', '');
    }

    public function adminAlertsEnabled(): bool
    {
        return (bool) $this->settings->get('email.admin_alerts_enabled', true);
    }

    public function footerText(string $locale): string
    {
        $key = $locale === 'ar' ? 'email.footer_ar' : 'email.footer_en';

        return (string) $this->settings->get($key, '');
    }

    public function logoUrl(): string
    {
        $logo = (string) $this->settings->get('site.logo', 'assets/landing/images/logo.webp');

        if (str_starts_with($logo, 'http')) {
            return $logo;
        }

        return url(asset($logo));
    }
}
