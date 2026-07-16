<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    public const PLATFORM_FACEBOOK = 'facebook';

    public const PLATFORM_INSTAGRAM = 'instagram';

    public const PLATFORM_X = 'x';

    public const PLATFORM_YOUTUBE = 'youtube';

    public const PLATFORM_WHATSAPP = 'whatsapp';

    public const PLATFORM_TIKTOK = 'tiktok';

    public const PLATFORM_LINKEDIN = 'linkedin';

    public const PLATFORM_TELEGRAM = 'telegram';

    public const PLATFORM_WEBSITE = 'website';

    protected $fillable = [
        'platform',
        'label_en',
        'label_ar',
        'url',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function localizedLabel(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $label = $locale === 'ar' ? $this->label_ar : $this->label_en;

        if (filled($label)) {
            return $label;
        }

        return \App\Support\SocialPlatform::label($this->platform, $locale);
    }

    public function resolvedIcon(): string
    {
        return filled($this->icon) ? $this->icon : $this->platform;
    }

    /**
     * Font Awesome class — uses platform map; custom `icon` may be a full FA class string.
     */
    public function fontAwesomeClass(): string
    {
        if (filled($this->icon) && str_contains((string) $this->icon, 'fa-')) {
            return (string) $this->icon;
        }

        return \App\Support\SocialPlatform::iconClass($this->resolvedIcon());
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereNotNull('url')
            ->where('url', '!=', '');
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return list<string>
     */
    public static function platforms(): array
    {
        return [
            self::PLATFORM_FACEBOOK,
            self::PLATFORM_INSTAGRAM,
            self::PLATFORM_X,
            self::PLATFORM_YOUTUBE,
            self::PLATFORM_WHATSAPP,
            self::PLATFORM_TIKTOK,
            self::PLATFORM_LINKEDIN,
            self::PLATFORM_TELEGRAM,
            self::PLATFORM_WEBSITE,
        ];
    }
}
