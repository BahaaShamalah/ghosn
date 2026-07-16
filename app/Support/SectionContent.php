<?php

namespace App\Support;

use App\Models\Media;

class SectionContent
{
    /** @var list<string> */
    public const STRUCTURED_KEYS = [
        'impact',
        'how_works',
        'ways',
        'testimonials',
        'join',
        'campaigns',
        'latest_news',
    ];

    /**
     * @param  array<string, mixed>|null  $settings
     * @param  array<int, array<string, mixed>>|null  $blocks
     * @return array<string, mixed>
     */
    public static function resolve(string $key, ?array $settings = null, ?array $blocks = null): array
    {
        $config = config("sections.{$key}");

        if (! is_array($config)) {
            return [];
        }

        $defaults = $config['defaults'] ?? [];
        $stored = is_array($settings) ? $settings : [];

        if (isset($stored['content']) && is_array($stored['content'])) {
            $stored = array_merge($stored, $stored['content']);
        }

        $merged = self::deepMerge($defaults, self::filterStored($defaults, $stored));

        self::applyLegacyHeading($merged, $blocks);

        return HtmlText::cleanArray(self::finalize($merged, $defaults));
    }

    public static function isStructured(string $key): bool
    {
        return in_array($key, self::STRUCTURED_KEYS, true) && is_array(config("sections.{$key}"));
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function config(string $key): ?array
    {
        $config = config("sections.{$key}");

        return is_array($config) ? $config : null;
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    private static function filterStored(array $defaults, array $stored): array
    {
        $filtered = [];

        foreach ($stored as $key => $value) {
            if (! array_key_exists($key, $defaults)) {
                continue;
            }

            if (is_array($defaults[$key])) {
                if (! is_array($value)) {
                    continue;
                }

                $filtered[$key] = self::normalizeRepeater($defaults[$key], $value);

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
     * @param  array<int, array<string, mixed>>  $defaultItems
     * @param  array<int, mixed>  $storedItems
     * @return array<int, array<string, mixed>>
     */
    private static function normalizeRepeater(array $defaultItems, array $storedItems): array
    {
        $normalized = [];

        foreach ($defaultItems as $index => $defaultItem) {
            if (! is_array($defaultItem)) {
                continue;
            }

            $storedItem = $storedItems[$index] ?? [];
            $item = is_array($storedItem) ? $storedItem : [];
            $mergedItem = [];

            foreach ($defaultItem as $field => $defaultValue) {
                $storedValue = $item[$field] ?? null;
                $mergedItem[$field] = filled($storedValue) ? $storedValue : $defaultValue;
            }

            $normalized[$index] = $mergedItem;
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    private static function deepMerge(array $defaults, array $stored): array
    {
        $merged = $defaults;

        foreach ($stored as $key => $value) {
            if (is_array($value) && isset($defaults[$key]) && is_array($defaults[$key])) {
                $merged[$key] = self::normalizeRepeater($defaults[$key], $value);

                continue;
            }

            $merged[$key] = $value;
        }

        return $merged;
    }

    /**
     * @param  array<string, mixed>  $merged
     * @param  array<int, array<string, mixed>>|null  $blocks
     */
    private static function applyLegacyHeading(array &$merged, ?array $blocks): void
    {
        if ($blocks === null) {
            return;
        }

        $heading = LandingBlockHelper::content($blocks, 'heading');

        if (! $heading) {
            return;
        }

        if (filled($heading['en'] ?? null) && ! filled($merged['heading_en'] ?? null)) {
            $merged['heading_en'] = $heading['en'];
        }

        if (filled($heading['ar'] ?? null) && ! filled($merged['heading_ar'] ?? null)) {
            $merged['heading_ar'] = $heading['ar'];
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $defaults
     * @return array<string, mixed>
     */
    private static function finalize(array $data, array $defaults): array
    {
        foreach ($data as $key => $value) {
            if (str_ends_with($key, '_media_id') && filled($value)) {
                $data[$key] = (int) $value;
                $urlKey = str_replace('_media_id', '_url', $key);
                $data[$urlKey] = self::mediaUrl($value);
            }
        }

        if (isset($defaults['items']) && is_array($defaults['items'])) {
            $data['items'] = self::normalizeRepeater(
                $defaults['items'],
                is_array($data['items'] ?? null) ? $data['items'] : [],
            );
        }

        if (isset($defaults['cards']) && is_array($defaults['cards'])) {
            $data['cards'] = self::normalizeRepeater(
                $defaults['cards'],
                is_array($data['cards'] ?? null) ? $data['cards'] : [],
            );
        }

        if (isset($defaults['partners']) && is_array($defaults['partners'])) {
            $data['partners'] = self::normalizeRepeater(
                $defaults['partners'],
                is_array($data['partners'] ?? null) ? $data['partners'] : [],
            );
        }

        foreach (['steps', 'bullets', 'areas', 'stats'] as $repeaterKey) {
            if (isset($defaults[$repeaterKey]) && is_array($defaults[$repeaterKey])) {
                $data[$repeaterKey] = self::normalizeRepeater(
                    $defaults[$repeaterKey],
                    is_array($data[$repeaterKey] ?? null) ? $data[$repeaterKey] : [],
                );
            }
        }

        return $data;
    }

    private static function mediaUrl(mixed $mediaId): ?string
    {
        if (empty($mediaId)) {
            return null;
        }

        $media = Media::query()->find((int) $mediaId);

        return $media?->url();
    }
}
