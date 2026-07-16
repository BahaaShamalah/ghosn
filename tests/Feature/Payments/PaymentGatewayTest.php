<?php

namespace Tests\Feature\Payments;

use App\Models\Donation;
use App\Models\PaymentGatewayEvent;
use App\Models\Setting;
use App\Services\Donations\DonationService;
use App\Services\Payments\DTOs\PaymentResultData;
use App\Services\Payments\DTOs\WebhookResultData;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\Settings\SettingsService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
        $this->seedPaymentSettings();
    }

    public function test_bank_transfer_creates_pending_donation(): void
    {
        Mail::fake();

        $response = $this->post(route('donate.store'), [
            'amount' => 100,
            'payment_method' => 'bank_transfer',
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
        ]);

        $donation = Donation::query()->first();

        $this->assertNotNull($donation);
        $response->assertRedirect(route('donate.thank-you', ['reference' => $donation->reference]));
        $this->assertDatabaseHas('donations', [
            'payment_method' => 'bank_transfer',
            'gateway' => 'bank_transfer',
            'status' => 'pending',
        ]);
    }

    public function test_disabled_stripe_does_not_appear_on_donate_page(): void
    {
        $response = $this->get(route('donate'));

        $response->assertOk();
        $response->assertDontSee('value="stripe_card"', false);
        $response->assertSee('value="bank_transfer"', false);
    }

    public function test_disabled_paypal_does_not_appear_on_donate_page(): void
    {
        $response = $this->get(route('donate'));

        $response->assertOk();
        $response->assertDontSee('value="paypal_business"', false);
    }

    public function test_stripe_checkout_redirects_when_enabled(): void
    {
        Mail::fake();

        $this->enableStripe();

        $this->mock(PaymentGatewayManager::class, function ($mock): void {
            $mock->shouldReceive('createCheckout')
                ->once()
                ->andReturn(new PaymentResultData(
                    success: true,
                    gateway: 'stripe',
                    status: 'pending',
                    checkoutUrl: 'https://checkout.stripe.com/test-session',
                    gatewayReference: 'cs_test_123',
                ));
        });

        $response = $this->post(route('donate.store'), [
            'amount' => 50,
            'payment_method' => 'stripe_card',
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
        ]);

        $response->assertRedirect('https://checkout.stripe.com/test-session');
        $this->assertDatabaseHas('donations', [
            'payment_method' => 'stripe_card',
            'gateway' => 'stripe',
            'status' => 'pending',
        ]);
    }

    public function test_paypal_store_route_rejects_paypal_method(): void
    {
        Mail::fake();

        $this->enablePayPal();

        $response = $this->post(route('donate.store'), [
            'amount' => 75,
            'payment_method' => 'paypal_business',
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
        ]);

        $response->assertSessionHasErrors('payment_method');
        $this->assertDatabaseCount('donations', 0);
    }

    public function test_stripe_webhook_rejects_invalid_signature(): void
    {
        $this->enableStripe(webhookSecret: 'whsec_test');

        $this->postJson(route('webhooks.stripe'), [], [
            'Stripe-Signature' => 'invalid',
        ])->assertForbidden();
    }

    public function test_successful_webhook_marks_payment_paid(): void
    {
        Mail::fake();

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-TESTWEB1',
            'amount' => 50,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_STRIPE_CARD,
            'gateway' => Donation::GATEWAY_STRIPE,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'cs_test_webhook',
            'locale' => 'en',
        ]);

        $this->mock(PaymentGatewayManager::class, function ($mock) use ($donation): void {
            $mock->shouldReceive('handleWebhook')
                ->once()
                ->andReturn(new WebhookResultData(
                    accepted: true,
                    processed: true,
                    duplicate: false,
                    donationId: $donation->id,
                    eventId: 'evt_test_1',
                    eventType: 'checkout.session.completed',
                    context: [
                        'transaction_id' => 'pi_test_1',
                        'gateway_reference' => 'cs_test_webhook',
                    ],
                ));
        });

        $this->postJson(route('webhooks.stripe'), ['id' => 'evt_test_1'])
            ->assertOk();

        $donation->refresh();
        $this->assertSame('paid', $donation->status);
        $this->assertNotNull($donation->paid_at);
        $this->assertSame('pi_test_1', $donation->gateway_transaction_id);
    }

    public function test_duplicate_webhook_does_not_double_process(): void
    {
        Mail::fake();

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-TESTWEB2',
            'amount' => 50,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_STRIPE_CARD,
            'gateway' => Donation::GATEWAY_STRIPE,
            'status' => Donation::STATUS_PAID,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'cs_test_dup',
            'gateway_transaction_id' => 'pi_existing',
            'paid_at' => now(),
            'locale' => 'en',
        ]);

        PaymentGatewayEvent::query()->create([
            'gateway' => 'stripe',
            'event_id' => 'evt_dup_1',
            'event_type' => 'checkout.session.completed',
            'donation_id' => $donation->id,
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        $this->mock(PaymentGatewayManager::class, function ($mock) use ($donation): void {
            $mock->shouldReceive('handleWebhook')
                ->once()
                ->andReturn(new WebhookResultData(
                    accepted: true,
                    processed: false,
                    duplicate: true,
                    donationId: $donation->id,
                    eventId: 'evt_dup_1',
                    eventType: 'checkout.session.completed',
                ));
        });

        $result = app(DonationService::class)->processWebhook('stripe', '{}', ['stripe-signature' => 'sig']);

        $this->assertTrue($result->duplicate);
        $this->assertSame('pi_existing', $donation->fresh()->gateway_transaction_id);
        $this->assertSame(1, PaymentGatewayEvent::query()->where('event_id', 'evt_dup_1')->count());
    }

    private function seedPaymentSettings(): void
    {
        $settings = [
            'donations.enabled' => '1',
            'donations.bank_transfer_enabled' => '1',
            'donations.iban' => 'PS00BANK00000000000000000000',
            'donations.bank_name_en' => 'Example Bank',
            'donations.bank_name_ar' => 'بنك تجريبي',
            'payments.currency' => 'USD',
            'payments.min_amount' => '5',
            'payments.max_amount' => '50000',
            'payments.stripe_enabled' => '0',
            'payments.paypal_enabled' => '0',
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['type' => 'string', 'value' => $value]);
        }

        app(SettingsService::class)->clearCache();
    }

    private function enableStripe(?string $webhookSecret = null): void
    {
        Setting::query()->updateOrCreate(['key' => 'payments.stripe_enabled'], ['type' => 'string', 'value' => '1']);

        config([
            'services.stripe.secret' => 'sk_test_example',
            'services.stripe.webhook_secret' => $webhookSecret ?? '',
        ]);

        app(SettingsService::class)->clearCache();
    }

    private function enablePayPal(): void
    {
        Setting::query()->updateOrCreate(['key' => 'payments.paypal_enabled'], ['type' => 'string', 'value' => '1']);
        Setting::query()->updateOrCreate(['key' => 'payments.paypal_mode'], ['type' => 'string', 'value' => 'sandbox']);

        config([
            'services.paypal.client_id' => 'client_test',
            'services.paypal.client_secret' => 'secret_test',
            'services.paypal.webhook_id' => 'WH-TEST',
        ]);

        app(SettingsService::class)->clearCache();
    }
}
