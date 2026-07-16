<?php



namespace Tests\Feature\Cms;



use App\Models\Category;

use App\Models\ContentPage;

use App\Models\Media;

use App\Models\Post;

use App\Models\User;

use Database\Seeders\AdminUserSeeder;

use Database\Seeders\CmsContentSeeder;

use Database\Seeders\LandingPageSeeder;

use Database\Seeders\SettingsSeeder;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\Storage;

use Tests\Concerns\AssertsReactLandingPayload;

use Tests\TestCase;



class CmsTest extends TestCase

{

    use AssertsReactLandingPayload;

    use RefreshDatabase;



    protected function setUp(): void

    {

        parent::setUp();



        $this->seed([AdminUserSeeder::class, SettingsSeeder::class, LandingPageSeeder::class, CmsContentSeeder::class]);

    }



    public function test_news_index_shows_published_posts_only(): void

    {

        Post::query()->create([

            'title_en' => 'Draft Story',

            'title_ar' => 'مسودة',

            'slug' => 'draft-story',

            'status' => Post::STATUS_DRAFT,

        ]);



        $response = $this->get(route('news.index'));



        $response->assertOk();

        $response->assertSee('Winter relief distribution begins', false);

        $response->assertDontSee('Draft Story', false);

    }



    public function test_draft_post_is_not_publicly_accessible(): void

    {

        $post = Post::query()->create([

            'title_en' => 'Hidden Story',

            'title_ar' => 'خبر مخفي',

            'slug' => 'hidden-story',

            'status' => Post::STATUS_DRAFT,

        ]);



        $this->get(route('news.show', $post->slug))->assertNotFound();

    }



    public function test_future_published_post_is_hidden_from_public_and_homepage(): void

    {

        Post::query()->create([

            'title_en' => 'Future Story',

            'title_ar' => 'خبر مستقبلي',

            'slug' => 'future-story',

            'status' => Post::STATUS_PUBLISHED,

            'published_at' => now()->addDay(),

        ]);



        $this->get(route('news.index'))->assertDontSee('Future Story', false);

        $this->get(route('news.show', 'future-story'))->assertNotFound();

        $payload = $this->landingPayloadFromResponse($this->get(route('home'))->assertOk());

        $this->assertStringNotContainsString('Future Story', json_encode($payload['posts'] ?? []));

    }



    public function test_single_post_renders(): void

    {

        $post = Post::query()->published()->firstOrFail();



        $this->get(route('news.show', $post->slug))

            ->assertOk()

            ->assertSee($post->title_en, false);

    }



    public function test_official_page_renders(): void

    {

        $this->get(route('about'))

            ->assertOk()

            ->assertSee('Who We Are', false)

            ->assertSee('id="ghosn-landing-root"', false);

    }



    public function test_admin_can_create_post(): void

    {

        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $category = Category::query()->firstOrFail();



        $response = $this->actingAs($user)->post(route('admin.posts.store'), [

            'title_en' => 'New GHOSN Update',

            'title_ar' => 'تحديث جديد',

            'excerpt_en' => 'Short excerpt',

            'excerpt_ar' => 'مقتطف قصير',

            'content_en' => '<p>Body content</p>',

            'content_ar' => '<p>محتوى</p>',

            'category_id' => $category->id,

            'status' => Post::STATUS_PUBLISHED,

            'published_at' => now()->toDateTimeString(),

        ]);



        $response->assertRedirect();

        $this->assertDatabaseHas('posts', [

            'slug' => 'new-ghosn-update',

            'status' => Post::STATUS_PUBLISHED,

        ]);

    }



    public function test_admin_posts_index_shows_actions(): void

    {

        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $post = Post::query()->published()->firstOrFail();



        $this->actingAs($user)

            ->get(route('admin.posts.index'))

            ->assertOk()

            ->assertSee(route('admin.posts.edit', $post), false)

            ->assertSee(route('admin.posts.preview', $post), false)

            ->assertSee(route('news.show', $post->slug), false)

            ->assertSee(__('admin.cms.delete'), false);

    }



