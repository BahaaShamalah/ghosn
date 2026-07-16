@php
    use App\Support\SiteChrome;

    $items = $settings['footer.links'] ?? null;
    if (! is_array($items) || $items === []) {
        $items = SiteChrome::defaultFooterLinkDefinitions();
    }

    $routeOptions = [
        'route:home' => __('admin.settings.nav_route_home'),
        'route:about' => __('admin.settings.nav_route_about'),
        'route:campaigns.index' => __('admin.settings.nav_route_campaigns'),
        'route:news.index' => __('admin.settings.nav_route_news'),
        'route:team' => __('admin.settings.nav_route_team'),
        'route:volunteer' => __('admin.settings.nav_route_volunteer'),
        'route:contact' => __('admin.settings.nav_route_contact'),
        'route:donate' => __('admin.settings.nav_route_donate'),
        '/donation-policy' => __('admin.settings.footer_route_donation'),
        '/privacy-policy' => __('admin.settings.footer_route_privacy'),
        '/terms-of-use' => __('admin.settings.footer_route_terms'),
        '/refund-policy' => __('admin.settings.footer_route_refund'),
    ];
@endphp

<form method="POST" action="{{ route('admin.settings.update.group', 'footer') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="footer">

    @include('admin.settings.partials.form-errors', ['group' => 'footer'])

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_desc_en') }}</label>
            <textarea name="footer[desc_en]" rows="3" class="ghosn-input">{{ old('footer.desc_en', $settings['footer.desc_en']) }}</textarea>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_desc_ar') }}</label>
            <textarea name="footer[desc_ar]" rows="3" class="ghosn-input" dir="rtl">{{ old('footer.desc_ar', $settings['footer.desc_ar']) }}</textarea>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_tagline_en') }}</label>
            <input type="text" name="footer[tagline_en]" value="{{ old('footer.tagline_en', $settings['footer.tagline_en']) }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_tagline_ar') }}</label>
            <input type="text" name="footer[tagline_ar]" value="{{ old('footer.tagline_ar', $settings['footer.tagline_ar']) }}" class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_quick_en') }}</label>
            <input type="text" name="footer[quick_title_en]" value="{{ old('footer.quick_title_en', $settings['footer.quick_title_en']) }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_quick_ar') }}</label>
            <input type="text" name="footer[quick_title_ar]" value="{{ old('footer.quick_title_ar', $settings['footer.quick_title_ar']) }}" class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_links_en') }}</label>
            <input type="text" name="footer[links_title_en]" value="{{ old('footer.links_title_en', $settings['footer.links_title_en']) }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_links_ar') }}</label>
            <input type="text" name="footer[links_title_ar]" value="{{ old('footer.links_title_ar', $settings['footer.links_title_ar']) }}" class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_contact_en') }}</label>
            <input type="text" name="footer[contact_title_en]" value="{{ old('footer.contact_title_en', $settings['footer.contact_title_en']) }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_contact_ar') }}</label>
            <input type="text" name="footer[contact_title_ar]" value="{{ old('footer.contact_title_ar', $settings['footer.contact_title_ar']) }}" class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_follow_en') }}</label>
            <input type="text" name="footer[follow_title_en]" value="{{ old('footer.follow_title_en', $settings['footer.follow_title_en']) }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_follow_ar') }}</label>
            <input type="text" name="footer[follow_title_ar]" value="{{ old('footer.follow_title_ar', $settings['footer.follow_title_ar']) }}" class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_rights_en') }}</label>
            <input type="text" name="footer[rights_en]" value="{{ old('footer.rights_en', $settings['footer.rights_en']) }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.footer_rights_ar') }}</label>
            <input type="text" name="footer[rights_ar]" value="{{ old('footer.rights_ar', $settings['footer.rights_ar']) }}" class="ghosn-input" dir="rtl">
        </div>
    </div>

    <div class="space-y-4 border-t border-[rgba(64,97,57,0.1)] pt-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-[#2f4327]">{{ __('admin.settings.footer_links_heading') }}</h3>
                <p class="text-xs text-[#8a9280]">{{ __('admin.settings.footer_links_help') }}</p>
            </div>
        </div>

        <div class="space-y-3">
            @foreach ($items as $index => $item)
                <div class="grid gap-3 rounded-[14px] border border-[rgba(64,97,57,0.12)] bg-[rgba(237,238,228,0.35)] p-4 md:grid-cols-[1fr_1fr_1.2fr]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.nav_label_en') }}</label>
                        <input type="text" name="footer[links][{{ $index }}][label_en]" value="{{ old('footer.links.'.$index.'.label_en', $item['label_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.nav_label_ar') }}</label>
                        <input type="text" name="footer[links][{{ $index }}][label_ar]" value="{{ old('footer.links.'.$index.'.label_ar', $item['label_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.nav_href') }}</label>
                        <input type="text" name="footer[links][{{ $index }}][href]" value="{{ old('footer.links.'.$index.'.href', $item['href'] ?? '') }}" list="footer-route-options" dir="ltr" class="ghosn-input" placeholder="/privacy-policy">
                    </div>
                </div>
            @endforeach

            @for ($extra = 0; $extra < 2; $extra++)
                @php $index = count($items) + $extra; @endphp
                <div class="grid gap-3 rounded-[14px] border border-dashed border-[rgba(64,97,57,0.18)] bg-[#fffdf8] p-4 md:grid-cols-[1fr_1fr_1.2fr]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.nav_label_en') }}</label>
                        <input type="text" name="footer[links][{{ $index }}][label_en]" value="{{ old('footer.links.'.$index.'.label_en') }}" class="ghosn-input" placeholder="{{ __('admin.settings.footer_links_new') }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.nav_label_ar') }}</label>
                        <input type="text" name="footer[links][{{ $index }}][label_ar]" value="{{ old('footer.links.'.$index.'.label_ar') }}" class="ghosn-input" dir="rtl">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.nav_href') }}</label>
                        <input type="text" name="footer[links][{{ $index }}][href]" value="{{ old('footer.links.'.$index.'.href') }}" list="footer-route-options" dir="ltr" class="ghosn-input" placeholder="/privacy-policy">
                    </div>
                </div>
            @endfor
        </div>

        <datalist id="footer-route-options">
            @foreach ($routeOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </datalist>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
