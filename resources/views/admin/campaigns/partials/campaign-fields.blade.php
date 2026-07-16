@php
    $statuses = $statuses ?? config('campaigns.statuses', []);
    $item = $campaign ?? null;
    $galleryIds = old('gallery_media_ids', $item?->gallery_media_ids ?? []);
    if (is_string($galleryIds)) {
        $galleryIds = json_decode($galleryIds, true) ?: [];
    }
    $galleryMedia = collect($galleryIds)->map(fn ($id) => $mediaLibrary->firstWhere('id', (int) $id) ?? \App\Models\Media::query()->find($id))->filter();
    $categoryOptions = $categories->mapWithKeys(fn ($c) => [$c->id => $c->name_en])->all();
@endphp

<div class="flex flex-col gap-[22px]" data-campaign-editor-fields>
    {{-- Basics --}}
    <section class="gh-admin-section-card !p-7">
        <h3 class="m-0 text-base font-bold text-[#2f4327]">{{ __('admin.campaigns.sec_basics') }}</h3>
        <div class="mt-5 flex flex-col gap-[18px]">
            <div class="grid gap-[18px] md:grid-cols-2">
                <label class="flex flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                    {{ __('admin.campaigns.title_en') }}
                    <input type="text" name="title_en" value="{{ old('title_en', $item?->title_en) }}" required class="gh-admin-field" data-campaign-preview-source="title_en">
                </label>
                <label class="flex flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                    {{ __('admin.campaigns.title_ar') }}
                    <input type="text" name="title_ar" value="{{ old('title_ar', $item?->title_ar) }}" required class="gh-admin-field" dir="rtl" data-campaign-preview-source="title_ar">
                </label>
            </div>
            <div class="grid gap-[18px] md:grid-cols-2">
                <label class="flex flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                    {{ __('admin.campaigns.excerpt_en') }}
                    <textarea name="excerpt_en" rows="3" class="gh-admin-field resize-y" data-campaign-preview-source="excerpt_en">{{ old('excerpt_en', $item?->excerpt_en) }}</textarea>
                </label>
                <label class="flex flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                    {{ __('admin.campaigns.excerpt_ar') }}
                    <textarea name="excerpt_ar" rows="3" class="gh-admin-field resize-y" dir="rtl" data-campaign-preview-source="excerpt_ar">{{ old('excerpt_ar', $item?->excerpt_ar) }}</textarea>
                </label>
            </div>
            <div class="flex flex-wrap gap-4">
                <label class="flex min-w-[160px] flex-1 flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                    {{ __('admin.campaigns.category') }}
                    <select name="category_id" class="gh-admin-field cursor-pointer" data-campaign-preview-source="category_id" data-category-labels='@json($categoryOptions)'>
                        <option value="">{{ __('admin.campaigns.no_category') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $item?->category_id) === (string) $category->id)>{{ $category->name_en }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="flex min-w-[160px] flex-1 flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                    {{ __('admin.campaigns.status') }}
                    <select name="status" class="gh-admin-field cursor-pointer">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', $item?->status ?? \App\Models\Campaign::STATUS_DRAFT) === $status)>{{ __('admin.campaigns.status_'.$status) }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <label class="flex flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                {{ __('admin.campaigns.slug') }}
                <div class="relative">
                    <span class="pointer-events-none absolute top-1/2 start-[14px] -translate-y-1/2 text-[13px] text-[#8a9280]">/</span>
                    <input type="text" name="slug" value="{{ old('slug', $item?->slug) }}" @if($item) required @endif class="gh-admin-field !ps-[26px]" dir="ltr" placeholder="winter-family-relief">
                </div>
            </label>
        </div>
    </section>

    {{-- Funding --}}
    <section class="gh-admin-section-card !p-7">
        <h3 class="m-0 text-base font-bold text-[#2f4327]">{{ __('admin.campaigns.sec_funding') }}</h3>
        <div class="mt-5 flex flex-wrap gap-4">
            <label class="flex min-w-[150px] flex-1 flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                {{ __('admin.campaigns.goal_amount') }}
                <div class="relative">
                    <span class="pointer-events-none absolute top-1/2 start-[14px] -translate-y-1/2 font-bold text-[#8a9280]" data-campaign-currency-prefix>$</span>
                    <input type="number" name="goal_amount" min="0" step="0.01" value="{{ old('goal_amount', $item?->goal_amount ?? 0) }}" required class="gh-admin-field !ps-[30px]" dir="ltr" data-campaign-preview-source="goal_amount">
                </div>
            </label>
            <label class="flex min-w-[150px] flex-1 flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                {{ __('admin.campaigns.raised_amount') }}
                <div class="relative">
                    <span class="pointer-events-none absolute top-1/2 start-[14px] -translate-y-1/2 font-bold text-[#8a9280]" data-campaign-currency-prefix>$</span>
                    <input type="number" name="raised_amount" min="0" step="0.01" value="{{ old('raised_amount', $item?->raised_amount ?? 0) }}" class="gh-admin-field !ps-[30px]" dir="ltr" data-campaign-preview-source="raised_amount">
                </div>
            </label>
            <label class="flex min-w-[130px] flex-1 flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                {{ __('admin.campaigns.currency') }}
                <select name="currency" class="gh-admin-field cursor-pointer" data-campaign-preview-source="currency">
                    @foreach ($currencies as $currency)
                        <option value="{{ $currency }}" @selected(old('currency', $item?->currency ?? 'USD') === $currency)>{{ $currency }}</option>
                    @endforeach
                </select>
            </label>
            <label class="flex min-w-[150px] flex-1 flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                {{ __('admin.campaigns.starts_at') }}
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($item?->starts_at)->format('Y-m-d\TH:i')) }}" class="gh-admin-field">
            </label>
            <label class="flex min-w-[150px] flex-1 flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                {{ __('admin.campaigns.ends_at') }}
                <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($item?->ends_at)->format('Y-m-d\TH:i')) }}" class="gh-admin-field" data-campaign-preview-source="ends_at">
            </label>
        </div>
    </section>

    {{-- Publishing --}}
    <section class="gh-admin-section-card !p-7">
        <h3 class="m-0 text-base font-bold text-[#2f4327]">{{ __('admin.campaigns.sec_publishing') }}</h3>
        <div class="mt-5 flex flex-wrap items-end gap-4">
            <label class="flex min-w-[140px] flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                {{ __('admin.campaigns.sort_order') }}
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $item?->sort_order ?? 0) }}" class="gh-admin-field">
            </label>
            <label class="flex items-center gap-3 pb-3 text-[13px] font-semibold text-[#4a5340]">
                <input type="hidden" name="is_featured_homepage" value="0">
                <input type="checkbox" name="is_featured_homepage" value="1" @checked(old('is_featured_homepage', $item?->is_featured_homepage)) class="h-4 w-4 rounded border-[rgba(64,97,57,0.25)] text-[#406139]">
                {{ __('admin.campaigns.featured_homepage') }}
            </label>
        </div>
    </section>

    {{-- Media --}}
    <section class="gh-admin-section-card !p-7">
        <h3 class="m-0 text-base font-bold text-[#2f4327]">{{ __('admin.campaigns.sec_media') }}</h3>
        <div class="mt-3 mb-4 flex items-center gap-2 text-[12.5px] text-[#8a9280]">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#8a9280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
            {{ __('admin.campaigns.drop_hint') }}
        </div>
        <div class="gh-campaign-cover-picker">
            @include('admin.cms.partials.featured-image-picker', [
                'name' => 'featured_image_media_id',
                'value' => old('featured_image_media_id', $item?->featured_image_media_id),
                'mediaLibrary' => $mediaLibrary,
                'label' => __('admin.campaigns.cover_image'),
            ])
        </div>
        @php
            $videoSource = old('video_source', filled($item?->video_url) && ! $item?->video_media_id ? 'url' : 'upload');
        @endphp
        <div class="mt-6 border-t border-[rgba(64,97,57,0.1)] pt-6" data-campaign-video>
            <label class="mb-3 block text-[13px] font-semibold text-[#4a5340]">{{ __('admin.campaigns.video') }}</label>
            <div class="mb-4 inline-flex rounded-[10px] border border-[rgba(64,97,57,0.2)] p-1" role="tablist">
                <label class="cursor-pointer rounded-[7px] px-4 py-1.5 text-[13px] font-semibold text-[#4a5340] transition data-[active=true]:bg-[#406139] data-[active=true]:text-[#F2F1EA]" data-video-tab="upload" data-active="{{ $videoSource === 'upload' ? 'true' : 'false' }}">
                    <input type="radio" name="video_source" value="upload" class="sr-only" @checked($videoSource === 'upload') data-video-source>
                    {{ __('admin.campaigns.video_source_upload') }}
                </label>
                <label class="cursor-pointer rounded-[7px] px-4 py-1.5 text-[13px] font-semibold text-[#4a5340] transition data-[active=true]:bg-[#406139] data-[active=true]:text-[#F2F1EA]" data-video-tab="url" data-active="{{ $videoSource === 'url' ? 'true' : 'false' }}">
                    <input type="radio" name="video_source" value="url" class="sr-only" @checked($videoSource === 'url') data-video-source>
                    {{ __('admin.campaigns.video_source_url') }}
                </label>
            </div>

            <div data-video-panel="upload" @class(['hidden' => $videoSource !== 'upload'])>
                @include('admin.cms.partials.featured-image-picker', [
                    'name' => 'video_media_id',
                    'value' => old('video_media_id', $item?->video_media_id),
                    'mediaLibrary' => $mediaLibrary,
                    'label' => __('admin.campaigns.video'),
                    'mediaType' => 'video',
                    'hideLabel' => true,
                ])
                <p class="mt-2 text-xs text-[#8a9280]">{{ __('admin.campaigns.video_help') }}</p>
            </div>

            <div data-video-panel="url" @class(['hidden' => $videoSource !== 'url'])>
                <input type="url" name="video_url" value="{{ old('video_url', $item?->video_url) }}" class="gh-admin-field" dir="ltr" placeholder="https://www.youtube.com/watch?v=...">
                <p class="mt-2 text-xs text-[#8a9280]">{{ __('admin.campaigns.video_url_help') }}</p>
            </div>
        </div>
        <div class="mt-6" data-media-gallery data-gallery-input-name="gallery_media_ids">
            <label class="mb-3 block text-[13px] font-semibold text-[#4a5340]">{{ __('admin.campaigns.gallery') }}</label>
            <input type="hidden" name="gallery_media_ids" value="{{ json_encode($galleryIds) }}" data-gallery-input>
            <div class="mb-3 flex flex-wrap gap-2" data-gallery-preview>
                @foreach ($galleryMedia as $media)
                    <div class="relative overflow-hidden rounded-xl border border-[rgba(64,97,57,0.1)]" data-gallery-item="{{ $media->id }}">
                        <img src="{{ $media->thumbnailUrl() ?? $media->url() }}" alt="" class="h-20 w-28 object-cover">
                        <button type="button" data-gallery-remove="{{ $media->id }}" class="absolute end-1 top-1 rounded-full bg-[#406139]/80 px-1.5 text-xs text-[#F2F1EA]">×</button>
                    </div>
                @endforeach
            </div>
            <button type="button" data-gallery-add class="gh-admin-btn-secondary !rounded-[9px] !px-3.5 !py-1.5 !text-[13px]">{{ __('admin.campaigns.add_gallery_images') }}</button>
        </div>
    </section>

    {{-- Story --}}
    <section class="gh-admin-section-card !p-7">
        <h3 class="m-0 text-base font-bold text-[#2f4327]">{{ __('admin.campaigns.sec_story') }}</h3>
        <div class="mt-5 flex flex-col gap-[18px]">
            <div>
                <label class="mb-2 block text-[13px] font-semibold text-[#4a5340]">{{ __('admin.campaigns.story_en') }}</label>
                @include('admin.cms.partials.cms-editor', ['name' => 'story_en', 'value' => $item?->story_en, 'dir' => 'ltr'])
            </div>
            <div>
                <label class="mb-2 block text-[13px] font-semibold text-[#4a5340]">{{ __('admin.campaigns.story_ar') }}</label>
                @include('admin.cms.partials.cms-editor', ['name' => 'story_ar', 'value' => $item?->story_ar, 'dir' => 'rtl'])
            </div>
        </div>
    </section>

    {{-- SEO --}}
    <section class="gh-admin-section-card !p-7">
        <h3 class="m-0 text-base font-bold text-[#2f4327]">{{ __('admin.campaigns.sec_seo') }}</h3>
        <div class="mt-5 grid gap-[18px] md:grid-cols-2">
            <label class="flex flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                {{ __('admin.campaigns.seo_title_en') }}
                <input type="text" name="seo_title_en" value="{{ old('seo_title_en', $item?->seo_title_en) }}" class="gh-admin-field">
            </label>
            <label class="flex flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340]">
                {{ __('admin.campaigns.seo_title_ar') }}
                <input type="text" name="seo_title_ar" value="{{ old('seo_title_ar', $item?->seo_title_ar) }}" class="gh-admin-field" dir="rtl">
            </label>
            <label class="flex flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340] md:col-span-2">
                {{ __('admin.campaigns.seo_description_en') }}
                <textarea name="seo_description_en" rows="2" class="gh-admin-field resize-y">{{ old('seo_description_en', $item?->seo_description_en) }}</textarea>
            </label>
            <label class="flex flex-col gap-[7px] text-[13px] font-semibold text-[#4a5340] md:col-span-2">
                {{ __('admin.campaigns.seo_description_ar') }}
                <textarea name="seo_description_ar" rows="2" class="gh-admin-field resize-y" dir="rtl">{{ old('seo_description_ar', $item?->seo_description_ar) }}</textarea>
            </label>
        </div>
    </section>
</div>

@include('admin.cms.partials.media-modal')
