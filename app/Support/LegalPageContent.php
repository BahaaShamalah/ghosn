<?php

namespace App\Support;

use App\Services\Settings\SettingsService;

class LegalPageContent
{
    /**
     * @return array<string, mixed>
     */
    public static function forReact(string $activeKey): array
    {
        $config = config('legal-pages', []);
        $stored = self::storedPages();
        $order = $config['order'] ?? [];
        $slugs = $config['slugs'] ?? [];
        $tabs = $config['tabs'] ?? [];
        $ui = self::uiStrings($config['ui'] ?? []);

        $pages = [];
        $tabLinks = [];

        foreach ($order as $key) {
            $page = array_replace_recursive(
                $config['pages'][$key] ?? [],
                $stored[$key] ?? [],
            );

            $pages[$key] = self::formatPage($key, $page);
            $tabLinks[] = [
                'key' => $key,
                'label' => [
                    'en' => $tabs[$key]['label_en'] ?? $page['title_en'] ?? '',
                    'ar' => $tabs[$key]['label_ar'] ?? $page['title_ar'] ?? '',
                ],
                'href' => url('/'.($slugs[$key] ?? $key)),
            ];
        }

        return [
            'activeKey' => $activeKey,
            'page' => $pages[$activeKey] ?? [],
            'tabs' => $tabLinks,
            'ui' => $ui,
        ];
    }

    public static function keyForSlug(string $slug): ?string
    {
        $slugs = config('legal-pages.slugs', []);

        foreach ($slugs as $key => $value) {
            if ($value === $slug) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'pages' => config('legal-pages.pages', []),
            'ui' => config('legal-pages.ui', []),
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function storedPages(): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $raw = $settings->get('legal.pages');

        return is_array($raw) ? $raw : [];
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @return array<string, array{en: string, ar: string}>
     */
    private static function uiStrings(array $defaults): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $stored = $settings->get('legal.ui');

        if (! is_array($stored)) {
            $stored = [];
        }

        $merged = array_replace_recursive($defaults, $stored);

        return [
            'lastUpdated' => [
                'en' => (string) ($merged['last_updated_en'] ?? 'Last updated'),
                'ar' => (string) ($merged['last_updated_ar'] ?? 'آخر تحديث'),
            ],
            'onThisPage' => [
                'en' => (string) ($merged['on_this_page_en'] ?? 'On this page'),
                'ar' => (string) ($merged['on_this_page_ar'] ?? 'في هذه الصفحة'),
            ],
            'homeCrumb' => [
                'en' => (string) ($merged['home_crumb_en'] ?? 'Home'),
                'ar' => (string) ($merged['home_crumb_ar'] ?? 'الرئيسية'),
            ],
            'contactNote' => [
                'title' => [
                    'en' => (string) ($merged['contact_note_title_en'] ?? ''),
                    'ar' => (string) ($merged['contact_note_title_ar'] ?? ''),
                ],
                'body' => [
                    'en' => (string) ($merged['contact_note_body_en'] ?? ''),
                    'ar' => (string) ($merged['contact_note_body_ar'] ?? ''),
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $page
     * @return array<string, mixed>
     */
    private static function formatPage(string $key, array $page): array
    {
        $sections = [];

        foreach ($page['sections'] ?? [] as $index => $section) {
            if (! is_array($section)) {
                continue;
            }

            $anchor = $key.'-sec-'.$index;
            $paragraphsEn = self::lines($section['paragraphs_en'] ?? []);
            $paragraphsAr = self::lines($section['paragraphs_ar'] ?? []);
            $bulletsEn = self::lines($section['bullets_en'] ?? []);
            $bulletsAr = self::lines($section['bullets_ar'] ?? []);

            $sections[] = [
                'num' => str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'anchor' => $anchor,
                'heading' => [
                    'en' => (string) ($section['heading_en'] ?? ''),
                    'ar' => (string) ($section['heading_ar'] ?? ''),
                ],
                'paragraphs' => [
                    'en' => $paragraphsEn,
                    'ar' => $paragraphsAr,
                ],
                'bullets' => [
                    'en' => $bulletsEn,
                    'ar' => $bulletsAr,
                ],
            ];
        }

        return [
            'title' => [
                'en' => (string) ($page['title_en'] ?? ''),
                'ar' => (string) ($page['title_ar'] ?? ''),
            ],
            'subtitle' => [
                'en' => (string) ($page['subtitle_en'] ?? ''),
                'ar' => (string) ($page['subtitle_ar'] ?? ''),
            ],
            'updated' => [
                'en' => (string) ($page['updated_en'] ?? ''),
                'ar' => (string) ($page['updated_ar'] ?? ''),
            ],
            'intro' => [
                'en' => (string) ($page['intro_en'] ?? ''),
                'ar' => (string) ($page['intro_ar'] ?? ''),
            ],
            'sections' => $sections,
        ];
    }

    /**
     * @param  array<int, string>|string  $value
     * @return list<string>
     */
    private static function lines(array|string $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/\r\n|\r|\n/', $value) ?: [];
        }

        return array_values(array_filter(array_map(
            static fn ($line): string => trim((string) $line),
            $value,
        ), static fn (string $line): bool => $line !== ''));
    }
}
