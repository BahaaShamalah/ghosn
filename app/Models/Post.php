<?php

namespace App\Models;

use App\Models\Concerns\HasCmsSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasCmsSlug;
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'title_en',
        'title_ar',
        'slug',
        'excerpt_en',
        'excerpt_ar',
        'content_en',
        'content_ar',
        'featured_image_media_id',
        'category_id',
        'status',
        'published_at',
        'seo_title_en',
        'seo_title_ar',
        'seo_description_en',
        'seo_description_ar',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_media_id');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && ($this->published_at === null || $this->published_at->lte(now()));
    }

    public function localizedTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function localizedExcerpt(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'ar' ? (string) $this->excerpt_ar : (string) $this->excerpt_en;
    }

    public function localizedContent(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'ar' ? (string) $this->content_ar : (string) $this->content_en;
    }

    public function localizedSeoTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $seo = $locale === 'ar' ? $this->seo_title_ar : $this->seo_title_en;

        return filled($seo) ? $seo : $this->localizedTitle($locale);
    }

    public function localizedSeoDescription(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $seo = $locale === 'ar' ? $this->seo_description_ar : $this->seo_description_en;

        return filled($seo) ? $seo : $this->localizedExcerpt($locale);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $inner): void {
                $inner->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
