@extends('admin.layouts.app')

@section('title', __('admin.pages.volunteer_edit'))
@section('page-title', __('admin.pages.volunteer_edit'))
@section('eyebrow', $page->slug.' / volunteer')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ghosn/45">volunteer</p>
            <h2 class="mt-1 text-2xl font-bold text-ghosn">{{ __('admin.pages.volunteer_edit') }}</h2>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.pages.show', $page) }}" class="gh-admin-btn-secondary">{{ __('admin.pages.back') }}</a>
            <a href="{{ \App\Support\BuilderPageRoutes::sectionPreviewUrl($page, $section) }}" target="_blank" rel="noopener" class="gh-admin-btn-primary">{{ __('admin.pages.preview') }}</a>
        </div>
    </div>

    <div class="mb-6 gh-admin-alert gh-admin-alert-success">
        {{ __('admin.pages.live_page_notice_volunteer') }}
    </div>

    @php $c = $volunteerContent; @endphp

    <form method="POST" action="{{ route('admin.pages.sections.volunteer.update', [$page, $section]) }}" enctype="multipart/form-data" class="space-y-8">
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
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.volunteer_hero') }}</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach (['hero_eyebrow', 'hero_title', 'hero_subtitle', 'hero_cta'] as $field)
                    <div @class(['md:col-span-2' => str_contains($field, 'subtitle')])>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (EN)</label>
                        <input type="text" name="content[{{ $field }}_en]" value="{{ old('content.'.$field.'_en', $c[$field.'_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div @class(['md:col-span-2' => str_contains($field, 'subtitle')])>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (AR)</label>
                        <input type="text" name="content[{{ $field }}_ar]" value="{{ old('content.'.$field.'_ar', $c[$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                @endforeach
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_hero_image') }}</label>
                    <select name="content[hero_image_media_id]" class="ghosn-input">
                        <option value="">{{ __('admin.settings.media_none') }}</option>
                        @foreach ($mediaLibrary->filter(fn ($m) => $m->isImage()) as $item)
                            <option value="{{ $item->id }}" @selected(old('content.hero_image_media_id', $c['hero_image_media_id'] ?? '') == $item->id)>{{ $item->original_filename }}</option>
                        @endforeach
                    </select>
                    <p class="mb-2 mt-3 text-xs text-ghosn-ink/55">{{ __('admin.settings.or_upload') }}</p>
                    <input type="file" name="hero_image_upload" accept="image/jpeg,image/png,image/webp" class="ghosn-input file:me-3 file:rounded-lg file:border-0 file:bg-ghosn file:px-3 file:py-2 file:text-sm file:font-medium file:text-offwhite">
                    @if (! empty($c['hero_image_url']))
                        <img src="{{ $c['hero_image_url'] }}" alt="" class="mt-3 max-h-40 rounded-xl border border-ghosn/10">
                    @endif
                </div>
            </div>
        </section>

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.volunteer_why') }}</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach (['why_eyebrow', 'why_title', 'why_intro'] as $field)
                    <div @class(['md:col-span-2' => $field === 'why_intro'])>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (EN)</label>
                        <input type="text" name="content[{{ $field }}_en]" value="{{ old('content.'.$field.'_en', $c[$field.'_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div @class(['md:col-span-2' => $field === 'why_intro'])>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (AR)</label>
                        <input type="text" name="content[{{ $field }}_ar]" value="{{ old('content.'.$field.'_ar', $c[$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                @endforeach
            </div>
            <div class="mt-8 space-y-6">
                @foreach ($c['benefits'] ?? [] as $index => $benefit)
                    <div class="rounded-xl border border-ghosn/10 p-4">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-ghosn/50">{{ $benefit['key'] ?? ('benefit '.$index) }}</p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_title') }} (EN)</label>
                                <input type="text" name="content[benefits][{{ $index }}][title_en]" value="{{ old('content.benefits.'.$index.'.title_en', $benefit['title_en'] ?? '') }}" class="ghosn-input">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_title') }} (AR)</label>
                                <input type="text" name="content[benefits][{{ $index }}][title_ar]" value="{{ old('content.benefits.'.$index.'.title_ar', $benefit['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_body') }} (EN)</label>
                                <textarea name="content[benefits][{{ $index }}][body_en]" rows="2" class="ghosn-input">{{ old('content.benefits.'.$index.'.body_en', $benefit['body_en'] ?? '') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_body') }} (AR)</label>
                                <textarea name="content[benefits][{{ $index }}][body_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('content.benefits.'.$index.'.body_ar', $benefit['body_ar'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.volunteer_areas') }}</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach (['areas_eyebrow', 'areas_title'] as $field)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (EN)</label>
                        <input type="text" name="content[{{ $field }}_en]" value="{{ old('content.'.$field.'_en', $c[$field.'_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (AR)</label>
                        <input type="text" name="content[{{ $field }}_ar]" value="{{ old('content.'.$field.'_ar', $c[$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                @endforeach
            </div>
            <div class="mt-8 space-y-6">
                @foreach ($c['area_items'] ?? [] as $index => $area)
                    <div class="rounded-xl border border-ghosn/10 p-4">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-ghosn/50">{{ $area['key'] ?? ('area '.$index) }}</p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_title') }} (EN)</label>
                                <input type="text" name="content[area_items][{{ $index }}][title_en]" value="{{ old('content.area_items.'.$index.'.title_en', $area['title_en'] ?? '') }}" class="ghosn-input">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_title') }} (AR)</label>
                                <input type="text" name="content[area_items][{{ $index }}][title_ar]" value="{{ old('content.area_items.'.$index.'.title_ar', $area['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_body') }} (EN)</label>
                                <textarea name="content[area_items][{{ $index }}][body_en]" rows="2" class="ghosn-input">{{ old('content.area_items.'.$index.'.body_en', $area['body_en'] ?? '') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_body') }} (AR)</label>
                                <textarea name="content[area_items][{{ $index }}][body_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('content.area_items.'.$index.'.body_ar', $area['body_ar'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.volunteer_how') }}</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach (['how_eyebrow', 'how_title'] as $field)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (EN)</label>
                        <input type="text" name="content[{{ $field }}_en]" value="{{ old('content.'.$field.'_en', $c[$field.'_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (AR)</label>
                        <input type="text" name="content[{{ $field }}_ar]" value="{{ old('content.'.$field.'_ar', $c[$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                @endforeach
            </div>
            <div class="mt-8 space-y-6">
                @foreach ($c['steps'] ?? [] as $index => $step)
                    <div class="rounded-xl border border-ghosn/10 p-4">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-ghosn/50">{{ __('admin.pages.volunteer_step') }} {{ $index + 1 }}</p>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_title') }} (EN)</label>
                                <input type="text" name="content[steps][{{ $index }}][title_en]" value="{{ old('content.steps.'.$index.'.title_en', $step['title_en'] ?? '') }}" class="ghosn-input">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_title') }} (AR)</label>
                                <input type="text" name="content[steps][{{ $index }}][title_ar]" value="{{ old('content.steps.'.$index.'.title_ar', $step['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_body') }} (EN)</label>
                                <textarea name="content[steps][{{ $index }}][body_en]" rows="2" class="ghosn-input">{{ old('content.steps.'.$index.'.body_en', $step['body_en'] ?? '') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_card_body') }} (AR)</label>
                                <textarea name="content[steps][{{ $index }}][body_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('content.steps.'.$index.'.body_ar', $step['body_ar'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.volunteer_testimonial') }}</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach (['testimonial_quote', 'testimonial_name', 'testimonial_role', 'testimonial_initial'] as $field)
                    <div @class(['md:col-span-2' => $field === 'testimonial_quote'])>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (EN)</label>
                        @if ($field === 'testimonial_quote')
                            <textarea name="content[{{ $field }}_en]" rows="3" class="ghosn-input">{{ old('content.'.$field.'_en', $c[$field.'_en'] ?? '') }}</textarea>
                        @else
                            <input type="text" name="content[{{ $field }}_en]" value="{{ old('content.'.$field.'_en', $c[$field.'_en'] ?? '') }}" class="ghosn-input">
                        @endif
                    </div>
                    <div @class(['md:col-span-2' => $field === 'testimonial_quote'])>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (AR)</label>
                        @if ($field === 'testimonial_quote')
                            <textarea name="content[{{ $field }}_ar]" rows="3" class="ghosn-input" dir="rtl">{{ old('content.'.$field.'_ar', $c[$field.'_ar'] ?? '') }}</textarea>
                        @else
                            <input type="text" name="content[{{ $field }}_ar]" value="{{ old('content.'.$field.'_ar', $c[$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.volunteer_apply') }}</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach (['apply_eyebrow', 'apply_title', 'apply_intro'] as $field)
                    <div @class(['md:col-span-2' => $field === 'apply_intro'])>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (EN)</label>
                        <input type="text" name="content[{{ $field }}_en]" value="{{ old('content.'.$field.'_en', $c[$field.'_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div @class(['md:col-span-2' => $field === 'apply_intro'])>
                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.volunteer_'.$field) }} (AR)</label>
                        <input type="text" name="content[{{ $field }}_ar]" value="{{ old('content.'.$field.'_ar', $c[$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                @endforeach
            </div>
            <div class="mt-8 space-y-4">
                @foreach ($c['form_areas'] ?? [] as $index => $area)
                    <div class="grid gap-4 rounded-xl border border-ghosn/10 p-4 md:grid-cols-3">
                        <input type="hidden" name="content[form_areas][{{ $index }}][value]" value="{{ $area['value'] ?? '' }}">
                        <div class="text-sm font-medium text-ghosn-ink/70">{{ $area['value'] ?? '' }}</div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.label_en') }}</label>
                            <input type="text" name="content[form_areas][{{ $index }}][label_en]" value="{{ old('content.form_areas.'.$index.'.label_en', $area['label_en'] ?? '') }}" class="ghosn-input">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.label_ar') }}</label>
                            <input type="text" name="content[form_areas][{{ $index }}][label_ar]" value="{{ old('content.form_areas.'.$index.'.label_ar', $area['label_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="gh-admin-btn-primary">{{ __('admin.pages.save_section') }}</button>
        </div>
    </form>
@endsection
