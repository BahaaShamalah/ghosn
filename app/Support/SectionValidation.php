<?php

namespace App\Support;

class SectionValidation
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(string $key): array
    {
        $config = SectionContent::config($key);

        if (! $config) {
            return [];
        }

        $rules = [
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];

        $defaults = $config['defaults'] ?? [];

        foreach ($defaults as $field => $defaultValue) {
            if (is_array($defaultValue)) {
                $rules = array_merge($rules, self::repeaterRules("content.{$field}", $defaultValue));

                continue;
            }

            if (str_ends_with($field, '_media_id')) {
                $rules["content.{$field}"] = ['nullable', 'integer', 'exists:media,id'];

                continue;
            }

            $max = str_contains($field, 'paragraph') || str_contains($field, 'description') || str_contains($field, '_text_')
                ? 5000
                : 500;

            $rules["content.{$field}"] = ['nullable', 'string', "max:{$max}"];
        }

        return $rules;
    }

    /**
     * @param  array<int, array<string, mixed>>  $defaultItems
     * @return array<string, mixed>
     */
    private static function repeaterRules(string $prefix, array $defaultItems): array
    {
        $rules = [
            $prefix => ['nullable', 'array'],
        ];

        $sample = $defaultItems[0] ?? [];

        foreach (array_keys($defaultItems) as $index) {
            foreach (array_keys($sample) as $fieldKey) {
                $max = str_contains($fieldKey, 'text') || $fieldKey === 'link_url' ? 5000 : 500;
                $rules["{$prefix}.{$index}.{$fieldKey}"] = ['nullable', 'string', "max:{$max}"];
            }
        }

        return $rules;
    }
}
