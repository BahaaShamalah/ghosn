<?php

namespace App\Support;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;

class AdminPermission
{
    public static function routePermission(?string $routeName = null): ?string
    {
        $routeName ??= Route::currentRouteName();

        if (! $routeName) {
            return null;
        }

        foreach (config('admin-permissions.route_permissions', []) as $pattern => $permission) {
            if (str_ends_with($pattern, '.*')) {
                $prefix = rtrim($pattern, '.*');

                if (str_starts_with($routeName, $prefix)) {
                    return $permission;
                }

                continue;
            }

            if ($routeName === $pattern) {
                return $permission;
            }
        }

        return null;
    }

    public static function userCan(User $user, string $permission): bool
    {
        return $user->hasPermission($permission);
    }

    /**
     * @return list<string>
     */
    public static function allSlugs(): array
    {
        return array_keys(config('admin-permissions.permissions', []));
    }

    /**
     * @return array<string, list<array{slug: string, label_en: string, label_ar: string}>>
     */
    public static function groupedDefinitions(): array
    {
        $groups = [];

        foreach (config('admin-permissions.permissions', []) as $slug => $meta) {
            $group = (string) ($meta['group'] ?? 'general');
            $groups[$group][] = [
                'slug' => $slug,
                'label_en' => (string) ($meta['label_en'] ?? $slug),
                'label_ar' => (string) ($meta['label_ar'] ?? $slug),
            ];
        }

        return $groups;
    }

    public static function syncCatalog(): void
    {
        foreach (config('admin-permissions.permissions', []) as $slug => $meta) {
            Permission::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'group' => (string) ($meta['group'] ?? 'general'),
                    'label_en' => (string) ($meta['label_en'] ?? $slug),
                    'label_ar' => (string) ($meta['label_ar'] ?? $slug),
                ],
            );
        }

        $allPermissionIds = Permission::query()->pluck('id', 'slug');

        foreach (config('admin-permissions.roles', []) as $slug => $meta) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'label_en' => (string) ($meta['label_en'] ?? $slug),
                    'label_ar' => (string) ($meta['label_ar'] ?? $slug),
                    'is_super' => (bool) ($meta['is_super'] ?? false),
                    'is_system' => (bool) ($meta['is_system'] ?? false),
                ],
            );

            $permissions = $meta['permissions'] ?? [];

            if ($permissions === '*') {
                $role->permissions()->sync($allPermissionIds->values()->all());

                continue;
            }

            $ids = collect($permissions)
                ->map(fn (string $permissionSlug): ?int => $allPermissionIds[$permissionSlug] ?? null)
                ->filter()
                ->values()
                ->all();

            $role->permissions()->sync($ids);
        }
    }

    public static function ensureSeeded(): void
    {
        if (Role::query()->exists()) {
            return;
        }

        self::syncCatalog();
    }
}
