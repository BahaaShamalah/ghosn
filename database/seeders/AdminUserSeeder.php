<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $this->call(RolePermissionSeeder::class);

        $user = User::query()->updateOrCreate(
            ['email' => 'admin@ghosn.test'],
            [
                'name' => 'Admin',
                'password' => 'password',
            ],
        );

        $superAdminId = Role::query()->where('slug', 'super-admin')->value('id');

        if ($superAdminId) {
            $user->forceFill(['role_id' => $superAdminId])->save();
        }
    }
}
