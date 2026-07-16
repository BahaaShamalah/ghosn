<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use App\Services\Settings\SettingsService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSettingsSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->seed(SettingsSeeder::class);
    }

    public function test_admin_payments_page_does_not_render_secret_fields(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        config([
            'services.stripe.secret' => 'sk_test_super_secret_value',
            'services.stripe.webhook_secret' => 'whsec_super_secret_value',
            'services.paypal.client_id' => 'paypal_client_secret_id',
            'services.paypal.client_secret' => 'paypal_super_secret_value',
            'services.paypal.webhook_id' => 'WH-SECRET-WEBHOOK-ID',
        ]);

        $response = $this->actingAs($user)->get(route('admin.settings.show', 'payments'));

        $response->assertOk();
        $response->assertSee(__('admin.settings.env_configured'), false);
        $response->assertSee(__('admin.settings.payments_env_notice'), false);
        $response->assertDontSee('name="payments[stripe_secret_key]"', false);
        $response->assertDontSee('name="payments[stripe_webhook_secret]"', false);
        $response->assertDontSee('name="payments[paypal_client_secret]"', false);
        $response->assertDontSee('name="payments[paypal_webhook_id]"', false);
        $response->assertDontSee('name="payments[paypal_client_id]"', false);
        $response->assertDontSee('sk_test_super_secret_value', false);
        $response->assertDontSee('whsec_super_secret_value', false);
        $response->assertDontSee('paypal_super_secret_value', false);
    }

    public function test_admin_payments_page_shows_missing_env_status(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        config([
            'services.stripe.secret' => '',
            'services.stripe.webhook_secret' => '',
            'services.paypal.client_id' => '',
            'services.paypal.client_secret' => '',
            'services.paypal.webhook_id' => '',
        ]);

        $response = $this->actingAs($user)->get(route('admin.settings.show', 'payments'));

        $response->assertOk();
        $response->assertSee(__('admin.settings.env_missing'), false);
    }

    public function test_admin_payments_page_shows_paypal_webhook_status_without_secrets(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        config([
            'services.paypal.client_id' => 'paypal_client_id',
            'services.paypal.client_secret' => 'paypal_secret_value',
            'services.paypal.webhook_id' => '',
        ]);

        $response = $this->actingAs($user)->get(route('admin.settings.show', 'payments'));

        $response->assertOk();
        $response->assertSee(__('admin.settings.payments_paypal_webhook_optional'), false);
        $response->assertSee(__('admin.settings.env_webhook_not_configured'), false);
        $response->assertDontSee('paypal_secret_value', false);
    }

    public function test_admin_payments_page_shows_configured_webhook_status(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        config([
            'services.paypal.client_id' => 'paypal_client_id',
            'services.paypal.client_secret' => 'paypal_secret_value',
            'services.paypal.webhook_id' => 'WH-TEST-ID',
        ]);

        $response = $this->actingAs($user)->get(route('admin.settings.show', 'payments'));

        $response->assertOk();
        $response->assertSee(__('admin.settings.env_webhook_configured'), false);
        $response->assertDontSee('WH-TEST-ID', false);
    }

    public function test_donate_page_renders_without_gateway_env_keys(): void
    {
        $this->seedDonationSettings();

        config([
            'services.stripe.secret' => '',
            'services.paypal.client_id' => '',
            'services.paypal.client_secret' => '',
        ]);

        $response = $this->get(route('donate'));

        $response->assertOk();
        $response->assertSee('id="give-form"', false);
        $response->assertSee(__('public.donate.checkout_title'), false);
    }

    public function test_stripe_option_hidden_when_env_keys_missing(): void
    {
        $this->seedDonationSettings([
            'payments.stripe_enabled' => '1',
        ]);

        config(['services.stripe.secret' => '']);

        $response = $this->get(route('donate'));

        $response->assertOk();
        $response->assertDontSee('value="stripe_card"', false);
        $response->assertSee('value="bank_transfer"', false);
    }

    public function test_paypal_option_hidden_when_env_keys_missing(): void
    {
        $this->seedDonationSettings([
            'payments.paypal_enabled' => '1',
        ]);

        config([
            'services.paypal.client_id' => '',
            'services.paypal.client_secret' => '',
        ]);

        $response = $this->get(route('donate'));

        $response->assertOk();
        $response->assertDontSee('value="paypal_business"', false);
        $response->assertSee('value="bank_transfer"', false);
    }

    public function test_bank_transfer_remains_available_without_gateway_keys(): void
    {
        $this->seedDonationSettings();

        config([
            'services.stripe.secret' => '',
            'services.paypal.client_id' => '',
            'services.paypal.client_secret' => '',
        ]);

        $response = $this->post(route('donate.store'), [
            'amount' => 100,
            'payment_method' => 'bank_transfer',
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('donations', [
            'payment_method' => 'bank_transfer',
            'donor_email' => 'sara@example.com',
        ]);
    }

    /**
     * @param  array<string, string>  $overrides
     */
    private function seedDonationSettings(array $overrides = []): void
    {
        $settings = array_merge([
            'donations.enabled' => '1',
            'donations.bank_transfer_enabled' => '1',
            'donations.iban' => 'PS00BANK00000000000000000000',
            'payments.stripe_enabled' => '0',
            'payments.paypal_enabled' => '0',
        ], $overrides);

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['type' => 'string', 'value' => $value]);
        }

        app(SettingsService::class)->clearCache();
    }
}
