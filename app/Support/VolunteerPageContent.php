<?php

namespace App\Support;

use App\Models\Media;

class VolunteerPageContent
{
    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    public static function resolve(?array $settings): array
    {
        $defaults = config('volunteer-page.defaults', []);
        $stored = is_array($settings) ? $settings : [];

        if (isset($stored['content']) && is_array($stored['content'])) {
            $stored = array_merge($stored, $stored['content']);
        }

        $merged = array_merge($defaults, self::filterStored($defaults, $stored));

        return HtmlText::cleanArray(self::finalize($merged, $defaults));
    }

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array<string, mixed>
     */
    public static function forReact(?array $settings): array
    {
        $page = self::resolve($settings);

        return [
            'hero' => [
                'eyebrow' => self::pair($page, 'hero_eyebrow'),
                'title' => self::pair($page, 'hero_title'),
                'subtitle' => self::pair($page, 'hero_subtitle'),
                'cta' => self::pair($page, 'hero_cta'),
                'image' => $page['hero_image_url'] ?? null,
            ],
            'why' => [
                'eyebrow' => self::pair($page, 'why_eyebrow'),
                'title' => self::pair($page, 'why_title'),
                'intro' => self::pair($page, 'why_intro'),
                'benefits' => collect($page['benefits'] ?? [])->map(fn (array $item): array => [
                    'key' => (string) ($item['key'] ?? ''),
                    'title' => self::pair($item, 'title'),
                    'body' => self::pair($item, 'body'),
                ])->values()->all(),
            ],
            'areas' => [
                'eyebrow' => self::pair($page, 'areas_eyebrow'),
                'title' => self::pair($page, 'areas_title'),
                'items' => collect($page['area_items'] ?? [])->map(fn (array $item): array => [
                    'key' => (string) ($item['key'] ?? ''),
                    'title' => self::pair($item, 'title'),
                    'body' => self::pair($item, 'body'),
                ])->values()->all(),
            ],
            'how' => [
                'eyebrow' => self::pair($page, 'how_eyebrow'),
                'title' => self::pair($page, 'how_title'),
                'steps' => collect($page['steps'] ?? [])->map(fn (array $step): array => [
                    'title' => self::pair($step, 'title'),
                    'body' => self::pair($step, 'body'),
                ])->values()->all(),
            ],
            'testimonial' => [
                'quote' => self::pair($page, 'testimonial_quote'),
                'name' => self::pair($page, 'testimonial_name'),
                'role' => self::pair($page, 'testimonial_role'),
                'initial' => self::pair($page, 'testimonial_initial'),
            ],
            'apply' => [
                'eyebrow' => self::pair($page, 'apply_eyebrow'),
                'title' => self::pair($page, 'apply_title'),
                'intro' => self::pair($page, 'apply_intro'),
            ],
            'form' => [
                'name' => self::pair($page, 'name_label'),
                'namePh' => self::pair($page, 'name_placeholder'),
                'age' => self::pair($page, 'age_label'),
                'agePh' => self::pair($page, 'age_placeholder'),
                'phone' => self::pair($page, 'phone_label'),
                'phonePh' => self::pair($page, 'phone_placeholder'),
                'email' => self::pair($page, 'email_label'),
                'emailPh' => self::pair($page, 'email_placeholder'),
                'areaLabel' => self::pair($page, 'area_label'),
                'areaPh' => self::pair($page, 'area_placeholder'),
                'availability' => self::pair($page, 'availability_label'),
                'availWeekdays' => self::pair($page, 'avail_weekdays'),
                'availWeekends' => self::pair($page, 'avail_weekends'),
                'availRemote' => self::pair($page, 'avail_remote'),
                'message' => self::pair($page, 'message_label'),
                'messagePh' => self::pair($page, 'message_placeholder'),
                'agree' => self::pair($page, 'agree_label'),
                'submit' => self::pair($page, 'submit'),
                'sending' => self::pair($page, 'sending'),
                'error' => self::pair($page, 'error'),
                'areas' => collect($page['form_areas'] ?? [])->map(fn (array $area): array => [
                    'value' => (string) ($area['value'] ?? ''),
                    'label' => self::pair($area, 'label'),
                ])->values()->all(),
            ],
            'thanks' => [
                'title' => self::pair($page, 'thanks_title'),
                'body' => self::pair($page, 'thanks_body'),
                'home' => self::pair($page, 'thanks_home'),
                'explore' => self::pair($page, 'thanks_explore'),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    private static function filterStored(array $defaults, array $stored): array
    {
        $filtered = [];

        foreach ($stored as $key => $value) {
            if (! array_key_exists($key, $defaults)) {
                continue;
            }

            if (is_array($defaults[$key])) {
                if (! is_array($value)) {
                    continue;
                }

                $filtered[$key] = self::normalizeRepeater($defaults[$key], $value);

                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $filtered[$key] = $value;
        }

        return $filtered;
    }

    /**
     * @param  array<int, array<string, mixed>>  $defaultItems
     * @param  array<int, mixed>  $storedItems
     * @return array<int, array<string, mixed>>
     */
    private static function normalizeRepeater(array $defaultItems, array $storedItems): array
    {
        $normalized = [];

        foreach ($defaultItems as $index => $defaultItem) {
            if (! is_array($defaultItem)) {
                continue;
            }

            $storedItem = $storedItems[$index] ?? [];
            $item = is_array($storedItem) ? $storedItem : [];
            $mergedItem = [];

            foreach ($defaultItem as $field => $defaultValue) {
                $storedValue = $item[$field] ?? null;
                $mergedItem[$field] = filled($storedValue) ? $storedValue : $defaultValue;
            }

            $normalized[$index] = $mergedItem;
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @return array<string, mixed>
     */
    private static function finalize(array $data, array $defaults): array
    {
        if (filled($data['hero_image_media_id'] ?? null)) {
            $data['hero_image_media_id'] = (int) $data['hero_image_media_id'];
            $data['hero_image_url'] = self::mediaUrl($data['hero_image_media_id']);
        } else {
            $data['hero_image_media_id'] = null;
            $data['hero_image_url'] = null;
        }

        foreach (['benefits', 'area_items', 'steps', 'form_areas'] as $repeaterKey) {
            if (isset($defaults[$repeaterKey]) && is_array($defaults[$repeaterKey])) {
                $data[$repeaterKey] = self::normalizeRepeater(
                    $defaults[$repeaterKey],
                    is_array($data[$repeaterKey] ?? null) ? $data[$repeaterKey] : [],
                );
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{en: string, ar: string}
     */
    private static function pair(array $data, string $key): array
    {
        return [
            'en' => (string) ($data["{$key}_en"] ?? ''),
            'ar' => (string) ($data["{$key}_ar"] ?? ''),
        ];
    }

    private static function mediaUrl(mixed $mediaId): ?string
    {
        if (empty($mediaId)) {
            return null;
        }

        return Media::query()->find((int) $mediaId)?->url();
    }
}
