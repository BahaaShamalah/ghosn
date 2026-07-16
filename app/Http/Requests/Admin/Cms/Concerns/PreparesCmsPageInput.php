<?php

namespace App\Http\Requests\Admin\Cms\Concerns;

use Illuminate\Support\Str;

trait PreparesCmsPageInput
{
    protected function prepareCmsPageInput(): void
    {
        $this->normalizeNullableString('slug');
        $this->normalizeNullableInteger('featured_image_media_id');
        $this->prepareAboutPageSettingsInput();

        if ($this->filled('slug')) {
            $this->merge([
                'slug' => Str::slug((string) $this->input('slug')),
            ]);
        }
    }

    protected function prepareAboutPageSettingsInput(): void
    {
        if (! $this->has('settings') || ! is_array($this->input('settings'))) {
            return;
        }

        $settings = $this->input('settings');

        foreach (['hero_image_media_id', 'hero_video_media_id', 'video_cover_media_id'] as $field) {
            if (! array_key_exists($field, $settings)) {
                continue;
            }

            $value = $settings[$field];

            if ($value === null || $value === '') {
                $settings[$field] = null;
            }
        }

        if (array_key_exists('gallery_media_ids', $settings)) {
            $gallery = $settings['gallery_media_ids'];

            if (is_string($gallery)) {
                $decoded = json_decode($gallery, true);
                $gallery = is_array($decoded) ? $decoded : [];
            }

            $settings['gallery_media_ids'] = collect(is_array($gallery) ? $gallery : [])
                ->map(fn ($id) => (int) $id)
                ->filter(fn (int $id) => $id > 0)
                ->values()
                ->all();
        }

        $this->merge(['settings' => $settings]);
    }

    protected function normalizeNullableString(string $field): void
    {
        if (! $this->has($field)) {
            return;
        }

        $value = trim((string) $this->input($field));

        $this->merge([
            $field => $value === '' ? null : $value,
        ]);
    }

    protected function normalizeNullableInteger(string $field): void
    {
        if (! $this->has($field)) {
            return;
        }

        $value = $this->input($field);

        if ($value === null || $value === '') {
            $this->merge([$field => null]);
        }
    }
}
