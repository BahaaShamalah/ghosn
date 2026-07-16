<?php

namespace App\Models;

use App\Models\Concerns\HasCmsSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Campaign extends Model
{
    use HasCmsSlug;
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_PAUSED = 'paused';

    protected $fillable = [
        'title_en',
        'title_ar',
        'slug',
        'excerpt_en',
        'excerpt_ar',
        'story_en',
        'story_ar',
        'featured_image_media_id',
        'video_media_id',
        'video_url',
        'gallery_media_ids',
        'goal_amount',
        'raised_amount',
        'currency',
        'status',
        'starts_at',
        'ends_at',
        'category_id',
        'is_featured_homepage',
        'sort_order',
        'donors_count',
        'seo_title_en',
        'seo_title_ar',
        'seo_description_en',
        'seo_description_ar',
    ];

    protected function casts(): array
    {
        return [
            'gallery_media_ids' => 'array',
            'goal_amount' => 'decimal:2',
            'raised_amount' => 'decimal:2',
            'is_featured_homepage' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'donors_count' => 'integer',
            'sort_order' => 'integer',
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

    public function video(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'video_media_id');
    }

    public function hasVideo(): bool
    {
        return $this->video_media_id !== null || filled($this->video_url);
    }

    /**
     * Resolve the single active video source (uploaded file takes priority).
     *
     * @return array{type: ?string, provider: ?string, embed_url: ?string, file_url: ?string, mime: ?string}
     */
    public function videoEmbed(): array
    {
        if ($this->video) {
            return [
                'type' => 'file',
                'provider' => 'file',
                'embed_url' => null,
                'file_url' => $this->video->url(),
                'mime' => $this->video->mime_type,
            ];
        }

        if (filled($this->video_url)) {
            $embed = \App\Support\VideoEmbed::parse($this->video_url);

            return [
                'type' => 'embed',
                'provider' => $embed['provider'],
                'embed_url' => $embed['embed_url'],
                'file_url' => $embed['provider'] === 'file' ? $embed['source_url'] : null,
                'mime' => null,
            ];
        }

        return [
            'type' => null,
            'provider' => null,
            'embed_url' => null,
            'file_url' => null,
            'mime' => null,
        ];
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function isPublic(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->starts_at !== null && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at !== null && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function progressPercent(): float
    {
        $goal = (float) $this->goal_amount;

        if ($goal <= 0) {
            return 0.0;
        }

        return min(100.0, round(((float) $this->raised_amount / $goal) * 100, 1));
    }

    public function daysRemaining(): ?int
    {
        if ($this->ends_at === null) {
            return null;
        }

        return max(0, (int) now()->startOfDay()->diffInDays($this->ends_at->copy()->startOfDay(), false));
    }

    public function isUrgent(): bool
    {
        $days = $this->daysRemaining();

        return $this->progressPercent() >= 85 || ($days !== null && $days <= 14);
    }

    /**
     * @return Collection<int, Media>
     */
    public function mediaGallery(): Collection
    {
        $images = collect();

        if ($this->featuredImage) {
            $images->push($this->featuredImage);
        }

        foreach ($this->galleryImages() as $image) {
            if (! $images->contains('id', $image->id)) {
                $images->push($image);
            }
        }

        return $images;
    }

    public function formattedRaised(): string
    {
        return $this->formatMoney((float) $this->raised_amount);
    }

    public function formattedGoal(): string
    {
        return $this->formatMoney((float) $this->goal_amount);
    }

    public function formatMoney(float $amount): string
    {
        $symbol = config("donations.currencies.{$this->currency}.symbol", $this->currency.' ');

        return $symbol.number_format($amount, 2);
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

    public function localizedStory(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $locale === 'ar' ? (string) $this->story_ar : (string) $this->story_en;
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
     * @return Collection<int, Media>
     */
    public function galleryImages(): Collection
    {
        $ids = array_values(array_filter($this->gallery_media_ids ?? []));

        if ($ids === []) {
            return collect();
        }

        $media = Media::query()->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)
            ->map(fn (int|string $id) => $media->get((int) $id))
            ->filter();
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->where(function (Builder $inner): void {
                $inner->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $inner): void {
                $inner->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeFeaturedHomepage(Builder $query): Builder
    {
        return $query
            ->public()
            ->where('is_featured_homepage', true);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('created_at');
    }
}
