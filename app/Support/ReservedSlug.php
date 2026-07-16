<?php

namespace App\Support;

class ReservedSlug
{
    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return config('cms.reserved_slugs', []);
    }

    public static function isReserved(?string $slug): bool
    {
        if ($slug === null || $slug === '') {
            return false;
        }

        return in_array(strtolower($slug), self::all(), true);
    }

    public static function routePattern(): string
    {
        $reserved = implode('|', array_map(static fn (string $slug): string => preg_quote($slug, '/'), self::all()));

        return '^(?!('.$reserved.')$)[a-z0-9]+(?:-[a-z0-9]+)*$';
    }
}
