<?php

namespace App\Support;

use App\Services\Settings\SettingsService;

class SiteSettings
{
    public static function name(?string $locale = null): string
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $locale ??= app()->getLocale();

        $english = trim((string) $settings->get('site.name_en', 'GHOSN Relief Team'));
        $arabic = trim((string) $settings->get('site.name_ar', 'فريق غُصن للإغاثة'));

        if ($locale === 'ar') {
            return $arabic !== '' ? $arabic : $english;
        }

        return $english !== '' ? $english : $arabic;
    }

    public static function title(?string $pageTitle = null, ?string $locale = null): string
    {
        $siteName = self::name($locale);
        $pageTitle = trim((string) $pageTitle);

        if ($pageTitle === '' || $pageTitle === $siteName) {
            return $siteName;
        }

        return $pageTitle.' - '.$siteName;
    }
}
