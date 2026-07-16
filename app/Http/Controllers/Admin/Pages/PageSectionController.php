<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Pages\ReorderRequest;
use App\Http\Requests\Admin\Pages\UpdatePageSectionAboutRequest;
use App\Http\Requests\Admin\Pages\UpdatePageSectionContentRequest;
use App\Http\Requests\Admin\Pages\UpdatePageSectionHeroRequest;
use App\Http\Requests\Admin\Pages\UpdatePageSectionRequest;
use App\Http\Requests\Admin\Pages\UpdatePageSectionVolunteerRequest;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Services\Media\MediaService;
use App\Services\Pages\PageBuilderService;
use App\Support\AboutContent;
use App\Support\HeroContent;
use App\Support\HtmlText;
use App\Support\LandingBlockHelper;
use App\Support\SectionContent;
use App\Support\VolunteerPageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageSectionController extends Controller
{
    public function __construct(
        private readonly PageBuilderService $builder,
        private readonly MediaService $media,
    ) {}

    public function edit(Page $page, PageSection $section): View|RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);

        if ($section->key === 'hero') {
            return redirect()->route('admin.pages.sections.hero.edit', [$page, $section]);
        }

        if ($section->key === 'about') {
            return redirect()->route('admin.pages.sections.about.edit', [$page, $section]);
        }

        if ($section->key === 'volunteer') {
            return redirect()->route('admin.pages.sections.volunteer.edit', [$page, $section]);
        }

        if (SectionContent::isStructured($section->key)) {
            return redirect()->route('admin.pages.sections.content.edit', [$page, $section]);
        }

        $section->load('blocks');

        return view('admin.pages.sections.edit', [
            'page' => $page,
            'section' => $section,
            'mediaLibrary' => Media::query()->latest()->limit(200)->get(),
        ]);
    }

    public function editHero(Page $page, PageSection $section): View
    {
        abort_unless($section->page_id === $page->id, 404);
        abort_unless($section->key === 'hero', 404);

        $section->load('blocks');

        $heroContent = HeroContent::resolve(
            $section->settings,
            LandingBlockHelper::normalize($section->blocks),
        );

        return view('admin.pages.sections.hero.edit', [
            'page' => $page,
            'section' => $section,
            'mediaLibrary' => Media::query()->latest()->limit(200)->get(),
            'heroContent' => $heroContent,
        ]);
    }

    public function editContent(Page $page, PageSection $section): View
    {
        abort_unless($section->page_id === $page->id, 404);
        abort_unless(SectionContent::isStructured($section->key), 404);

        $section->load('blocks');
        $schema = SectionContent::config($section->key);

        return view('admin.pages.sections.structured.edit', [
            'page' => $page,
            'section' => $section,
            'schema' => $schema,
            'sectionContent' => SectionContent::resolve(
                $section->key,
                $section->settings,
                LandingBlockHelper::normalize($section->blocks),
            ),
        ]);
    }

    public function updateContent(UpdatePageSectionContentRequest $request, Page $page, PageSection $section): RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);
        abort_unless(SectionContent::isStructured($section->key), 404);

        $validated = $request->validated();

        $section->update([
            'title_en' => $validated['title_en'],
            'title_ar' => $validated['title_ar'],
            'is_active' => $validated['is_active'] ?? $section->is_active,
            'settings' => array_merge($section->settings ?? [], [
                'content' => array_merge(
                    $section->settings['content'] ?? [],
                    HtmlText::cleanArray($validated['content'] ?? []),
                ),
            ]),
        ]);

        return redirect()
            ->route('admin.pages.sections.content.edit', [$page, $section])
            ->with('status', __('admin.pages.section_content_saved'));
    }

    public function editAbout(Page $page, PageSection $section): View|RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);
        abort_unless($section->key === 'about', 404);

        if ($page->slug === 'who-we-are') {
            return redirect()->route('admin.pages.show', $page);
        }

        $section->load('blocks');

        $aboutContent = AboutContent::resolve(
            $section->settings,
            LandingBlockHelper::normalize($section->blocks),
        );

        return view('admin.pages.sections.about.edit', [
            'page' => $page,
            'section' => $section,
            'mediaLibrary' => Media::query()->latest()->limit(200)->get(),
            'aboutContent' => $aboutContent,
        ]);
    }

    public function updateAbout(UpdatePageSectionAboutRequest $request, Page $page, PageSection $section): RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);
        abort_unless($section->key === 'about', 404);

        $validated = $request->validated();
        $content = $this->normalizeAboutContent($validated['content'] ?? []);
        $content = $this->applyAboutMediaUploads($request, $content);
        $content = HtmlText::cleanArray($content);

        $existingContent = is_array($section->settings['content'] ?? null)
            ? $section->settings['content']
            : [];
        $mergedContent = array_merge($existingContent, $content);

        foreach ([1, 2, 3] as $index) {
            unset($mergedContent["paragraph{$index}_en"], $mergedContent["paragraph{$index}_ar"]);
        }

        $section->update([
            'title_en' => $validated['title_en'],
            'title_ar' => $validated['title_ar'],
            'is_active' => $validated['is_active'] ?? $section->is_active,
            'settings' => array_merge($section->settings ?? [], [
                'content' => $mergedContent,
            ]),
        ]);

        return redirect()
            ->route('admin.pages.sections.about.edit', [$page, $section])
            ->with('status', __('admin.pages.about_saved'));
    }

    public function editVolunteer(Page $page, PageSection $section): View
    {
        abort_unless($section->page_id === $page->id, 404);
        abort_unless($section->key === 'volunteer', 404);

        $section->load('blocks');

        $volunteerContent = VolunteerPageContent::resolve($section->settings);

        return view('admin.pages.sections.volunteer.edit', [
            'page' => $page,
            'section' => $section,
            'mediaLibrary' => Media::query()->latest()->limit(200)->get(),
            'volunteerContent' => $volunteerContent,
        ]);
    }

    public function updateVolunteer(UpdatePageSectionVolunteerRequest $request, Page $page, PageSection $section): RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);
        abort_unless($section->key === 'volunteer', 404);

        $validated = $request->validated();
        $content = $this->normalizeVolunteerContent($validated['content'] ?? []);
        $content = $this->applyVolunteerMediaUploads($request, $content);
        $content = HtmlText::cleanArray($content);

        $section->update([
            'title_en' => $validated['title_en'],
            'title_ar' => $validated['title_ar'],
            'is_active' => $validated['is_active'] ?? $section->is_active,
            'settings' => array_merge($section->settings ?? [], [
                'content' => array_merge($section->settings['content'] ?? [], $content),
            ]),
        ]);

        return redirect()
            ->route('admin.pages.sections.volunteer.edit', [$page, $section])
            ->with('status', __('admin.pages.volunteer_saved'));
    }

    public function update(UpdatePageSectionRequest $request, Page $page, PageSection $section): RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);

        $section->update($request->validated());

        return redirect()
            ->route('admin.pages.sections.edit', [$page, $section])
            ->with('status', __('admin.pages.section_saved'));
    }

    public function updateHero(UpdatePageSectionHeroRequest $request, Page $page, PageSection $section): RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);
        abort_unless($section->key === 'hero', 404);

        $validated = $request->validated();
        $content = $this->normalizeHeroContent($validated['content'] ?? []);
        $content = $this->applyHeroMediaUploads($request, $content);
        $content = HtmlText::cleanArray($content);

        $section->update([
            'title_en' => $validated['title_en'],
            'title_ar' => $validated['title_ar'],
            'is_active' => $validated['is_active'] ?? $section->is_active,
            'settings' => array_merge($section->settings ?? [], [
                'content' => array_merge($section->settings['content'] ?? [], $content),
            ]),
        ]);

        return redirect()
            ->route('admin.pages.sections.hero.edit', [$page, $section])
            ->with('status', __('admin.pages.hero_saved'));
    }

    public function reorder(ReorderRequest $request, Page $page, PageSection $section): RedirectResponse
    {
        abort_unless($section->page_id === $page->id, 404);

        $this->builder->reorderSection($section, $request->validated('direction'));

        return redirect()
            ->route('admin.pages.show', $page)
            ->with('status', __('admin.pages.reordered'));
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    private function normalizeHeroContent(array $content): array
    {
        if (empty($content['background_media_id'])) {
            $content['background_media_id'] = null;
        } else {
            $content['background_media_id'] = (int) $content['background_media_id'];
        }

        return $content;
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    private function applyHeroMediaUploads(UpdatePageSectionHeroRequest $request, array $content): array
    {
        if ($request->hasFile('background_upload')) {
            $media = $this->media->upload($request->file('background_upload'));
            $content['background_media_id'] = $media->id;
        }

        return $content;
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    private function normalizeAboutContent(array $content): array
    {
        if (empty($content['image_media_id'])) {
            $content['image_media_id'] = null;
        } else {
            $content['image_media_id'] = (int) $content['image_media_id'];
        }

        if (empty($content['video_cover_media_id'])) {
            $content['video_cover_media_id'] = null;
        } else {
            $content['video_cover_media_id'] = (int) $content['video_cover_media_id'];
        }

        if (empty($content['video_url'])) {
            $content['video_url'] = '';
        }

        foreach ([1, 2, 3] as $index) {
            unset($content["paragraph{$index}_en"], $content["paragraph{$index}_ar"]);
        }

        return $content;
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    private function applyAboutMediaUploads(UpdatePageSectionAboutRequest $request, array $content): array
    {
        if ($request->hasFile('image_upload')) {
            $media = $this->media->upload($request->file('image_upload'));
            $content['image_media_id'] = $media->id;
        }

        if ($request->hasFile('video_cover_upload')) {
            $media = $this->media->upload($request->file('video_cover_upload'));
            $content['video_cover_media_id'] = $media->id;
        }

        return $content;
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    private function normalizeVolunteerContent(array $content): array
    {
        if (empty($content['hero_image_media_id'])) {
            $content['hero_image_media_id'] = null;
        } else {
            $content['hero_image_media_id'] = (int) $content['hero_image_media_id'];
        }

        return $content;
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    private function applyVolunteerMediaUploads(UpdatePageSectionVolunteerRequest $request, array $content): array
    {
        if ($request->hasFile('hero_image_upload')) {
            $media = $this->media->upload($request->file('hero_image_upload'));
            $content['hero_image_media_id'] = $media->id;
        }

        return $content;
    }
}
