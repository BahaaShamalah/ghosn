<?php

namespace App\Http\Requests\Admin\Cms;

use App\Http\Requests\Admin\Cms\Concerns\LogsCmsValidationFailures;
use App\Http\Requests\Admin\Cms\Concerns\PreparesCmsPageInput;
use App\Http\Requests\Admin\Cms\Concerns\SanitizesCmsHtml;
use App\Models\ContentPage;
use App\Rules\NotReservedContentPageSlug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContentPageRequest extends FormRequest
{
    use LogsCmsValidationFailures;
    use PreparesCmsPageInput;
    use SanitizesCmsHtml;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->sanitizeCmsHtmlFields();
        $this->prepareCmsPageInput();

        if (! $this->filled('template')) {
            $this->merge(['template' => ContentPage::TEMPLATE_DEFAULT]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('content_pages', 'slug'), new NotReservedContentPageSlug],
            'template' => ['required', Rule::in(ContentPage::templates())],
            'content_en' => ['nullable', 'string', 'max:50000'],
            'content_ar' => ['nullable', 'string', 'max:50000'],
            'featured_image_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'status' => ['required', Rule::in(ContentPage::statuses())],
            'seo_title_en' => ['nullable', 'string', 'max:255'],
            'seo_title_ar' => ['nullable', 'string', 'max:255'],
            'seo_description_en' => ['nullable', 'string', 'max:500'],
            'seo_description_ar' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge(['settings' => null]);
    }
}
