<?php

namespace Tests\Feature\Public;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicHomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, LandingPageSeeder::class]);
    }

    public function test_homepage_renders_react_landing_shell(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="ghosn-landing-root"', false)
            ->assertSee('__GHOSN_LANDING__', false);
    }

    public function test_builder_hero_edit_persists_in_database(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'hero')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.pages.sections.hero.update', [$page, $section]), [
                'title_en' => 'Hero',
                'title_ar' => 'الواجهة',
                'is_active' => '1',
                'content' => array_merge(config('hero.defaults', []), [
                    'title_line1_en' => 'Live builder headline',
                    'title_accent_en' => '',
                    'title_line1_ar' => 'عنوان مباشر',
                    'title_accent_ar' => '',
                ]),
            ])
            ->assertRedirect();

        $section->refresh();
        $this->assertSame('Live builder headline', $section->settings['content']['title_line1_en'] ?? null);
    }

    public function test_inactive_section_still_stored_in_builder(): void
    {
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'about')->firstOrFail();
        $section->update(['is_active' => false]);

        $section->refresh();
        $this->assertFalse((bool) $section->is_active);
    }
}
