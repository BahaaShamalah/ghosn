<?php

namespace Tests\Feature\Public;

use App\Models\User;
use App\Services\Settings\SettingsService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleServicesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([RolePermissionSeeder::class, AdminUserSeeder::class, SettingsSeeder::class]);
    }

    public function test_admin_can_save_google_settings(): void
    {
        $admin = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $this->actingAs($admin)
            ->from(route('admin.settings.show', 'google'))
            ->put(route('admin.settings.update.group', 'google'), [
                '_group' => 'google',
                'google' => [
                    'analytics' => [
                        'enabled' => '1',
                        'measurement_id' => 'G-TEST12345',
                        'anonymize_ip' => '1',
                        'debug' => '0',
                        'enhanced_measurement' => '1',
                        'ecommerce' => '0',
                    ],
                    'gtm' => [
                        'enabled' => '1',
                        'container_id' => 'GTM-TEST123',
                        'inject_head' => '1',
                        'inject_body' => '1',
                    ],
                    'search_console' => [
                        'meta_tag' => 'abc123verification',
                        'verification_file' => '',
                    ],
                    'consent' => [
                        'enabled' => '1',
                        'analytics_storage' => 'denied',
                        'ad_storage' => 'denied',
                        'ad_user_data' => 'denied',
                        'ad_personalization' => 'denied',
                        'wait_for_update' => 500,
                        'regions' => '',
                        'cookie_days' => 365,
                    ],
                    'adsense' => [
                        'enabled' => '0',
                        'publisher_id' => '',
                        'auto_ads' => '1',
                    ],
                    'maps' => [
                        'enabled' => '0',
                        'api_key' => '',
                        'language' => '',
                        'region' => '',
                    ],
                    'recaptcha' => [
                        'enabled' => '0',
                        'site_key' => '',
                        'secret_key' => 'should-not-appear-publicly',
                        'score_threshold' => '0.5',
                        'contact' => '1',
                        'login' => '0',
                        'register' => '0',
                    ],
                    'merchant' => [
                        'meta_tag' => '',
                    ],
                    'fonts' => [
                        'enable_cdn' => '0',
                        'preconnect' => '1',
                        'display_swap' => '1',
                        'prefer_local' => '1',
                        'family_en' => 'Montserrat',
                        'family_ar' => 'Cairo',
                    ],
                ],
            ])
            ->assertRedirect(route('admin.settings.show', 'google'))
            ->assertSessionHasNoErrors();

        $settings = app(SettingsService::class);
        $settings->clearCache();

        $this->assertTrue((bool) $settings->get('google.analytics.enabled'));
        $this->assertSame('G-TEST12345', $settings->get('google.analytics.measurement_id'));
        $this->assertSame('GTM-TEST123', $settings->get('google.gtm.container_id'));
    }

    public function test_invalid_measurement_id_is_rejected(): void
    {
        $admin = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $this->actingAs($admin)
            ->from(route('admin.settings.show', 'google'))
            ->put(route('admin.settings.update.group', 'google'), [
                '_group' => 'google',
                'google' => [
                    'analytics' => [
                        'enabled' => '1',
                        'measurement_id' => 'INVALID-ID',
                    ],
                    'gtm' => ['enabled' => '0', 'container_id' => ''],
                    'consent' => [
                        'enabled' => '1',
                        'analytics_storage' => 'denied',
                        'ad_storage' => 'denied',
                        'ad_user_data' => 'denied',
                        'ad_personalization' => 'denied',
                    ],
                    'adsense' => ['enabled' => '0'],
                    'maps' => ['enabled' => '0'],
                    'recaptcha' => ['enabled' => '0'],
                    'fonts' => ['prefer_local' => '1'],
                    'search_console' => [],
                    'merchant' => [],
                ],
            ])
            ->assertRedirect(route('admin.settings.show', 'google'))
            ->assertSessionHasErrors(['google.analytics.measurement_id']);
    }

    public function test_homepage_injects_gtm_when_enabled_and_hides_secret(): void
    {
        app(SettingsService::class)->setMany([
            'google.gtm.enabled' => true,
            'google.gtm.container_id' => 'GTM-ABCDEF1',
            'google.gtm.inject_head' => true,
            'google.gtm.inject_body' => true,
            'google.consent.enabled' => true,
            'google.recaptcha.secret_key' => 'super-secret-key-value',
        ]);
        app(SettingsService::class)->clearCache();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('GTM-ABCDEF1', false);
        $response->assertSee('googletagmanager.com/gtm.js', false);
        $response->assertDontSee('super-secret-key-value', false);
        $response->assertSee('data-consent-root', false);
    }

    public function test_robots_txt_and_sitemap_are_served(): void
    {
        $robots = $this->get(route('robots'));
        $robots->assertOk();
        $robots->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $robots->assertSee('User-agent:', false);
        $robots->assertSee('Disallow: /admin', false);
        $robots->assertSee('Sitemap:', false);

        $sitemap = $this->get(route('sitemap'));
        $sitemap->assertOk();
        $sitemap->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $sitemap->assertSee('<urlset', false);
        $sitemap->assertSee(route('home'), false);
    }

    public function test_seo_meta_includes_canonical_and_json_ld(): void
    {
        app(SettingsService::class)->setMany([
            'seo.title_en' => 'Canonical SEO Title',
            'seo.robots_default' => 'index,follow',
            'seo.schema_organization' => true,
            'seo.schema_website' => true,
        ]);
        app(SettingsService::class)->clearCache();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('rel="canonical"', false)
            ->assertSee('name="robots" content="index,follow"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('"@type":"NGO"', false);
    }
}
