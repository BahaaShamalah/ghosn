<?php

namespace Tests\Feature\Public;

use App\Models\Media;
use App\Support\AboutContent;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AboutSectionImageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, LandingPageSeeder::class]);
    }

    public function test_about_section_has_full_editor_in_admin(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'about')->firstOrFail();

        $this->actingAs($user)
            ->get(route('admin.pages.sections.about.edit', [$page, $section]))
            ->assertOk()
            ->assertSee(__('admin.pages.about_edit'), false)
            ->assertSee('name="content[heading_en]"', false)
            ->assertSee('name="content[paragraphs_en]"', false)
            ->assertSee('name="content[video_url]"', false)
            ->assertSee('name="content[video_cover_media_id]"', false);
    }

    public function test_youtube_video_url_is_resolved_for_about_section(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'about')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.pages.sections.about.update', [$page, $section]), [
                'title_en' => 'Who We Are',
                'title_ar' => 'من نحن',
                'content' => [
                    'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                ],
                'is_active' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $resolved = \App\Support\AboutContent::resolve($section->fresh()->settings ?? null, null);

        $this->assertSame('youtube', $resolved['video_embed_provider']);
        $this->assertStringContainsString('youtube-nocookie.com/embed/dQw4w9WgXcQ', (string) $resolved['video_embed_url']);
        $this->assertStringContainsString('i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg', (string) $resolved['video_poster_url']);
    }

    public function test_generic_about_edit_redirects_to_dedicated_editor(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'about')->firstOrFail();

        $this->actingAs($user)
            ->get(route('admin.pages.sections.edit', [$page, $section]))
            ->assertRedirect(route('admin.pages.sections.about.edit', [$page, $section]));
    }

    public function test_uploaded_about_image_appears_on_homepage(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'about')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.pages.sections.about.update', [$page, $section]), [
                'title_en' => 'Who We Are',
                'title_ar' => 'من نحن',
                'content' => [
                    'heading_en' => 'Human first — in every step.',
                    'heading_ar' => 'الإنسان أولاً... في كلِّ خطوة',
                    'image_alt_en' => 'Team photo',
                    'image_alt_ar' => 'صورة الفريق',
                ],
                'image_upload' => UploadedFile::fake()->image('about-team.jpg', 800, 1000),
                'is_active' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $section->refresh();
        $mediaId = $section->settings['content']['image_media_id'] ?? null;
        $this->assertNotEmpty($mediaId);

        $media = Media::query()->findOrFail($mediaId);
        $this->assertStringContainsString('about-team', $media->original_filename);
    }

    public function test_about_text_updates_persist_in_builder(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'about')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.pages.sections.about.update', [$page, $section]), [
                'title_en' => 'Who We Are',
                'title_ar' => 'من نحن',
                'content' => [
                    'heading_en' => 'Builder about heading EN',
                    'heading_ar' => 'عنوان من نحن',
                    'paragraphs_en' => "Custom paragraph one.\n\nSecond paragraph from builder.",
                    'paragraphs_ar' => "فقرة مخصصة.\n\nفقرة ثانية.",
                ],
                'is_active' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $section->refresh();
        $this->assertSame('Builder about heading EN', $section->settings['content']['heading_en'] ?? null);
        $this->assertStringContainsString('Custom paragraph one.', $section->settings['content']['paragraphs_en'] ?? '');

        $react = AboutContent::forReact($section->settings ?? null, null);
        $this->assertSame('Custom paragraph one.', $react['paragraphs']['en'][0] ?? null);
        $this->assertSame('Second paragraph from builder.', $react['paragraphs']['en'][1] ?? null);
    }

    public function test_about_paragraphs_can_be_cleared_without_defaults_returning(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'about')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.pages.sections.about.update', [$page, $section]), [
                'title_en' => 'Who We Are',
                'title_ar' => 'من نحن',
                'content' => [
                    'paragraphs_en' => "Only one paragraph remains.\n\n",
                    'paragraphs_ar' => "فقرة واحدة فقط.\n\n",
                ],
                'is_active' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $react = AboutContent::forReact($section->fresh()->settings ?? null, null);

        $this->assertSame(['Only one paragraph remains.'], $react['paragraphs']['en']);
        $this->assertSame(['فقرة واحدة فقط.'], $react['paragraphs']['ar']);
    }
}
