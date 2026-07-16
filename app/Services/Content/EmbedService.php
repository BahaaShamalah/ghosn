<?php

namespace App\Services\Content;

class EmbedService
{
    /** @var list<string> */
    private const IFRAME_HOSTS = [
        'www.youtube.com',
        'youtube.com',
        'www.youtube-nocookie.com',
        'youtube-nocookie.com',
        'player.vimeo.com',
        'vimeo.com',
    ];

    public function youtubeIdFromUrl(string $url): ?string
    {
        $url = trim($url);

        if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{6,})~i', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function vimeoIdFromUrl(string $url): ?string
    {
        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~i', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function embedMarker(string $type, string $value): string
    {
        $type = e($type);
        $value = e($value);

        if ($type === 'youtube' || $type === 'vimeo') {
            return '<div class="ghosn-embed" data-embed-type="'.$type.'" data-embed-id="'.$value.'"></div>';
        }

        return '<div class="ghosn-embed" data-embed-type="social" data-embed-url="'.$value.'"></div>';
    }

    public function markerFromUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        if ($id = $this->youtubeIdFromUrl($url)) {
            return $this->embedMarker('youtube', $id);
        }

        if ($id = $this->vimeoIdFromUrl($url)) {
            return $this->embedMarker('vimeo', $id);
        }

        if ($this->isAllowedSocialUrl($url)) {
            return $this->embedMarker('social', $url);
        }

        return null;
    }

    public function processForStorage(string $html): string
    {
        $html = $this->replaceRawIframes($html);
        $html = $this->replaceYoutubeLinksInText($html);

        return $html;
    }

    public function expandEmbeds(string $html): string
    {
        $html = preg_replace_callback(
            '/<div class="ghosn-embed" data-embed-type="youtube" data-embed-id="([A-Za-z0-9_-]+)"><\/div>/',
            fn (array $m) => $this->renderYoutube($m[1]),
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/<div class="ghosn-embed" data-embed-type="vimeo" data-embed-id="(\d+)"><\/div>/',
            fn (array $m) => $this->renderVimeo($m[1]),
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/<div class="ghosn-embed" data-embed-type="social" data-embed-url="([^"]+)"><\/div>/',
            fn (array $m) => $this->renderSocialCard(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8')),
            $html
        ) ?? $html;

        return $html;
    }

    public function sanitizeIframeHtml(string $html): string
    {
        return preg_replace_callback('/<iframe\b[^>]*>.*?<\/iframe>/is', function (array $matches): string {
            if (! preg_match('/\bsrc=(["\'])([^"\']+)\1/i', $matches[0], $srcMatch)) {
                return '';
            }

            return $this->iframeFromSrc($srcMatch[2]) ?? '';
        }, $html) ?? $html;
    }

    /**
     * @return list<string>
     */
    public function extractValidEmbedMarkers(string $html): array
    {
        preg_match_all('/<div class="ghosn-embed" data-embed-type="(?:youtube|vimeo|social)" (?:data-embed-id="[A-Za-z0-9_-]+"|data-embed-url="[^"]+")><\/div>/', $html, $matches);

        return $matches[0] ?? [];
    }

    private function replaceRawIframes(string $html): string
    {
        return preg_replace_callback('/<iframe\b[^>]*>.*?<\/iframe>/is', function (array $matches): string {
            if (! preg_match('/\bsrc=(["\'])([^"\']+)\1/i', $matches[0], $srcMatch)) {
                return '';
            }

            $safe = $this->iframeFromSrc($srcMatch[2]);

            return $safe ?? '';
        }, $html) ?? $html;
    }

    private function replaceYoutubeLinksInText(string $html): string
    {
        return preg_replace_callback(
            '~https?://(?:www\.)?(?:youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_-]{6,})~i',
            fn (array $m) => $this->embedMarker('youtube', $m[1]),
            $html
        ) ?? $html;
    }

    private function iframeFromSrc(string $src): ?string
    {
        $host = parse_url($src, PHP_URL_HOST);

        if (! is_string($host) || ! in_array(strtolower($host), self::IFRAME_HOSTS, true)) {
            return null;
        }

        if ($id = $this->youtubeIdFromUrl($src)) {
            return $this->renderYoutube($id);
        }

        if ($id = $this->vimeoIdFromUrl($src)) {
            return $this->renderVimeo($id);
        }

        if (preg_match('~player\.vimeo\.com/video/(\d+)~i', $src, $matches)) {
            return $this->renderVimeo($matches[1]);
        }

        return null;
    }

    private function renderYoutube(string $id): string
    {
        $id = preg_replace('/[^A-Za-z0-9_-]/', '', $id) ?? '';

        if ($id === '') {
            return '';
        }

        $src = 'https://www.youtube-nocookie.com/embed/'.e($id);

        return $this->responsiveIframe($src, 'YouTube video');
    }

    private function renderVimeo(string $id): string
    {
        $id = preg_replace('/\D/', '', $id) ?? '';

        if ($id === '') {
            return '';
        }

        return $this->responsiveIframe('https://player.vimeo.com/video/'.e($id), 'Vimeo video');
    }

    private function renderSocialCard(string $url): string
    {
        if (! $this->isAllowedSocialUrl($url)) {
            return '';
        }

        $label = e($url);
        $safeUrl = e($url);

        return '<div class="ghosn-social-embed"><a href="'.$safeUrl.'" target="_blank" rel="noopener noreferrer" class="ghosn-social-embed__link">'.$label.'</a><p class="ghosn-social-embed__hint">'.e(__('public.content.social_embed_hint')).'</p></div>';
    }

    private function responsiveIframe(string $src, string $title): string
    {
        return '<div class="ghosn-video-embed"><iframe src="'.e($src).'" title="'.e($title).'" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>';
    }

    private function isAllowedSocialUrl(string $url): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return str_contains($host, 'instagram.com')
            || str_contains($host, 'facebook.com')
            || str_contains($host, 'fb.watch')
            || str_contains($host, 'twitter.com')
            || str_contains($host, 'x.com');
    }
}
