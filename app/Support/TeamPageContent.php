<?php

namespace App\Support;

use App\Models\Media;
use App\Services\Settings\SettingsService;

class TeamPageContent
{
    /**
     * @return array<string, mixed>
     */
    public static function forReact(): array
    {
        $defaults = config('team-page', []);
        $stored = self::stored();

        return self::format(array_replace_recursive($defaults, $stored));
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return config('team-page', []);
    }

    /**
     * @return array<string, mixed>
     */
    private static function stored(): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $raw = $settings->get('team.page');

        return is_array($raw) ? $raw : [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function format(array $data): array
    {
        $pair = static fn (string $enKey, string $arKey, array $source): array => [
            'en' => (string) ($source[$enKey] ?? ''),
            'ar' => (string) ($source[$arKey] ?? ''),
        ];

        $hero = $data['hero'] ?? [];

        $stats = array_map(static function (array $stat): array {
            return [
                'end' => (int) ($stat['end'] ?? 0),
                'suffix' => (string) ($stat['suffix'] ?? ''),
                'label' => [
                    'en' => (string) ($stat['label_en'] ?? ''),
                    'ar' => (string) ($stat['label_ar'] ?? ''),
                ],
            ];
        }, is_array($data['stats'] ?? null) ? $data['stats'] : []);

        $leadership = $data['leadership'] ?? [];
        $leaders = array_map(static function (array $leader): array {
            return [
                'name' => [
                    'en' => (string) ($leader['name_en'] ?? ''),
                    'ar' => (string) ($leader['name_ar'] ?? ''),
                ],
                'role' => [
                    'en' => (string) ($leader['role_en'] ?? ''),
                    'ar' => (string) ($leader['role_ar'] ?? ''),
                ],
                'bio' => [
                    'en' => (string) ($leader['bio_en'] ?? ''),
                    'ar' => (string) ($leader['bio_ar'] ?? ''),
                ],
                'imageUrl' => self::resolveImageUrl(
                    $leader['image_media_id'] ?? null,
                    (string) ($leader['image_url'] ?? ''),
                ),
                'linkedinUrl' => (string) ($leader['linkedin_url'] ?? ''),
                'xUrl' => (string) ($leader['x_url'] ?? ''),
                'email' => (string) ($leader['email'] ?? ''),
            ];
        }, is_array($data['leaders'] ?? null) ? $data['leaders'] : []);

        $departments = $data['departments'] ?? [];
        $deptItems = array_map(static function (array $dept): array {
            return [
                'key' => (string) ($dept['key'] ?? 'field'),
                'count' => (int) ($dept['count'] ?? 0),
                'name' => [
                    'en' => (string) ($dept['name_en'] ?? ''),
                    'ar' => (string) ($dept['name_ar'] ?? ''),
                ],
                'desc' => [
                    'en' => (string) ($dept['desc_en'] ?? ''),
                    'ar' => (string) ($dept['desc_ar'] ?? ''),
                ],
            ];
        }, is_array($departments['items'] ?? null) ? $departments['items'] : []);

        $culture = $data['culture'] ?? [];
        $culturePoints = array_map(static function (array $point): array {
            return [
                'title' => [
                    'en' => (string) ($point['title_en'] ?? ''),
                    'ar' => (string) ($point['title_ar'] ?? ''),
                ],
                'body' => [
                    'en' => (string) ($point['body_en'] ?? ''),
                    'ar' => (string) ($point['body_ar'] ?? ''),
                ],
            ];
        }, is_array($culture['points'] ?? null) ? $culture['points'] : []);

        $cta = $data['cta'] ?? [];

        return [
            'sections' => [
                'hero' => (bool) ($data['sections']['hero'] ?? true),
                'stats' => (bool) ($data['sections']['stats'] ?? true),
                'leadership' => (bool) ($data['sections']['leadership'] ?? true),
                'departments' => (bool) ($data['sections']['departments'] ?? true),
                'culture' => (bool) ($data['sections']['culture'] ?? true),
                'cta' => (bool) ($data['sections']['cta'] ?? true),
            ],
            'hero' => [
                'eyebrow' => $pair('eyebrow_en', 'eyebrow_ar', $hero),
                'title' => $pair('title_en', 'title_ar', $hero),
                'subtitle' => $pair('subtitle_en', 'subtitle_ar', $hero),
            ],
            'stats' => $stats,
            'leadership' => [
                'eyebrow' => $pair('eyebrow_en', 'eyebrow_ar', $leadership),
                'title' => $pair('title_en', 'title_ar', $leadership),
                'intro' => $pair('intro_en', 'intro_ar', $leadership),
            ],
            'leaders' => $leaders,
            'departments' => [
                'eyebrow' => $pair('eyebrow_en', 'eyebrow_ar', $departments),
                'title' => $pair('title_en', 'title_ar', $departments),
                'membersLabel' => $pair('members_label_en', 'members_label_ar', $departments),
                'items' => $deptItems,
            ],
            'culture' => [
                'eyebrow' => $pair('eyebrow_en', 'eyebrow_ar', $culture),
                'title' => $pair('title_en', 'title_ar', $culture),
                'body' => $pair('body_en', 'body_ar', $culture),
                'imageUrl' => self::resolveImageUrl(
                    $culture['image_media_id'] ?? null,
                    (string) ($culture['image_url'] ?? ''),
                ),
                'points' => $culturePoints,
            ],
            'cta' => [
                'title' => $pair('title_en', 'title_ar', $cta),
                'subtitle' => $pair('subtitle_en', 'subtitle_ar', $cta),
                'primary' => $pair('primary_en', 'primary_ar', $cta),
                'primaryUrl' => SiteChrome::resolveHref((string) ($cta['primary_url'] ?? '/volunteer')),
                'secondary' => $pair('secondary_en', 'secondary_ar', $cta),
                'secondaryUrl' => SiteChrome::resolveHref((string) ($cta['secondary_url'] ?? '/contact')),
            ],
        ];
    }

    private static function resolveImageUrl(mixed $mediaId, string $fallbackUrl = ''): string
    {
        if (! empty($mediaId)) {
            $media = Media::query()->find((int) $mediaId);

            if ($media) {
                return $media->url();
            }
        }

        return trim($fallbackUrl);
    }
}
