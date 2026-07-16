<?php

namespace App\Support;

use App\Services\Settings\SettingsService;

class SiteChrome
{
    /**
     * @return list<array{label_en: string, label_ar: string, href: string}>
     */
    public static function defaultNavItemDefinitions(): array
    {
        return [
            ['label_en' => 'Home', 'label_ar' => 'الرئيسية', 'href' => 'route:home'],
            ['label_en' => 'About', 'label_ar' => 'من نحن', 'href' => 'route:about'],
            ['label_en' => 'Campaigns', 'label_ar' => 'الحملات', 'href' => 'route:campaigns.index'],
            ['label_en' => 'Updates', 'label_ar' => 'المستجدات', 'href' => 'route:news.index'],
            ['label_en' => 'Our Team', 'label_ar' => 'فريقنا', 'href' => 'route:team'],
            ['label_en' => 'Contact', 'label_ar' => 'تواصل معنا', 'href' => 'route:contact'],
        ];
    }

    /**
     * @return list<array{label_en: string, label_ar: string, href: string}>
     */
    public static function defaultNavItems(): array
    {
        return array_map(fn (array $item): array => [
            'label_en' => $item['label_en'],
            'label_ar' => $item['label_ar'],
            'href' => self::resolveHref($item['href']),
        ], self::defaultNavItemDefinitions());
    }

    /**
     * @return list<array{label_en: string, label_ar: string, href: string}>
     */
    public static function navItems(): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $items = $settings->get('navigation.items');

        if (! is_array($items) || $items === []) {
            return self::defaultNavItems();
        }

        $resolved = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $labelEn = trim((string) ($item['label_en'] ?? ''));
            $labelAr = trim((string) ($item['label_ar'] ?? ''));
            $href = trim((string) ($item['href'] ?? $item['url'] ?? ''));

            if ($labelEn === '' && $labelAr === '') {
                continue;
            }

            if ($href === '') {
                continue;
            }

