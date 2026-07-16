<?php

namespace App\Support;

class AboutPageInput
{
    /**
     * @param  array<string, mixed>  $page
     * @return array<string, mixed>
     */
    public static function normalize(array $page): array
    {
        if (isset($page['sections']) && is_array($page['sections'])) {
            foreach ($page['sections'] as $key => $value) {
                $page['sections'][$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
        }

        if (isset($page['intro']['paragraphs_en']) && is_string($page['intro']['paragraphs_en'])) {
            $page['intro']['paragraphs_en'] = self::linesToList($page['intro']['paragraphs_en'], true);
        }

        if (isset($page['intro']['paragraphs_ar']) && is_string($page['intro']['paragraphs_ar'])) {
            $page['intro']['paragraphs_ar'] = self::linesToList($page['intro']['paragraphs_ar'], true);
        }

        if (isset($page['partners']['items_en']) && is_string($page['partners']['items_en'])) {
            $page['partners']['items_en'] = self::linesToList($page['partners']['items_en'], false);
        }

        if (isset($page['partners']['items_ar']) && is_string($page['partners']['items_ar'])) {
            $page['partners']['items_ar'] = self::linesToList($page['partners']['items_ar'], false);
        }

        if (isset($page['hero']) && is_array($page['hero'])) {
            $page['hero']['image_media_id'] = self::nullableMediaId($page['hero']['image_media_id'] ?? null);
        }

        if (isset($page['intro']) && is_array($page['intro'])) {
            $page['intro']['image_media_id'] = self::nullableMediaId($page['intro']['image_media_id'] ?? null);
            $page['intro']['video_cover_media_id'] = self::nullableMediaId($page['intro']['video_cover_media_id'] ?? null);
        }

        if (isset($page['team']['members']) && is_array($page['team']['members'])) {
            foreach ($page['team']['members'] as $index => $member) {
                if (! is_array($member)) {
                    continue;
                }

                $page['team']['members'][$index]['image_media_id'] = self::nullableMediaId($member['image_media_id'] ?? null);
            }
        }

        return $page;
    }

    /**
     * @return list<string>
     */
    private static function linesToList(string $value, bool $paragraphs): array
    {
        $parts = $paragraphs
            ? (preg_split("/\n\s*\n/", $value) ?: [])
            : (preg_split("/\r\n|\n|\r/", $value) ?: []);

        return array_values(array_filter(array_map('trim', $parts)));
    }

    private static function nullableMediaId(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }

        $id = (int) $value;

        return $id > 0 ? $id : null;
    }
}
