<?php

namespace App\Http\Requests\Admin\Users;

use App\Models\Role;
use App\Support\AdminPermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
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
        /** @var Role $role */
        $role = $this->route('role');

        return [
            'slug' => ['required', 'string', 'max:80', 'alpha_dash', Rule::unique('roles', 'slug')->ignore($role->id)],
            'label_en' => ['required', 'string', 'max:120'],
            'label_ar' => ['required', 'string', 'max:120'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(AdminPermission::allSlugs())],
        ];
    }
}