            $resolved[] = [
                'label_en' => $labelEn !== '' ? $labelEn : $labelAr,
                'label_ar' => $labelAr !== '' ? $labelAr : $labelEn,
                'href' => self::resolveHref($href),
            ];
        }

        return $resolved !== [] ? $resolved : self::defaultNavItems();
    }

    /**
     * @return list<array{label_en: string, label_ar: string, href: string}>
     */
    public static function navLinks(): array
    {
        return self::navItems();
    }

    public static function donateLabel(?string $locale = null): string
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $locale ??= app()->getLocale();
        $key = $locale === 'ar' ? 'navigation.donate_label_ar' : 'navigation.donate_label_en';

        return (string) $settings->get($key, $locale === 'ar' ? 'تبرّع الآن' : 'Donate Now');
    }

    /**
     * @return list<array{label_en: string, label_ar: string, href: string}>
     */
    public static function defaultFooterLinkDefinitions(): array
    {
        return [
            ['label_en' => 'Donation Policy', 'label_ar' => 'سياسة التبرّع', 'href' => '/donation-policy'],
            ['label_en' => 'Privacy Policy', 'label_ar' => 'سياسة الخصوصية', 'href' => '/privacy-policy'],
            ['label_en' => 'Terms of Use', 'label_ar' => 'شروط الاستخدام', 'href' => '/terms-of-use'],
            ['label_en' => 'Volunteer', 'label_ar' => 'تطوّع معنا', 'href' => 'route:volunteer'],
            ['label_en' => 'Contact', 'label_ar' => 'تواصل معنا', 'href' => 'route:contact'],
        ];
    }

    /**
     * @return list<array{label_en: string, label_ar: string, href: string}>
     */
    public static function defaultFooterLinks(): array
    {
        return array_map(fn (array $item): array => [
            'label_en' => $item['label_en'],
            'label_ar' => $item['label_ar'],
            'href' => self::resolveHref($item['href']),
        ], self::defaultFooterLinkDefinitions());
    }

    /**
     * @return list<array{label_en: string, label_ar: string, href: string}>
     */
    public static function footerLinks(): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $items = $settings->get('footer.links');

        if (! is_array($items) || $items === []) {
            return self::defaultFooterLinks();
        }

        $resolved = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $labelEn = trim((string) ($item['label_en'] ?? ''));
            $labelAr = trim((string) ($item['label_ar'] ?? ''));
            $href = trim((string) ($item['href'] ?? $item['url'] ?? ''));

            if ($labelEn === '' && $labelAr === '') {
                continue;
            }

            if ($href === '') {
                continue;
            }

            $resolved[] = [
                'label_en' => $labelEn !== '' ? $labelEn : $labelAr,
                'label_ar' => $labelAr !== '' ? $labelAr : $labelEn,
                'href' => self::resolveHref($href),
            ];
        }

        return $resolved !== [] ? $resolved : self::defaultFooterLinks();
    }

    /**
     * @return array<string, mixed>
     */
    public static function footerCopy(): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        return [
            'desc' => [
                'en' => (string) $settings->get('footer.desc_en', 'GHOSN Relief Team brings together donors, volunteers, and partners to deliver lasting humanitarian impact.'),
                'ar' => (string) $settings->get('footer.desc_ar', 'يجمع فريق غُصن للإغاثة المتبرعين والمتطوعين والشركاء لتقديم أثر إنساني دائم.'),
            ],
            'tagline' => [
                'en' => (string) $settings->get('footer.tagline_en', (string) $settings->get('site.slogan_en', 'Giving that grows. Impact that lasts.')),
                'ar' => (string) $settings->get('footer.tagline_ar', (string) $settings->get('site.slogan_ar', 'عطاءٌ ينمو... وأثرٌ يبقى.')),
            ],
            'quickTitle' => [
                'en' => (string) $settings->get('footer.quick_title_en', 'Quick links'),
                'ar' => (string) $settings->get('footer.quick_title_ar', 'روابط سريعة'),
            ],
            'linksTitle' => [
                'en' => (string) $settings->get('footer.links_title_en', 'Explore'),
                'ar' => (string) $settings->get('footer.links_title_ar', 'استكشف'),
            ],
            'contactTitle' => [
                'en' => (string) $settings->get('footer.contact_title_en', 'Contact'),
                'ar' => (string) $settings->get('footer.contact_title_ar', 'تواصل معنا'),
            ],
            'followTitle' => [
                'en' => (string) $settings->get('footer.follow_title_en', 'Follow us'),
                'ar' => (string) $settings->get('footer.follow_title_ar', 'تابعنا'),
            ],
            'rights' => [
                'en' => (string) $settings->get('footer.rights_en', 'All rights reserved.'),
                'ar' => (string) $settings->get('footer.rights_ar', 'جميع الحقوق محفوظة.'),
            ],
            'address' => [
                'en' => (string) $settings->get('contact.address_en', ''),
                'ar' => (string) $settings->get('contact.address_ar', ''),
            ],
            'links' => array_map(fn (array $link): array => [
                'label' => ['en' => $link['label_en'], 'ar' => $link['label_ar']],
                'href' => $link['href'],
            ], self::footerLinks()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function contactPageCopy(): array
    {
        return ContactPageContent::forReact();
    }

    /**
     * @return array<string, mixed>
     */
    public static function reactPayload(): array
    {
        $footer = self::footerCopy();
        $contact = SiteFooter::contact();

        return [
            'navLinks' => array_map(fn (array $link): array => [
                'label' => ['en' => $link['label_en'], 'ar' => $link['label_ar']],
                'href' => $link['href'],
            ], self::navLinks()),
            'donateLabel' => [
                'en' => self::donateLabel('en'),
                'ar' => self::donateLabel('ar'),
            ],
            'footer' => $footer,
            'contact' => array_merge($contact, [
                'address' => $footer['address'],
            ]),
            'contactPage' => self::contactPageCopy(),
        ];
    }

    public static function resolveHref(string $href): string
    {
        if (str_starts_with($href, 'route:')) {
            $route = substr($href, 6);

            if ($route !== '' && app('router')->has($route)) {
                return route($route);
            }
        }

        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }

        if (str_starts_with($href, '#')) {
            return route('home').$href;
        }

        return url(str_starts_with($href, '/') ? $href : '/'.$href);
    }
}
