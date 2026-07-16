<?php

namespace App\Support;

use App\Models\SocialLink;

class SocialPlatform
{
    /**
     * @return array<string, array{label_en: string, label_ar: string, icon: string}>
     */
    public static function definitions(): array
    {
        return [
            SocialLink::PLATFORM_FACEBOOK => [
                'label_en' => 'Facebook',
                'label_ar' => 'فيسبوك',
                'icon' => 'fa-brands fa-facebook-f',
            ],
            SocialLink::PLATFORM_INSTAGRAM => [
                'label_en' => 'Instagram',
                'label_ar' => 'إنستغرام',
                'icon' => 'fa-brands fa-instagram',
            ],
            SocialLink::PLATFORM_X => [
                'label_en' => 'X',
                'label_ar' => 'X',
                'icon' => 'fa-brands fa-x-twitter',
            ],
            SocialLink::PLATFORM_YOUTUBE => [
                'label_en' => 'YouTube',
                'label_ar' => 'يوتيوب',
                'icon' => 'fa-brands fa-youtube',
            ],
            SocialLink::PLATFORM_WHATSAPP => [
                'label_en' => 'WhatsApp',
                'label_ar' => 'واتساب',
                'icon' => 'fa-brands fa-whatsapp',
            ],
            SocialLink::PLATFORM_TIKTOK => [
                'label_en' => 'TikTok',
                'label_ar' => 'تيك توك',
                'icon' => 'fa-brands fa-tiktok',
            ],
            SocialLink::PLATFORM_LINKEDIN => [
                'label_en' => 'LinkedIn',
                'label_ar' => 'لينكدإن',
                'icon' => 'fa-brands fa-linkedin-in',
            ],
            SocialLink::PLATFORM_TELEGRAM => [
                'label_en' => 'Telegram',
                'label_ar' => 'تلغرام',
                'icon' => 'fa-brands fa-telegram',
            ],
            SocialLink::PLATFORM_WEBSITE => [
                'label_en' => 'Website',
                'label_ar' => 'الموقع',
                'icon' => 'fa-solid fa-globe',
            ],
        ];
    }

    /**
     * Font Awesome class for a platform key (single source of truth).
     */
    public static function iconClass(string $platform): string
    {
        return self::definitions()[$platform]['icon']
            ?? 'fa-solid fa-link';
    }

    public static function label(string $platform, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $definitions = self::definitions();
        $key = $locale === 'ar' ? 'label_ar' : 'label_en';

        return $definitions[$platform][$key] ?? ucfirst($platform);
    }

    /**
     * @return list<string>
     */
    public static function platforms(): array
    {
        return array_keys(self::definitions());
    }

    public static function isValid(string $platform): bool
    {
        return array_key_exists($platform, self::definitions());
    }
}
