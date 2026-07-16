@extends('admin.layouts.app')

@section('title', __('admin.pages.about_edit'))
@section('page-title', __('admin.pages.about_edit'))
@section('eyebrow', $page->slug.' / about')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ghosn/45">about</p>
            <h2 class="mt-1 text-2xl font-bold text-ghosn">{{ __('admin.pages.about_edit') }}</h2>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.pages.show', $page) }}" class="gh-admin-btn-secondary">{{ __('admin.pages.back') }}</a>
            <a href="{{ \App\Support\BuilderPageRoutes::sectionPreviewUrl($page, $section) }}" target="_blank" rel="noopener" class="gh-admin-btn-primary">{{ __('admin.pages.preview') }}</a>
        </div>
    </div>

    <div class="mb-6 gh-admin-alert gh-admin-alert-success">
        @if ($page->slug === 'volunteer')
            {{ __('admin.pages.live_page_notice_volunteer') }}
        @elseif ($page->slug === 'who-we-are')
            {{ __('admin.pages.live_page_notice_who-we-are') }}
        @else
            {{ __('admin.pages.live_homepage_notice') }}
        @endif
    </div>

    @php $c = $aboutContent; @endphp

    <form method="POST" action="{{ route('admin.pages.sections.about.update', [$page, $section]) }}" enctype="multipart/form-data" class="space-y-8">
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
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.about_video') }}</h3>
            <p class="mt-2 text-sm text-ghosn-ink/60">{{ __('admin.pages.about_video_help') }}</p>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_video_url') }}</label>
                    <input type="url" name="content[video_url]" value="{{ old('content.video_url', $c['video_url'] ?? '') }}" class="ghosn-input" placeholder="https://www.youtube.com/watch?v=...">
                    <p class="mt-1 text-xs text-ghosn-ink/55">{{ __('admin.pages.about_video_url_help') }}</p>
                    @if (! empty($c['video_embed_url']))
                        <iframe src="{{ str_replace('autoplay=1', 'autoplay=0', $c['video_embed_url']) }}" class="mt-3 aspect-video w-full rounded-xl border border-ghosn/10" allowfullscreen loading="lazy"></iframe>
                    @endif
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_video_cover') }}</label>
                    <select name="content[video_cover_media_id]" class="ghosn-input">
                        <option value="">{{ __('admin.settings.media_none') }}</option>
                        @foreach ($mediaLibrary->filter(fn ($m) => $m->isImage()) as $item)
                            <option value="{{ $item->id }}" @selected(old('content.video_cover_media_id', $c['video_cover_media_id'] ?? '') == $item->id)>{{ $item->original_filename }}</option>
                        @endforeach
                    </select>
                    <p class="mb-2 mt-3 text-xs text-ghosn-ink/55">{{ __('admin.settings.or_upload') }}</p>
                    <input type="file" name="video_cover_upload" accept="image/jpeg,image/png,image/webp" class="ghosn-input file:me-3 file:rounded-lg file:border-0 file:bg-ghosn file:px-3 file:py-2 file:text-sm file:font-medium file:text-offwhite">
                    @if (! empty($c['video_poster_url']))
                        <img src="{{ $c['video_poster_url'] }}" alt="" class="mt-3 max-h-40 rounded-xl border border-ghosn/10">
                    @endif
                </div>
                <div class="space-y-4">
                    @foreach (['watch_label', 'read_more'] as $field)
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_'.$field) }} (EN)</label>
                            <input type="text" name="content[{{ $field }}_en]" value="{{ old('content.'.$field.'_en', $c[$field.'_en'] ?? '') }}" class="ghosn-input">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_'.$field) }} (AR)</label>
                            <input type="text" name="content[{{ $field }}_ar]" value="{{ old('content.'.$field.'_ar', $c[$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                        </div>
                    @endforeach
                </div>
            </div>
            <p class="mt-4 text-xs text-ghosn-ink/55"><a href="{{ route('admin.media.index') }}" class="font-semibold text-ghosn underline">{{ __('admin.settings.open_media') }}</a></p>
        </section>

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.about_content') }}</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach (['eyebrow', 'heading'] as $field)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_'.$field) }} (EN)</label>
                        <input type="text" name="content[{{ $field }}_en]" value="{{ old('content.'.$field.'_en', $c[$field.'_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_'.$field) }} (AR)</label>
                        <input type="text" name="content[{{ $field }}_ar]" value="{{ old('content.'.$field.'_ar', $c[$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                @endforeach
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_paragraphs') }} (EN)</label>
                    <textarea name="content[paragraphs_en]" rows="9" class="ghosn-input text-sm leading-relaxed">{{ old('content.paragraphs_en', \App\Support\AboutContent::paragraphsEditorText($c, 'en')) }}</textarea>
                    <p class="mt-1 text-xs text-ghosn-ink/55">{{ __('admin.pages.about_paragraphs_help') }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_paragraphs') }} (AR)</label>
                    <textarea name="content[paragraphs_ar]" rows="9" class="ghosn-input text-sm leading-relaxed" dir="rtl">{{ old('content.paragraphs_ar', \App\Support\AboutContent::paragraphsEditorText($c, 'ar')) }}</textarea>
                    <p class="mt-1 text-xs text-ghosn-ink/55">{{ __('admin.pages.about_paragraphs_help') }}</p>
                </div>
            </div>
        </section>

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.about_stats') }}</h3>
            <div class="mt-6 space-y-6">
                @foreach (($c['stats'] ?? config('about.defaults.stats', [])) as $index => $stat)
                    <div class="rounded-xl border border-ghosn/10 p-4">
                        <p class="mb-4 text-sm font-semibold text-ghosn">{{ __('admin.pages.about_stat') }} {{ $index + 1 }}</p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_stat_value') }} (EN)</label>
                                <input type="text" name="content[stats][{{ $index }}][value_en]" value="{{ old('content.stats.'.$index.'.value_en', $stat['value_en'] ?? '') }}" class="ghosn-input">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_stat_value') }} (AR)</label>
                                <input type="text" name="content[stats][{{ $index }}][value_ar]" value="{{ old('content.stats.'.$index.'.value_ar', $stat['value_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_stat_label') }} (EN)</label>
                                <input type="text" name="content[stats][{{ $index }}][label_en]" value="{{ old('content.stats.'.$index.'.label_en', $stat['label_en'] ?? '') }}" class="ghosn-input">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.about_stat_label') }} (AR)</label>
                                <input type="text" name="content[stats][{{ $index }}][label_ar]" value="{{ old('content.stats.'.$index.'.label_ar', $stat['label_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="gh-admin-btn-primary">{{ __('admin.pages.save_about') }}</button>
        </div>
    </form>
@endsection
