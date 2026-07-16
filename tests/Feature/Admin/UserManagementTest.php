<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_super_admin_can_manage_users_and_roles(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee(__('admin.users.title'), false);

        $this->actingAs($admin)
            ->get(route('admin.roles.index'))
            ->assertOk()
            ->assertSee('Super Administrator', false);
    }

    public function test_viewer_cannot_access_user_management(): void
    {
        $viewerRole = Role::query()->where('slug', 'viewer')->firstOrFail();
        $viewer = User::factory()->create(['role_id' => $viewerRole->id]);

        $this->actingAs($viewer)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_user_with_role(): void
    {
        $admin = User::factory()->create();
        $editorRole = Role::query()->where('slug', 'editor')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Editor User',
                'email' => 'editor@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role_id' => $editorRole->id,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'editor@example.com',
            'role_id' => $editorRole->id,
        ]);
    }

    public function test_viewer_only_sees_allowed_nav_items(): void
    {
        $viewerRole = Role::query()->where('slug', 'viewer')->firstOrFail();
        $viewer = User::factory()->create(['role_id' => $viewerRole->id]);

        $this->actingAs($viewer)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(__('admin.nav.dashboard'), false)
            ->assertDontSee(__('admin.nav.users'), false);

        $this->actingAs($viewer)
            ->get(route('admin.settings.index'))
            ->assertForbidden();
    }
}
