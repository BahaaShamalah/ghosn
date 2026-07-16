<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use App\Services\Settings\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_admin_can_update_general_settings_with_bracket_notation(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $response = $this->actingAs($user)->put(route('admin.settings.update.group', 'general'), [
            '_group' => 'general',
            'site' => [
                'name_en' => 'GHOSN Relief Team',
                'name_ar' => 'فريق غُصن للإغاثة',
                'slogan_en' => 'Updated slogan',
                'slogan_ar' => 'شعار محدّث',
                'default_language' => 'en',
                'enable_animations' => '1',
            ],
        ]);

        $response->assertRedirect(route('admin.settings.show', 'general'));
        $response->assertSessionHasNoErrors();

        $settings = app(SettingsService::class);

        $this->assertSame('Updated slogan', $settings->get('site.slogan_en'));
        $this->assertSame('GHOSN Relief Team', $settings->get('site.name_en'));
    }

    public function test_admin_can_update_contact_settings(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $page = Page::query()->where('slug', 'contact')->firstOrFail();

        $response = $this->actingAs($user)->put(route('admin.settings.update.group', 'contact'), [
            '_group' => 'contact',
            'contact' => [
                'email' => 'updated@ghosn.test',
            ],
        ]);

        $response->assertRedirect(route('admin.pages.show', $page));
        $response->assertSessionHasNoErrors();

        $this->assertSame('updated@ghosn.test', app(SettingsService::class)->get('contact.email'));
    }

    public function test_typography_defaults_validate_without_false_required_errors(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();
        $settings = app(SettingsService::class);

        $response = $this->actingAs($user)->put(route('admin.settings.update.group', 'typography'), [
            '_group' => 'typography',
            'theme' => [
                'font_en' => $settings->get('theme.font_en'),
                'font_ar' => $settings->get('theme.font_ar'),
            ],
        ]);

        $response->assertRedirect(route('admin.settings.show', 'typography'));
        $response->assertSessionHasNoErrors();
        app(SettingsService::class)->clearCache();
        $settings = app(SettingsService::class);
        $this->assertSame('Montserrat', $settings->get('theme.font_en'));
        $this->assertSame('Cairo', $settings->get('theme.font_ar'));
    }
}
