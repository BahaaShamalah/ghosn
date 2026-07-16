<?php

namespace App\Support;

use App\Services\Settings\SettingsService;

class MaintenanceSettings
{
    public function __construct(
        private readonly SettingsService $settings,
    ) {}

    public function enabled(): bool
    {
        return (bool) $this->settings->get('maintenance.enabled', false);
    }

    /**
     * @return array{title: string, message: string, eyebrow: string, siteName: string, logoUrl: string}
     */
    public function copy(?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $isAr = $locale === 'ar';

        return [
            'title' => (string) $this->settings->get(
                $isAr ? 'maintenance.title_ar' : 'maintenance.title_en',
                $isAr ? 'الموقع قيد الصيانة' : 'We\'ll be back soon',
            ),
            'message' => (string) $this->settings->get(
                $isAr ? 'maintenance.message_ar' : 'maintenance.message_en',
                $isAr
                    ? 'نقوم حالياً بتحديثات على الموقع. شكراً لصبركم — سنعود قريباً.'
                    : 'We\'re making a few updates behind the scenes. Thank you for your patience — we\'ll be back shortly.',
            ),
            'eyebrow' => (string) $this->settings->get(
                $isAr ? 'maintenance.eyebrow_ar' : 'maintenance.eyebrow_en',
                $isAr ? 'صيانة مؤقتة' : 'Scheduled maintenance',
            ),
            'siteName' => SiteSettings::name($locale),
            'logoUrl' => SiteAsset::logoUrl() ?: asset('assets/landing/images/logo.webp'),
        ];
    }
}
