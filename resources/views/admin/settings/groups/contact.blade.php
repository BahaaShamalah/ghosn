@php
    use App\Support\ContactPageContent;

    $defaults = ContactPageContent::defaults();
    $stored = $settings['contact.page'] ?? null;
    $contactPage = is_array($stored) && $stored !== []
        ? array_replace_recursive($defaults, $stored)
        : $defaults;

    $subjectsEn = $contactPage['form']['subjects_en'] ?? [];
    $subjectsAr = $contactPage['form']['subjects_ar'] ?? [];
    if (is_array($subjectsEn)) {
        $subjectsEn = implode("\n", $subjectsEn);
    }
    if (is_array($subjectsAr)) {
        $subjectsAr = implode("\n", $subjectsAr);
    }
@endphp

<form method="POST" action="{{ route('admin.settings.update.group', 'contact') }}" class="space-y-4">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="contact">

    @include('admin.settings.partials.form-errors', ['group' => 'contact'])

    <div class="sticky top-0 z-20 -mx-2 flex items-center justify-between gap-4 rounded-[14px] border border-[rgba(64,97,57,0.12)] bg-[rgba(255,253,248,0.96)] px-4 py-3 backdrop-blur-md">
        <p class="text-sm text-[#5f6857]">{{ __('admin.settings.contact_intro') }}</p>
        <button type="submit" class="gh-admin-btn-primary shrink-0 shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]" open>
        <summary class="cursor-pointer select-none px-4 py-3 text-sm font-bold text-[#2f4327]">{{ __('admin.settings.contact_site_details') }}</summary>
        <div class="grid gap-4 border-t border-[rgba(64,97,57,0.08)] p-4 md:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_email') }}</label>
                <input type="email" name="contact[email]" value="{{ old('contact.email', $settings['contact.email']) }}" required dir="ltr" class="ghosn-input">
                <p class="mt-1 text-xs text-[#8a9280]">{{ __('admin.settings.contact_email_help') }}</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_inbox_email') }}</label>
                <input type="email" name="contact[inbox_email]" value="{{ old('contact.inbox_email', $settings['contact.inbox_email']) }}" dir="ltr" class="ghosn-input" placeholder="{{ $settings['email.admin_notification_email'] ?: $settings['contact.email'] }}">
                <p class="mt-1 text-xs text-[#8a9280]">{{ __('admin.settings.contact_inbox_email_help') }}</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_phone') }}</label>
                <input type="text" name="contact[phone]" value="{{ old('contact.phone', $settings['contact.phone']) }}" dir="ltr" class="ghosn-input">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_address_en') }}</label>
                <input type="text" name="contact[address_en]" value="{{ old('contact.address_en', $settings['contact.address_en']) }}" class="ghosn-input">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_address_ar') }}</label>
                <input type="text" name="contact[address_ar]" value="{{ old('contact.address_ar', $settings['contact.address_ar']) }}" class="ghosn-input" dir="rtl">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_office_en') }}</label>
                <input type="text" name="contact[office_en]" value="{{ old('contact.office_en', $settings['contact.office_en']) }}" class="ghosn-input">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_office_ar') }}</label>
                <input type="text" name="contact[office_ar]" value="{{ old('contact.office_ar', $settings['contact.office_ar']) }}" class="ghosn-input" dir="rtl">
            </div>
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]" open>
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.contact_section_hero') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="contact[page][sections][hero]" value="0">
                <input type="checkbox" name="contact[page][sections][hero]" value="1" class="rounded border-ghosn/30" @checked(old('contact.page.sections.hero', $contactPage['sections']['hero'] ?? true))>
                {{ __('admin.settings.contact_section_show') }}
            </label>
        </summary>
        <div class="grid gap-4 border-t border-[rgba(64,97,57,0.08)] p-4 md:grid-cols-2">
            @foreach (['page_hero_eyebrow' => __('admin.settings.contact_hero_eyebrow'), 'page_hero_title' => __('admin.settings.contact_hero_title'), 'page_hero_subtitle' => __('admin.settings.contact_hero_subtitle')] as $field => $label)
                <div @class(['md:col-span-2' => str_ends_with($field, 'subtitle')])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ $label }} (EN)</label>
                    @if (str_ends_with($field, 'subtitle'))
                        <textarea name="contact[{{ $field }}_en]" rows="2" class="ghosn-input">{{ old('contact.'.$field.'_en', $settings['contact.'.$field.'_en']) }}</textarea>
                    @else
                        <input type="text" name="contact[{{ $field }}_en]" value="{{ old('contact.'.$field.'_en', $settings['contact.'.$field.'_en']) }}" class="ghosn-input">
                    @endif
                </div>
                <div @class(['md:col-span-2' => str_ends_with($field, 'subtitle')])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ $label }} (AR)</label>
                    @if (str_ends_with($field, 'subtitle'))
                        <textarea name="contact[{{ $field }}_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('contact.'.$field.'_ar', $settings['contact.'.$field.'_ar']) }}</textarea>
                    @else
                        <input type="text" name="contact[{{ $field }}_ar]" value="{{ old('contact.'.$field.'_ar', $settings['contact.'.$field.'_ar']) }}" class="ghosn-input" dir="rtl">
                    @endif
                </div>
            @endforeach
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]">
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.contact_section_details') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="contact[page][sections][details]" value="0">
                <input type="checkbox" name="contact[page][sections][details]" value="1" class="rounded border-ghosn/30" @checked(old('contact.page.sections.details', $contactPage['sections']['details'] ?? true))>
                {{ __('admin.settings.contact_section_show') }}
            </label>
        </summary>
        <div class="space-y-4 border-t border-[rgba(64,97,57,0.08)] p-4">
            <div class="grid gap-4 md:grid-cols-2">
                @foreach (['page_info_eyebrow' => __('admin.settings.contact_info_eyebrow'), 'page_info_title' => __('admin.settings.contact_info_title'), 'page_info_body' => __('admin.settings.contact_info_body')] as $field => $label)
                    <div @class(['md:col-span-2' => str_ends_with($field, 'body')])>
                        <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ $label }} (EN)</label>
                        @if (str_ends_with($field, 'body'))
                            <textarea name="contact[{{ $field }}_en]" rows="2" class="ghosn-input">{{ old('contact.'.$field.'_en', $settings['contact.'.$field.'_en']) }}</textarea>
                        @else
                            <input type="text" name="contact[{{ $field }}_en]" value="{{ old('contact.'.$field.'_en', $settings['contact.'.$field.'_en']) }}" class="ghosn-input">
                        @endif
                    </div>
                    <div @class(['md:col-span-2' => str_ends_with($field, 'body')])>
                        <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ $label }} (AR)</label>
                        @if (str_ends_with($field, 'body'))
                            <textarea name="contact[{{ $field }}_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('contact.'.$field.'_ar', $settings['contact.'.$field.'_ar']) }}</textarea>
                        @else
                            <input type="text" name="contact[{{ $field }}_ar]" value="{{ old('contact.'.$field.'_ar', $settings['contact.'.$field.'_ar']) }}" class="ghosn-input" dir="rtl">
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_links_title_en') }}</label>
                    <input type="text" name="contact[page][links][title_en]" value="{{ old('contact.page.links.title_en', $contactPage['links']['title_en'] ?? '') }}" class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_links_title_ar') }}</label>
                    <input type="text" name="contact[page][links][title_ar]" value="{{ old('contact.page.links.title_ar', $contactPage['links']['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_links_follow_en') }}</label>
                    <input type="text" name="contact[page][links][follow_en]" value="{{ old('contact.page.links.follow_en', $contactPage['links']['follow_en'] ?? '') }}" class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_links_follow_ar') }}</label>
                    <input type="text" name="contact[page][links][follow_ar]" value="{{ old('contact.page.links.follow_ar', $contactPage['links']['follow_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                </div>
            </div>
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]">
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.contact_section_form') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="contact[page][sections][form]" value="0">
                <input type="checkbox" name="contact[page][sections][form]" value="1" class="rounded border-ghosn/30" @checked(old('contact.page.sections.form', $contactPage['sections']['form'] ?? true))>
                {{ __('admin.settings.contact_section_show') }}
            </label>
        </summary>
        <div class="grid gap-4 border-t border-[rgba(64,97,57,0.08)] p-4 md:grid-cols-2">
            @foreach (['title', 'subtitle', 'name', 'name_ph', 'email', 'email_ph', 'subject', 'subject_ph', 'message', 'message_ph', 'submit', 'sending', 'success', 'error'] as $field)
                @php
                    $labelKey = 'contact_form_'.$field;
                    $isTextarea = in_array($field, ['subtitle', 'success', 'error', 'message_ph'], true);
                @endphp
                <div @class(['md:col-span-2' => $isTextarea])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.'.$labelKey) }} (EN)</label>
                    @if ($isTextarea)
                        <textarea name="contact[page][form][{{ $field }}_en]" rows="2" class="ghosn-input">{{ old('contact.page.form.'.$field.'_en', $contactPage['form'][$field.'_en'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="contact[page][form][{{ $field }}_en]" value="{{ old('contact.page.form.'.$field.'_en', $contactPage['form'][$field.'_en'] ?? '') }}" class="ghosn-input">
                    @endif
                </div>
                <div @class(['md:col-span-2' => $isTextarea])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.'.$labelKey) }} (AR)</label>
                    @if ($isTextarea)
                        <textarea name="contact[page][form][{{ $field }}_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('contact.page.form.'.$field.'_ar', $contactPage['form'][$field.'_ar'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="contact[page][form][{{ $field }}_ar]" value="{{ old('contact.page.form.'.$field.'_ar', $contactPage['form'][$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    @endif
                </div>
            @endforeach
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_form_subjects_en') }}</label>
                <textarea name="contact[page][form][subjects_en]" rows="4" class="ghosn-input" placeholder="{{ __('admin.settings.contact_form_subjects_help') }}">{{ old('contact.page.form.subjects_en', $subjectsEn) }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_form_subjects_ar') }}</label>
                <textarea name="contact[page][form][subjects_ar]" rows="4" class="ghosn-input" dir="rtl">{{ old('contact.page.form.subjects_ar', $subjectsAr) }}</textarea>
            </div>
        </div>
    </details>

    <details class="rounded-[16px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8]">
        <summary class="flex cursor-pointer select-none items-center justify-between gap-3 px-4 py-3">
            <span class="text-sm font-bold text-[#2f4327]">{{ __('admin.settings.contact_section_cta') }}</span>
            <label class="flex items-center gap-2 text-xs font-medium text-[#5f6857]" onclick="event.stopPropagation()">
                <input type="hidden" name="contact[page][sections][cta]" value="0">
                <input type="checkbox" name="contact[page][sections][cta]" value="1" class="rounded border-ghosn/30" @checked(old('contact.page.sections.cta', $contactPage['sections']['cta'] ?? true))>
                {{ __('admin.settings.contact_section_show') }}
            </label>
        </summary>
        <div class="grid gap-4 border-t border-[rgba(64,97,57,0.08)] p-4 md:grid-cols-2">
            @foreach (['title', 'subtitle', 'primary', 'secondary'] as $field)
                <div @class(['md:col-span-2' => $field === 'subtitle'])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_cta_'.$field.'_en') }}</label>
                    @if ($field === 'subtitle')
                        <textarea name="contact[page][cta][{{ $field }}_en]" rows="2" class="ghosn-input">{{ old('contact.page.cta.'.$field.'_en', $contactPage['cta'][$field.'_en'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="contact[page][cta][{{ $field }}_en]" value="{{ old('contact.page.cta.'.$field.'_en', $contactPage['cta'][$field.'_en'] ?? '') }}" class="ghosn-input">
                    @endif
                </div>
                <div @class(['md:col-span-2' => $field === 'subtitle'])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_cta_'.$field.'_ar') }}</label>
                    @if ($field === 'subtitle')
                        <textarea name="contact[page][cta][{{ $field }}_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('contact.page.cta.'.$field.'_ar', $contactPage['cta'][$field.'_ar'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="contact[page][cta][{{ $field }}_ar]" value="{{ old('contact.page.cta.'.$field.'_ar', $contactPage['cta'][$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    @endif
                </div>
            @endforeach
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_cta_primary_url') }}</label>
                <input type="text" name="contact[page][cta][primary_url]" value="{{ old('contact.page.cta.primary_url', $contactPage['cta']['primary_url'] ?? '/donate') }}" class="ghosn-input" dir="ltr">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.contact_cta_secondary_url') }}</label>
                <input type="text" name="contact[page][cta][secondary_url]" value="{{ old('contact.page.cta.secondary_url', $contactPage['cta']['secondary_url'] ?? '/volunteer') }}" class="ghosn-input" dir="ltr">
            </div>
        </div>
    </details>
</form>
