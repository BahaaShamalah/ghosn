<?php

namespace App\Http\Requests\Admin\Cms\Concerns;

use App\Support\ContentHtml;

trait SanitizesCmsHtml
{
    protected function sanitizeCmsHtmlFields(): void
    {
        $fields = ['content_en', 'content_ar'];

        foreach ($fields as $field) {
            if (! $this->has($field)) {
                continue;
            }

            $this->merge([
                $field => ContentHtml::sanitizeStorage($this->input($field)),
            ]);
        }
    }
}
