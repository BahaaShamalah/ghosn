<?php

namespace Tests\Feature\Admin;

use App\Models\Donation;
use App\Models\Setting;
use App\Models\User;
use App\Services\Settings\SettingsService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDonationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->seed(SettingsSeeder::class);
    }

    public function test_admin_donations_stats_calculate_correctly(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        Donation::query()->create([
            'reference' => 'GHOSN-STAT1',
            'amount' => 100,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PAID,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_transaction_id' => 'CAPTURE1',
            'paid_at' => now(),
            'locale' => 'en',
        ]);

        Donation::query()->create([
            'reference' => 'GHOSN-STAT2',
            'amount' => 50,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_STRIPE_CARD,
            'gateway' => Donation::GATEWAY_STRIPE,
            'status' => Donation::STATUS_PAID,
            'donor_name' => 'Ali Hassan',
            'donor_email' => 'ali@example.com',
            'gateway_transaction_id' => 'pi_test',
            'paid_at' => now(),
            'locale' => 'en',
        ]);

        Donation::query()->create([
            'reference' => 'GHOSN-STAT3',
            'amount' => 25,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_BANK,
            'gateway' => Donation::GATEWAY_BANK,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Nour Ali',
            'donor_email' => 'nour@example.com',
            'locale' => 'en',
        ]);

        $response = $this->actingAs($user)->get(route('admin.donations.index'));

        $response->assertOk();
        $response->assertSee(__('admin.donations.stat_total'), false);
        $response->assertSee('3', false);
        $response->assertSee('$150.00', false);
        $response->assertSee('$25.00', false);
        $response->assertSee(__('admin.donations.stat_paypal_paid'), false);
        $response->assertSee(__('admin.donations.stat_stripe_paid'), false);
    }

    public function test_admin_donations_filters_by_status_and_search(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        Donation::query()->create([
            'reference' => 'GHOSN-FILTER1',
            'amount' => 40,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Unique Donor',
            'donor_email' => 'unique@example.com',
            'locale' => 'en',
        ]);

        Donation::query()->create([
            'reference' => 'GHOSN-FILTER2',
            'amount' => 60,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_BANK,
            'gateway' => Donation::GATEWAY_BANK,
            'status' => Donation::STATUS_PAID,
            'donor_name' => 'Other Person',
            'donor_email' => 'other@example.com',
            'paid_at' => now(),
            'locale' => 'en',
        ]);

        $this->actingAs($user)
            ->get(route('admin.donations.index', ['status' => 'pending', 'search' => 'unique@example.com']))
            ->assertOk()
            ->assertSee('GHOSN-FILTER1', false)
            ->assertDontSee('GHOSN-FILTER2', false);
    }

    public function test_receipt_page_renders_for_admin(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-RECEIPT1',
            'amount' => 80,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PAID,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'gateway_transaction_id' => 'CAPTURE-RECEIPT',
            'paid_at' => now(),
            'locale' => 'en',
        ]);

        $response = $this->actingAs($user)->get(route('admin.donations.receipt.show', $donation));

        $response->assertOk();
        $response->assertSee('GHOSN-RECEIPT1', false);
        $response->assertSee('CAPTURE-RECEIPT', false);
        $response->assertSee('sara@example.com', false);
        $response->assertSee(__('admin.donations.receipt_thank_you'), false);
        $response->assertDontSee('secret_test', false);
    }

    public function test_guest_cannot_view_receipt(): void
    {
        $donation = Donation::query()->create([
            'reference' => 'GHOSN-RECEIPT2',
            'amount' => 80,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PAID,
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'paid_at' => now(),
            'locale' => 'en',
        ]);

        $this->get(route('admin.donations.receipt.show', $donation))
            ->assertRedirect();
    }
}
