<?php

namespace Tests\Unit;

use App\Support\VideoEmbed;
use PHPUnit\Framework\TestCase;

class VideoEmbedTest extends TestCase
{
    public function test_parses_youtube_watch_url(): void
    {
        $result = VideoEmbed::parse('https://www.youtube.com/watch?v=dQw4w9WgXcQ');

        $this->assertSame('youtube', $result['provider']);
        $this->assertStringContainsString('youtube-nocookie.com/embed/dQw4w9WgXcQ', $result['embed_url']);
    }

    public function test_parses_youtube_short_url(): void
    {
        $result = VideoEmbed::parse('https://youtu.be/dQw4w9WgXcQ');

        $this->assertSame('youtube', $result['provider']);
        $this->assertSame('dQw4w9WgXcQ', VideoEmbed::youtubeId('https://youtu.be/dQw4w9WgXcQ'));
    }

    public function test_parses_direct_file_url(): void
    {
        $result = VideoEmbed::parse('https://cdn.example.com/intro.mp4');

        $this->assertSame('file', $result['provider']);
        $this->assertNull($result['embed_url']);
        $this->assertSame('https://cdn.example.com/intro.mp4', $result['source_url']);
    }
}
