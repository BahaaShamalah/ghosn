<?php

namespace App\Support;

use App\Services\Settings\SettingsService;

class JsonLdBuilder
{
    /**
     * @param  array{title?: string, description?: string, url?: string, image?: string|null, type?: string, breadcrumbs?: list<array{name: string, url: string}>, faq?: list<array{question: string, answer: string}>, article?: array<string, mixed>|null}  $context
     * @return list<array<string, mixed>>
     */
    public static function graphs(?string $locale = null, array $context = []): array
    {
        $locale ??= app()->getLocale();
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $graphs = [];

        if ((bool) $settings->get('seo.schema_organization', true)) {
            $graphs[] = self::organization($locale, $settings);
        }

        if ((bool) $settings->get('seo.schema_website', true)) {
            $graphs[] = self::website($locale, $settings);
        }

        if ((bool) $settings->get('seo.schema_article', true) && ($context['type'] ?? '') === 'article' && is_array($context['article'] ?? null)) {
            $graphs[] = self::article($context);
        }

        if ((bool) $settings->get('seo.schema_breadcrumb', true) && ! empty($context['breadcrumbs'])) {
            $graphs[] = self::breadcrumb($context['breadcrumbs']);
        }

        if ((bool) $settings->get('seo.schema_faq', true) && ! empty($context['faq'])) {
            $graphs[] = self::faq($context['faq']);
        }

        return array_values(array_filter($graphs));
    }

    /**
     * @return array<string, mixed>
     */
    private static function organization(string $locale, SettingsService $settings): array
    {
        $nameKey = $locale === 'ar' ? 'seo.organization_name_ar' : 'seo.organization_name_en';
        $name = trim((string) $settings->get($nameKey, ''));
        if ($name === '') {
            $name = SiteSettings::name($locale);
        }

        $type = trim((string) $settings->get('seo.organization_type', 'NGO')) ?: 'NGO';

        $data = [
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $name,
            'url' => url('/'),
        ];

        $logo = SeoSettings::absoluteUrl(SiteAsset::logoUrl($settings));
        if ($logo) {
            $data['logo'] = $logo;
            $data['image'] = $logo;
        }

        $email = trim((string) $settings->get('contact.email', ''));
        if ($email !== '') {
            $data['email'] = $email;
        }

        $phone = trim((string) $settings->get('contact.phone', ''));
        if ($phone !== '') {
            $data['telephone'] = $phone;
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private static function website(string $locale, SettingsService $settings): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => SiteSettings::name($locale),
            'url' => url('/'),
            'inLanguage' => $locale === 'ar' ? 'ar' : 'en',
            'description' => SeoSettings::description($locale),
            'publisher' => [
                '@type' => trim((string) $settings->get('seo.organization_type', 'NGO')) ?: 'NGO',
                'name' => SiteSettings::name($locale),
            ],
        ];
    }

    /**
     * @param  array{title?: string, description?: string, url?: string, image?: string|null, article?: array<string, mixed>}  $context
     * @return array<string, mixed>
     */
    private static function article(array $context): array
    {
        $article = $context['article'] ?? [];

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => (string) ($context['title'] ?? $article['headline'] ?? ''),
            'description' => (string) ($context['description'] ?? $article['description'] ?? ''),
            'url' => (string) ($context['url'] ?? url()->current()),
            'mainEntityOfPage' => (string) ($context['url'] ?? url()->current()),
        ];

        if (! empty($context['image'])) {
            $data['image'] = [$context['image']];
        }

        if (! empty($article['datePublished'])) {
            $data['datePublished'] = $article['datePublished'];
        }

        if (! empty($article['dateModified'])) {
            $data['dateModified'] = $article['dateModified'];
        }

        return $data;
    }

    /**
     * @param  list<array{name: string, url: string}>  $crumbs
     * @return array<string, mixed>
     */
    private static function breadcrumb(array $crumbs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_map(
                static fn (array $crumb, int $index): array => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $crumb['name'],
                    'item' => $crumb['url'],
                ],
                $crumbs,
                array_keys($crumbs),
            )),
        ];
    }

    /**
     * @param  list<array{question: string, answer: string}>  $items
     * @return array<string, mixed>
     */
    private static function faq(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_values(array_map(
                static fn (array $item): array => [
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item['answer'],
                    ],
                ],
                $items,
            )),
        ];
    }
}
