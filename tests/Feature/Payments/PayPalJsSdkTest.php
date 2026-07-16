<?php

namespace Tests\Feature\Payments;

use App\Models\Donation;
use App\Models\Setting;
use App\Services\Settings\SettingsService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PayPalJsSdkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
        $this->seedPaymentSettings();
    }

    public function test_paypal_webhook_id_is_optional_for_checkout(): void
    {
        $this->enablePayPal(webhookId: '');

        config(['services.paypal.webhook_id' => '']);

        $this->assertTrue(app(\App\Support\PaymentSettings::class)->paypalEnabled());
        $this->assertFalse(app(\App\Support\PaymentSettings::class)->paypalWebhookConfigured());
    }

    public function test_donation_page_renders_without_webhook_id(): void
    {
        $this->enablePayPal(webhookId: '');

        config(['services.paypal.webhook_id' => '']);

        $response = $this->get(route('donate'));

        $response->assertOk();
        $response->assertSee('data-paypal-enabled="1"', false);
        $response->assertSee('data-paypal-client-id="client_test"', false);
        $response->assertDontSee('secret_test', false);
    }

    public function test_paypal_option_appears_when_client_id_and_secret_configured(): void
    {
        $this->enablePayPal();

        $response = $this->get(route('donate'));

        $response->assertOk();
        $response->assertSee('value="paypal_business"', false);
        $response->assertSee('data-paypal-buttons', false);
    }

    public function test_create_order_requires_paypal_business_payment_method(): void
    {
        $this->enablePayPal();

        $this->postJson(route('donate.paypal.create-order'), [
            'amount' => 75,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
        ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Validation failed')
            ->assertJsonValidationErrors(['payment_method']);
    }

    public function test_create_order_maps_legacy_paypal_alias_to_paypal_business(): void
    {
        Mail::fake();

        $this->enablePayPal();
        $this->fakePayPalApi();

        $this->postJson(route('donate.paypal.create-order'), [
            'payment_method' => 'paypal',
            'amount' => 75,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
        ])
            ->assertOk()
            ->assertJsonPath('orderID', 'ORDER123');

        $this->assertDatabaseHas('donations', [
            'payment_method' => 'paypal_business',
            'status' => 'pending',
        ]);
    }

    public function test_capture_order_does_not_require_payment_method(): void
    {
        Mail::fake();

        $this->enablePayPal();
        $this->fakePayPalApi(includeCapture: true);

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL11',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'locale' => 'en',
        ]);

        $this->postJson(route('donate.paypal.capture-order'), [
            'orderID' => 'ORDER123',
            'donation_id' => $donation->id,
            'reference' => $donation->reference,
            'payment_method' => 'paypal',
        ])->assertOk()->assertJsonPath('paid', true);
    }

    public function test_store_route_rejects_legacy_paypal_alias(): void
    {
        $this->enablePayPal();

        $this->from(route('donate'))
            ->post(route('donate.store'), [
                'amount' => 75,
                'payment_method' => 'paypal',
                'donor_name' => 'Sara Ahmad',
                'donor_email' => 'sara@example.com',
            ])
            ->assertRedirect(route('donate'))
            ->assertSessionHasErrors('payment_method');

        $this->assertDatabaseCount('donations', 0);
    }

    public function test_create_order_creates_pending_donation(): void
    {
        Mail::fake();

        $this->enablePayPal();
        $this->fakePayPalApi();

        $response = $this->postJson(route('donate.paypal.create-order'), [
            'payment_method' => 'paypal_business',
            'amount' => 75,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['orderID', 'order_id', 'donation_id', 'reference'])
            ->assertJsonPath('orderID', 'ORDER123')
            ->assertJsonPath('order_id', 'ORDER123');

        $this->assertDatabaseHas('donations', [
            'id' => $response->json('donation_id'),
            'payment_method' => 'paypal_business',
            'gateway' => 'paypal',
            'gateway_reference' => 'ORDER123',
            'status' => 'pending',
            'amount' => 75,
        ]);
    }

    public function test_capture_order_marks_donation_paid(): void
    {
        Mail::fake();

        $this->enablePayPal();
        $this->fakePayPalApi(includeCapture: true);

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL1',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'locale' => 'en',
        ]);

        $response = $this->postJson(route('donate.paypal.capture-order'), $this->capturePayload($donation));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('paid', true)
            ->assertJsonPath('reference', 'GHOSN-PAYPAL1');

        $donation->refresh();
        $this->assertSame('paid', $donation->status);
        $this->assertSame('CAPTURE123', $donation->gateway_transaction_id);
        $this->assertSame('ORDER123', $donation->gateway_reference);
        $this->assertNotNull($donation->paid_at);
        $this->assertSame('COMPLETED', $donation->metadata['paypal_capture_response']['status'] ?? null);
    }

    public function test_capture_order_can_resolve_donation_by_paypal_order_id(): void
    {
        Mail::fake();

        $this->enablePayPal();
        $this->fakePayPalApi(includeCapture: true);

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL7',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'locale' => 'en',
        ]);

        $this->postJson(route('donate.paypal.capture-order'), $this->capturePayload($donation, donationId: 999999))
            ->assertOk()
            ->assertJsonPath('paid', true);

        $this->assertSame('paid', $donation->fresh()->status);
    }

    public function test_duplicate_capture_does_not_double_count_campaign_amount(): void
    {
        Mail::fake();

        $this->enablePayPal();
        $this->fakePayPalApi(includeCapture: true);

        $campaign = \App\Models\Campaign::query()->create([
            'title_en' => 'PayPal Campaign',
            'title_ar' => 'حملة باي بال',
            'slug' => 'paypal-campaign-test',
            'goal_amount' => 1000,
            'raised_amount' => 0,
            'donors_count' => 0,
            'currency' => 'USD',
            'status' => \App\Models\Campaign::STATUS_ACTIVE,
            'starts_at' => now(),
        ]);
        $raisedBefore = (float) $campaign->raised_amount;
        $donorsBefore = (int) $campaign->donors_count;

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL8',
            'campaign_id' => $campaign->id,
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'locale' => 'en',
        ]);

        $payload = $this->capturePayload($donation);

        $this->postJson(route('donate.paypal.capture-order'), $payload)->assertOk()->assertJsonPath('paid', true);
        $this->postJson(route('donate.paypal.capture-order'), $payload)->assertOk()->assertJsonPath('paid', true);

        $campaign->refresh();
        $this->assertEquals($raisedBefore + 75, (float) $campaign->raised_amount);
        $this->assertEquals($donorsBefore + 1, $campaign->donors_count);
    }

    public function test_donate_success_rejects_unpaid_paypal_return(): void
    {
        Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL9',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'locale' => 'en',
        ]);

        $this->get(route('donate.success', ['gateway' => 'paypal', 'token' => 'ORDER123']))
            ->assertRedirect(route('donate'));
    }

    public function test_duplicate_capture_does_not_double_process(): void
    {
        Mail::fake();

        $this->enablePayPal();

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL2',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PAID,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'gateway_transaction_id' => 'CAPTURE_EXISTING',
            'paid_at' => now(),
            'locale' => 'en',
        ]);

        Http::fake();

        $response = $this->postJson(route('donate.paypal.capture-order'), $this->capturePayload($donation));

        $response->assertOk();
        $this->assertSame('CAPTURE_EXISTING', $donation->fresh()->gateway_transaction_id);
        Http::assertNothingSent();
    }

    public function test_capture_order_sends_empty_json_object_to_paypal(): void
    {
        Mail::fake();

        $this->enablePayPal();

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL12',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'locale' => 'en',
        ]);

        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'test-token'], 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders/ORDER123/capture' => function ($request) {
                $this->assertSame('{}', $request->body());

                return Http::response([
                    'id' => 'ORDER123',
                    'status' => 'COMPLETED',
                    'purchase_units' => [[
                        'custom_id' => '1',
                        'amount' => ['currency_code' => 'USD', 'value' => '75.00'],
                        'payments' => [
                            'captures' => [[
                                'id' => 'CAPTURE123',
                                'status' => 'COMPLETED',
                                'amount' => ['currency_code' => 'USD', 'value' => '75.00'],
                            ]],
                        ],
                    ]],
                ], 200);
            },
        ]);

        $this->postJson(route('donate.paypal.capture-order'), $this->capturePayload($donation))
            ->assertOk()
            ->assertJsonPath('paid', true);
    }

    public function test_capture_order_recovers_when_paypal_order_already_completed(): void
    {
        Mail::fake();

        $this->enablePayPal();

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL13',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'locale' => 'en',
        ]);

        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'test-token'], 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders/ORDER123/capture' => Http::response([
                'name' => 'INVALID_REQUEST',
                'message' => 'Request is not well-formed.',
                'details' => [['issue' => 'MALFORMED_REQUEST_JSON']],
            ], 400),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders/ORDER123' => Http::response([
                'id' => 'ORDER123',
                'status' => 'COMPLETED',
                'purchase_units' => [[
                    'custom_id' => (string) $donation->id,
                    'amount' => ['currency_code' => 'USD', 'value' => '75.00'],
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAPTURE_RECOVERED',
                            'status' => 'COMPLETED',
                            'amount' => ['currency_code' => 'USD', 'value' => '75.00'],
                        ]],
                    ],
                ]],
            ], 200),
        ]);

        $this->postJson(route('donate.paypal.capture-order'), $this->capturePayload($donation))
            ->assertOk()
            ->assertJsonPath('paid', true);

        $this->assertSame('paid', $donation->fresh()->status);
        $this->assertSame('CAPTURE_RECOVERED', $donation->fresh()->gateway_transaction_id);
    }

    public function test_capture_order_rejects_missing_order_id_with_422(): void
    {
        $this->enablePayPal();

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL3',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'locale' => 'en',
        ]);

        $this->postJson(route('donate.paypal.capture-order'), [
            'donation_id' => $donation->id,
            'reference' => $donation->reference,
        ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Validation failed')
            ->assertJsonValidationErrors(['order_id']);

        $this->assertSame('pending', $donation->fresh()->status);
    }

    public function test_capture_order_accepts_order_id_camel_case_key(): void
    {
        Mail::fake();

        $this->enablePayPal();
        $this->fakePayPalApi(includeCapture: true);

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL10',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'locale' => 'en',
        ]);

        $this->postJson(route('donate.paypal.capture-order'), [
            'orderID' => 'ORDER123',
            'donation_id' => $donation->id,
            'reference' => $donation->reference,
        ])->assertOk()->assertJsonPath('paid', true);
    }

    public function test_paypal_cannot_be_submitted_via_standard_donate_form(): void
    {
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

    public function test_already_captured_paypal_order_still_marks_donation_paid(): void
    {
        Mail::fake();

        $this->enablePayPal();

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL4',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'locale' => 'en',
        ]);

        Http::fake([
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'test-token'], 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders/ORDER123/capture' => Http::response([
                'name' => 'UNPROCESSABLE_ENTITY',
                'details' => [['issue' => 'ORDER_ALREADY_CAPTURED']],
            ], 422),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders/ORDER123' => Http::response([
                'id' => 'ORDER123',
                'status' => 'COMPLETED',
                'purchase_units' => [[
                    'custom_id' => (string) $donation->id,
                    'amount' => ['currency_code' => 'USD', 'value' => '75.00'],
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAPTURE_ALREADY',
                            'amount' => ['currency_code' => 'USD', 'value' => '75.00'],
                        ]],
                    ],
                ]],
            ], 200),
        ]);

        $this->postJson(route('donate.paypal.capture-order'), $this->capturePayload($donation))
            ->assertOk()
            ->assertJsonPath('paid', true);

        $donation->refresh();
        $this->assertSame('paid', $donation->status);
        $this->assertSame('CAPTURE_ALREADY', $donation->gateway_transaction_id);
    }

    public function test_pending_paypal_donation_does_not_show_complete_page(): void
    {
        Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL5',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'locale' => 'en',
        ]);

        $this->get(route('donate.complete', ['reference' => 'GHOSN-PAYPAL5']))
            ->assertRedirect(route('donate'));
    }

    public function test_paid_paypal_donation_shows_complete_page(): void
    {
        Donation::query()->create([
            'reference' => 'GHOSN-PAYPAL6',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PAID,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_reference' => 'ORDER123',
            'gateway_transaction_id' => 'CAPTURE123',
            'paid_at' => now(),
            'locale' => 'en',
        ]);

        $this->get(route('donate.complete', ['reference' => 'GHOSN-PAYPAL6']))
            ->assertOk()
            ->assertSee('GHOSN-PAYPAL6', false);
    }

    /**
     * Minimal capture-order payload (matches resources/js/public/donate-paypal.js).
     *
     * @return array<string, int|string>
     */
    private function capturePayload(Donation $donation, string $orderId = 'ORDER123', ?int $donationId = null): array
    {
        return [
            'order_id' => $orderId,
            'donation_id' => $donationId ?? $donation->id,
            'reference' => $donation->reference,
        ];
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

    private function enablePayPal(?string $webhookId = 'WH-TEST'): void
    {
        Setting::query()->updateOrCreate(['key' => 'payments.paypal_enabled'], ['type' => 'string', 'value' => '1']);
        Setting::query()->updateOrCreate(['key' => 'payments.paypal_mode'], ['type' => 'string', 'value' => 'sandbox']);

        config([
            'services.paypal.client_id' => 'client_test',
            'services.paypal.client_secret' => 'secret_test',
            'services.paypal.webhook_id' => $webhookId ?? '',
        ]);

        app(SettingsService::class)->clearCache();
    }

    private function fakePayPalApi(bool $includeCapture = false): void
    {
        $responses = [
            'https://api-m.sandbox.paypal.com/v1/oauth2/token' => Http::response(['access_token' => 'test-token'], 200),
            'https://api-m.sandbox.paypal.com/v2/checkout/orders' => Http::response([
                'id' => 'ORDER123',
                'status' => 'CREATED',
            ], 201),
        ];

        if ($includeCapture) {
            $responses['https://api-m.sandbox.paypal.com/v2/checkout/orders/ORDER123/capture'] = Http::response([
                'id' => 'ORDER123',
                'status' => 'COMPLETED',
                'purchase_units' => [[
                    'custom_id' => '1',
                    'amount' => ['currency_code' => 'USD', 'value' => '75.00'],
                    'payments' => [
                        'captures' => [[
                            'id' => 'CAPTURE123',
                            'status' => 'COMPLETED',
                            'amount' => ['currency_code' => 'USD', 'value' => '75.00'],
                        ]],
                    ],
                ]],
            ], 200);
        }

        Http::fake($responses);
    }
}
