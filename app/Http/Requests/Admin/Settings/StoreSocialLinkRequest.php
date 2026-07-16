<?php

namespace App\Http\Requests\Admin\Settings;

use App\Support\SocialPlatform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSocialLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge(['is_active' => $this->boolean('is_active')]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->linkRules();
    }

    /**
     * @return array<string, mixed>
     */
    protected function linkRules(): array
    {
        return [
            'platform' => ['required', Rule::in(SocialPlatform::platforms())],
            'label_en' => ['nullable', 'string', 'max:120'],
            'label_ar' => ['nullable', 'string', 'max:120'],
            'url' => ['required', 'url', 'max:500'],
            'icon' => ['nullable', 'string', 'max:64'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
