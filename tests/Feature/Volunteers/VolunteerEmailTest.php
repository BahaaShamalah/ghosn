<?php

namespace Tests\Feature\Volunteers;

use App\Mail\VolunteerTemplatedMail;
use App\Models\EmailLog;
use App\Models\User;
use App\Models\VolunteerApplication;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class VolunteerEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
    }

    public function test_submitting_application_queues_confirmation_and_admin_emails(): void
    {
        Mail::fake();

        $this->postJson(route('volunteer-applications.store'), [
            'name' => 'Sara Mansour',
            'email' => 'sara@example.com',
            'phone' => '+970599000000',
            'area' => 'field_relief',
            'message' => 'Ready to help.',
        ])->assertOk();

        Mail::assertQueued(VolunteerTemplatedMail::class, 2);

        $this->assertDatabaseHas('email_logs', [
            'type' => EmailLog::TYPE_VOLUNTEER_CONFIRMATION,
            'recipient' => 'sara@example.com',
        ]);

        $this->assertDatabaseHas('email_logs', [
            'type' => EmailLog::TYPE_VOLUNTEER_ADMIN_ALERT,
        ]);
    }

    public function test_approving_application_queues_welcome_email(): void
    {
        Mail::fake();

        $application = VolunteerApplication::query()->create([
            'name' => 'Test Volunteer',
            'email' => 'vol@example.com',
            'area' => 'logistics',
            'status' => VolunteerApplication::STATUS_PENDING,
            'locale' => 'en',
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('admin.volunteers.update-status', $application), ['status' => 'approved'])
            ->assertRedirect();

        Mail::assertQueued(VolunteerTemplatedMail::class, function (VolunteerTemplatedMail $mail): bool {
            return $mail->templateType === 'welcome'
                && $mail->application->email === 'vol@example.com';
        });
    }

    public function test_rejecting_application_queues_rejection_email(): void
    {
        Mail::fake();

        $application = VolunteerApplication::query()->create([
            'name' => 'Test Volunteer',
            'email' => 'vol@example.com',
            'area' => 'media',
            'status' => VolunteerApplication::STATUS_PENDING,
            'locale' => 'en',
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('admin.volunteers.update-status', $application), ['status' => 'rejected'])
            ->assertRedirect();

        Mail::assertQueued(VolunteerTemplatedMail::class, function (VolunteerTemplatedMail $mail): bool {
            return $mail->templateType === 'rejected';
        });
    }

    public function test_admin_can_open_volunteer_email_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.settings.show', 'volunteers'))
            ->assertOk()
            ->assertSee(__('admin.settings.volunteers_confirmation'), false);
    }
}
