<?php

namespace App\Support;

class ContactPageInput
{
    /**
     * @param  array<string, mixed>  $page
     * @return array<string, mixed>
     */
    public static function normalize(array $page): array
    {
        if (isset($page['sections']) && is_array($page['sections'])) {
            foreach ($page['sections'] as $key => $value) {
                $page['sections'][$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
        }

        if (isset($page['form']['subjects_en']) && is_string($page['form']['subjects_en'])) {
            $page['form']['subjects_en'] = self::linesToList($page['form']['subjects_en']);
        }

        if (isset($page['form']['subjects_ar']) && is_string($page['form']['subjects_ar'])) {
            $page['form']['subjects_ar'] = self::linesToList($page['form']['subjects_ar']);
        }

        return $page;
    }

    /**
     * @return list<string>
     */
    private static function linesToList(string $value): array
    {
        $parts = preg_split("/\r\n|\n|\r/", $value) ?: [];

        return array_values(array_filter(array_map('trim', $parts)));
    }
}
