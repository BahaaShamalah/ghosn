<?php

namespace Tests\Feature\Public;

use App\Models\ContentPage;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\CmsContentSeeder;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficialPageRoutingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([AdminUserSeeder::class, SettingsSeeder::class, LandingPageSeeder::class, CmsContentSeeder::class]);
    }

    public function test_about_page_is_available_at_root_slug(): void
    {
        $this->get('/about')
            ->assertOk()
            ->assertSee('Who We Are', false);
    }

    public function test_donation_policy_page_is_available_at_root_slug(): void
    {
        $this->get('/donation-policy')
            ->assertOk()
            ->assertSee('Donation Policy', false)
            ->assertSee('id="ghosn-landing-root"', false);
    }

    public function test_legacy_pages_prefix_redirects_permanently(): void
    {
        $this->get('/pages/donation-policy')
            ->assertRedirect('/donation-policy')
            ->assertStatus(301);
    }

    public function test_pages_show_route_generates_root_url(): void
    {
        $this->assertSame(
            url('/donation-policy'),
            route('pages.show', 'donation-policy')
        );
    }

    public function test_news_routes_still_work(): void
    {
        $this->get(route('news.index'))->assertOk();
    }

    public function test_campaign_routes_still_work(): void
    {
        $this->get(route('campaigns.index'))->assertOk();
    }

    public function test_admin_route_is_not_captured_by_page_slug(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_reserved_slug_admin_is_rejected_when_creating_page(): void
    {
        $admin = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.content-pages.store'), [
                'title_en' => 'Admin Page',
                'title_ar' => 'صفحة',
                'slug' => 'admin',
                'status' => ContentPage::STATUS_DRAFT,
            ])
            ->assertSessionHasErrors('slug');

        $this->assertDatabaseMissing('content_pages', ['slug' => 'admin']);
    }
}
