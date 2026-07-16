<?php

namespace Tests\Feature\Public;

use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AssertsReactLandingPayload;
use Tests\TestCase;

class LegalTeamPagesTest extends TestCase
{
    use AssertsReactLandingPayload;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
    }

    public function test_donation_policy_renders_legal_page(): void
    {
        $response = $this->get('/donation-policy')->assertOk();

        $response->assertSee('id="ghosn-landing-root"', false);
        $response->assertSee('Donation Policy', false);

        $payload = $this->landingPayloadFromResponse($response);
        $this->assertSame('legal', $payload['pageType']);
        $this->assertSame('donation', $payload['legal']['activeKey']);
    }

    public function test_privacy_policy_renders(): void
    {
        $this->get('/privacy-policy')
            ->assertOk()
            ->assertSee('Privacy Policy', false);
    }

    public function test_our_team_page_renders(): void
    {
        $response = $this->get(route('team'))->assertOk();

        $response->assertSee('Meet the team growing hope', false);
        $response->assertSee('Guided by experience and heart', false);

        $payload = $this->landingPayloadFromResponse($response);
        $this->assertSame('team', $payload['pageType']);
    }

    public function test_default_navigation_includes_our_team_link(): void
    {
        $links = \App\Support\SiteChrome::navItems();

        $this->assertTrue(
            collect($links)->contains(fn (array $link): bool => $link['href'] === route('team'))
        );
    }
}
