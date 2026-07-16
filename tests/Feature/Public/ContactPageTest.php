<?php

namespace Tests\Feature\Public;

use App\Mail\ContactMessageAdminMail;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
    }

    public function test_contact_page_renders(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('ghosn-landing-root', false);
    }

    public function test_contact_form_creates_message(): void
    {
        Mail::fake();

        $this->postJson(route('contact-messages.store'), [
            'name' => 'Sara',
            'email' => 'sara@example.com',
            'subject' => 'General inquiry',
            'message' => 'Hello from the contact page.',
        ])->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'sara@example.com',
            'subject' => 'General inquiry',
            'is_read' => false,
        ]);

        Mail::assertQueued(ContactMessageAdminMail::class);
    }

    public function test_navigation_includes_contact_link_by_default(): void
    {
        $links = \App\Support\PublicNavigation::links();

        $this->assertTrue(
            collect($links)->contains(fn (array $link): bool => $link['href'] === route('contact'))
        );
    }
}
