@php

    $statuses = [\App\Models\Post::STATUS_DRAFT, \App\Models\Post::STATUS_PUBLISHED];

    $item = $post ?? null;

@endphp



<div class="grid gap-5 md:grid-cols-2">

    <div><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.title_en') }}</label><input type="text" name="title_en" value="{{ old('title_en', $item?->title_en) }}" required class="ghosn-input"></div>

    <div><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.title_ar') }}</label><input type="text" name="title_ar" value="{{ old('title_ar', $item?->title_ar) }}" required class="ghosn-input" dir="rtl"></div>

    <div><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.slug') }}</label><input type="text" name="slug" value="{{ old('slug', $item?->slug) }}" @if($item) required @endif class="ghosn-input" dir="ltr" placeholder="{{ __('admin.cms.slug_placeholder') }}"></div>

    <div>

        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.status') }}</label>

        <select name="status" class="ghosn-input">

            @foreach ($statuses as $status)

                <option value="{{ $status }}" @selected(old('status', $item?->status ?? \App\Models\Post::STATUS_DRAFT) === $status)>{{ __('admin.cms.status_'.$status) }}</option>

            @endforeach

        </select>

    </div>

    <div>

        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.category') }}</label>

        <select name="category_id" class="ghosn-input">

            <option value="">{{ __('admin.cms.no_category') }}</option>

            @foreach ($categories as $category)

                <option value="{{ $category->id }}" @selected((string) old('category_id', $item?->category_id) === (string) $category->id)>{{ $category->name_en }}</option>

            @endforeach

        </select>

    </div>

    <div>

        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.published_at') }}</label>

        <input type="datetime-local" name="published_at" value="{{ old('published_at', optional($item?->published_at)->format('Y-m-d\TH:i')) }}" class="ghosn-input">

    </div>

    <div class="md:col-span-2">@include('admin.cms.partials.featured-image-picker', ['name' => 'featured_image_media_id', 'value' => old('featured_image_media_id', $item?->featured_image_media_id), 'mediaLibrary' => $mediaLibrary])</div>

    <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.excerpt_en') }}</label><textarea name="excerpt_en" rows="3" class="ghosn-input">{{ old('excerpt_en', $item?->excerpt_en) }}</textarea></div>

    <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.excerpt_ar') }}</label><textarea name="excerpt_ar" rows="3" class="ghosn-input" dir="rtl">{{ old('excerpt_ar', $item?->excerpt_ar) }}</textarea></div>

    <div class="md:col-span-2">

        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.content_en') }}</label>

        @include('admin.cms.partials.cms-editor', ['name' => 'content_en', 'value' => $item?->content_en, 'dir' => 'ltr'])

    </div>

    <div class="md:col-span-2">

        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.content_ar') }}</label>

        @include('admin.cms.partials.cms-editor', ['name' => 'content_ar', 'value' => $item?->content_ar, 'dir' => 'rtl'])

    </div>

    <div><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.seo_title_en') }}</label><input type="text" name="seo_title_en" value="{{ old('seo_title_en', $item?->seo_title_en) }}" class="ghosn-input"></div>

    <div><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.seo_title_ar') }}</label><input type="text" name="seo_title_ar" value="{{ old('seo_title_ar', $item?->seo_title_ar) }}" class="ghosn-input" dir="rtl"></div>

    <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.seo_description_en') }}</label><textarea name="seo_description_en" rows="2" class="ghosn-input">{{ old('seo_description_en', $item?->seo_description_en) }}</textarea></div>

    <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.seo_description_ar') }}</label><textarea name="seo_description_ar" rows="2" class="ghosn-input" dir="rtl">{{ old('seo_description_ar', $item?->seo_description_ar) }}</textarea></div>

</div>



@include('admin.cms.partials.media-modal')

