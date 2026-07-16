<?php

namespace App\Http\Requests\Admin\Cms;

use App\Http\Requests\Admin\Cms\Concerns\SanitizesCmsHtml;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    use SanitizesCmsHtml;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->sanitizeCmsHtmlFields();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('posts', 'slug')],
            'excerpt_en' => ['nullable', 'string', 'max:1000'],
            'excerpt_ar' => ['nullable', 'string', 'max:1000'],
            'content_en' => ['nullable', 'string', 'max:50000'],
            'content_ar' => ['nullable', 'string', 'max:50000'],
            'featured_image_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['required', Rule::in([Post::STATUS_DRAFT, Post::STATUS_PUBLISHED])],
            'published_at' => ['nullable', 'date'],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_title_ar' => ['nullable', 'string', 'max:255'],
            'seo_description_en' => ['nullable', 'string', 'max:500'],
            'seo_description_ar' => ['nullable', 'string', 'max:500'],
        ];
    }
}
