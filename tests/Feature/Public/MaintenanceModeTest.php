<?php

namespace Tests\Feature\Public;

use App\Models\User;
use App\Services\Settings\SettingsService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([AdminUserSeeder::class, SettingsSeeder::class]);
    }

    public function test_public_site_is_accessible_when_maintenance_is_off(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_public_site_returns_maintenance_page_when_enabled(): void
    {
        app(SettingsService::class)->set('maintenance.enabled', true);
        app(SettingsService::class)->clearCache();

        $this->get(route('home'))
            ->assertStatus(503)
            ->assertSee('be back soon', false);
    }

    public function test_admin_routes_remain_accessible_during_maintenance(): void
    {
        app(SettingsService::class)->set('maintenance.enabled', true);
        app(SettingsService::class)->clearCache();

        $admin = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_can_update_maintenance_settings(): void
    {
        $admin = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.settings.update.group', 'maintenance'), [
                '_group' => 'maintenance',
                'maintenance' => [
                    'enabled' => '1',
                    'title_en' => 'Under maintenance',
                    'title_ar' => 'قيد الصيانة',
                    'message_en' => 'Back soon.',
                    'message_ar' => 'نعود قريباً.',
                    'eyebrow_en' => 'Maintenance',
                    'eyebrow_ar' => 'صيانة',
                ],
            ])
            ->assertRedirect(route('admin.settings.show', 'maintenance'));

        $this->assertTrue((bool) app(SettingsService::class)->get('maintenance.enabled'));
        $this->assertSame('Under maintenance', app(SettingsService::class)->get('maintenance.title_en'));
    }
}
