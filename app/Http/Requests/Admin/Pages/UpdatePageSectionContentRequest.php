<?php

namespace App\Http\Requests\Admin\Pages;

use App\Support\SectionContent;
use App\Support\SectionValidation;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePageSectionContentRequest extends FormRequest
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
        $section = $this->route('section');

        if (! $section || ! SectionContent::isStructured($section->key)) {
            return [];
        }

        return SectionValidation::rules($section->key);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return route('admin.pages.sections.content.edit', [
            $this->route('page'),
            $this->route('section'),
        ]);
    }
}
