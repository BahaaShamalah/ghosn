<?php

namespace Tests\Unit\Services;

use App\Services\Content\EmbedService;
use Tests\TestCase;

class EmbedServiceTest extends TestCase
{
    public function test_youtube_url_becomes_marker(): void
    {
        $service = new EmbedService;

        $marker = $service->markerFromUrl('https://www.youtube.com/watch?v=dQw4w9WgXcQ');

        $this->assertStringContainsString('data-embed-type="youtube"', (string) $marker);
        $this->assertStringContainsString('dQw4w9WgXcQ', (string) $marker);
    }

    public function test_unapproved_iframe_is_stripped(): void
    {
        $service = new EmbedService;

        $result = $service->sanitizeIframeHtml('<iframe src="https://evil.example/embed"></iframe>');

        $this->assertSame('', $result);
    }
}
