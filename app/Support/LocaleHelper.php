<?php

namespace App\Support;

class LocaleHelper
{
    public static function direction(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return in_array($locale, config('locale.rtl', ['ar']), true) ? 'rtl' : 'ltr';
    }

    public static function isSupported(string $locale): bool
    {
        return in_array($locale, config('locale.supported', ['en', 'ar']), true);
    }
}
