<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_authenticated_admin_can_view_password_form(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $this->actingAs($user)
            ->get(route('admin.password.edit'))
            ->assertOk()
            ->assertSee(__('admin.profile.password.title'), false);
    }

    public function test_admin_can_change_password_with_valid_current_password(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $this->actingAs($user)
            ->put(route('admin.password.update'), [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', __('admin.profile.password.updated'));

        $user->refresh();

        $this->assertTrue(Hash::check('new-password', $user->password));
    }

    public function test_password_change_rejects_invalid_current_password(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $this->actingAs($user)
            ->from(route('admin.password.edit'))
            ->put(route('admin.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirect(route('admin.password.edit'))
            ->assertSessionHasErrors('current_password');

        $user->refresh();

        $this->assertTrue(Hash::check('old-password', $user->password));
    }
}
