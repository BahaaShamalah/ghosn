<?php

namespace App\Models;

use App\Models\Concerns\HasCmsSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentPage extends Model
{
    use HasCmsSlug;
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    public const TEMPLATE_DEFAULT = 'default';

    protected $table = 'content_pages';

    protected $fillable = [
        'author_id',
        'title_en',
        'title_ar',
        'slug',
        'template',
        'settings',
        'content_en',
        'content_ar',
        'featured_image_media_id',
        'status',
        'seo_title_en',
        'seo_title_ar',
        'seo_description_en',
        'seo_description_ar',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_media_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return list<string>
     */
    public static function templates(): array
    {
        return [
            self::TEMPLATE_DEFAULT,
        ];
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function isProtected(): bool
    {
        return in_array($this->slug, config('cms.protected_content_page_slugs', []), true);
    }

    public function canDelete(): bool
    {
        return ! $this->isProtected();
    }

    /**
     * @return array{en: bool, ar: bool}
     */
    public function languageAvailability(): array
    {
        return [
            'en' => filled($this->title_en) || filled(strip_tags((string) $this->content_en)),
            'ar' => filled($this->title_ar) || filled(strip_tags((string) $this->content_ar)),
        ];
    }

    public function localizedTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'ar' ? $this->title_ar : $this->title_en;
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

        return filled($seo) ? $seo : '';
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeWithLanguage(Builder $query, string $language): Builder
    {
        return match ($language) {
            'en' => $query->where(function (Builder $inner): void {
                $inner->whereNotNull('title_en')->where('title_en', '!=', '')
                    ->orWhereNotNull('content_en')->where('content_en', '!=', '');
            }),
            'ar' => $query->where(function (Builder $inner): void {
                $inner->whereNotNull('title_ar')->where('title_ar', '!=', '')
                    ->orWhereNotNull('content_ar')->where('content_ar', '!=', '');
            }),
            default => $query,
        };
    }

    /**
     * @return list<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_PUBLISHED,
            self::STATUS_ARCHIVED,
        ];
    }
}
