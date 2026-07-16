<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\StoreRoleRequest;
use App\Http\Requests\Admin\Users\UpdateRoleRequest;
use App\Models\Role;
use App\Support\AdminPermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->withCount('users')
            ->with('permissions')
            ->orderBy('label_en')
            ->get();

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.create', $this->formData());
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role = Role::query()->create([
            ...$data,
            'is_super' => false,
            'is_system' => false,
        ]);

        $role->permissions()->sync(
            $this->permissionIds($permissions),
        );

        return redirect()
            ->route('admin.roles.index')
            ->with('status', __('admin.roles.created'));
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', array_merge($this->formData(), [
            'role' => $role->load('permissions'),
        ]));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $data = $request->validated();
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        if (! $role->is_super) {
            $role->update($data);
            $role->permissions()->sync($this->permissionIds($permissions));
        } else {
            $role->update(collect($data)->only(['label_en', 'label_ar'])->all());
        }

        return back()->with('status', __('admin.roles.updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->withErrors(['role' => __('admin.roles.cannot_delete_system')]);
        }

        if ($role->users()->exists()) {
            return back()->withErrors(['role' => __('admin.roles.cannot_delete_assigned')]);
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('status', __('admin.roles.deleted'));
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'permissionGroups' => AdminPermission::groupedDefinitions(),
        ];
    }

    /**
     * @param  list<string>  $slugs
     * @return list<int>
     */
    private function permissionIds(array $slugs): array
    {
        return \App\Models\Permission::query()
            ->whereIn('slug', $slugs)
            ->pluck('id')
            ->all();
    }
}
