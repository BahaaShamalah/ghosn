<?php

namespace App\Models;

use App\Support\CmsSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    public const TYPE_POST = 'post';

    public const TYPE_CAMPAIGN = 'campaign';

    protected $fillable = [
        'name_en',
        'name_ar',
        'slug',
        'type',
    ];

    protected static function booted(): void
    {
        static::saving(function (Category $category): void {
            if (! filled($category->slug) && filled($category->name_en)) {
                $category->slug = CmsSlug::uniqueFrom($category->name_en, $category);
            }
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }
}
