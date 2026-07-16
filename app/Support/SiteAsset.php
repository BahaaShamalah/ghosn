<?php

namespace App\Support;

use App\Models\Media;
use App\Services\Settings\SettingsService;

class SiteAsset
{
    public static function logoUrl(?SettingsService $settings = null): string
    {
        $settings ??= app(SettingsService::class);

        $mediaId = $settings->get('site.logo_media_id');

        if ($mediaId) {
            $media = Media::query()->find($mediaId);

            if ($media) {
                return $media->url();
            }
        }

        $path = (string) $settings->get('site.logo', 'assets/landing/images/logo.webp');

        return str_starts_with($path, 'http') ? $path : asset($path);
    }

    public static function faviconUrl(?SettingsService $settings = null): ?string
    {
        $settings ??= app(SettingsService::class);

        $mediaId = $settings->get('site.favicon_media_id');

        if ($mediaId) {
            $media = Media::query()->find($mediaId);

            if ($media) {
                return $media->url();
            }
        }

        $path = (string) $settings->get('site.favicon', '');

        if ($path === '') {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : asset($path);
    }
}
