<?php

namespace App\Support;

use App\Services\Content\EmbedService;

class ContentHtml
{
    private const ALLOWED_TAGS = '<p><br><strong><em><b><i><ul><ol><li><a><h1><h2><h3><h4><blockquote><img><div>';

    public static function sanitizeStorage(?string $content): ?string
    {
        if ($content === null || trim($content) === '') {
            return null;
        }

        /** @var EmbedService $embeds */
        $embeds = app(EmbedService::class);

        $markers = $embeds->extractValidEmbedMarkers($content);
        $placeholder = '___GHOSN_EMBED___';
        $storedMarkers = [];

        foreach ($markers as $index => $marker) {
            $key = $placeholder.$index.$placeholder;
            $storedMarkers[$key] = $marker;
            $content = str_replace($marker, $key, $content);
        }

        $content = $embeds->processForStorage($content);

        $cleaned = strip_tags($content, self::ALLOWED_TAGS);
        $cleaned = preg_replace('/\son\w+\s*=\s*("|\').*?\1/iu', '', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/\s(href|src)\s*=\s*("\s*javascript:[^"]*"|\'\s*javascript:[^\']*\')/iu', '', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/<(div)\b[^>]*>/iu', '<$1>', $cleaned) ?? $cleaned;

        foreach ($storedMarkers as $key => $marker) {
            $cleaned = str_replace($key, $marker, $cleaned);
        }

        $cleaned = HtmlText::clean($cleaned);
        $cleaned = self::sanitizeImages($cleaned);
        $cleaned = self::sanitizeLinks($cleaned);
        $cleaned = $embeds->sanitizeIframeHtml($cleaned);

        return trim($cleaned) === '' ? null : $cleaned;
    }

    public static function render(?string $content): string
    {
        if ($content === null || $content === '') {
            return '';
        }

        if (preg_match('/<[^>]+>/', $content)) {
            $html = self::sanitizeStorage($content) ?? '';

            return app(EmbedService::class)->expandEmbeds($html);
        }

        $paragraphs = preg_split("/\r\n|\r|\n/", e($content)) ?: [];

        return collect($paragraphs)
            ->filter(fn (string $line) => trim($line) !== '')
            ->map(fn (string $line) => '<p>'.trim($line).'</p>')
            ->implode('');
    }

    public static function plainToHtml(?string $content): string
    {
        return self::render($content);
    }

    private static function sanitizeImages(string $html): string
    {
        return preg_replace_callback('/<img\b[^>]*>/iu', function (array $matches): string {
            if (! preg_match('/\bsrc=(["\'])([^"\']+)\1/i', $matches[0], $srcMatch)) {
                return '';
            }

            $src = $srcMatch[2];

            if (! self::isAllowedAssetUrl($src)) {
                return '';
            }

            $alt = '';
            if (preg_match('/\balt=(["\'])([^"\']*)\1/i', $matches[0], $altMatch)) {
                $alt = ' alt="'.e($altMatch[2]).'"';
            }

            return '<img src="'.e($src).'"'.$alt.' loading="lazy">';
        }, $html) ?? $html;
    }

    private static function sanitizeLinks(string $html): string
    {
        return preg_replace_callback('/<a\b[^>]*>/iu', function (array $matches): string {
            if (! preg_match('/\bhref=(["\'])([^"\']+)\1/i', $matches[0], $hrefMatch)) {
                return '<a>';
            }

            $href = $hrefMatch[2];

            if (preg_match('/^\s*javascript:/iu', $href)) {
                return '';
            }

            return '<a href="'.e($href).'" rel="noopener noreferrer" target="_blank">';
        }, $html) ?? $html;
    }

    private static function isAllowedAssetUrl(string $url): bool
    {
        if (str_starts_with($url, '/storage/')) {
            return true;
        }

        $appUrl = rtrim((string) config('app.url'), '/');

        return $appUrl !== '' && str_starts_with($url, $appUrl.'/storage/');
    }
}
