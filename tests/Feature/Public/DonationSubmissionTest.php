<?php

namespace Tests\Feature\Public;

use App\Models\Donation;
use App\Models\Setting;
use App\Services\Settings\SettingsService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DonationSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
        $this->seedDonationBankSettings();
    }

    public function test_visitor_can_submit_bank_transfer_donation(): void
    {
        Mail::fake();

        $response = $this->post(route('donate.store'), [
            'amount' => 100,
            'payment_method' => 'bank_transfer',
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'donor_phone' => '+970599000000',
            'message' => 'For relief work',
        ]);

        $donation = Donation::query()->first();

        $this->assertNotNull($donation);
        $response->assertRedirect(route('donate.thank-you', ['reference' => $donation->reference]));
        $this->assertDatabaseHas('donations', [
            'donor_email' => 'sara@example.com',
            'amount' => '100.00',
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
        ]);
    }

    public function test_donation_form_requires_valid_amount(): void
    {
        $response = $this->from(route('donate'))
            ->post(route('donate.store'), [
                'amount' => 1,
                'payment_method' => 'bank_transfer',
                'donor_name' => 'Sara Ahmad',
                'donor_email' => 'sara@example.com',
            ]);

        $response->assertRedirect(route('donate'));
        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseCount('donations', 0);
    }

    private function seedDonationBankSettings(): void
    {
        $settings = [
            'donations.enabled' => '1',
            'donations.bank_transfer_enabled' => '1',
            'payments.currency' => 'USD',
            'payments.min_amount' => '5',
            'payments.stripe_enabled' => '0',
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
