<?php

namespace App\Support;

class YouTubeUrl
{
    public static function extractId(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/|youtube\.com/shorts/)([A-Za-z0-9_-]{11})~', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function watchUrl(?string $url): ?string
    {
        $id = self::extractId($url);

        return $id ? 'https://www.youtube.com/watch?v='.$id : null;
    }

    public static function thumbnailUrl(?string $url): ?string
    {
        $id = self::extractId($url);

        return $id ? 'https://img.youtube.com/vi/'.$id.'/hqdefault.jpg' : null;
    }

    /**
     * @return list<array{watch_url: string, thumbnail_url: string}>
     */
    public static function normalizeMany(array $urls): array
    {
        $videos = [];

        foreach ($urls as $url) {
            $watchUrl = self::watchUrl($url);
            $thumbnail = self::thumbnailUrl($url);

            if ($watchUrl && $thumbnail) {
                $videos[] = [
                    'watch_url' => $watchUrl,
                    'thumbnail_url' => $thumbnail,
                ];
            }
        }

        return $videos;
    }
}
