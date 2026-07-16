<?php

namespace App\Services\Pages;

use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionBlock;
use App\Support\LandingBlockHelper;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class PageBuilderService
{
    public function reorderSection(PageSection $section, string $direction): void
    {
        $this->reorderModel(
            PageSection::query()->where('page_id', $section->page_id),
            $section,
            $direction,
        );
    }

    public function reorderBlock(PageSectionBlock $block, string $direction): void
    {
        $this->reorderModel(
            PageSectionBlock::query()->where('page_section_id', $block->page_section_id),
            $block,
            $direction,
        );
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<PageSection|PageSectionBlock>  $query
     * @param  PageSection|PageSectionBlock  $model
     */
    private function reorderModel($query, $model, string $direction): void
    {
        if (! in_array($direction, ['up', 'down'], true)) {
            throw new InvalidArgumentException('Direction must be up or down.');
        }

        $neighbor = $direction === 'up'
            ? (clone $query)->where('sort_order', '<', $model->sort_order)->orderByDesc('sort_order')->first()
            : (clone $query)->where('sort_order', '>', $model->sort_order)->orderBy('sort_order')->first();

        if (! $neighbor) {
            return;
        }

        $currentOrder = $model->sort_order;
        $model->update(['sort_order' => $neighbor->sort_order]);
        $neighbor->update(['sort_order' => $currentOrder]);
    }

    public function findHomePage(): ?Page
    {
        return Page::query()
            ->where('slug', 'home')
            ->where('is_active', true)
            ->with([
                'sections' => fn ($q) => $q
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->with([
                        'blocks' => fn ($b) => $b
                            ->where('is_active', true)
                            ->orderBy('sort_order'),
                    ]),
            ])
            ->first();
    }

    /**
     * @return Collection<int, array{key: string, type: string, title_en: string, title_ar: string, sort_order: int, blocks: array<int, array<string, mixed>>}>
     */
    public function homeSectionsForRender(): Collection
    {
        $page = $this->findHomePage();

        if (! $page) {
            return collect();
        }

        return $page->sections->map(fn (PageSection $section) => [
            'key' => $section->key,
            'type' => $section->type,
            'title_en' => $section->title_en,
            'title_ar' => $section->title_ar,
            'sort_order' => $section->sort_order,
            'settings' => $section->settings ?? [],
            'blocks' => LandingBlockHelper::normalize($section->blocks),
        ]);
    }
}
