<?php

namespace Tests\Feature\Public;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
    }

    public function test_visitor_can_subscribe_to_newsletter(): void
    {
        $this->postJson(route('newsletter-subscriptions.store'), [
            'email' => 'subscriber@example.com',
        ])->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'subscriber@example.com',
        ]);
    }

    public function test_duplicate_subscription_is_idempotent(): void
    {
        NewsletterSubscriber::query()->create([
            'email' => 'existing@example.com',
            'locale' => 'en',
        ]);

        $this->postJson(route('newsletter-subscriptions.store'), [
            'email' => 'existing@example.com',
        ])->assertOk();

        $this->assertSame(1, NewsletterSubscriber::query()->where('email', 'existing@example.com')->count());
    }

    public function test_admin_can_view_newsletter_subscribers(): void
    {
        NewsletterSubscriber::query()->create([
            'email' => 'admin-test@example.com',
            'locale' => 'en',
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.newsletter.index'))
            ->assertOk()
            ->assertSee('admin-test@example.com', false);
    }
}
