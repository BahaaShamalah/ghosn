<?php

namespace App\Support;

use App\Models\Media;
use App\Services\Settings\SettingsService;

class SeoSettings
{
    public static function absoluteUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url($path);
    }

    public static function title(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $settings = app(SettingsService::class);

        $configured = trim((string) $settings->get(
            $locale === 'ar' ? 'seo.title_ar' : 'seo.title_en',
            '',
        ));

        if ($configured !== '') {
            return $configured;
        }

        return SiteSettings::name($locale);
    }

    public static function description(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $settings = app(SettingsService::class);

        $configured = trim((string) $settings->get(
            $locale === 'ar' ? 'seo.description_ar' : 'seo.description_en',
            '',
        ));

        if ($configured !== '') {
            return $configured;
        }

        $slogan = trim((string) $settings->get(
            $locale === 'ar' ? 'site.slogan_ar' : 'site.slogan_en',
            '',
        ));

        if ($slogan !== '') {
            return $slogan;
        }

        return trim((string) $settings->get(
            $locale === 'ar' ? 'footer.desc_ar' : 'footer.desc_en',
            '',
        ));
    }

    public static function imageUrl(?SettingsService $settings = null): ?string
    {
        $settings ??= app(SettingsService::class);

        $mediaId = $settings->get('seo.image_media_id');

        if ($mediaId) {
            $media = Media::query()->find((int) $mediaId);

            if ($media) {
                return self::absoluteUrl($media->url());
            }
        }

        $path = trim((string) $settings->get('seo.image', ''));

        if ($path !== '') {
            return self::absoluteUrl(str_starts_with($path, 'http') ? $path : asset($path));
        }

        return self::absoluteUrl(SiteAsset::logoUrl($settings));
    }

    public static function robotsDefault(?SettingsService $settings = null): string
    {
        $settings ??= app(SettingsService::class);
        $value = trim((string) $settings->get('seo.robots_default', 'index,follow'));

        return $value !== '' ? $value : 'index,follow';
    }

    public static function twitterSite(?SettingsService $settings = null): string
    {
        $settings ??= app(SettingsService::class);
        $value = trim((string) $settings->get('seo.twitter_site', ''));

        if ($value === '') {
            return '';
        }

        return str_starts_with($value, '@') ? $value : '@'.$value;
    }

    public static function canonicalUrl(?string $override = null, ?SettingsService $settings = null): string
    {
        $settings ??= app(SettingsService::class);

        if ($override !== null && trim($override) !== '') {
            return self::absoluteUrl($override) ?? url('/');
        }

        $mode = trim((string) $settings->get('seo.canonical_mode', 'current'));

        if ($mode === 'homepage_prefer' && request()->routeIs('home')) {
            return url('/');
        }

        return url()->current();
    }

    /**
     * @param  array{title?: string|null, description?: string|null, image?: string|null, url?: string|null, type?: string|null, robots?: string|null, canonical?: string|null, breadcrumbs?: list<array{name: string, url: string}>, faq?: list<array{question: string, answer: string}>, article?: array<string, mixed>|null}  $overrides
     * @return array{title: string, description: string, image: string|null, url: string, type: string, site_name: string, locale: string, locale_alternate: string, robots: string, canonical: string, twitter_site: string, json_ld: list<array<string, mixed>>}
     */
    public static function meta(?string $locale = null, array $overrides = []): array
    {
        $locale ??= app()->getLocale();
        $settings = app(SettingsService::class);

        $title = trim((string) ($overrides['title'] ?? self::title($locale)));
        $description = trim((string) ($overrides['description'] ?? self::description($locale)));
        $image = array_key_exists('image', $overrides)
            ? self::absoluteUrl($overrides['image'])
            : self::imageUrl($settings);
        $url = self::absoluteUrl($overrides['url'] ?? url()->current()) ?? url('/');
        $type = trim((string) ($overrides['type'] ?? 'website')) ?: 'website';

        $meta = [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'url' => $url,
            'type' => $type,
            'site_name' => SiteSettings::name($locale),
            'locale' => $locale === 'ar' ? 'ar_AR' : 'en_US',
            'locale_alternate' => $locale === 'ar' ? 'en_US' : 'ar_AR',
            'robots' => trim((string) ($overrides['robots'] ?? self::robotsDefault($settings))) ?: 'index,follow',
            'canonical' => self::canonicalUrl($overrides['canonical'] ?? $url, $settings),
            'twitter_site' => self::twitterSite($settings),
        ];

        $meta['json_ld'] = JsonLdBuilder::graphs($locale, [
            'title' => $meta['title'],
            'description' => $meta['description'],
            'url' => $meta['url'],
            'image' => $meta['image'],
            'type' => $meta['type'],
            'breadcrumbs' => $overrides['breadcrumbs'] ?? [],
            'faq' => $overrides['faq'] ?? [],
            'article' => $overrides['article'] ?? null,
        ]);

        return $meta;
    }
}
