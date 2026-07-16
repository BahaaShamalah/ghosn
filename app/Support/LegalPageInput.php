<?php

namespace App\Support;

class LegalPageInput
{
    /**
     * @param  array<string, mixed>  $pages
     * @return array<string, mixed>
     */
    public static function normalizePages(array $pages): array
    {
        $normalized = [];

        foreach ($pages as $key => $page) {
            if (! is_array($page)) {
                continue;
            }

            $sections = [];

            foreach ($page['sections'] ?? [] as $section) {
                if (! is_array($section)) {
                    continue;
                }

                $sections[] = [
                    'heading_en' => trim((string) ($section['heading_en'] ?? '')),
                    'heading_ar' => trim((string) ($section['heading_ar'] ?? '')),
                    'paragraphs_en' => self::lines($section['paragraphs_en'] ?? []),
                    'paragraphs_ar' => self::lines($section['paragraphs_ar'] ?? []),
                    'bullets_en' => self::lines($section['bullets_en'] ?? []),
                    'bullets_ar' => self::lines($section['bullets_ar'] ?? []),
                ];
            }

            $normalized[$key] = [
                'title_en' => trim((string) ($page['title_en'] ?? '')),
                'title_ar' => trim((string) ($page['title_ar'] ?? '')),
                'subtitle_en' => trim((string) ($page['subtitle_en'] ?? '')),
                'subtitle_ar' => trim((string) ($page['subtitle_ar'] ?? '')),
                'updated_en' => trim((string) ($page['updated_en'] ?? '')),
                'updated_ar' => trim((string) ($page['updated_ar'] ?? '')),
                'intro_en' => trim((string) ($page['intro_en'] ?? '')),
                'intro_ar' => trim((string) ($page['intro_ar'] ?? '')),
                'sections' => $sections,
            ];
        }

        return $normalized;
    }

    /**
     * @param  array<int, string>|string  $value
     * @return list<string>
     */
    private static function lines(array|string $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/\r\n|\r|\n/', $value) ?: [];
        }

        return array_values(array_filter(array_map(
            static fn ($line): string => trim((string) $line),
            $value,
        ), static fn (string $line): bool => $line !== ''));
    }
}
