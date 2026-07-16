<?php

namespace Tests\Feature\Public;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Support\ContentHtml;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\CampaignSeeder;
use Database\Seeders\CmsContentSeeder;
use Database\Seeders\LandingPageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentFormattingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            AdminUserSeeder::class,
            SettingsSeeder::class,
            LandingPageSeeder::class,
            CmsContentSeeder::class,
            CampaignSeeder::class,
        ]);
    }

    public function test_campaign_category_can_be_created_and_assigned(): void
    {
        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $this->actingAs($user)->post(route('admin.categories.store'), [
            'name_en' => 'Emergency Relief',
            'name_ar' => 'إغاثة طارئة',
            'type' => Category::TYPE_CAMPAIGN,
        ])->assertRedirect();

        $category = Category::query()->where('slug', 'emergency-relief')->firstOrFail();

        $this->actingAs($user)->post(route('admin.campaigns.store'), [
            'title_en' => 'Urgent Aid Campaign',
            'title_ar' => 'حملة عاجلة',
            'goal_amount' => 5000,
            'currency' => 'USD',
            'status' => Campaign::STATUS_ACTIVE,
            'category_id' => $category->id,
            'starts_at' => now()->toDateTimeString(),
        ])->assertRedirect();

        $this->assertDatabaseHas('campaigns', [
            'slug' => 'urgent-aid-campaign',
            'category_id' => $category->id,
        ]);
    }

    public function test_campaign_single_page_renders_content_layout_and_share_buttons(): void
    {
        $campaign = Campaign::query()->where('slug', 'winter-family-relief')->firstOrFail();

        $this->get(route('campaigns.show', $campaign->slug))
            ->assertOk()
            ->assertSee('public-content-shell', false)
            ->assertSee('gh-campaign-page', false)
            ->assertSee('gh-campaign-donate-panel', false)
            ->assertSee('prose-ghosn', false)
            ->assertSee('share-buttons', false)
            ->assertSee('data-share-copy', false)
            ->assertSee($campaign->title_en, false);
    }

    public function test_post_page_renders_prose_lists_and_share_buttons(): void
    {
        $post = Post::query()->published()->firstOrFail();
        $post->update([
            'content_en' => '<p>Intro</p><ul><li>First item</li><li>Second item</li></ul>',
        ]);

        $this->get(route('news.show', $post->slug))
            ->assertOk()
            ->assertSee('public-content-shell', false)
            ->assertSee('gh-post-header', false)
            ->assertSee('prose-ghosn', false)
            ->assertSee('<ul>', false)
            ->assertSee('First item', false)
            ->assertSee('share-buttons', false);
    }

    public function test_youtube_embed_renders_safely(): void
    {
        $html = ContentHtml::render('<div class="ghosn-embed" data-embed-type="youtube" data-embed-id="dQw4w9WgXcQ"></div>');

        $this->assertStringContainsString('ghosn-video-embed', $html);
        $this->assertStringContainsString('youtube-nocookie.com/embed/dQw4w9WgXcQ', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    public function test_unsafe_script_is_removed_on_save(): void
    {
        $clean = ContentHtml::sanitizeStorage('<p>Safe</p><script>alert("x")</script>');

        $this->assertStringContainsString('<p>Safe</p>', (string) $clean);
        $this->assertStringNotContainsString('<script', (string) $clean);
    }

    public function test_layout_does_not_use_dark_merging_hero(): void
    {
        $this->get(route('campaigns.show', 'winter-family-relief'))
            ->assertOk()
            ->assertDontSee('bg-ghosn-900 text-offwhite pt-28', false);
    }
}
