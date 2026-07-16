<?php

namespace Tests\Unit\Support;

use App\Support\YouTubeUrl;
use PHPUnit\Framework\TestCase;

class YouTubeUrlTest extends TestCase
{
    public function test_extracts_video_id_from_watch_url(): void
    {
        $this->assertSame('dQw4w9WgXcQ', YouTubeUrl::extractId('https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
    }

    public function test_extracts_video_id_from_short_url(): void
    {
        $this->assertSame('dQw4w9WgXcQ', YouTubeUrl::extractId('https://youtu.be/dQw4w9WgXcQ'));
    }

    public function test_builds_thumbnail_and_watch_urls(): void
    {
        $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

        $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', YouTubeUrl::watchUrl($url));
        $this->assertSame('https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg', YouTubeUrl::thumbnailUrl($url));
    }
}
