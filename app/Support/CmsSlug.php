<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsSlug
{
    public static function uniqueFrom(string $title, Model $model, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'item';
        $slug = $base;
        $counter = 2;

        while (self::exists($model, $slug, $ignoreId ?? $model->getKey()) || \App\Support\ReservedSlug::isReserved($slug)) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private static function exists(Model $model, string $slug, ?int $ignoreId): bool
    {
        $query = $model->newQuery()->where('slug', $slug);

        if ($ignoreId) {
            $query->whereKeyNot($ignoreId);
        }

        return $query->exists();
    }
}
