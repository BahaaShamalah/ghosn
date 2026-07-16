@extends('admin.layouts.app')

@section('title', __('admin.pages.hero_edit'))
@section('page-title', __('admin.pages.hero_edit'))
@section('eyebrow', $page->slug.' / hero')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ghosn/45">hero</p>
            <h2 class="mt-1 text-2xl font-bold text-ghosn">{{ __('admin.pages.hero_edit') }}</h2>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.pages.show', $page) }}" class="gh-admin-btn-secondary">{{ __('admin.pages.back') }}</a>
            <a href="{{ route('home') }}#home" target="_blank" rel="noopener" class="gh-admin-btn-primary">{{ __('admin.pages.preview') }}</a>
        </div>
    </div>

    <div class="mb-6 gh-admin-alert gh-admin-alert-success">
        {{ __('admin.pages.live_homepage_notice') }}
    </div>

    @php $c = $heroContent; @endphp

    <form method="POST" action="{{ route('admin.pages.sections.hero.update', [$page, $section]) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.section_meta') }}</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.title_en') }}</label>
                    <input type="text" name="title_en" value="{{ old('title_en', $section->title_en) }}" required class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.title_ar') }}</label>
                    <input type="text" name="title_ar" value="{{ old('title_ar', $section->title_ar) }}" required class="ghosn-input" dir="rtl">
                </div>
            </div>
            <label class="mt-5 flex cursor-pointer items-start gap-3 rounded-[12px] border border-[rgba(64,97,57,0.14)] bg-[rgba(237,238,228,0.45)] p-4">
                <input type="hidden" name="is_active" value="1">
                <input type="checkbox" name="is_active" value="0" @checked(! old('is_active', $section->is_active)) class="mt-0.5 rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <span class="text-sm font-medium text-ghosn-ink/80">
                    {{ __('admin.pages.section_hidden') }}
                    <span class="mt-1 block text-xs font-normal text-ghosn-ink/55">{{ __('admin.pages.section_hidden_help') }}</span>
                </span>
            </label>
        </section>

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.hero_background') }}</h3>
            <p class="mt-2 text-sm text-ghosn-ink/60">{{ __('admin.pages.hero_background_help') }}</p>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.hero_background_image') }}</label>
                    <select name="content[background_media_id]" class="ghosn-input">
                        <option value="">{{ __('admin.settings.media_none') }}</option>
                        @foreach ($mediaLibrary->filter(fn ($m) => $m->isImage()) as $item)
                            <option value="{{ $item->id }}" @selected(old('content.background_media_id', $c['background_media_id'] ?? '') == $item->id)>{{ $item->original_filename }}</option>
                        @endforeach
                    </select>
                    <p class="mb-2 mt-3 text-xs text-ghosn-ink/55">{{ __('admin.settings.or_upload') }}</p>
                    <input type="file" name="background_upload" accept="image/jpeg,image/png,image/webp" class="ghosn-input file:me-3 file:rounded-lg file:border-0 file:bg-ghosn file:px-3 file:py-2 file:text-sm file:font-medium file:text-offwhite">
                    @if (! empty($c['background_image_url']))
                        <img src="{{ $c['background_image_url'] }}" alt="" class="mt-3 max-h-56 w-full rounded-xl border border-ghosn/10 object-cover">
                    @endif
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.image_alt_en') }}</label>
                        <input type="text" name="content[background_alt_en]" value="{{ old('content.background_alt_en', $c['background_alt_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.image_alt_ar') }}</label>
                        <input type="text" name="content[background_alt_ar]" value="{{ old('content.background_alt_ar', $c['background_alt_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                </div>
            </div>
            <p class="mt-4 text-xs text-ghosn-ink/55"><a href="{{ route('admin.media.index') }}" class="font-semibold text-ghosn underline">{{ __('admin.settings.open_media') }}</a></p>
        </section>

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.hero_text') }}</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach (['eyebrow', 'title_line1', 'title_accent'] as $field)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.hero_'.$field) }} (EN)</label>
                        <input type="text" name="content[{{ $field }}_en]" value="{{ old('content.'.$field.'_en', $c[$field.'_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.hero_'.$field) }} (AR)</label>
                        <input type="text" name="content[{{ $field }}_ar]" value="{{ old('content.'.$field.'_ar', $c[$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                @endforeach
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.hero_description') }} (EN)</label>
                    <textarea name="content[description_en]" rows="3" class="ghosn-input">{{ old('content.description_en', $c['description_en'] ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.hero_description') }} (AR)</label>
                    <textarea name="content[description_ar]" rows="3" class="ghosn-input" dir="rtl">{{ old('content.description_ar', $c['description_ar'] ?? '') }}</textarea>
                </div>
            </div>
        </section>

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.hero_buttons') }}</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.hero_primary_button') }} (EN)</label>
                    <input type="text" name="content[primary_button_text_en]" value="{{ old('content.primary_button_text_en', $c['primary_button_text_en'] ?? '') }}" class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.hero_primary_button') }} (AR)</label>
                    <input type="text" name="content[primary_button_text_ar]" value="{{ old('content.primary_button_text_ar', $c['primary_button_text_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.hero_primary_url') }}</label>
                    <input type="text" name="content[primary_button_url]" value="{{ old('content.primary_button_url', $c['primary_button_url'] ?? '#campaigns') }}" class="ghosn-input" placeholder="#campaigns">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.hero_secondary_button') }} (EN)</label>
                    <input type="text" name="content[secondary_button_text_en]" value="{{ old('content.secondary_button_text_en', $c['secondary_button_text_en'] ?? '') }}" class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.hero_secondary_button') }} (AR)</label>
                    <input type="text" name="content[secondary_button_text_ar]" value="{{ old('content.secondary_button_text_ar', $c['secondary_button_text_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.hero_secondary_url') }}</label>
                    <input type="text" name="content[secondary_button_url]" value="{{ old('content.secondary_button_url', $c['secondary_button_url'] ?? '#team') }}" class="ghosn-input" placeholder="#team">
                </div>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="gh-admin-btn-primary">{{ __('admin.pages.save_hero') }}</button>
        </div>
    </form>
@endsection
