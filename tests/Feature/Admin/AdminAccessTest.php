<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AssertsReactAdminPayload;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use AssertsReactAdminPayload;
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $response->assertSee('id="ghosn-admin-root"', false);
        $response->assertSee('__GHOSN_ADMIN__', false);

        $payload = $this->adminPayloadFromResponse($response);
        $this->assertSame($user->name, $payload['user']['name'] ?? null);
        $this->assertNotEmpty($payload['nav'] ?? []);
        $this->assertArrayHasKey('kpis', $payload['dashboard'] ?? []);
    }

    public function test_authenticated_admin_can_view_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee(__('admin.settings.title'), false)
            ->assertSee(__('admin.settings.manage'), false);
    }

    public function test_authenticated_admin_can_view_pages_builder(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.pages.index'))
            ->assertOk()
            ->assertSee(__('admin.pages.title'), false);
    }
}
