@php
    use App\Support\AboutPageContent;

    $defaults = AboutPageContent::defaults();
    $stored = $settings['about.page'] ?? null;
    $about = is_array($stored) && $stored !== []
        ? array_replace_recursive($defaults, $stored)
        : $defaults;

    $paragraphsEn = $about['intro']['paragraphs_en'] ?? [];
    $paragraphsAr = $about['intro']['paragraphs_ar'] ?? [];
    if (is_array($paragraphsEn)) {
        $paragraphsEn = implode("\n\n", $paragraphsEn);
    }
    if (is_array($paragraphsAr)) {
        $paragraphsAr = implode("\n\n", $paragraphsAr);
    }
    $partnersEn = $about['partners']['items_en'] ?? [];
    $partnersAr = $about['partners']['items_ar'] ?? [];
    if (is_array($partnersEn)) {
        $partnersEn = implode("\n", $partnersEn);
    }
    if (is_array($partnersAr)) {
        $partnersAr = implode("\n", $partnersAr);
    }
@endphp

<form method="POST" action="{{ route('admin.settings.update.group', 'about') }}" class="space-y-4">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="about">

    @include('admin.settings.partials.form-errors', ['group' => 'about'])

    <div class="sticky top-0 z-20 -mx-2 flex items-center justify-between gap-4 rounded-[14px] border border-[rgba(64,97,57,0.12)] bg-[rgba(255,253,248,0.96)] px-4 py-3 backdrop-blur-md">
        <p class="text-sm text-[#5f6857]">{{ __('admin.settings.about_intro') }}</p>
        <button type="submit" class="gh-admin-btn-primary shrink-0 shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]" open>
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.about_hero') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="about[page][sections][hero]" value="0">
                <input type="checkbox" name="about[page][sections][hero]" value="1" class="rounded border-ghosn/30" @checked(old('about.page.sections.hero', $about['sections']['hero'] ?? true))>
                {{ __('admin.settings.about_section_show') }}
            </label>
        </summary>
        <div class="grid gap-4 border-t border-[rgba(64,97,57,0.08)] p-4 md:grid-cols-2">
            @foreach (['eyebrow', 'title', 'subtitle'] as $field)
                <div @class(['md:col-span-2' => $field === 'subtitle'])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_'.$field.'_en') }}</label>
                    @if ($field === 'subtitle')
                        <textarea name="about[page][hero][{{ $field }}_en]" rows="2" class="ghosn-input">{{ old('about.page.hero.'.$field.'_en', $about['hero'][$field.'_en'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="about[page][hero][{{ $field }}_en]" value="{{ old('about.page.hero.'.$field.'_en', $about['hero'][$field.'_en'] ?? '') }}" class="ghosn-input">
                    @endif
                </div>
                <div @class(['md:col-span-2' => $field === 'subtitle'])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_'.$field.'_ar') }}</label>
                    @if ($field === 'subtitle')
                        <textarea name="about[page][hero][{{ $field }}_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('about.page.hero.'.$field.'_ar', $about['hero'][$field.'_ar'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="about[page][hero][{{ $field }}_ar]" value="{{ old('about.page.hero.'.$field.'_ar', $about['hero'][$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    @endif
                </div>
            @endforeach
            <div class="md:col-span-2">
                @include('admin.cms.partials.featured-image-picker', [
                    'name' => 'about[page][hero][image_media_id]',
                    'value' => old('about.page.hero.image_media_id', $about['hero']['image_media_id'] ?? null),
                    'label' => __('admin.settings.about_hero_image'),
                    'mediaLibrary' => $mediaLibrary ?? collect(),
                    'compact' => true,
                ])
            </div>
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]" open>
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.about_intro_section') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="about[page][sections][intro]" value="0">
                <input type="checkbox" name="about[page][sections][intro]" value="1" class="rounded border-ghosn/30" @checked(old('about.page.sections.intro', $about['sections']['intro'] ?? true))>
                {{ __('admin.settings.about_section_show') }}
            </label>
        </summary>
        <div class="grid gap-4 border-t border-[rgba(64,97,57,0.08)] p-4 md:grid-cols-2">
            @foreach (['eyebrow', 'title'] as $field)
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_'.$field.'_en') }}</label>
                    <input type="text" name="about[page][intro][{{ $field }}_en]" value="{{ old('about.page.intro.'.$field.'_en', $about['intro'][$field.'_en'] ?? '') }}" class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_'.$field.'_ar') }}</label>
                    <input type="text" name="about[page][intro][{{ $field }}_ar]" value="{{ old('about.page.intro.'.$field.'_ar', $about['intro'][$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                </div>
            @endforeach
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_paragraphs_en') }}</label>
                <textarea name="about[page][intro][paragraphs_en]" rows="5" class="ghosn-input" placeholder="{{ __('admin.settings.about_paragraphs_help') }}">{{ old('about.page.intro.paragraphs_en', $paragraphsEn) }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_paragraphs_ar') }}</label>
                <textarea name="about[page][intro][paragraphs_ar]" rows="5" class="ghosn-input" dir="rtl">{{ old('about.page.intro.paragraphs_ar', $paragraphsAr) }}</textarea>
            </div>
            <div class="md:col-span-2">
                @include('admin.cms.partials.featured-image-picker', [
                    'name' => 'about[page][intro][image_media_id]',
                    'value' => old('about.page.intro.image_media_id', $about['intro']['image_media_id'] ?? null),
                    'label' => __('admin.settings.about_intro_image'),
                    'mediaLibrary' => $mediaLibrary ?? collect(),
                    'compact' => true,
                ])
            </div>
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_intro_video') }}</label>
                <input type="url" name="about[page][intro][video_url]" value="{{ old('about.page.intro.video_url', $about['intro']['video_url'] ?? '') }}" dir="ltr" class="ghosn-input" placeholder="https://www.youtube.com/watch?v=...">
                <p class="mt-1 text-xs text-[#8a9280]">{{ __('admin.settings.about_intro_video_help') }}</p>
            </div>
            <div class="md:col-span-2">
                @include('admin.cms.partials.featured-image-picker', [
                    'name' => 'about[page][intro][video_cover_media_id]',
                    'value' => old('about.page.intro.video_cover_media_id', $about['intro']['video_cover_media_id'] ?? null),
                    'label' => __('admin.settings.about_intro_video_cover'),
                    'mediaLibrary' => $mediaLibrary ?? collect(),
                    'compact' => true,
                ])
            </div>
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]">
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.about_stats') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="about[page][sections][stats]" value="0">
                <input type="checkbox" name="about[page][sections][stats]" value="1" class="rounded border-ghosn/30" @checked(old('about.page.sections.stats', $about['sections']['stats'] ?? true))>
                {{ __('admin.settings.about_section_show') }}
            </label>
        </summary>
        <div class="space-y-3 border-t border-[rgba(64,97,57,0.08)] p-4">
            @foreach ($about['stats'] ?? [] as $index => $stat)
                <div class="grid gap-3 rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.35)] p-4 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_stat_end') }}</label>
                        <input type="number" name="about[page][stats][{{ $index }}][end]" value="{{ old('about.page.stats.'.$index.'.end', $stat['end'] ?? 0) }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_stat_suffix') }}</label>
                        <input type="text" name="about[page][stats][{{ $index }}][suffix]" value="{{ old('about.page.stats.'.$index.'.suffix', $stat['suffix'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_label_en') }}</label>
                        <input type="text" name="about[page][stats][{{ $index }}][label_en]" value="{{ old('about.page.stats.'.$index.'.label_en', $stat['label_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_label_ar') }}</label>
                        <input type="text" name="about[page][stats][{{ $index }}][label_ar]" value="{{ old('about.page.stats.'.$index.'.label_ar', $stat['label_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                </div>
            @endforeach
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]">
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.about_pillars') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="about[page][sections][pillars]" value="0">
                <input type="checkbox" name="about[page][sections][pillars]" value="1" class="rounded border-ghosn/30" @checked(old('about.page.sections.pillars', $about['sections']['pillars'] ?? true))>
                {{ __('admin.settings.about_section_show') }}
            </label>
        </summary>
        <div class="space-y-3 border-t border-[rgba(64,97,57,0.08)] p-4">
            @foreach ($about['pillars'] ?? [] as $index => $pillar)
                <div class="grid gap-3 rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.35)] p-4 md:grid-cols-2">
                    <input type="hidden" name="about[page][pillars][{{ $index }}][key]" value="{{ $pillar['key'] ?? 'mission' }}">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_title_en') }}</label>
                        <input type="text" name="about[page][pillars][{{ $index }}][title_en]" value="{{ old('about.page.pillars.'.$index.'.title_en', $pillar['title_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_title_ar') }}</label>
                        <input type="text" name="about[page][pillars][{{ $index }}][title_ar]" value="{{ old('about.page.pillars.'.$index.'.title_ar', $pillar['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_body_en') }}</label>
                        <textarea name="about[page][pillars][{{ $index }}][body_en]" rows="2" class="ghosn-input">{{ old('about.page.pillars.'.$index.'.body_en', $pillar['body_en'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_body_ar') }}</label>
                        <textarea name="about[page][pillars][{{ $index }}][body_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('about.page.pillars.'.$index.'.body_ar', $pillar['body_ar'] ?? '') }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]">
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.about_values') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="about[page][sections][values]" value="0">
                <input type="checkbox" name="about[page][sections][values]" value="1" class="rounded border-ghosn/30" @checked(old('about.page.sections.values', $about['sections']['values'] ?? true))>
                {{ __('admin.settings.about_section_show') }}
            </label>
        </summary>
        <div class="space-y-4 border-t border-[rgba(64,97,57,0.08)] p-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_title_en') }}</label>
                    <input type="text" name="about[page][values][title_en]" value="{{ old('about.page.values.title_en', $about['values']['title_en'] ?? '') }}" class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_title_ar') }}</label>
                    <input type="text" name="about[page][values][title_ar]" value="{{ old('about.page.values.title_ar', $about['values']['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_values_intro_en') }}</label>
                    <textarea name="about[page][values][intro_en]" rows="2" class="ghosn-input">{{ old('about.page.values.intro_en', $about['values']['intro_en'] ?? '') }}</textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_values_intro_ar') }}</label>
                    <textarea name="about[page][values][intro_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('about.page.values.intro_ar', $about['values']['intro_ar'] ?? '') }}</textarea>
                </div>
            </div>
            <div class="space-y-3">
                @foreach ($about['values']['items'] ?? [] as $index => $item)
                    <div class="grid gap-3 rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.35)] p-4 md:grid-cols-2">
                        <input type="hidden" name="about[page][values][items][{{ $index }}][key]" value="{{ $item['key'] ?? 'heart' }}">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_title_en') }}</label>
                            <input type="text" name="about[page][values][items][{{ $index }}][title_en]" value="{{ old('about.page.values.items.'.$index.'.title_en', $item['title_en'] ?? '') }}" class="ghosn-input">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_title_ar') }}</label>
                            <input type="text" name="about[page][values][items][{{ $index }}][title_ar]" value="{{ old('about.page.values.items.'.$index.'.title_ar', $item['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_body_en') }}</label>
                            <textarea name="about[page][values][items][{{ $index }}][body_en]" rows="2" class="ghosn-input">{{ old('about.page.values.items.'.$index.'.body_en', $item['body_en'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_body_ar') }}</label>
                            <textarea name="about[page][values][items][{{ $index }}][body_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('about.page.values.items.'.$index.'.body_ar', $item['body_ar'] ?? '') }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]">
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.about_story') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="about[page][sections][story]" value="0">
                <input type="checkbox" name="about[page][sections][story]" value="1" class="rounded border-ghosn/30" @checked(old('about.page.sections.story', $about['sections']['story'] ?? true))>
                {{ __('admin.settings.about_section_show') }}
            </label>
        </summary>
        <div class="space-y-4 border-t border-[rgba(64,97,57,0.08)] p-4">
            <div class="grid gap-4 md:grid-cols-2">
                @foreach (['eyebrow', 'title'] as $field)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_'.$field.'_en') }}</label>
                        <input type="text" name="about[page][story][{{ $field }}_en]" value="{{ old('about.page.story.'.$field.'_en', $about['story'][$field.'_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_'.$field.'_ar') }}</label>
                        <input type="text" name="about[page][story][{{ $field }}_ar]" value="{{ old('about.page.story.'.$field.'_ar', $about['story'][$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                @endforeach
            </div>
            <div class="space-y-3">
                @foreach ($about['story']['milestones'] ?? [] as $index => $item)
                    <div class="grid gap-3 rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.35)] p-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_year_en') }}</label>
                            <input type="text" name="about[page][story][milestones][{{ $index }}][year_en]" value="{{ old('about.page.story.milestones.'.$index.'.year_en', $item['year_en'] ?? '') }}" class="ghosn-input" dir="ltr">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_year_ar') }}</label>
                            <input type="text" name="about[page][story][milestones][{{ $index }}][year_ar]" value="{{ old('about.page.story.milestones.'.$index.'.year_ar', $item['year_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_title_en') }}</label>
                            <input type="text" name="about[page][story][milestones][{{ $index }}][title_en]" value="{{ old('about.page.story.milestones.'.$index.'.title_en', $item['title_en'] ?? '') }}" class="ghosn-input">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_title_ar') }}</label>
                            <input type="text" name="about[page][story][milestones][{{ $index }}][title_ar]" value="{{ old('about.page.story.milestones.'.$index.'.title_ar', $item['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_body_en') }}</label>
                            <textarea name="about[page][story][milestones][{{ $index }}][body_en]" rows="2" class="ghosn-input">{{ old('about.page.story.milestones.'.$index.'.body_en', $item['body_en'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_body_ar') }}</label>
                            <textarea name="about[page][story][milestones][{{ $index }}][body_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('about.page.story.milestones.'.$index.'.body_ar', $item['body_ar'] ?? '') }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]">
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.about_team') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="about[page][sections][team]" value="0">
                <input type="checkbox" name="about[page][sections][team]" value="1" class="rounded border-ghosn/30" @checked(old('about.page.sections.team', $about['sections']['team'] ?? true))>
                {{ __('admin.settings.about_section_show') }}
            </label>
        </summary>
        <div class="space-y-4 border-t border-[rgba(64,97,57,0.08)] p-4">
            <div class="grid gap-4 md:grid-cols-2">
                @foreach (['eyebrow', 'title', 'intro'] as $field)
                    <div @class(['md:col-span-2' => $field === 'intro'])>
                        <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_'.$field.'_en') }}</label>
                        @if ($field === 'intro')
                            <textarea name="about[page][team][{{ $field }}_en]" rows="2" class="ghosn-input">{{ old('about.page.team.'.$field.'_en', $about['team'][$field.'_en'] ?? '') }}</textarea>
                        @else
                            <input type="text" name="about[page][team][{{ $field }}_en]" value="{{ old('about.page.team.'.$field.'_en', $about['team'][$field.'_en'] ?? '') }}" class="ghosn-input">
                        @endif
                    </div>
                    <div @class(['md:col-span-2' => $field === 'intro'])>
                        <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_'.$field.'_ar') }}</label>
                        @if ($field === 'intro')
                            <textarea name="about[page][team][{{ $field }}_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('about.page.team.'.$field.'_ar', $about['team'][$field.'_ar'] ?? '') }}</textarea>
                        @else
                            <input type="text" name="about[page][team][{{ $field }}_ar]" value="{{ old('about.page.team.'.$field.'_ar', $about['team'][$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="space-y-3">
                @foreach ($about['team']['members'] ?? [] as $index => $member)
                    <div class="grid gap-3 rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.35)] p-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_name_en') }}</label>
                            <input type="text" name="about[page][team][members][{{ $index }}][name_en]" value="{{ old('about.page.team.members.'.$index.'.name_en', $member['name_en'] ?? '') }}" class="ghosn-input">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_name_ar') }}</label>
                            <input type="text" name="about[page][team][members][{{ $index }}][name_ar]" value="{{ old('about.page.team.members.'.$index.'.name_ar', $member['name_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_role_en') }}</label>
                            <input type="text" name="about[page][team][members][{{ $index }}][role_en]" value="{{ old('about.page.team.members.'.$index.'.role_en', $member['role_en'] ?? '') }}" class="ghosn-input">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.about_role_ar') }}</label>
                            <input type="text" name="about[page][team][members][{{ $index }}][role_ar]" value="{{ old('about.page.team.members.'.$index.'.role_ar', $member['role_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                        </div>
                        <div class="md:col-span-2">
                            @include('admin.cms.partials.featured-image-picker', [
                                'name' => 'about[page][team][members]['.$index.'][image_media_id]',
                                'value' => old('about.page.team.members.'.$index.'.image_media_id', $member['image_media_id'] ?? null),
                                'label' => __('admin.settings.about_member_image'),
                                'mediaLibrary' => $mediaLibrary ?? collect(),
                                'compact' => true,
                            ])
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]">
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.about_partners') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="about[page][sections][partners]" value="0">
                <input type="checkbox" name="about[page][sections][partners]" value="1" class="rounded border-ghosn/30" @checked(old('about.page.sections.partners', $about['sections']['partners'] ?? true))>
                {{ __('admin.settings.about_section_show') }}
            </label>
        </summary>
        <div class="grid gap-4 border-t border-[rgba(64,97,57,0.08)] p-4 md:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_title_en') }}</label>
                <input type="text" name="about[page][partners][title_en]" value="{{ old('about.page.partners.title_en', $about['partners']['title_en'] ?? '') }}" class="ghosn-input">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_title_ar') }}</label>
                <input type="text" name="about[page][partners][title_ar]" value="{{ old('about.page.partners.title_ar', $about['partners']['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_partners_en') }}</label>
                <textarea name="about[page][partners][items_en]" rows="4" class="ghosn-input" placeholder="{{ __('admin.settings.about_partners_help') }}">{{ old('about.page.partners.items_en', $partnersEn) }}</textarea>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_partners_ar') }}</label>
                <textarea name="about[page][partners][items_ar]" rows="4" class="ghosn-input" dir="rtl">{{ old('about.page.partners.items_ar', $partnersAr) }}</textarea>
            </div>
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]">
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.about_cta') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="about[page][sections][cta]" value="0">
                <input type="checkbox" name="about[page][sections][cta]" value="1" class="rounded border-ghosn/30" @checked(old('about.page.sections.cta', $about['sections']['cta'] ?? true))>
                {{ __('admin.settings.about_section_show') }}
            </label>
        </summary>
        <div class="grid gap-4 border-t border-[rgba(64,97,57,0.08)] p-4 md:grid-cols-2">
            @foreach (['title', 'subtitle', 'primary', 'secondary'] as $field)
                <div @class(['md:col-span-2' => in_array($field, ['title', 'subtitle'], true)])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_cta_'.$field.'_en') }}</label>
                    @if ($field === 'subtitle')
                        <textarea name="about[page][cta][{{ $field }}_en]" rows="2" class="ghosn-input">{{ old('about.page.cta.'.$field.'_en', $about['cta'][$field.'_en'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="about[page][cta][{{ $field }}_en]" value="{{ old('about.page.cta.'.$field.'_en', $about['cta'][$field.'_en'] ?? '') }}" class="ghosn-input">
                    @endif
                </div>
                <div @class(['md:col-span-2' => in_array($field, ['title', 'subtitle'], true)])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_cta_'.$field.'_ar') }}</label>
                    @if ($field === 'subtitle')
                        <textarea name="about[page][cta][{{ $field }}_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('about.page.cta.'.$field.'_ar', $about['cta'][$field.'_ar'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="about[page][cta][{{ $field }}_ar]" value="{{ old('about.page.cta.'.$field.'_ar', $about['cta'][$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    @endif
                </div>
            @endforeach
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_cta_primary_url') }}</label>
                <input type="text" name="about[page][cta][primary_url]" value="{{ old('about.page.cta.primary_url', $about['cta']['primary_url'] ?? '/campaigns') }}" class="ghosn-input" dir="ltr">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.about_cta_secondary_url') }}</label>
                <input type="text" name="about[page][cta][secondary_url]" value="{{ old('about.page.cta.secondary_url', $about['cta']['secondary_url'] ?? '/our-team') }}" class="ghosn-input" dir="ltr">
            </div>
        </div>
    </details>
</form>

@include('admin.cms.partials.media-modal')
