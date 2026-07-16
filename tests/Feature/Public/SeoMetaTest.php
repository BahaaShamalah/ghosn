<?php

namespace Tests\Feature\Public;

use App\Models\User;
use App\Services\Settings\SettingsService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([AdminUserSeeder::class, SettingsSeeder::class]);
    }

    public function test_homepage_includes_open_graph_meta_tags(): void
    {
        app(SettingsService::class)->setMany([
            'seo.title_en' => 'GHOSN Relief',
            'seo.description_en' => 'Humanitarian relief in Gaza.',
        ]);
        app(SettingsService::class)->clearCache();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('property="og:title" content="GHOSN Relief"', false)
            ->assertSee('property="og:description" content="Humanitarian relief in Gaza."', false)
            ->assertSee('property="og:image"', false)
            ->assertSee('name="twitter:card" content="summary_large_image"', false);
    }

    public function test_admin_can_update_seo_settings(): void
    {
        $admin = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $this->actingAs($admin)
            ->from(route('admin.settings.show', 'seo'))
            ->put(route('admin.settings.update.group', 'seo'), [
                '_group' => 'seo',
                'seo' => [
                    'title_en' => 'Shared title EN',
                    'title_ar' => 'عنوان مشاركة',
                    'description_en' => 'Shared description for WhatsApp.',
                    'description_ar' => 'نبذة للمشاركة على واتساب.',
                    'image_media_id' => '',
                ],
            ])
            ->assertRedirect(route('admin.settings.show', 'seo'));

        app(SettingsService::class)->clearCache();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('property="og:title" content="Shared title EN"', false)
            ->assertSee('property="og:description" content="Shared description for WhatsApp."', false);
    }
}
