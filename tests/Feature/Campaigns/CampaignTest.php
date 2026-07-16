<?php

namespace Tests\Feature\Campaigns;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Media;
use App\Models\User;
use App\Services\Donations\DonationService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\CampaignSeeder;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AssertsReactLandingPayload;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use AssertsReactLandingPayload;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([AdminUserSeeder::class, SettingsSeeder::class, LandingPageSeeder::class, CampaignSeeder::class]);
    }

    public function test_active_campaigns_appear_on_public_index(): void
    {
        $this->get(route('campaigns.index'))
            ->assertOk()
            ->assertSee('Winter Family Relief', false)
            ->assertSee('School Supplies Drive', false)
            ->assertDontSee('Draft Campaign Hidden', false);
    }

    public function test_draft_campaign_is_hidden_publicly(): void
    {
        $this->get(route('campaigns.show', 'draft-campaign-hidden'))->assertNotFound();
    }

    public function test_featured_campaigns_appear_on_homepage(): void
    {
        $response = $this->get(route('home'))->assertOk();
        $payload = $this->landingPayloadFromResponse($response);

        $this->assertNotEmpty($payload['campaigns']);
        $this->assertStringContainsString('Winter Family Relief', json_encode($payload['campaigns']));
        $this->assertSame(route('campaigns.index'), $payload['routes']['campaigns'] ?? null);
    }

    public function test_campaign_page_renders_progress(): void
    {
        $campaign = Campaign::query()->where('slug', 'winter-family-relief')->firstOrFail();

        $this->get(route('campaigns.show', $campaign->slug))
            ->assertOk()
            ->assertSee($campaign->formattedRaised(), false)
            ->assertSee($campaign->formattedGoal(), false)
            ->assertSee('Donate to this campaign', false);
    }

    public function test_paid_donation_updates_campaign_raised_amount(): void
    {
        $campaign = Campaign::query()->where('slug', 'winter-family-relief')->firstOrFail();
        $raisedBefore = (float) $campaign->raised_amount;
        $donorsBefore = $campaign->donors_count;

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-TESTCAM1',
            'campaign_id' => $campaign->id,
            'amount' => 150,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_STRIPE_CARD,
            'gateway' => Donation::GATEWAY_STRIPE,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Campaign Donor',
            'donor_email' => 'donor@example.com',
        ]);

        app(DonationService::class)->markPaid($donation);

        $campaign->refresh();

        $this->assertEquals($raisedBefore + 150, (float) $campaign->raised_amount);
        $this->assertEquals($donorsBefore + 1, $campaign->donors_count);
    }

    public function test_pending_bank_transfer_does_not_update_campaign_until_confirmed(): void
    {
        $campaign = Campaign::query()->where('slug', 'school-supplies-drive')->firstOrFail();
        $raisedBefore = (float) $campaign->raised_amount;

        $donation = Donation::query()->create([
            'reference' => 'GHOSN-TESTCAM2',
            'campaign_id' => $campaign->id,
            'amount' => 200,
            'currency' => 'USD',
            'payment_method' => Donation::METHOD_BANK,
            'gateway' => Donation::GATEWAY_BANK,
            'status' => Donation::STATUS_PENDING,
            'donor_name' => 'Bank Donor',
            'donor_email' => 'bank@example.com',
        ]);

        $campaign->refresh();
        $this->assertEquals($raisedBefore, (float) $campaign->raised_amount);

        app(DonationService::class)->markBankTransferConfirmed($donation);

        $campaign->refresh();
        $this->assertEquals($raisedBefore + 200, (float) $campaign->raised_amount);
    }

    public function test_donate_page_shows_campaign_when_slug_provided(): void
    {
        $this->get(route('donate', ['campaign' => 'winter-family-relief']))
            ->assertOk()
            ->assertSee('Winter Family Relief', false)
            ->assertSee('name="campaign_id"', false);
    }

    public function test_admin_can_create_campaign(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $this->actingAs($user)->post(route('admin.campaigns.store'), [
            'title_en' => 'Emergency Medical Fund',
            'title_ar' => 'صندوق طبي طارئ',
            'goal_amount' => 8000,
            'currency' => 'USD',
            'status' => Campaign::STATUS_ACTIVE,
            'starts_at' => now()->toDateTimeString(),
        ])->assertRedirect();

        $this->assertDatabaseHas('campaigns', [
            'slug' => 'emergency-medical-fund',
            'status' => Campaign::STATUS_ACTIVE,
        ]);
    }

    public function test_admin_can_add_video_when_creating_campaign(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();
        $video = Media::query()->create([
            'disk' => 'public',
            'path' => 'media/campaign-story.mp4',
            'filename' => 'campaign-story.mp4',
            'original_filename' => 'campaign-story.mp4',
            'mime_type' => 'video/mp4',
            'size' => 1024,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)->post(route('admin.campaigns.store'), [
            'title_en' => 'Campaign With Video',
            'title_ar' => 'حملة مع فيديو',
            'goal_amount' => 5000,
            'currency' => 'USD',
            'status' => Campaign::STATUS_ACTIVE,
            'starts_at' => now()->subMinute()->toDateTimeString(),
            'video_media_id' => $video->id,
        ])->assertRedirect();

        $campaign = Campaign::query()->where('slug', 'campaign-with-video')->firstOrFail();

        $this->assertSame($video->id, $campaign->video_media_id);
        $this->get(route('campaigns.show', $campaign->slug))
            ->assertOk()
            ->assertSee('campaign-story.mp4', false)
            ->assertSee('<video', false);
    }
}
