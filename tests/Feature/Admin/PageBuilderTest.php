<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\LandingPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LandingPageSeeder::class);
    }

    public function test_admin_can_edit_section_meta(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $section = $page->sections()->where('key', 'impact')->firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.pages.sections.update', [$page, $section]), [
                'title_en' => 'Updated Impact',
                'title_ar' => 'أثر محدّث',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.pages.sections.edit', [$page, $section]));

        $this->assertDatabaseHas('page_sections', [
            'id' => $section->id,
            'title_en' => 'Updated Impact',
        ]);
    }

    public function test_admin_can_reorder_sections(): void
    {
        $user = User::factory()->create();
        $page = Page::query()->where('slug', 'home')->firstOrFail();
        $first = $page->sections()->orderBy('sort_order')->firstOrFail();
        $second = $page->sections()->orderBy('sort_order')->skip(1)->firstOrFail();

        $firstOrder = $first->sort_order;
        $secondOrder = $second->sort_order;

        $this->actingAs($user)
            ->patch(route('admin.pages.sections.reorder', [$page, $second]), [
                'direction' => 'up',
            ])
            ->assertRedirect(route('admin.pages.show', $page));

        $first->refresh();
        $second->refresh();

        $this->assertSame($secondOrder, $first->sort_order);
        $this->assertSame($firstOrder, $second->sort_order);
    }
}
