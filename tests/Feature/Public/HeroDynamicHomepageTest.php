<?php

namespace Tests\Feature\Public;

use App\Models\Media;
use App\Models\Page;
use App\Models\User;
use App\Support\HeroContent;
use App\Support\LandingReactPayload;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroDynamicHomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, LandingPageSeeder::class]);
    }

    public function test_editing_hero_title_is_resolved_from_builder_settings(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'hero')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.pages.sections.hero.update', [$page, $section]), $this->heroPayload([
                'title_line1_en' => 'Updated hero title',
                'title_accent_en' => 'Still growing.',
                'title_line1_ar' => 'عنوان محدّث',
                'title_accent_ar' => 'ما زال ينمو.',
            ]))
            ->assertRedirect();

        $section->refresh();
        $resolved = HeroContent::resolve($section->settings ?? null, null);

        $this->assertSame('Updated hero title', $resolved['title_line1_en']);
        $this->assertSame('Still growing.', $resolved['title_accent_en']);
    }

    public function test_hero_background_image_is_resolved_from_builder_settings(): void
    {
        Storage::fake('public');

        $background = Media::query()->create([
            'disk' => 'public',
            'path' => 'media/hero-bg.jpg',
            'filename' => 'hero-bg.jpg',
            'original_filename' => 'hero-bg.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'width' => 1920,
            'height' => 1080,
        ]);
        Storage::disk('public')->put($background->path, 'fake-image');

        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'hero')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.pages.sections.hero.update', [$page, $section]), $this->heroPayload([
                'background_media_id' => $background->id,
                'background_alt_en' => 'Relief team in the field',
            ]))
            ->assertRedirect();

        $section->refresh();
        $resolved = HeroContent::resolve($section->settings ?? null, null);

        $this->assertSame($background->url(), $resolved['background_image_url']);
        $this->assertSame('Relief team in the field', $resolved['background_alt_en']);
    }

    public function test_empty_hero_fields_fallback_to_defaults(): void
    {
        $defaults = config('hero.defaults');
        $resolved = HeroContent::resolve(['content' => []], []);

        $this->assertSame($defaults['eyebrow_en'], $resolved['eyebrow_en']);
        $this->assertSame($defaults['primary_button_url'], $resolved['primary_button_url']);
        $this->assertNull($resolved['background_image_url']);
    }

    public function test_hero_background_upload_from_editor_stores_in_media_library(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'hero')->firstOrFail();

        $background = UploadedFile::fake()->image('hero-bg.jpg', 1920, 1080);

        $this->actingAs($user)
            ->put(route('admin.pages.sections.hero.update', [$page, $section]), array_merge($this->heroPayload(), [
                'background_upload' => $background,
            ]))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $section->refresh();
        $mediaId = $section->settings['content']['background_media_id'] ?? null;

        $this->assertNotNull($mediaId);

        $media = Media::query()->findOrFail($mediaId);
        $this->assertStringStartsWith('image/', $media->mime_type);

        $resolved = HeroContent::resolve($section->settings ?? null, null);
        $this->assertSame($media->url(), $resolved['background_image_url']);
    }

    public function test_hero_content_is_included_in_react_payload(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'hero')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.pages.sections.hero.update', [$page, $section]), $this->heroPayload([
                'eyebrow_en' => 'Custom badge',
                'title_line1_en' => 'Custom title.',
                'title_accent_en' => 'Custom accent.',
            ]))
            ->assertRedirect();

        $payload = LandingReactPayload::build();

        $this->assertSame('Custom badge', $payload['hero']['badge']['en']);
        $this->assertSame('Custom title. Custom accent.', $payload['hero']['title']['en']);
    }

    public function test_hero_admin_editor_no_longer_shows_video_fields(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'hero')->firstOrFail();

        $this->actingAs($user)
            ->get(route('admin.pages.sections.hero.edit', [$page, $section]))
            ->assertOk()
            ->assertSee('name="content[background_media_id]"', false)
            ->assertDontSee('name="content[video_media_id]"', false)
            ->assertDontSee('name="video_upload"', false);
    }

    /**
     * @param  array<string, mixed>  $contentOverrides
     * @return array<string, mixed>
     */
    private function heroPayload(array $contentOverrides = []): array
    {
        $content = array_merge(config('hero.defaults', []), $contentOverrides);

        return [
            'title_en' => 'Hero',
            'title_ar' => 'الواجهة',
            'is_active' => '1',
            'content' => $content,
        ];
    }
}
