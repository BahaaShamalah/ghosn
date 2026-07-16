<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_matches_admin_login_design(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee(__('admin.login.heading'), false)
            ->assertSee(__('admin.login.brand_title'), false)
            ->assertSee(__('admin.login.secure'), false)
            ->assertSee('data-admin-login-form', false)
            ->assertSee('data-admin-password-toggle', false);
    }

    public function test_guest_admin_locale_switch_works_on_login_page(): void
    {
        $this->get(route('admin.locale.switch', 'ar'))
            ->assertRedirect(route('admin.login'));

        $this->assertSame('ar', session('admin_locale'));
    }

    public function test_failed_login_shows_localized_error(): void
    {
        $this->from(route('admin.login'))
            ->post(route('admin.login.store'), [
                'email' => 'wrong@example.com',
                'password' => 'invalid-password',
            ])
            ->assertSessionHasErrors(['email' => __('admin.login.bad_credentials')])
            ->assertRedirect(route('admin.login'));
    }

    public function test_successful_login_redirects_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@ghosn.org',
            'password' => bcrypt('secret-password'),
        ]);

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_shows_logged_out_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee(__('admin.logout_page.title'), false)
            ->assertSee(__('admin.logout_page.again'), false);
    }
}
