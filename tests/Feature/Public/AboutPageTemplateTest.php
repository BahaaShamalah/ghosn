<?php

namespace Tests\Feature\Public;

use App\Models\User;
use App\Services\Settings\SettingsService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\CmsContentSeeder;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([AdminUserSeeder::class, SettingsSeeder::class, LandingPageSeeder::class, CmsContentSeeder::class]);
    }

    public function test_about_renders_dc_react_page(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertSee('id="ghosn-landing-root"', false)
            ->assertSee('"pageType":"about"', false)
            ->assertSee('Giving that grows. Impact that lasts.', false)
            ->assertSee('Relief rooted in dignity and hope', false)
            ->assertSee('What We Stand For', false)
            ->assertSee('Trusted by partners who share our mission', false);
    }

    public function test_admin_about_settings_update_appears_on_public_page(): void
    {
        $admin = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();
        $defaults = config('about-page');

        $this->actingAs($admin)
            ->from(route('admin.pages.show', \App\Models\Page::query()->where('slug', 'who-we-are')->firstOrFail()))
            ->put(route('admin.settings.update.group', 'about'), [
                '_group' => 'about',
                'about' => [
                    'page' => array_replace_recursive($defaults, [
                        'hero' => [
                            'title_en' => 'Custom About Hero From Settings',
                        ],
                        'intro' => [
                            'title_en' => 'Custom Intro Title',
                            'paragraphs_en' => "First custom paragraph.\n\nSecond custom paragraph.",
                        ],
                    ]),
                ],
            ])
            ->assertRedirect(route('admin.pages.show', \App\Models\Page::query()->where('slug', 'who-we-are')->firstOrFail()));

        app(SettingsService::class)->clearCache();

        $this->get('/about')
            ->assertOk()
            ->assertSee('Custom About Hero From Settings', false)
            ->assertSee('Custom Intro Title', false)
            ->assertSee('First custom paragraph.', false);
    }

    public function test_default_official_page_still_renders_standard_layout(): void
    {
        $this->get('/donation-policy')
            ->assertOk()
            ->assertSee('id="ghosn-landing-root"', false)
            ->assertSee('Donation Policy', false);
    }
}
