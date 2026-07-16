<?php

namespace App\Support;

use App\Models\Media;

class LandingBlockHelper
{
    /**
     * @param  iterable<int, array<string, mixed>|object>|null  $blocks
     * @return array{en?: string, ar?: string, media_id?: int|null}|null
     */
    public static function content(?iterable $blocks, string $type): ?array
    {
        if ($blocks === null) {
            return null;
        }

        foreach ($blocks as $block) {
            $blockType = is_array($block) ? ($block['type'] ?? null) : ($block->type ?? null);

            if ($blockType !== $type) {
                continue;
            }

            $content = is_array($block) ? ($block['content'] ?? null) : ($block->content ?? null);

            return is_array($content) ? $content : null;
        }

        return null;
    }

    /**
     * @param  iterable<int, array<string, mixed>|object>|null  $blocks
     */
    public static function mediaUrl(?iterable $blocks, string $type): ?string
    {
        $content = self::content($blocks, $type);

        if (! $content || empty($content['media_id'])) {
            return null;
        }

        $media = Media::query()->find($content['media_id']);

        return $media?->url();
    }

    /**
     * @param  iterable<int, array<string, mixed>|object>  $blocks
     * @return array<int, array<string, mixed>>
     */
    public static function normalize(iterable $blocks): array
    {
        $normalized = [];

        foreach ($blocks as $block) {
            if (is_array($block)) {
                $normalized[] = $block;

                continue;
            }

            $normalized[] = [
                'type' => $block->type,
                'content' => $block->content ?? [],
            ];
        }

        return $normalized;
    }
}
