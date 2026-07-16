<?php

namespace App\Http\Requests\Admin\Pages;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageSectionHeroRequest extends FormRequest
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

            'content.title_line1_en' => ['nullable', 'string', 'max:500'],
            'content.title_line1_ar' => ['nullable', 'string', 'max:500'],
            'content.title_accent_en' => ['nullable', 'string', 'max:500'],
            'content.title_accent_ar' => ['nullable', 'string', 'max:500'],

            'content.description_en' => ['nullable', 'string', 'max:5000'],
            'content.description_ar' => ['nullable', 'string', 'max:5000'],

            'content.primary_button_text_en' => ['nullable', 'string', 'max:255'],
            'content.primary_button_text_ar' => ['nullable', 'string', 'max:255'],
            'content.primary_button_url' => ['nullable', 'string', 'max:500'],

            'content.secondary_button_text_en' => ['nullable', 'string', 'max:255'],
            'content.secondary_button_text_ar' => ['nullable', 'string', 'max:255'],
            'content.secondary_button_url' => ['nullable', 'string', 'max:500'],

            'content.background_media_id' => [
                'nullable',
                'integer',
                Rule::exists('media', 'id')->where(function ($query) {
                    $query->where('mime_type', 'like', 'image/%');
                }),
            ],
            'content.background_alt_en' => ['nullable', 'string', 'max:255'],
            'content.background_alt_ar' => ['nullable', 'string', 'max:255'],

            'background_upload' => [
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
        return route('admin.pages.sections.hero.edit', [
            $this->route('page'),
            $this->route('section'),
        ]);
    }
}
