<?php

namespace App\Support;

class HtmlText
{
    public static function clean(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Collapse double-encoded entities (e.g. &amp;#039; stored in DB).
        if (str_contains($decoded, '&')) {
            $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function cleanArray(array $data): array
    {
        $cleaned = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $cleaned[$key] = self::clean($value);

                continue;
            }

            if (is_array($value)) {
                $cleaned[$key] = self::cleanArray($value);

                continue;
            }

            $cleaned[$key] = $value;
        }

        return $cleaned;
    }
}
