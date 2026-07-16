<?php

namespace App\Http\Requests\Admin\Pages;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageSectionBlockRequest extends FormRequest
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
            'content.en' => ['nullable', 'string', 'max:5000'],
            'content.ar' => ['nullable', 'string', 'max:5000'],
            'content.alt_en' => ['nullable', 'string', 'max:255'],
            'content.alt_ar' => ['nullable', 'string', 'max:255'],
            'content.media_id' => ['nullable', 'integer', 'exists:media,id'],
            'is_active' => ['sometimes', 'boolean'],
            'image_upload' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
