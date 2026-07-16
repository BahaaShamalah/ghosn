<?php

namespace App\Http\Requests\Admin\Cms\Concerns;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;

trait LogsCmsValidationFailures
{
    protected function failedValidation(Validator $validator): void
    {
        if (config('app.debug')) {
            Log::debug(static::class.' validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $this->except(['_token', '_method']),
            ]);
        }

        $count = $validator->errors()->count();

        if ($count > 0) {
            session()->flash(
                'error',
                trans_choice('admin.validation_summary', $count, ['count' => $count]),
            );
        }

        parent::failedValidation($validator);
    }
}
