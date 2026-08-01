<?php

namespace App\Support;

class TeamPageInput
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

        if (isset($page['leaders']) && is_array($page['leaders'])) {
            foreach ($page['leaders'] as $index => $leader) {
                if (! is_array($leader)) {
                    continue;
                }

                $page['leaders'][$index]['image_media_id'] = self::nullableMediaId($leader['image_media_id'] ?? null);
                unset($page['leaders'][$index]['image_url']);
            }
        }

        if (isset($page['culture']) && is_array($page['culture'])) {
            $page['culture']['image_media_id'] = self::nullableMediaId($page['culture']['image_media_id'] ?? null);
            unset($page['culture']['image_url']);
        }

        return $page;
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
