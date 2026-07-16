<?php

namespace App\Rules;

use App\Support\ReservedSlug;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotReservedContentPageSlug implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (ReservedSlug::isReserved(is_string($value) ? $value : null)) {
            $fail(__('admin.cms.slug_reserved'));
        }
    }
}
