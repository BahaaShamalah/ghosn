<?php

namespace App\Http\Requests\Admin\Pages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageSectionAboutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],

            'content.eyebrow_en' => ['nullable', 'string', 'max:255'],
            'content.eyebrow_ar' => ['nullable', 'string', 'max:255'],

            'content.heading_en' => ['nullable', 'string', 'max:500'],
            'content.heading_ar' => ['nullable', 'string', 'max:500'],

            'content.paragraphs_en' => ['nullable', 'string', 'max:15000'],
            'content.paragraphs_ar' => ['nullable', 'string', 'max:15000'],

            'content.stats' => ['nullable', 'array'],
            'content.stats.*.value_en' => ['nullable', 'string', 'max:50'],
            'content.stats.*.value_ar' => ['nullable', 'string', 'max:50'],
            'content.stats.*.label_en' => ['nullable', 'string', 'max:255'],
            'content.stats.*.label_ar' => ['nullable', 'string', 'max:255'],

            'content.video_url' => ['nullable', 'url', 'max:500'],

            'content.video_cover_media_id' => [
                'nullable',
                'integer',
                Rule::exists('media', 'id')->where(function ($query) {
                    $query->where('mime_type', 'like', 'image/%');
                }),
            ],

            'content.watch_label_en' => ['nullable', 'string', 'max:255'],
            'content.watch_label_ar' => ['nullable', 'string', 'max:255'],
            'content.read_more_en' => ['nullable', 'string', 'max:255'],
            'content.read_more_ar' => ['nullable', 'string', 'max:255'],

            'content.image_alt_en' => ['nullable', 'string', 'max:255'],
            'content.image_alt_ar' => ['nullable', 'string', 'max:255'],

            'content.image_media_id' => [
                'nullable',
                'integer',
                Rule::exists('media', 'id')->where(function ($query) {
                    $query->where('mime_type', 'like', 'image/%');
                }),
            ],

            'video_cover_upload' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
            'image_upload' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return route('admin.pages.sections.about.edit', [
            $this->route('page'),
            $this->route('section'),
        ]);
    }
}
