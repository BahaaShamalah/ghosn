<?php

namespace Tests\Feature\Public;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuilderPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, LandingPageSeeder::class]);
    }

    public function test_builder_lists_home_volunteer_who_we_are_and_contact_pages(): void
    {
        $this->assertSame(
            ['contact', 'home', 'volunteer', 'who-we-are'],
            Page::query()->orderBy('slug')->pluck('slug')->all(),
        );
    }

    public function test_volunteer_page_renders_full_volunteer_content(): void
    {
        $this->get(route('volunteer'))
            ->assertOk()
            ->assertSee('id="ghosn-landing-root"', false)
            ->assertSee('"pageType":"volunteer"', false)
            ->assertSee('Lend your hands. Grow lasting hope.', false)
            ->assertSee('Volunteering areas', false)
            ->assertSee('Volunteer application', false);
    }

    public function test_who_we_are_page_renders_react_about_section(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('id="ghosn-landing-root"', false)
            ->assertSee('"pageType":"about"', false)
            ->assertSee('Who We Are', false)
            ->assertSee('Giving that grows. Impact that lasts.', false);
    }

    public function test_join_us_redirects_to_volunteer(): void
    {
        $this->get('/join-us')
            ->assertRedirect(route('volunteer'));
    }

    public function test_admin_can_edit_volunteer_page_section(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'volunteer')->firstOrFail();
        $section = $page->sections()->where('key', 'volunteer')->firstOrFail();

        $this->actingAs($user)
            ->get(route('admin.pages.sections.volunteer.edit', [$page, $section]))
            ->assertOk();
    }

    public function test_admin_can_edit_who_we_are_about_section(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'who-we-are')->firstOrFail();
        $section = $page->sections()->where('key', 'about')->firstOrFail();

        $this->actingAs($user)
            ->get(route('admin.pages.sections.about.edit', [$page, $section]))
            ->assertRedirect(route('admin.pages.show', $page));

        $this->actingAs($user)
            ->get(route('admin.pages.show', $page))
            ->assertOk()
            ->assertSee(__('admin.settings.about_intro'), false)
            ->assertSee('name="about[page][hero][title_en]"', false)
            ->assertSee('data-media-picker', false)
            ->assertSee('data-media-dropzone', false)
            ->assertSee('data-media-library-open', false)
            ->assertSee('id="cms-media-modal"', false);
    }

    public function test_admin_can_edit_contact_page_in_builder(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'contact')->firstOrFail();

        $this->actingAs($user)
            ->get(route('admin.settings.show', 'contact'))
            ->assertRedirect(route('admin.pages.show', $page));

        $this->actingAs($user)
            ->get(route('admin.pages.show', $page))
            ->assertOk()
            ->assertSee(__('admin.settings.contact_intro'), false)
            ->assertSee('name="contact[page][form][title_en]"', false)
            ->assertSee('name="contact[page][sections][hero]"', false);
    }

    public function test_contact_page_renders_editable_form_copy(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('"pageType":"contact"', false)
            ->assertSee('Send us a message', false)
            ->assertSee('Turn your support into lasting impact', false);
    }
}
