<?php

namespace App\Http\Requests\Admin\Media;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreMediaRequest extends FormRequest
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
        $maxKb = (int) config('media.max_upload_kb', 10240);
        $mimes = array_keys(config('media.allowed_mimes', []));

        return [
            'file' => [
                'required',
                File::types($mimes)->max($maxKb),
            ],
        ];
    }
}
