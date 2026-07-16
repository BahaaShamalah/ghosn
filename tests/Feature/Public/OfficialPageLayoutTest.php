<?php

namespace Tests\Feature\Public;

use App\Models\ContentPage;
use App\Models\SocialLink;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\CmsContentSeeder;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AssertsReactLandingPayload;
use Tests\TestCase;

class OfficialPageLayoutTest extends TestCase
{
    use AssertsReactLandingPayload;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([AdminUserSeeder::class, SettingsSeeder::class, LandingPageSeeder::class, CmsContentSeeder::class]);
    }

    public function test_official_page_renders_about_template(): void
    {
        $response = $this->get(route('about'));

        $response->assertOk();
        $response->assertSee('Who We Are', false);
        $response->assertSee('id="ghosn-landing-root"', false);
    }

    public function test_default_official_page_renders_centered_layout(): void
    {
        $response = $this->get('/donation-policy');

        $response->assertOk();
        $response->assertSee('Donation Policy', false);
        $response->assertSee('id="ghosn-landing-root"', false);
    }

    public function test_footer_does_not_show_raw_blade_placeholders(): void
    {
        SocialLink::query()->create([
            'platform' => SocialLink::PLATFORM_INSTAGRAM,
            'label_en' => '@ghosn.team',
            'url' => 'https://instagram.com/ghosn.team',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('home'))->assertOk();
        $payload = $this->landingPayloadFromResponse($response);
        $urls = array_column($payload['socialLinks'] ?? [], 'url');

        $this->assertContains('https://instagram.com/ghosn.team', $urls);
        $this->assertStringNotContainsString('{{ $instagramHandle }}', $response->getContent());
    }

    public function test_active_social_links_render_in_footer(): void
    {
        SocialLink::query()->create([
            'platform' => SocialLink::PLATFORM_FACEBOOK,
            'label_en' => 'Facebook',
            'url' => 'https://facebook.com/ghosnteam',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        SocialLink::query()->create([
            'platform' => SocialLink::PLATFORM_YOUTUBE,
            'label_en' => 'YouTube',
            'url' => 'https://youtube.com/@ghosn',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('home'))->assertOk();
        $payload = $this->landingPayloadFromResponse($response);
        $urls = array_column($payload['socialLinks'] ?? [], 'url');

        $this->assertContains('https://facebook.com/ghosnteam', $urls);
        $this->assertContains('https://youtube.com/@ghosn', $urls);
    }

    public function test_inactive_social_links_are_hidden(): void
    {
        SocialLink::query()->create([
            'platform' => SocialLink::PLATFORM_FACEBOOK,
            'label_en' => 'Hidden FB',
            'url' => 'https://facebook.com/hidden-page',
            'is_active' => false,
            'sort_order' => 0,
        ]);

        SocialLink::query()->create([
            'platform' => SocialLink::PLATFORM_INSTAGRAM,
            'label_en' => 'Visible IG',
            'url' => 'https://instagram.com/visible',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('home'))->assertOk();
        $payload = $this->landingPayloadFromResponse($response);
        $urls = array_column($payload['socialLinks'] ?? [], 'url');

        $this->assertNotContains('https://facebook.com/hidden-page', $urls);
        $this->assertContains('https://instagram.com/visible', $urls);
    }

    public function test_platform_icons_render_in_footer(): void
    {
        SocialLink::query()->create([
            'platform' => SocialLink::PLATFORM_X,
            'url' => 'https://x.com/ghosn',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('home'))->assertOk();
        $payload = $this->landingPayloadFromResponse($response);
        $links = $payload['socialLinks'] ?? [];

        $this->assertTrue(collect($links)->contains(
            fn (array $link): bool => ($link['url'] ?? '') === 'https://x.com/ghosn'
                && ($link['iconClass'] ?? '') === 'fa-brands fa-x-twitter'
        ));

        $blade = $this->get(route('donate'))->assertOk();
        $blade->assertSee('fa-brands fa-x-twitter', false);
        $blade->assertSee('Donation Policy', false);
        $blade->assertSee('gh-site-footer__column', false);
    }

    public function test_footer_explore_links_column_is_present(): void
    {
        $this->get(route('donate'))
            ->assertOk()
            ->assertSee('Explore', false)
            ->assertSee('Privacy Policy', false);

        $response = $this->get(route('home'))->assertOk();
        $payload = $this->landingPayloadFromResponse($response);
        $footer = $payload['footer'] ?? [];

        $this->assertSame('Explore', $footer['linksTitle']['en'] ?? null);
        $this->assertNotEmpty($footer['links'] ?? []);
    }

    public function test_custom_official_page_with_featured_image(): void
    {
        ContentPage::query()->create([
            'title_en' => 'Official Policy',
            'title_ar' => 'سياسة',
            'slug' => 'official-policy',
            'content_en' => '<p>Policy body content.</p>',
            'content_ar' => '<p>محتوى.</p>',
            'status' => ContentPage::STATUS_PUBLISHED,
        ]);

        $this->get(route('pages.show', 'official-policy'))
            ->assertOk()
            ->assertSee('Official Policy', false)
            ->assertSee('Policy body content.', false);
    }
}
