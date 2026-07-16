<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Support\AdminPermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        AdminPermission::syncCatalog();
    }

    public static function ensureSeeded(): void
    {
        AdminPermission::ensureSeeded();
    }

    public static function assignSuperAdmin(User $user): void
    {
        self::ensureSeeded();

        $roleId = Role::query()->where('slug', 'super-admin')->value('id');

        if ($roleId) {
            $user->forceFill(['role_id' => $roleId])->save();
        }
    }
}
