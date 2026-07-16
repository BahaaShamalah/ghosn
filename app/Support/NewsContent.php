<?php

namespace App\Support;

use App\Models\Post;
use Illuminate\Support\Collection;

class NewsContent
{
    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    public static function resolve(?array $settings = null): array
    {
        $defaults = config('news.defaults', []);
        $stored = is_array($settings) ? $settings : [];

        if (isset($stored['content']) && is_array($stored['content'])) {
            $stored = array_merge($stored, $stored['content']);
        }

        unset($stored['content']);

        $content = array_merge($defaults, $stored);
        $visible = self::isTruthy($content['is_visible'] ?? true);
        $limit = max(1, min(12, (int) ($content['posts_count'] ?? 3)));

        /** @var Collection<int, Post> $posts */
        $posts = $visible
            ? Post::query()
                ->published()
                ->with(['category', 'featuredImage'])
                ->orderByDesc('published_at')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
            : collect();

        return [
            'visible' => $visible,
            'eyebrow_en' => (string) ($content['eyebrow_en'] ?? 'News & Articles'),
            'eyebrow_ar' => (string) ($content['eyebrow_ar'] ?? 'الأخبار والمقالات'),
            'title_en' => (string) ($content['title_en'] ?? 'Latest News'),
            'title_ar' => (string) ($content['title_ar'] ?? 'آخر الأخبار'),
            'subtitle_en' => (string) ($content['subtitle_en'] ?? ''),
            'subtitle_ar' => (string) ($content['subtitle_ar'] ?? ''),
            'posts_count' => $limit,
            'posts' => $posts,
        ];
    }

    private static function isTruthy(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($filtered !== null) {
            return $filtered;
        }

        return filled($value);
    }
}
