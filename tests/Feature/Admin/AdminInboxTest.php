<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\VolunteerApplication;
use Database\Seeders\AdminInboxSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_volunteers_index(): void
    {
        $this->seed(AdminInboxSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.volunteers.index'))
            ->assertOk()
            ->assertSee(__('admin.volunteers.title'), false)
            ->assertSee('Nadia Farouk', false);
    }

    public function test_admin_can_approve_volunteer_application(): void
    {
        $application = VolunteerApplication::query()->create([
            'name' => 'Test Volunteer',
            'email' => 'vol@example.com',
            'area' => 'logistics',
            'status' => VolunteerApplication::STATUS_PENDING,
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('admin.volunteers.update-status', $application), ['status' => 'approved'])
            ->assertRedirect();

        $this->assertSame('approved', $application->fresh()->status);
    }

    public function test_admin_can_view_messages_index(): void
    {
        $this->seed(AdminInboxSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.messages.index'))
            ->assertOk()
            ->assertSee(__('admin.messages.title'), false)
            ->assertSee('Partnership proposal', false);
    }

    public function test_public_can_submit_volunteer_application(): void
    {
        $this->postJson(route('volunteer-applications.store'), [
            'name' => 'Sara Mansour',
            'email' => 'sara@example.com',
            'area' => 'field_relief',
            'message' => 'Ready to help.',
        ])->assertOk();

        $this->assertDatabaseHas('volunteer_applications', [
            'email' => 'sara@example.com',
            'area' => 'field_relief',
        ]);
    }
}
