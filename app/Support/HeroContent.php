<?php

namespace App\Support;

use App\Models\Media;

class HeroContent
{
    /**
     * Resolve hero fields from builder settings/blocks merged with defaults.
     *
     * @param  array<string, mixed>|null  $settings
     * @param  array<int, array<string, mixed>>|null  $blocks
     * @return array<string, mixed>
     */
    public static function resolve(?array $settings, ?array $blocks = null): array
    {
        $defaults = config('hero.defaults', []);
        $stored = is_array($settings) ? $settings : [];

        if (isset($stored['content']) && is_array($stored['content'])) {
            $stored = array_merge($stored, $stored['content']);
        }

        $merged = array_merge($defaults, self::filterStored($stored));

        self::applyLegacyBlocks($merged, $blocks);

        return HtmlText::cleanArray(self::finalize($merged));
    }

    /**
     * Shape hero content for the React landing payload.
     *
     * @param  array<string, mixed>|null  $settings
     * @param  array<int, array<string, mixed>>|null  $blocks
     * @return array<string, mixed>
     */
    public static function forReact(?array $settings, ?array $blocks = null): array
    {
        $hero = self::resolve($settings, $blocks);

        return [
            'badge' => [
                'en' => (string) ($hero['eyebrow_en'] ?? ''),
                'ar' => (string) ($hero['eyebrow_ar'] ?? ''),
            ],
            'title' => [
                'en' => self::combinedTitle($hero, 'en'),
                'ar' => self::combinedTitle($hero, 'ar'),
            ],
            'subtitle' => [
                'en' => (string) ($hero['description_en'] ?? ''),
                'ar' => (string) ($hero['description_ar'] ?? ''),
            ],
            'ctaPrimary' => [
                'en' => (string) ($hero['primary_button_text_en'] ?? ''),
                'ar' => (string) ($hero['primary_button_text_ar'] ?? ''),
            ],
            'ctaSecondary' => [
                'en' => (string) ($hero['secondary_button_text_en'] ?? ''),
                'ar' => (string) ($hero['secondary_button_text_ar'] ?? ''),
            ],
            'ctaPrimaryUrl' => (string) ($hero['primary_button_url'] ?? '#campaigns'),
            'ctaSecondaryUrl' => (string) ($hero['secondary_button_url'] ?? '#team'),
            'backgroundImage' => $hero['background_image_url'] ?? null,
            'backgroundAlt' => [
                'en' => (string) ($hero['background_alt_en'] ?? ''),
                'ar' => (string) ($hero['background_alt_ar'] ?? ''),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $merged
     * @param  array<int, array<string, mixed>>|null  $blocks
     */
    private static function applyLegacyBlocks(array &$merged, ?array $blocks): void
    {
        if ($blocks === null) {
            return;
        }

        $heading = LandingBlockHelper::content($blocks, 'heading');
        $text = LandingBlockHelper::content($blocks, 'text');

        if ($heading) {
            if (filled($heading['en'] ?? null) && ! filled($merged['title_line1_en'] ?? null)) {
                self::splitTitleIntoLines($heading['en'], $merged, 'en');
            }

            if (filled($heading['ar'] ?? null) && ! filled($merged['title_line1_ar'] ?? null)) {
                self::splitTitleIntoLines($heading['ar'], $merged, 'ar');
            }
        }

        if ($text) {
            if (filled($text['en'] ?? null) && ! filled($merged['description_en'] ?? null)) {
                $merged['description_en'] = $text['en'];
            }

            if (filled($text['ar'] ?? null) && ! filled($merged['description_ar'] ?? null)) {
                $merged['description_ar'] = $text['ar'];
            }
        }
    }

    /**
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    private static function filterStored(array $stored): array
    {
        $allowed = array_keys(config('hero.defaults', []));
        $filtered = [];

        foreach ($stored as $key => $value) {
            if (! in_array($key, $allowed, true)) {
                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $filtered[$key] = $value;
        }

        return $filtered;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function finalize(array $data): array
    {
        if (! empty($data['background_media_id'])) {
            $data['background_media_id'] = (int) $data['background_media_id'];
        } else {
            $data['background_media_id'] = null;
        }

        $data['background_image_url'] = self::mediaUrl($data['background_media_id']);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $hero
     */
    private static function combinedTitle(array $hero, string $locale): string
    {
        $line1 = trim((string) ($hero["title_line1_{$locale}"] ?? ''));
        $accent = trim((string) ($hero["title_accent_{$locale}"] ?? ''));

        if ($line1 === '') {
            return $accent;
        }

        if ($accent === '') {
            return $line1;
        }

        return rtrim($line1, '.').'. '.$accent;
    }

    private static function mediaUrl(mixed $mediaId): ?string
    {
        if (empty($mediaId)) {
            return null;
        }

        $media = Media::query()->find((int) $mediaId);

        return $media?->url();
    }

    /**
     * @param  array<string, mixed>  $merged
     */
    private static function splitTitleIntoLines(string $title, array &$merged, string $locale): void
    {
        $parts = preg_split('/\.\s*/', trim($title), 2);

        if ($parts && count($parts) === 2) {
            $merged["title_line1_{$locale}"] = rtrim($parts[0], '.').'.';
            $merged["title_accent_{$locale}"] = $parts[1];
        } else {
            $merged["title_line1_{$locale}"] = $title;
            $merged["title_accent_{$locale}"] = '';
        }
    }
}
