<?php

namespace App\Support;

class VideoEmbed
{
    /**
     * @return array{provider: ?string, embed_url: ?string, source_url: ?string}
     */
    public static function parse(?string $url): array
    {
        if ($url === null || trim($url) === '') {
            return [
                'provider' => null,
                'embed_url' => null,
                'source_url' => null,
            ];
        }

        $url = trim($url);

        if ($id = self::youtubeId($url)) {
            return [
                'provider' => 'youtube',
                'embed_url' => 'https://www.youtube-nocookie.com/embed/'.$id.'?autoplay=1&rel=0&modestbranding=1',
                'source_url' => $url,
            ];
        }

        if ($id = self::vimeoId($url)) {
            return [
                'provider' => 'vimeo',
                'embed_url' => 'https://player.vimeo.com/video/'.$id.'?autoplay=1',
                'source_url' => $url,
            ];
        }

        return [
            'provider' => 'file',
            'embed_url' => null,
            'source_url' => $url,
        ];
    }

    public static function youtubeId(string $url): ?string
    {
        $patterns = [
            '/youtu\.be\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/embed\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/watch\?v=([A-Za-z0-9_-]{11})/',
            '/youtube\.com\/watch\?.*[&?]v=([A-Za-z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public static function vimeoId(string $url): ?string
    {
        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
