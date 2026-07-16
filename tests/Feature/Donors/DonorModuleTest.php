<?php

namespace Tests\Feature\Donors;

use App\Mail\AdminNewDonationAlertMail;
use App\Mail\BankTransferInstructionsMail;
use App\Mail\CustomDonorMessageMail;
use App\Mail\PaymentConfirmedMail;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\EmailLog;
use App\Models\Setting;
use App\Models\User;
use App\Services\Donations\DonationService;
use App\Services\Settings\SettingsService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DonorModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->seed(SettingsSeeder::class);
        $this->seedEmailAndBankSettings();
    }

    public function test_donor_is_created_when_bank_donation_is_submitted(): void
    {
        Mail::fake();

        $this->post(route('donate.store'), [
            'amount' => 50,
            'payment_method' => 'bank_transfer',
            'donor_name' => 'Sara Ahmad',
            'donor_email' => 'sara@example.com',
            'donor_phone' => '+970599000000',
        ])->assertRedirect();

        $donation = Donation::query()->first();
        $this->assertNotNull($donation->donor_id);

        $this->assertDatabaseHas('donors', [
            'email' => 'sara@example.com',
            'name' => 'Sara Ahmad',
            'donations_count' => 0,
        ]);

        Mail::assertQueued(BankTransferInstructionsMail::class);
        Mail::assertQueued(AdminNewDonationAlertMail::class);
    }

    public function test_donor_stats_update_when_donation_is_marked_paid(): void
    {
        Mail::fake();

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-DONOR1',
            'amount' => 75,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_BANK,
            'gateway' => Donation::GATEWAY_BANK,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Ali Hassan',
            'donor_email' => 'ali@example.com',
            'locale' => 'en',
        ]);

        app(DonationService::class)->markBankTransferConfirmed($donation);

        $donor = Donor::query()->where('email', 'ali@example.com')->first();
        $this->assertNotNull($donor);
        $this->assertSame(1, $donor->donations_count);
        $this->assertEquals(75.0, (float) $donor->total_donated);

        Mail::assertQueued(PaymentConfirmedMail::class);
    }

    public function test_duplicate_payment_confirmed_email_is_not_queued_twice(): void
    {
        Mail::fake();

        $donor = Donor::query()->create([
            'name' => 'Sara',
            'email' => 'sara@example.com',
            'locale' => 'en',
        ]);

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-DUP1',
            'donor_id' => $donor->id,
            'amount' => 40,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_PAYPAL,
            'gateway' => Donation::GATEWAY_PAYPAL,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Sara',
            'donor_email' => 'sara@example.com',
            'locale' => 'en',
        ]);

        $service = app(DonationService::class);
        $service->markPaid($donation);
        $service->markPaid($donation->fresh());

        Mail::assertQueued(PaymentConfirmedMail::class, 1);
        $this->assertSame(1, EmailLog::query()->where('type', EmailLog::TYPE_PAYMENT_CONFIRMED)->count());
    }

    public function test_admin_can_send_custom_email_to_donor(): void
    {
        Mail::fake();

        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();
        $donor = Donor::query()->create([
            'name' => 'Nour Ali',
            'email' => 'nour@example.com',
            'locale' => 'en',
        ]);

        $this->actingAs($user)
            ->post(route('admin.donors.send-email', $donor), [
                'subject' => 'Thank you from GHOSN',
                'message' => 'We appreciate your continued support.',
                'cta_text' => 'Visit website',
                'cta_url' => 'https://example.com',
                'attachment_media_ids' => '[]',
                'youtube_urls' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ])
            ->assertRedirect();

        Mail::assertQueued(CustomDonorMessageMail::class, function (CustomDonorMessageMail $mail): bool {
            return count($mail->youtubeVideos) === 1
                && $mail->youtubeVideos[0]['watch_url'] === 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        });

        $this->assertDatabaseHas('email_logs', [
            'donor_id' => $donor->id,
            'type' => EmailLog::TYPE_CUSTOM_DONOR_MESSAGE,
            'recipient' => 'nour@example.com',
        ]);
    }

    public function test_custom_email_renders_attachments_in_html(): void
    {
        $donor = Donor::query()->create([
            'name' => 'Sara',
            'email' => 'sara@example.com',
            'locale' => 'en',
        ]);

        $html = (new CustomDonorMessageMail(
            $donor,
            'Update',
            'Hello',
            null,
            null,
            ['https://example.com/photo.jpg'],
            [[
                'watch_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'thumbnail_url' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
            ]],
        ))->render();

        $this->assertStringContainsString('https://example.com/photo.jpg', $html);
        $this->assertStringContainsString('img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg', $html);
    }

    public function test_branded_payment_confirmed_email_renders_html(): void
    {
        $donor = Donor::query()->create([
            'name' => 'Sara',
            'email' => 'sara@example.com',
            'locale' => 'en',
        ]);

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-HTML1',
            'donor_id' => $donor->id,
            'amount' => 100,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_STRIPE_CARD,
            'gateway' => Donation::GATEWAY_STRIPE,
            'status' => Donation::STATUS_PAID,
            'donor_name' => 'Sara',
            'donor_email' => 'sara@example.com',
            'paid_at' => now(),
            'locale' => 'en',
        ]);

        $html = (new PaymentConfirmedMail($donation, $donor))->render();

        $this->assertStringContainsString('GHOSN-HTML1', $html);
        $this->assertStringContainsString('#0C5A2E', $html);
        $this->assertStringContainsString('border-radius:24px', $html);
    }

    public function test_admin_donors_index_requires_auth(): void
    {
        $this->get(route('admin.donors.index'))->assertRedirect();
    }

    public function test_admin_can_view_donors_list(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        Donor::query()->create([
            'name' => 'Test Donor',
            'email' => 'donor@example.com',
            'total_donated' => 200,
            'donations_count' => 2,
            'last_donation_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('admin.donors.index'))
            ->assertOk()
            ->assertSee('donor@example.com', false)
            ->assertSee(__('admin.donors.stat_total_donors'), false);
    }

    private function seedEmailAndBankSettings(): void
    {
        $settings = [
            'donations.enabled' => '1',
            'donations.bank_transfer_enabled' => '1',
            'payments.currency' => 'USD',
            'payments.min_amount' => '5',
            'payments.receipt_email' => 'admin@ghosn.test',
            'email.admin_notification_email' => 'admin@ghosn.test',
            'email.donor_receipts_enabled' => '1',
            'email.admin_alerts_enabled' => '1',
            'donations.iban' => 'PS00BANK00000000000000000000',
            'donations.bank_name_en' => 'Example Bank',
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['type' => 'string', 'value' => $value]);
        }

        app(SettingsService::class)->clearCache();
    }
}
