<?php

namespace App\Support;

use App\Models\Media;

class AboutContent
{
    /**
     * @param  array<string, mixed>|null  $settings
     * @param  array<int, array<string, mixed>>|null  $blocks
     * @return array<string, mixed>
     */
    public static function resolve(?array $settings, ?array $blocks = null): array
    {
        $defaults = config('about.defaults', []);
        $stored = is_array($settings) ? $settings : [];
        $rawContent = is_array($stored['content'] ?? null) ? $stored['content'] : [];

        if (isset($stored['content']) && is_array($stored['content'])) {
            $stored = array_merge($stored, $stored['content']);
        }

        $merged = array_merge($defaults, self::filterStored($defaults, $stored));

        foreach (['paragraphs_en', 'paragraphs_ar'] as $key) {
            if (array_key_exists($key, $rawContent)) {
                $merged[$key] = is_string($rawContent[$key]) ? $rawContent[$key] : '';
            }
        }

        self::applyLegacyBlocks($merged, $blocks);

        return HtmlText::cleanArray(self::finalize($merged, $defaults));
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @param  array<int, array<string, mixed>>|null  $blocks
     * @return array<string, mixed>
     */
    public static function forReact(?array $settings, ?array $blocks = null): array
    {
        $about = self::resolve($settings, $blocks);

        return [
            'eyebrow' => [
                'en' => (string) ($about['eyebrow_en'] ?? ''),
                'ar' => (string) ($about['eyebrow_ar'] ?? ''),
            ],
            'title' => [
                'en' => (string) ($about['heading_en'] ?? ''),
                'ar' => (string) ($about['heading_ar'] ?? ''),
            ],
            'paragraphs' => [
                'en' => self::splitParagraphs(self::paragraphsEditorText($about, 'en')),
                'ar' => self::splitParagraphs(self::paragraphsEditorText($about, 'ar')),
            ],
            'stats' => collect($about['stats'] ?? [])->map(fn (array $stat): array => [
                'value' => [
                    'en' => (string) ($stat['value_en'] ?? ''),
                    'ar' => (string) ($stat['value_ar'] ?? ''),
                ],
                'label' => [
                    'en' => (string) ($stat['label_en'] ?? ''),
                    'ar' => (string) ($stat['label_ar'] ?? ''),
                ],
            ])->values()->all(),
            'image' => $about['image_url'] ?? null,
            'imageAlt' => [
                'en' => (string) ($about['image_alt_en'] ?? ''),
                'ar' => (string) ($about['image_alt_ar'] ?? ''),
            ],
            'video' => [
                'embedUrl' => $about['video_embed_url'] ?? null,
                'poster' => $about['video_poster_url'] ?? null,
                'provider' => $about['video_embed_provider'] ?? null,
            ],
            'watch' => [
                'en' => (string) ($about['watch_label_en'] ?? ''),
                'ar' => (string) ($about['watch_label_ar'] ?? ''),
            ],
            'readMore' => [
                'en' => (string) ($about['read_more_en'] ?? ''),
                'ar' => (string) ($about['read_more_ar'] ?? ''),
            ],
        ];
    }

    /**
     * Combined paragraph text for the admin editor.
     *
     * @param  array<string, mixed>  $content
     */
    public static function paragraphsEditorText(array $content, string $locale): string
    {
        $key = "paragraphs_{$locale}";

        if (array_key_exists($key, $content)) {
            return (string) $content[$key];
        }

        return self::joinLegacyParagraphs($content, $locale);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function joinLegacyParagraphs(array $data, string $locale): string
    {
        $parts = [];

        foreach ([1, 2, 3] as $index) {
            $text = trim((string) ($data["paragraph{$index}_{$locale}"] ?? ''));

            if ($text !== '') {
                $parts[] = $text;
            }
        }

        return implode("\n\n", $parts);
    }

    /**
     * @return list<string>
     */
    private static function splitParagraphs(string $text): array
    {
        if (trim($text) === '') {
            return [];
        }

        $parts = preg_split("/\r\n\r\n|\n\r\n|\r\n\n|\n\n/", trim($text)) ?: [];

        return array_values(array_filter(
            array_map(static fn (string $part): string => rtrim($part), $parts),
            fn (string $part): bool => trim($part) !== '',
        ));
    }

    /**
     * @param  array<string, mixed>  $about
     * @return list<array{en: string, ar: string}>
     */
    private static function paragraphPairs(array $about): array
    {
        $english = self::splitParagraphs(self::paragraphsEditorText($about, 'en'));
        $arabic = self::splitParagraphs(self::paragraphsEditorText($about, 'ar'));
        $count = max(count($english), count($arabic));
        $pairs = [];

        for ($i = 0; $i < $count; $i++) {
            $pairs[] = [
                'en' => $english[$i] ?? '',
                'ar' => $arabic[$i] ?? '',
            ];
        }

        return $pairs;
    }

    public static function hasStoredParagraphs(array $stored): bool
    {
        foreach (['paragraphs_en', 'paragraphs_ar'] as $key) {
            if (array_key_exists($key, $stored)) {
                return true;
            }
        }

        foreach ([1, 2, 3] as $index) {
            foreach (['en', 'ar'] as $locale) {
                if (filled($stored["paragraph{$index}_{$locale}"] ?? null)) {
                    return true;
                }
            }
        }

        return false;
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

            if ($key === 'stats' && is_array($value) && is_array($defaults['stats'] ?? null)) {
                $filtered['stats'] = self::normalizeRepeater($defaults['stats'], $value);

                continue;
            }

            if (in_array($key, ['paragraphs_en', 'paragraphs_ar'], true)) {
                if (array_key_exists($key, $stored)) {
                    $filtered[$key] = is_string($value) ? $value : '';
                }

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
     * @param  array<string, mixed>  $merged
     * @param  array<int, array<string, mixed>>|null  $blocks
     */
    private static function applyLegacyBlocks(array &$merged, ?array $blocks): void
    {
        if ($blocks === null) {
            return;
        }

        $heading = LandingBlockHelper::content($blocks, 'heading');

        if ($heading) {
            if (filled($heading['en'] ?? null) && ! filled($merged['heading_en'] ?? null)) {
                $merged['heading_en'] = $heading['en'];
            }

            if (filled($heading['ar'] ?? null) && ! filled($merged['heading_ar'] ?? null)) {
                $merged['heading_ar'] = $heading['ar'];
            }
        }

        $image = LandingBlockHelper::content($blocks, 'image');

        if ($image) {
            if (! empty($image['media_id']) && empty($merged['image_media_id'])) {
                $merged['image_media_id'] = $image['media_id'];
            }

            foreach (['image_alt_en' => 'alt_en', 'image_alt_ar' => 'alt_ar'] as $target => $source) {
                if (filled($image[$source] ?? null) && ! filled($merged[$target] ?? null)) {
                    $merged[$target] = $image[$source];
                }
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $defaults
     * @return array<string, mixed>
     */
    private static function finalize(array $data, array $defaults): array
    {
        if (! empty($data['image_media_id'])) {
            $data['image_media_id'] = (int) $data['image_media_id'];
        }

        $data['image_url'] = self::mediaUrl($data['image_media_id'] ?? null);

        if (! empty($data['video_cover_media_id'])) {
            $data['video_cover_media_id'] = (int) $data['video_cover_media_id'];
        }

        $embed = VideoEmbed::parse($data['video_url'] ?? null);
        $data['video_embed_provider'] = $embed['provider'];
        $data['video_embed_url'] = $embed['embed_url'];
        $data['video_poster_url'] = self::resolveVideoPoster($data, $embed);

        if (isset($defaults['stats']) && is_array($defaults['stats'])) {
            $data['stats'] = self::normalizeRepeater(
                $defaults['stats'],
                is_array($data['stats'] ?? null) ? $data['stats'] : [],
            );
        }

        foreach (['en', 'ar'] as $locale) {
            $key = "paragraphs_{$locale}";

            if (array_key_exists($key, $data)) {
                continue;
            }

            $legacy = self::joinLegacyParagraphs($data, $locale);

            if ($legacy !== '') {
                $data[$key] = $legacy;
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array{provider: ?string, embed_url: ?string, source_url: ?string}  $embed
     */
    private static function resolveVideoPoster(array $data, array $embed): ?string
    {
        $coverUrl = self::mediaUrl($data['video_cover_media_id'] ?? null);

        if (filled($coverUrl)) {
            return $coverUrl;
        }

        $imageUrl = self::mediaUrl($data['image_media_id'] ?? null);

        if (filled($imageUrl)) {
            return $imageUrl;
        }

        if (($embed['provider'] ?? null) === 'youtube' && filled($embed['source_url'] ?? null)) {
            $youtubeId = VideoEmbed::youtubeId((string) $embed['source_url']);

            if ($youtubeId) {
                return 'https://i.ytimg.com/vi/'.$youtubeId.'/hqdefault.jpg';
            }
        }

        return null;
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
