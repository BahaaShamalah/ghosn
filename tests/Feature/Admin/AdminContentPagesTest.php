<?php

namespace Tests\Feature\Admin;

use App\Models\ContentPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContentPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::query()->where('email', 'admin@ghosn.test')->firstOrFail();
    }

    public function test_admin_can_view_content_pages_index_with_topbar(): void
    {
        $page = ContentPage::query()->where('slug', 'donation-policy')->firstOrFail();
        $page->update(['author_id' => $this->admin->id]);

        $this->actingAs($this->admin)
            ->get(route('admin.content-pages.index'))
            ->assertOk()
            ->assertSee(__('admin.cms.pages_title'), false)
            ->assertSee($page->title_en, false)
            ->assertSee(__('admin.topbar.my_profile'), false)
            ->assertSee(__('admin.nav.content_pages'), false);
    }

    public function test_admin_locale_switch_persists_in_session(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.locale.switch', 'ar'))
            ->assertRedirect();

        $this->assertSame('ar', session('admin_locale'));

        $this->actingAs($this->admin)
            ->get(route('admin.content-pages.index'))
            ->assertOk()
            ->assertSee(__('admin.cms.pages_title', [], 'ar'), false);
    }

    public function test_admin_can_filter_content_pages_by_status(): void
    {
        ContentPage::query()->create([
            'title_en' => 'Draft Page',
            'title_ar' => 'مسودة',
            'slug' => 'draft-page',
            'status' => ContentPage::STATUS_DRAFT,
        ]);

        ContentPage::query()->create([
            'title_en' => 'Live Page',
            'title_ar' => 'منشور',
            'slug' => 'live-page',
            'status' => ContentPage::STATUS_PUBLISHED,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.content-pages.index', ['status' => ContentPage::STATUS_DRAFT]))
            ->assertOk()
            ->assertSee('Draft Page', false)
            ->assertDontSee('Live Page', false);
    }

    public function test_admin_can_duplicate_content_page(): void
    {
        $page = ContentPage::query()->create([
            'title_en' => 'Policy',
            'title_ar' => 'سياسة',
            'slug' => 'policy',
            'status' => ContentPage::STATUS_PUBLISHED,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.content-pages.duplicate', $page))
            ->assertRedirect();

        $this->assertDatabaseHas('content_pages', [
            'title_en' => 'Policy',
            'status' => ContentPage::STATUS_DRAFT,
        ]);

        $this->assertSame(2, ContentPage::query()->where('title_en', 'Policy')->count());
    }

    public function test_admin_cannot_delete_protected_system_page(): void
    {
        $page = ContentPage::query()->where('slug', 'donation-policy')->firstOrFail();

        $this->actingAs($this->admin)
            ->delete(route('admin.content-pages.destroy', $page))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('content_pages', ['id' => $page->id, 'deleted_at' => null]);
    }

    public function test_admin_can_soft_delete_regular_content_page(): void
    {
        $page = ContentPage::query()->create([
            'title_en' => 'Removable',
            'title_ar' => 'قابل للحذف',
            'slug' => 'removable',
            'status' => ContentPage::STATUS_DRAFT,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.content-pages.destroy', $page))
            ->assertRedirect(route('admin.content-pages.index'))
            ->assertSessionHas('status');

        $this->assertSoftDeleted('content_pages', ['id' => $page->id]);
    }

    public function test_admin_can_bulk_publish_pages(): void
    {
        $first = ContentPage::query()->create([
            'title_en' => 'One',
            'title_ar' => 'واحد',
            'slug' => 'one',
            'status' => ContentPage::STATUS_DRAFT,
        ]);

        $second = ContentPage::query()->create([
            'title_en' => 'Two',
            'title_ar' => 'اثنان',
            'slug' => 'two',
            'status' => ContentPage::STATUS_DRAFT,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.content-pages.bulk'), [
                'action' => 'publish',
                'ids' => [$first->id, $second->id],
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('content_pages', ['id' => $first->id, 'status' => ContentPage::STATUS_PUBLISHED]);
        $this->assertDatabaseHas('content_pages', ['id' => $second->id, 'status' => ContentPage::STATUS_PUBLISHED]);
    }

    public function test_bulk_delete_skips_protected_pages(): void
    {
        $protected = ContentPage::query()->where('slug', 'donation-policy')->firstOrFail();

        $regular = ContentPage::query()->create([
            'title_en' => 'Extra',
            'title_ar' => 'إضافي',
            'slug' => 'extra',
            'status' => ContentPage::STATUS_DRAFT,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.content-pages.bulk'), [
                'action' => 'delete',
                'ids' => [$protected->id, $regular->id],
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('content_pages', ['id' => $protected->id, 'deleted_at' => null]);
        $this->assertSoftDeleted('content_pages', ['id' => $regular->id]);
    }

    public function test_admin_can_create_content_page_with_empty_slug(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.content-pages.store'), [
            'title_en' => 'About GHOSN',
            'title_ar' => 'عن غُصن',
            'slug' => '',
            'featured_image_media_id' => '',
            'status' => ContentPage::STATUS_DRAFT,
            'template' => ContentPage::TEMPLATE_DEFAULT,
            'content_en' => '<p>Welcome</p>',
            'content_ar' => '<p>مرحباً</p>',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('content_pages', [
            'title_en' => 'About GHOSN',
            'slug' => 'about-ghosn',
            'status' => ContentPage::STATUS_DRAFT,
        ]);
    }

    public function test_duplicate_title_generates_unique_slug(): void
    {
        ContentPage::query()->create([
            'title_en' => 'Annual Report',
            'title_ar' => 'التقرير السنوي',
            'slug' => 'annual-report',
            'template' => ContentPage::TEMPLATE_DEFAULT,
            'status' => ContentPage::STATUS_DRAFT,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.content-pages.store'), [
                'title_en' => 'Annual Report',
                'title_ar' => 'نسخة ثانية',
                'slug' => '',
                'featured_image_media_id' => '',
                'status' => ContentPage::STATUS_DRAFT,
                'template' => ContentPage::TEMPLATE_DEFAULT,
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('content_pages', [
            'title_en' => 'Annual Report',
            'slug' => 'annual-report-2',
        ]);
    }

    public function test_update_does_not_change_slug_when_only_title_is_updated(): void
    {
        $page = ContentPage::query()->create([
            'title_en' => 'Original Title',
            'title_ar' => 'العنوان الأصلي',
            'slug' => 'fixed-slug',
            'template' => ContentPage::TEMPLATE_DEFAULT,
            'status' => ContentPage::STATUS_DRAFT,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.content-pages.update', $page), [
                'title_en' => 'Updated Title',
                'title_ar' => 'العنوان المحدّث',
                'slug' => 'fixed-slug',
                'template' => ContentPage::TEMPLATE_DEFAULT,
                'featured_image_media_id' => '',
                'status' => ContentPage::STATUS_DRAFT,
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $page->refresh();

        $this->assertSame('fixed-slug', $page->slug);
        $this->assertSame('Updated Title', $page->title_en);
    }

    public function test_flash_toast_is_rendered_once_after_page_create(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.content-pages.store'), [
            'title_en' => 'Toast Test Page',
            'title_ar' => 'صفحة اختبار',
            'slug' => '',
            'featured_image_media_id' => '',
            'status' => ContentPage::STATUS_DRAFT,
            'template' => ContentPage::TEMPLATE_DEFAULT,
        ]);

        $response->assertRedirect()
            ->assertSessionHas('status')
            ->assertSessionMissing('error');

        $followUp = $this->get($response->headers->get('Location'));

        $followUp->assertOk();
        $this->assertSame(1, substr_count($followUp->getContent(), 'data-flash='));
        $this->assertSame(1, substr_count($followUp->getContent(), e(__('admin.cms.page_created'))));
        $this->assertSame(1, substr_count($followUp->getContent(), 'id="admin-toast-root"'));
    }

    public function test_admin_can_update_default_page(): void
    {
        $page = ContentPage::query()->create([
            'title_en' => 'Policy Page',
            'title_ar' => 'صفحة السياسة',
            'slug' => 'policy-page',
            'template' => ContentPage::TEMPLATE_DEFAULT,
            'content_en' => '<p>Original</p>',
            'content_ar' => '<p>أصلي</p>',
            'status' => ContentPage::STATUS_DRAFT,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.content-pages.update', $page), [
                'title_en' => 'Policy Page Updated',
                'title_ar' => 'صفحة محدّثة',
                'slug' => 'policy-page',
                'template' => ContentPage::TEMPLATE_DEFAULT,
                'featured_image_media_id' => '',
                'content_en' => '<p>Updated body</p>',
                'content_ar' => '<p>محتوى محدّث</p>',
                'status' => ContentPage::STATUS_PUBLISHED,
            ])
            ->assertRedirect()
            ->assertSessionHas('status')
            ->assertSessionMissing('error');

        $page->refresh();

        $this->assertSame('Policy Page Updated', $page->title_en);
        $this->assertSame('<p>Updated body</p>', $page->content_en);
        $this->assertSame(ContentPage::STATUS_PUBLISHED, $page->status);
    }

    public function test_admin_settings_about_redirects_to_builder_who_we_are(): void
    {
        $page = \App\Models\Page::query()->where('slug', 'who-we-are')->firstOrFail();

        $this->actingAs($this->admin)
            ->get(route('admin.settings.show', 'about'))
            ->assertRedirect(route('admin.pages.show', $page));
    }

    public function test_cms_about_content_page_is_not_seeded(): void
    {
        $this->assertDatabaseMissing('content_pages', ['slug' => 'about']);
    }

    public function test_validation_errors_redirect_back_with_field_errors_and_toast_payload(): void
    {
        $response = $this->actingAs($this->admin)->from(route('admin.content-pages.create'))
            ->post(route('admin.content-pages.store'), [
                'title_en' => '',
                'title_ar' => '',
                'slug' => 'admin',
                'template' => ContentPage::TEMPLATE_DEFAULT,
                'status' => ContentPage::STATUS_DRAFT,
            ]);

        $response->assertRedirect(route('admin.content-pages.create'))
            ->assertInvalid(['title_en', 'title_ar', 'slug'])
            ->assertSessionHas('error')
            ->assertSessionMissing('status');

        $followUp = $this->actingAs($this->admin)
            ->withSession($response->getSession()->all())
            ->get(route('admin.content-pages.create'));

        $followUp->assertOk();
        $this->assertSame(1, substr_count($followUp->getContent(), 'data-flash='));
        $followUp->assertSee(e(trans_choice('admin.validation_summary', 3, ['count' => 3])), false);
    }

    public function test_save_does_not_create_duplicate_flash_keys(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.content-pages.store'), [
            'title_en' => 'Single Flash Page',
            'title_ar' => 'صفحة واحدة',
            'slug' => 'single-flash-page',
            'template' => ContentPage::TEMPLATE_DEFAULT,
            'status' => ContentPage::STATUS_DRAFT,
        ]);

        $response->assertSessionHas('status');
        $this->assertFalse($response->getSession()->has('error'));
        $this->assertSame(1, count(array_filter(array_keys($response->getSession()->all()), fn (string $key) => in_array($key, ['status', 'error'], true))));
    }
}
