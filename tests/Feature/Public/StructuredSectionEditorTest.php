<?php

namespace Tests\Feature\Public;

use App\Models\Page;
use App\Models\User;
use App\Support\LandingPageContent;
use App\Support\SectionContent;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StructuredSectionEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, LandingPageSeeder::class]);
    }

    public function test_structured_sections_have_full_editor(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();

        foreach (['impact', 'how_works', 'ways', 'testimonials', 'join'] as $key) {
            $section = $page->sections()->where('key', $key)->firstOrFail();

            $this->actingAs($user)
                ->get(route('admin.pages.sections.content.edit', [$page, $section]))
                ->assertOk();
        }
    }

    public function test_homepage_has_only_react_aligned_sections(): void
    {
        $page = Page::query()->where('slug', 'home')->firstOrFail();

        $this->assertSame(
            ['hero', 'about', 'impact', 'how_works', 'ways', 'testimonials', 'campaigns', 'latest_news', 'join'],
            $page->sections()->orderBy('sort_order')->pluck('key')->all(),
        );
    }

    public function test_impact_section_update_persists_in_builder(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'impact')->firstOrFail();
        $defaults = config('sections.impact.defaults');

        $this->actingAs($user)
            ->put(route('admin.pages.sections.content.update', [$page, $section]), [
                'title_en' => 'Impact',
                'title_ar' => 'الأثر',
                'content' => [
                    'title_en' => 'Builder impact title',
                    'title_ar' => 'عنوان الأثر',
                    'stats' => [
                        [
                            'end' => '200000',
                            'label_en' => 'People helped',
                            'label_ar' => 'أشخاص تمت مساعدتهم',
                        ],
                    ],
                ],
                'is_active' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $resolved = SectionContent::resolve('impact', $section->fresh()->settings ?? null, null);
        $this->assertSame('Builder impact title', $resolved['title_en']);
        $this->assertSame(200000.0, (float) $resolved['stats'][0]['end']);
        $this->assertSame('People helped', $resolved['stats'][0]['label_en']);
        $this->assertSame($defaults['stats'][0]['key'], $resolved['stats'][0]['key']);
        $this->assertSame($defaults['stats'][0]['suffix'], $resolved['stats'][0]['suffix']);
    }

    public function test_how_works_steps_update_persists_in_builder(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'how_works')->firstOrFail();
        $defaults = config('sections.how_works.defaults');

        $this->actingAs($user)
            ->put(route('admin.pages.sections.content.update', [$page, $section]), [
                'title_en' => 'How it works',
                'title_ar' => 'كيف يعمل',
                'content' => [
                    'heading_en' => $defaults['heading_en'],
                    'heading_ar' => $defaults['heading_ar'],
                    'steps' => [
                        array_merge($defaults['steps'][0], ['title_en' => 'Custom step title']),
                        $defaults['steps'][1],
                        $defaults['steps'][2],
                    ],
                ],
                'is_active' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $resolved = SectionContent::resolve('how_works', $section->fresh()->settings ?? null, null);
        $this->assertSame('Custom step title', $resolved['steps'][0]['title_en'] ?? null);
    }

    public function test_builder_content_is_included_in_react_payload(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'ways')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.pages.sections.content.update', [$page, $section]), [
                'title_en' => 'Ways',
                'title_ar' => 'طرق',
                'content' => [
                    'heading_en' => 'Custom ways heading',
                    'heading_ar' => 'عنوان طرق',
                ],
                'is_active' => '1',
            ])
            ->assertRedirect();

        $payload = LandingPageContent::forReact();
        $this->assertSame('Custom ways heading', $payload['ways']['title']['en']);
    }

    public function test_react_landing_payload_is_valid_json(): void
    {
        $response = $this->get(route('home'))->assertOk();

        $this->assertStringContainsString('id="ghosn-landing-root"', $response->getContent());
        $this->assertStringContainsString('__GHOSN_LANDING__', $response->getContent());
        $this->assertStringContainsString('"impact"', $response->getContent());
        $this->assertStringContainsString('"howWorks"', $response->getContent());
    }
}
