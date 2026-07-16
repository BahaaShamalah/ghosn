<?php

namespace App\Support;

use App\Models\VolunteerApplication;
use App\Services\Settings\SettingsService;

class VolunteerEmailSettings
{
    public const TYPE_CONFIRMATION = 'confirmation';

    public const TYPE_ADMIN_ALERT = 'admin_alert';

    public const TYPE_WELCOME = 'welcome';

    public const TYPE_REJECTED = 'rejected';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_CONFIRMATION,
        self::TYPE_ADMIN_ALERT,
        self::TYPE_WELCOME,
        self::TYPE_REJECTED,
    ];

    public function __construct(
        private readonly SettingsService $settings,
        private readonly EmailSettings $emailSettings,
    ) {}

    public function enabled(string $type): bool
    {
        return (bool) $this->settings->get("volunteers.{$type}_enabled", true);
    }

    public function adminRecipient(): string
    {
        $email = trim($this->emailSettings->adminNotificationEmail());

        if (filled($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        $contact = trim((string) $this->settings->get('contact.email', ''));

        return filter_var($contact, FILTER_VALIDATE_EMAIL) ? $contact : '';
    }

    /**
     * @return array{subject: string, heading: string, body: string}
     */
    public function render(string $type, VolunteerApplication $application, ?string $locale = null): array
    {
        $locale = $locale ?: ($application->locale ?: 'en');
        $suffix = $locale === 'ar' ? '_ar' : '_en';

        return [
            'subject' => $this->replacePlaceholders(
                (string) $this->settings->get("volunteers.{$type}_subject{$suffix}", ''),
                $application,
                $locale,
            ),
            'heading' => $this->replacePlaceholders(
                (string) $this->settings->get("volunteers.{$type}_heading{$suffix}", ''),
                $application,
                $locale,
            ),
            'body' => $this->replacePlaceholders(
                (string) $this->settings->get("volunteers.{$type}_body{$suffix}", ''),
                $application,
                $locale,
            ),
        ];
    }

    private function replacePlaceholders(string $text, VolunteerApplication $application, string $locale): string
    {
        $siteName = $locale === 'ar'
            ? (string) $this->settings->get('site.name_ar', 'فريق غُصن للإغاثة')
            : (string) $this->settings->get('site.name_en', 'GHOSN Relief Team');

        $replacements = [
            '{name}' => $application->name,
            '{email}' => $application->email,
            '{phone}' => $application->phone ?: '—',
            '{area}' => $application->area,
            '{area_label}' => $application->localizedArea($locale),
            '{message}' => filled($application->message) ? $application->message : '—',
            '{site_name}' => $siteName,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
}
