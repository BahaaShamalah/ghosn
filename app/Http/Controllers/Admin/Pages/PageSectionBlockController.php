<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Pages\ReorderRequest;
use App\Http\Requests\Admin\Pages\UpdatePageSectionBlockRequest;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionBlock;
use App\Services\Media\MediaService;
use App\Services\Pages\PageBuilderService;
use Illuminate\Http\RedirectResponse;

class PageSectionBlockController extends Controller
{
    public function __construct(
        private readonly PageBuilderService $builder,
        private readonly MediaService $media,
    ) {}

    public function update(
        UpdatePageSectionBlockRequest $request,
        Page $page,
        PageSection $section,
        PageSectionBlock $block,
    ): RedirectResponse {
        abort_unless($section->page_id === $page->id, 404);
        abort_unless($block->page_section_id === $section->id, 404);

        $validated = $request->validated();
        $content = array_merge($block->content ?? [], $validated['content'] ?? []);

        if ($request->hasFile('image_upload') && $block->type === 'image') {
            $uploaded = $this->media->upload($request->file('image_upload'));
            $content['media_id'] = $uploaded->id;
        }

        if (array_key_exists('media_id', $content) && empty($content['media_id'])) {
            unset($content['media_id']);
        }

        $block->update([
            'content' => $content,
            'is_active' => $validated['is_active'] ?? $block->is_active,
        ]);

        return redirect()
            ->route('admin.pages.sections.edit', [$page, $section])
            ->with('status', __('admin.pages.block_saved'));
    }

    public function reorder(
        ReorderRequest $request,
        Page $page,
        PageSection $section,
        PageSectionBlock $block,
    ): RedirectResponse {
        abort_unless($section->page_id === $page->id, 404);
        abort_unless($block->page_section_id === $section->id, 404);

        $this->builder->reorderBlock($block, $request->validated('direction'));

        return redirect()
            ->route('admin.pages.sections.edit', [$page, $section])
            ->with('status', __('admin.pages.reordered'));
    }
}
