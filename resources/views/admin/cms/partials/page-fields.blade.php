@php
    $statuses = \App\Models\ContentPage::statuses();
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.title_en') }}</label>
        <input type="text" name="title_en" value="{{ old('title_en', $page->title_en ?? '') }}" required class="ghosn-input @error('title_en') border-red-300 @enderror">
        @include('admin.partials.field-error', ['field' => 'title_en'])
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.title_ar') }}</label>
        <input type="text" name="title_ar" value="{{ old('title_ar', $page->title_ar ?? '') }}" required class="ghosn-input @error('title_ar') border-red-300 @enderror" dir="rtl">
        @include('admin.partials.field-error', ['field' => 'title_ar'])
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.slug') }}</label>
        <input type="text" name="slug" value="{{ old('slug', $page->slug ?? '') }}" placeholder="{{ isset($page->id) ? '' : __('admin.cms.slug_placeholder') }}" @if(isset($page->id)) required @endif class="ghosn-input @error('slug') border-red-300 @enderror" dir="ltr">
        @include('admin.partials.field-error', ['field' => 'slug'])
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.status') }}</label>
        <select name="status" class="ghosn-input @error('status') border-red-300 @enderror">
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $page->status ?? \App\Models\ContentPage::STATUS_DRAFT) === $status)>{{ __('admin.cms.status_'.$status) }}</option>
            @endforeach
        </select>
        @include('admin.partials.field-error', ['field' => 'status'])
    </div>
</div>

<input type="hidden" name="template" value="{{ \App\Models\ContentPage::TEMPLATE_DEFAULT }}">

<div class="space-y-5">
    <div class="grid gap-5 md:grid-cols-2">
        <div class="md:col-span-2">@include('admin.cms.partials.featured-image-picker', ['name' => 'featured_image_media_id', 'value' => old('featured_image_media_id', $page->featured_image_media_id ?? null), 'mediaLibrary' => $mediaLibrary])</div>
        @include('admin.partials.field-error', ['field' => 'featured_image_media_id'])

        <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.content_en') }}</label>
            @include('admin.cms.partials.cms-editor', ['name' => 'content_en', 'value' => old('content_en', $page->content_en ?? ''), 'dir' => 'ltr'])
            @include('admin.partials.field-error', ['field' => 'content_en'])
        </div>

        <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.content_ar') }}</label>
            @include('admin.cms.partials.cms-editor', ['name' => 'content_ar', 'value' => old('content_ar', $page->content_ar ?? ''), 'dir' => 'rtl'])
            @include('admin.partials.field-error', ['field' => 'content_ar'])
        </div>
    </div>
</div>

<div class="grid gap-5 md:grid-cols-2">
    <div><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.seo_title_en') }}</label><input type="text" name="seo_title_en" value="{{ old('seo_title_en', $page->seo_title_en ?? '') }}" class="ghosn-input @error('seo_title_en') border-red-300 @enderror">@include('admin.partials.field-error', ['field' => 'seo_title_en'])</div>
    <div><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.seo_title_ar') }}</label><input type="text" name="seo_title_ar" value="{{ old('seo_title_ar', $page->seo_title_ar ?? '') }}" class="ghosn-input @error('seo_title_ar') border-red-300 @enderror" dir="rtl">@include('admin.partials.field-error', ['field' => 'seo_title_ar'])</div>
    <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.seo_description_en') }}</label><textarea name="seo_description_en" rows="2" class="ghosn-input @error('seo_description_en') border-red-300 @enderror">{{ old('seo_description_en', $page->seo_description_en ?? '') }}</textarea>@include('admin.partials.field-error', ['field' => 'seo_description_en'])</div>
    <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.seo_description_ar') }}</label><textarea name="seo_description_ar" rows="2" class="ghosn-input @error('seo_description_ar') border-red-300 @enderror" dir="rtl">{{ old('seo_description_ar', $page->seo_description_ar ?? '') }}</textarea>@include('admin.partials.field-error', ['field' => 'seo_description_ar'])</div>
</div>

@include('admin.cms.partials.media-modal')
