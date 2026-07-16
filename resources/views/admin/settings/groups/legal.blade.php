@php
    use App\Support\LegalPageContent;

    $defaults = LegalPageContent::defaults()['pages'] ?? [];
    $stored = $settings['legal.pages'] ?? null;
    $pages = is_array($stored) && $stored !== []
        ? array_replace_recursive($defaults, $stored)
        : $defaults;
    $order = config('legal-pages.order', []);
    $tabs = config('legal-pages.tabs', []);
@endphp

<form method="POST" action="{{ route('admin.settings.update.group', 'legal') }}" class="space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="legal">

    @include('admin.settings.partials.form-errors', ['group' => 'legal'])

    <p class="text-sm text-[#5f6857]">{{ __('admin.settings.legal_intro') }}</p>

    @foreach ($order as $pageKey)
        @php
            $page = $pages[$pageKey] ?? [];
            $tabLabel = $tabs[$pageKey]['label_en'] ?? $pageKey;
        @endphp
        <div class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
            <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ $tabLabel }}</h3>
            <div class="mb-5 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.legal_title_en') }}</label>
                    <input type="text" name="legal[pages][{{ $pageKey }}][title_en]" value="{{ old('legal.pages.'.$pageKey.'.title_en', $page['title_en'] ?? '') }}" class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.legal_title_ar') }}</label>
                    <input type="text" name="legal[pages][{{ $pageKey }}][title_ar]" value="{{ old('legal.pages.'.$pageKey.'.title_ar', $page['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.legal_subtitle_en') }}</label>
                    <textarea name="legal[pages][{{ $pageKey }}][subtitle_en]" rows="2" class="ghosn-input">{{ old('legal.pages.'.$pageKey.'.subtitle_en', $page['subtitle_en'] ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.legal_subtitle_ar') }}</label>
                    <textarea name="legal[pages][{{ $pageKey }}][subtitle_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('legal.pages.'.$pageKey.'.subtitle_ar', $page['subtitle_ar'] ?? '') }}</textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.legal_updated_en') }}</label>
                    <input type="text" name="legal[pages][{{ $pageKey }}][updated_en]" value="{{ old('legal.pages.'.$pageKey.'.updated_en', $page['updated_en'] ?? '') }}" class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.legal_updated_ar') }}</label>
                    <input type="text" name="legal[pages][{{ $pageKey }}][updated_ar]" value="{{ old('legal.pages.'.$pageKey.'.updated_ar', $page['updated_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.legal_intro_en') }}</label>
                    <textarea name="legal[pages][{{ $pageKey }}][intro_en]" rows="3" class="ghosn-input">{{ old('legal.pages.'.$pageKey.'.intro_en', $page['intro_en'] ?? '') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.legal_intro_ar') }}</label>
                    <textarea name="legal[pages][{{ $pageKey }}][intro_ar]" rows="3" class="ghosn-input" dir="rtl">{{ old('legal.pages.'.$pageKey.'.intro_ar', $page['intro_ar'] ?? '') }}</textarea>
                </div>
            </div>

            <div class="space-y-4 border-t border-[rgba(64,97,57,0.1)] pt-5">
                <h4 class="text-sm font-bold uppercase tracking-wide text-[#819562]">{{ __('admin.settings.legal_sections') }}</h4>
                @foreach ($page['sections'] ?? [] as $sectionIndex => $section)
                    <div class="rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.35)] p-4">
                        <div class="mb-3 grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.legal_section_heading_en') }}</label>
                                <input type="text" name="legal[pages][{{ $pageKey }}][sections][{{ $sectionIndex }}][heading_en]" value="{{ old('legal.pages.'.$pageKey.'.sections.'.$sectionIndex.'.heading_en', $section['heading_en'] ?? '') }}" class="ghosn-input">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.legal_section_heading_ar') }}</label>
                                <input type="text" name="legal[pages][{{ $pageKey }}][sections][{{ $sectionIndex }}][heading_ar]" value="{{ old('legal.pages.'.$pageKey.'.sections.'.$sectionIndex.'.heading_ar', $section['heading_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.legal_paragraphs_en') }}</label>
                                <textarea name="legal[pages][{{ $pageKey }}][sections][{{ $sectionIndex }}][paragraphs_en]" rows="3" class="ghosn-input">{{ old('legal.pages.'.$pageKey.'.sections.'.$sectionIndex.'.paragraphs_en', is_array($section['paragraphs_en'] ?? null) ? implode("\n", $section['paragraphs_en']) : '') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.legal_paragraphs_ar') }}</label>
                                <textarea name="legal[pages][{{ $pageKey }}][sections][{{ $sectionIndex }}][paragraphs_ar]" rows="3" class="ghosn-input" dir="rtl">{{ old('legal.pages.'.$pageKey.'.sections.'.$sectionIndex.'.paragraphs_ar', is_array($section['paragraphs_ar'] ?? null) ? implode("\n", $section['paragraphs_ar']) : '') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.legal_bullets_en') }}</label>
                                <textarea name="legal[pages][{{ $pageKey }}][sections][{{ $sectionIndex }}][bullets_en]" rows="3" class="ghosn-input">{{ old('legal.pages.'.$pageKey.'.sections.'.$sectionIndex.'.bullets_en', is_array($section['bullets_en'] ?? null) ? implode("\n", $section['bullets_en']) : '') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.legal_bullets_ar') }}</label>
                                <textarea name="legal[pages][{{ $pageKey }}][sections][{{ $sectionIndex }}][bullets_ar]" rows="3" class="ghosn-input" dir="rtl">{{ old('legal.pages.'.$pageKey.'.sections.'.$sectionIndex.'.bullets_ar', is_array($section['bullets_ar'] ?? null) ? implode("\n", $section['bullets_ar']) : '') }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
