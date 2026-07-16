<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsHubTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_settings_hub_lists_all_cards(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $response = $this->actingAs($user)->get(route('admin.settings.index'));

        $response->assertOk();
        $response->assertSee(__('admin.settings.hub_intro'), false);
        $response->assertSee(__('admin.settings.hub_card_general_title'), false);
        $response->assertSee(__('admin.settings.hub_card_payments_title'), false);
        $response->assertSee(route('admin.settings.show', 'general'), false);
        $response->assertSee(route('admin.settings.show', 'payments'), false);
    }

    public function test_each_settings_group_page_renders(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        foreach (config('settings.hub_order', []) as $group) {
            $response = $this->actingAs($user)->get(route('admin.settings.show', $group));

            // About/contact settings redirect into Pages Builder when those pages exist.
            if (in_array($group, ['about', 'contact'], true) && $response->isRedirect()) {
                $response->assertRedirect();

                continue;
            }

            $response->assertOk();
            $response->assertSee(__('admin.settings.back_to_hub'), false);
            $response->assertSee(__('admin.settings.hub_card_'.$group.'_desc'), false);
        }
    }

    public function test_unknown_settings_group_returns_not_found(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $this->actingAs($user)
            ->get('/admin/settings/not-a-group')
            ->assertNotFound();
    }

    public function test_homepage_group_cannot_be_updated_via_put(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.settings.update.group', 'homepage'), ['_group' => 'homepage'])
            ->assertNotFound();
    }
}
