<?php

namespace App\Support;

use App\Services\Settings\SettingsService;

class ThemeHelper
{
    /**
     * @return array{en: string, ar: string}
     */
    public static function fonts(?SettingsService $settings = null): array
    {
        $settings ??= app(SettingsService::class);

        return [
            'en' => (string) $settings->get('theme.font_en', 'Montserrat'),
            'ar' => (string) $settings->get('theme.font_ar', 'Cairo'),
        ];
    }
}
