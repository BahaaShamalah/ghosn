<?php

namespace App\Support;

use App\Models\Media;
use App\Services\Content\EmbedService;

class AboutPageSettings
{
    /**
     * @param  array<string, mixed>|null  $input
     * @return array<string, mixed>
     */
    public static function sanitize(?array $input): array
    {
        $input = is_array($input) ? $input : [];
        $clean = [];

        foreach (['hero_title_en', 'hero_title_ar', 'hero_subtitle_en', 'hero_subtitle_ar', 'intro_title_en', 'intro_title_ar', 'vision_en', 'vision_ar', 'mission_en', 'mission_ar', 'slogan_en', 'slogan_ar'] as $field) {
            if (array_key_exists($field, $input)) {
                $clean[$field] = HtmlText::clean((string) $input[$field]);
            }
        }

        foreach (['intro_content_en', 'intro_content_ar'] as $field) {
            if (array_key_exists($field, $input)) {
                $clean[$field] = ContentHtml::sanitizeStorage((string) $input[$field]) ?? '';
            }
        }

        foreach (['hero_image_media_id', 'hero_video_media_id', 'video_cover_media_id'] as $field) {
            if (array_key_exists($field, $input)) {
                $clean[$field] = self::nullableMediaId($input[$field]);
            }
        }

        if (array_key_exists('youtube_url', $input)) {
            $clean['youtube_url'] = self::sanitizeYoutubeUrl((string) ($input['youtube_url'] ?? ''));
        }

        if (array_key_exists('gallery_media_ids', $input)) {
            $clean['gallery_media_ids'] = self::sanitizeMediaIdList($input['gallery_media_ids']);
        }

        if (array_key_exists('values', $input)) {
            $clean['values'] = self::sanitizeRepeater($input['values'], ['key', 'title_en', 'title_ar', 'text_en', 'text_ar']);
        }

        if (array_key_exists('timeline', $input)) {
            $clean['timeline'] = self::sanitizeRepeater($input['timeline'], ['year', 'title_en', 'title_ar', 'text_en', 'text_ar']);
        }

        if (array_key_exists('stats', $input)) {
            $clean['stats'] = self::sanitizeRepeater($input['stats'], ['key', 'label_en', 'label_ar', 'value']);
        }

        if (array_key_exists('team_cards', $input)) {
            $clean['team_cards'] = self::sanitizeRepeater($input['team_cards'], ['title_en', 'title_ar', 'text_en', 'text_ar']);
        }

        if (array_key_exists('cta_buttons', $input)) {
            $clean['cta_buttons'] = self::sanitizeCtaButtons($input['cta_buttons']);
        }

        return $clean;
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationRules(): array
    {
        return [
            'settings' => ['nullable', 'array'],
            'settings.hero_title_en' => ['nullable', 'string', 'max:255'],
            'settings.hero_title_ar' => ['nullable', 'string', 'max:255'],
            'settings.hero_subtitle_en' => ['nullable', 'string', 'max:500'],
            'settings.hero_subtitle_ar' => ['nullable', 'string', 'max:500'],
            'settings.hero_image_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'settings.hero_video_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'settings.video_cover_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'settings.youtube_url' => ['nullable', 'string', 'max:500'],
            'settings.intro_title_en' => ['nullable', 'string', 'max:255'],
            'settings.intro_title_ar' => ['nullable', 'string', 'max:255'],
            'settings.intro_content_en' => ['nullable', 'string', 'max:10000'],
            'settings.intro_content_ar' => ['nullable', 'string', 'max:10000'],
            'settings.vision_en' => ['nullable', 'string', 'max:1000'],
            'settings.vision_ar' => ['nullable', 'string', 'max:1000'],
            'settings.mission_en' => ['nullable', 'string', 'max:1000'],
            'settings.mission_ar' => ['nullable', 'string', 'max:1000'],
            'settings.slogan_en' => ['nullable', 'string', 'max:500'],
            'settings.slogan_ar' => ['nullable', 'string', 'max:500'],
            'settings.gallery_media_ids' => ['nullable', 'array'],
            'settings.gallery_media_ids.*' => ['integer', 'exists:media,id'],
            'settings.values' => ['nullable', 'array'],
            'settings.timeline' => ['nullable', 'array'],
            'settings.stats' => ['nullable', 'array'],
            'settings.team_cards' => ['nullable', 'array'],
            'settings.cta_buttons' => ['nullable', 'array'],
        ];
    }

    public static function sanitizeYoutubeUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        /** @var EmbedService $embeds */
        $embeds = app(EmbedService::class);

        return $embeds->youtubeIdFromUrl($url) ? $url : null;
    }

    private static function nullableMediaId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $id = (int) $value;

        return Media::query()->whereKey($id)->exists() ? $id : null;
    }

    /**
     * @return list<int>
     */
    private static function sanitizeMediaIdList(mixed $value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($value)) {
            return [];
        }

        return collect($value)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0 && Media::query()->whereKey($id)->exists())
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $fields
     * @return list<array<string, string>>
     */
    private static function sanitizeRepeater(mixed $items, array $fields): array
    {
        if (! is_array($items)) {
            return [];
        }

        $rows = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $row = [];
            $hasContent = false;

            foreach ($fields as $field) {
                $value = HtmlText::clean((string) ($item[$field] ?? ''));
                $row[$field] = $value;

                if ($value !== '') {
                    $hasContent = true;
                }
            }

            if ($hasContent) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * @return list<array<string, string>>
     */
    private static function sanitizeCtaButtons(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        $rows = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $labelEn = HtmlText::clean((string) ($item['label_en'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));

            if ($labelEn === '' || $url === '') {
                continue;
            }

            if (preg_match('/^\s*javascript:/i', $url)) {
                continue;
            }

            $rows[] = [
                'label_en' => $labelEn,
                'label_ar' => HtmlText::clean((string) ($item['label_ar'] ?? '')),
                'url' => $url,
                'style' => in_array($item['style'] ?? '', ['primary', 'secondary'], true) ? $item['style'] : 'secondary',
            ];
        }

        return $rows;
    }
}
