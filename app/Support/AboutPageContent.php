<?php

namespace App\Support;

use App\Models\Media;
use App\Services\Settings\SettingsService;

class AboutPageContent
{
    /**
     * @return array<string, mixed>
     */
    public static function forReact(): array
    {
        $defaults = self::defaults();
        $stored = self::stored();

        return self::format(array_replace_recursive($defaults, $stored));
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return config('about-page', []);
    }

    /**
     * @return array<string, mixed>
     */
    private static function stored(): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $raw = $settings->get('about.page');

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
        $intro = $data['intro'] ?? [];
        $values = $data['values'] ?? [];
        $story = $data['story'] ?? [];
        $team = $data['team'] ?? [];
        $partners = $data['partners'] ?? [];
        $cta = $data['cta'] ?? [];

        $paragraphsEn = self::stringList($intro['paragraphs_en'] ?? []);
        $paragraphsAr = self::stringList($intro['paragraphs_ar'] ?? []);

        return [
            'sections' => [
                'hero' => (bool) ($data['sections']['hero'] ?? true),
                'intro' => (bool) ($data['sections']['intro'] ?? true),
                'stats' => (bool) ($data['sections']['stats'] ?? true),
                'pillars' => (bool) ($data['sections']['pillars'] ?? true),
                'values' => (bool) ($data['sections']['values'] ?? true),
                'story' => (bool) ($data['sections']['story'] ?? true),
                'team' => (bool) ($data['sections']['team'] ?? true),
                'partners' => (bool) ($data['sections']['partners'] ?? true),
                'cta' => (bool) ($data['sections']['cta'] ?? true),
            ],
            'hero' => [
                'eyebrow' => $pair('eyebrow_en', 'eyebrow_ar', $hero),
                'title' => $pair('title_en', 'title_ar', $hero),
                'subtitle' => $pair('subtitle_en', 'subtitle_ar', $hero),
                'imageUrl' => self::resolveImageUrl(
                    $hero['image_media_id'] ?? null,
                    (string) ($hero['image_url'] ?? ''),
                ),
            ],
            'intro' => [
                'eyebrow' => $pair('eyebrow_en', 'eyebrow_ar', $intro),
                'title' => $pair('title_en', 'title_ar', $intro),
                'paragraphs' => [
                    'en' => $paragraphsEn,
                    'ar' => $paragraphsAr,
                ],
                'imageUrl' => self::resolveImageUrl(
                    $intro['image_media_id'] ?? null,
                    (string) ($intro['image_url'] ?? ''),
                ),
                'video' => self::introVideo($intro),
            ],
            'stats' => array_values(array_map(static function (array $stat): array {
                return [
                    'end' => (int) ($stat['end'] ?? 0),
                    'suffix' => (string) ($stat['suffix'] ?? ''),
                    'label' => [
                        'en' => (string) ($stat['label_en'] ?? ''),
                        'ar' => (string) ($stat['label_ar'] ?? ''),
                    ],
                ];
            }, is_array($data['stats'] ?? null) ? $data['stats'] : [])),
            'pillars' => array_values(array_map(static function (array $pillar): array {
                return [
                    'key' => (string) ($pillar['key'] ?? 'mission'),
                    'title' => [
                        'en' => (string) ($pillar['title_en'] ?? ''),
                        'ar' => (string) ($pillar['title_ar'] ?? ''),
                    ],
                    'body' => [
                        'en' => (string) ($pillar['body_en'] ?? ''),
                        'ar' => (string) ($pillar['body_ar'] ?? ''),
                    ],
                ];
            }, is_array($data['pillars'] ?? null) ? $data['pillars'] : [])),
            'values' => [
                'title' => $pair('title_en', 'title_ar', $values),
                'intro' => $pair('intro_en', 'intro_ar', $values),
                'items' => array_values(array_map(static function (array $item): array {
                    return [
                        'key' => (string) ($item['key'] ?? 'heart'),
                        'title' => [
                            'en' => (string) ($item['title_en'] ?? ''),
                            'ar' => (string) ($item['title_ar'] ?? ''),
                        ],
                        'body' => [
                            'en' => (string) ($item['body_en'] ?? ''),
                            'ar' => (string) ($item['body_ar'] ?? ''),
                        ],
                    ];
                }, is_array($values['items'] ?? null) ? $values['items'] : [])),
            ],
            'story' => [
                'eyebrow' => $pair('eyebrow_en', 'eyebrow_ar', $story),
                'title' => $pair('title_en', 'title_ar', $story),
                'milestones' => array_values(array_map(static function (array $item): array {
                    return [
                        'year' => [
                            'en' => (string) ($item['year_en'] ?? $item['year'] ?? ''),
                            'ar' => (string) ($item['year_ar'] ?? $item['year'] ?? ''),
                        ],
                        'title' => [
                            'en' => (string) ($item['title_en'] ?? ''),
                            'ar' => (string) ($item['title_ar'] ?? ''),
                        ],
                        'body' => [
                            'en' => (string) ($item['body_en'] ?? ''),
                            'ar' => (string) ($item['body_ar'] ?? ''),
                        ],
                    ];
                }, is_array($story['milestones'] ?? null) ? $story['milestones'] : [])),
            ],
            'team' => [
                'eyebrow' => $pair('eyebrow_en', 'eyebrow_ar', $team),
                'title' => $pair('title_en', 'title_ar', $team),
                'intro' => $pair('intro_en', 'intro_ar', $team),
                'members' => array_values(array_map(static function (array $member): array {
                    return [
                        'name' => [
                            'en' => (string) ($member['name_en'] ?? ''),
                            'ar' => (string) ($member['name_ar'] ?? ''),
                        ],
                        'role' => [
                            'en' => (string) ($member['role_en'] ?? ''),
                            'ar' => (string) ($member['role_ar'] ?? ''),
                        ],
                        'imageUrl' => self::resolveImageUrl(
                            $member['image_media_id'] ?? null,
                            (string) ($member['image_url'] ?? ''),
                        ),
                    ];
                }, is_array($team['members'] ?? null) ? $team['members'] : [])),
            ],
            'partners' => [
                'title' => $pair('title_en', 'title_ar', $partners),
                'items' => [
                    'en' => self::stringList($partners['items_en'] ?? []),
                    'ar' => self::stringList($partners['items_ar'] ?? []),
                ],
            ],
            'cta' => [
                'title' => $pair('title_en', 'title_ar', $cta),
                'subtitle' => $pair('subtitle_en', 'subtitle_ar', $cta),
                'primary' => $pair('primary_en', 'primary_ar', $cta),
                'secondary' => $pair('secondary_en', 'secondary_ar', $cta),
                'primaryUrl' => (string) ($cta['primary_url'] ?? '/campaigns'),
                'secondaryUrl' => (string) ($cta['secondary_url'] ?? '/our-team'),
            ],
        ];
    }

    /**
     * @param  mixed  $value
     * @return list<string>
     */
    private static function stringList(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split("/\r\n|\n|\r/", $value) ?: [];
        }

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($item): string => trim((string) $item),
            $value,
        )));
    }

    /**
     * @param  array<string, mixed>  $intro
     * @return array{provider: ?string, embedUrl: ?string, sourceUrl: ?string, posterUrl: string}
     */
    private static function introVideo(array $intro): array
    {
        $embed = VideoEmbed::parse((string) ($intro['video_url'] ?? ''));
        $poster = self::resolveImageUrl(
            $intro['video_cover_media_id'] ?? null,
            (string) ($intro['video_cover_url'] ?? ''),
        );

        if ($poster === '') {
            $poster = self::resolveImageUrl(
                $intro['image_media_id'] ?? null,
                (string) ($intro['image_url'] ?? ''),
            );
        }

        if ($poster === '' && ($embed['provider'] ?? null) === 'youtube' && filled($embed['source_url'] ?? null)) {
            $youtubeId = VideoEmbed::youtubeId((string) $embed['source_url']);
            if ($youtubeId) {
                $poster = 'https://i.ytimg.com/vi/'.$youtubeId.'/hqdefault.jpg';
            }
        }

        return [
            'provider' => $embed['provider'],
            'embedUrl' => $embed['embed_url'],
            'sourceUrl' => $embed['source_url'],
            'posterUrl' => $poster,
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