    public function test_featured_image_can_be_assigned_from_media(): void

    {

        Storage::fake('public');

        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();



        $file = UploadedFile::fake()->image('featured.jpg', 800, 500);

        $path = $file->store('media', 'public');



        $media = Media::query()->create([

            'disk' => 'public',

            'path' => $path,

            'filename' => basename($path),

            'original_filename' => 'featured.jpg',

            'mime_type' => 'image/jpeg',

            'size' => 1200,

        ]);



        $this->actingAs($user)->post(route('admin.posts.store'), [

            'title_en' => 'Post With Image',

            'title_ar' => 'مقال بصورة',

            'status' => Post::STATUS_PUBLISHED,

            'published_at' => now()->toDateTimeString(),

            'featured_image_media_id' => $media->id,

        ])->assertRedirect();



        $this->assertDatabaseHas('posts', [

            'slug' => 'post-with-image',

            'featured_image_media_id' => $media->id,

        ]);

    }



    public function test_editor_html_is_sanitized_on_save(): void

    {

        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();



        $this->actingAs($user)->post(route('admin.posts.store'), [

            'title_en' => 'Sanitized Post',

            'title_ar' => 'مقال',

            'status' => Post::STATUS_DRAFT,

            'content_en' => '<p>Safe</p><script>alert(1)</script><img src="javascript:alert(1)">',

        ])->assertRedirect();



        $post = Post::query()->where('slug', 'sanitized-post')->firstOrFail();



        $this->assertStringContainsString('<p>Safe</p>', (string) $post->content_en);

        $this->assertStringNotContainsString('<script>', (string) $post->content_en);

        $this->assertStringNotContainsString('javascript:', (string) $post->content_en);

    }



    public function test_slug_uniqueness_is_enforced_for_posts(): void

    {

        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();



        $this->actingAs($user)->post(route('admin.posts.store'), [

            'title_en' => 'Duplicate Slug Post',

            'title_ar' => 'مقال',

            'slug' => 'winter-relief-distribution-begins',

            'status' => Post::STATUS_DRAFT,

        ])->assertSessionHasErrors('slug');

    }



    public function test_latest_news_appears_on_homepage(): void

    {

        $response = $this->get(route('home'))->assertOk();

        $payload = $this->landingPayloadFromResponse($response);

        $this->assertNotEmpty($payload['posts']);

        $this->assertStringContainsString('Winter relief distribution begins', json_encode($payload['posts']));

        $this->assertSame(route('news.index'), $payload['routes']['news'] ?? null);

    }



    public function test_published_post_appears_on_homepage_immediately_after_publish(): void

    {

        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();



        $this->actingAs($user)->post(route('admin.posts.store'), [

            'title_en' => 'Immediate Homepage Story',

            'title_ar' => 'خبر فوري',

            'excerpt_en' => 'Appears right away',

            'excerpt_ar' => 'يظهر فوراً',

            'status' => Post::STATUS_PUBLISHED,

            'published_at' => now()->toDateTimeString(),

        ])->assertRedirect();



        $payload = $this->landingPayloadFromResponse($this->get(route('home'))->assertOk());

        $this->assertStringContainsString('Immediate Homepage Story', json_encode($payload['posts'] ?? []));

    }



    public function test_draft_content_page_is_hidden(): void

    {

        ContentPage::query()->create([

            'title_en' => 'Hidden Page',

            'title_ar' => 'صفحة مخفية',

            'slug' => 'hidden-page',

            'status' => ContentPage::STATUS_DRAFT,

        ]);



        $this->get(route('pages.show', 'hidden-page'))->assertNotFound();

    }



    public function test_admin_can_soft_delete_post(): void

    {

        $user = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();

        $post = Post::query()->published()->firstOrFail();



        $this->actingAs($user)

            ->delete(route('admin.posts.destroy', $post))

            ->assertRedirect(route('admin.posts.index'));



        $this->assertSoftDeleted('posts', ['id' => $post->id]);

        $this->get(route('news.show', $post->slug))->assertNotFound();

    }

}

