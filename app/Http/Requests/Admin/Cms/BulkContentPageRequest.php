<?php

namespace App\Http\Requests\Admin\Cms;

use App\Models\ContentPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkContentPageRequest extends FormRequest
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
            'action' => ['required', Rule::in(['publish', 'unpublish', 'delete', 'duplicate'])],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:content_pages,id'],
        ];
    }
}
