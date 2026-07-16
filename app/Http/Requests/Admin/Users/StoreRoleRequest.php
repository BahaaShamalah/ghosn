<?php

namespace App\Http\Requests\Admin\Users;

use App\Support\AdminPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('roles.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:80', 'alpha_dash', 'unique:roles,slug'],
            'label_en' => ['required', 'string', 'max:120'],
            'label_ar' => ['required', 'string', 'max:120'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(AdminPermission::allSlugs())],
        ];
    }
}
