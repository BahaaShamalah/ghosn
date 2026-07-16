<?php

namespace Tests\Feature\Public;

use App\Models\Setting;
use App\Services\Settings\SettingsService;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DonatePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
        $this->seed(LandingPageSeeder::class);
        $this->seedDonationBankSettings();
    }

    public function test_donate_page_renders_focused_checkout(): void
    {
        $response = $this->get(route('donate'));

        $response->assertOk();
        $response->assertSee('id="ghosn-root"', false);
        $response->assertSee('id="give-form"', false);
        $response->assertSee('data-donate-form', false);
        $response->assertSee('data-donate-summary', false);
        $response->assertSee('Make a Donation', false);
        $response->assertSee('Donation summary', false);
    }

    public function test_donate_page_does_not_include_landing_sections(): void
    {
        $response = $this->get(route('donate'));

        $response->assertOk();
        $response->assertDontSee('id="donate-hero"', false);
        $response->assertDontSee('id="ghosn-nav"', false);
        $response->assertDontSee('id="about"', false);
        $response->assertDontSee('id="vision"', false);
        $response->assertDontSee('Your gift grows relief', false);
        $response->assertDontSee('blob-anim-1', false);
    }

    public function test_donate_page_includes_action_links(): void
    {
        $response = $this->get(route('donate'));

        $response->assertOk();
        $response->assertSee(route('pages.show', 'donation-policy'), false);
        $response->assertSee(route('about'), false);
        $response->assertSee(route('volunteer'), false);
        $response->assertSee('Donation Policy', false);
        $response->assertSee('About GHOSN', false);
        $response->assertSee('Join Our Team', false);
    }

    public function test_static_support_pages_render(): void
    {
        $this->get(route('pages.show', 'donation-policy'))->assertOk()->assertSee('Donation Policy', false);
        $this->get(route('about'))->assertOk()->assertSee('Who We Are', false);
        $this->get(route('volunteer'))->assertOk()->assertSee('Lend your hands. Grow lasting hope.', false);
    }

    public function test_stripe_donation_redirects_when_enabled(): void
    {
        Mail::fake();

        Setting::query()->updateOrCreate(
            ['key' => 'payments.stripe_enabled'],
            ['type' => 'string', 'value' => '1'],
        );
        config(['services.stripe.secret' => 'sk_test_example']);

        app(SettingsService::class)->clearCache();

        $this->mock(\App\Services\Payments\PaymentGatewayManager::class, function ($mock): void {
            $mock->shouldReceive('createCheckout')
                ->once()
                ->andReturn(new \App\Services\Payments\DTOs\PaymentResultData(
                    success: true,
                    gateway: 'stripe',
                    status: 'pending',
                    checkoutUrl: 'https://checkout.stripe.com/test-session',
                    gatewayReference: 'cs_test_page',
                ));
        });

        $response = $this->post(route('donate.store'), [
            'amount' => 50,
            'payment_method' => 'stripe_card',
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
        ]);

        $response->assertRedirect('https://checkout.stripe.com/test-session');
    }

    private function seedDonationBankSettings(): void
    {
        $settings = [
            'donations.enabled' => '1',
            'donations.bank_transfer_enabled' => '1',
            'payments.currency' => 'USD',
            'payments.min_amount' => '5',
            'payments.stripe_enabled' => '0',
            'payments.paypal_enabled' => '0',
            'donations.iban' => 'PS00BANK00000000000000000000',
            'donations.bank_name_en' => 'Example Bank',
            'donations.bank_name_ar' => 'بنك تجريبي',
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['type' => 'string', 'value' => $value]);
        }

        app(SettingsService::class)->clearCache();
    }
}
