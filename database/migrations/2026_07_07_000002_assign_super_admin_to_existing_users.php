<?php

use App\Models\Role;
use App\Models\User;
use App\Support\AdminPermission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        AdminPermission::syncCatalog();

        $superAdminId = Role::query()->where('slug', 'super-admin')->value('id');

        if (! $superAdminId) {
            return;
        }

        User::query()
            ->whereNull('role_id')
            ->update(['role_id' => $superAdminId]);
    }

    public function down(): void
    {
        // No rollback — role assignments are intentional.
    }
};
