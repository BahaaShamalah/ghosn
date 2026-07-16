<form method="POST" action="{{ route('admin.settings.update.group', 'google') }}" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="google">

    @include('admin.settings.partials.form-errors', ['group' => 'google'])

    <p class="text-sm text-[#5f6857]">{{ __('admin.settings.google_intro') }}</p>

    {{-- Analytics --}}
    <section class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.google_analytics') }}</h3>
        <div class="space-y-4">
            <label class="flex items-center gap-3">
                <input type="hidden" name="google[analytics][enabled]" value="0">
                <input type="checkbox" name="google[analytics][enabled]" value="1" @checked(old('google.analytics.enabled', $settings['google.analytics.enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_analytics_enabled') }}</span>
            </label>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_measurement_id') }}</label>
                <input type="text" name="google[analytics][measurement_id]" value="{{ old('google.analytics.measurement_id', $settings['google.analytics.measurement_id']) }}" class="ghosn-input" dir="ltr" placeholder="G-XXXXXXXXXX">
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                @foreach ([
                    'anonymize_ip' => 'google_anonymize_ip',
                    'debug' => 'google_debug',
                    'enhanced_measurement' => 'google_enhanced_measurement',
                    'ecommerce' => 'google_ecommerce',
                ] as $field => $label)
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="google[analytics][{{ $field }}]" value="0">
                        <input type="checkbox" name="google[analytics][{{ $field }}]" value="1" @checked(old('google.analytics.'.$field, $settings['google.analytics.'.$field])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                        <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.'.$label) }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </section>

    {{-- GTM --}}
    <section class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.google_gtm') }}</h3>
        <div class="space-y-4">
            <label class="flex items-center gap-3">
                <input type="hidden" name="google[gtm][enabled]" value="0">
                <input type="checkbox" name="google[gtm][enabled]" value="1" @checked(old('google.gtm.enabled', $settings['google.gtm.enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_gtm_enabled') }}</span>
            </label>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_container_id') }}</label>
                <input type="text" name="google[gtm][container_id]" value="{{ old('google.gtm.container_id', $settings['google.gtm.container_id']) }}" class="ghosn-input" dir="ltr" placeholder="GTM-XXXXXXX">
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                <label class="flex items-center gap-3">
                    <input type="hidden" name="google[gtm][inject_head]" value="0">
                    <input type="checkbox" name="google[gtm][inject_head]" value="1" @checked(old('google.gtm.inject_head', $settings['google.gtm.inject_head'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                    <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_inject_head') }}</span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="hidden" name="google[gtm][inject_body]" value="0">
                    <input type="checkbox" name="google[gtm][inject_body]" value="1" @checked(old('google.gtm.inject_body', $settings['google.gtm.inject_body'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                    <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_inject_body') }}</span>
                </label>
            </div>
        </div>
    </section>

    {{-- Search Console + Merchant --}}
    <section class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.google_search_console') }}</h3>
        <div class="space-y-4">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_verification_meta') }}</label>
                <input type="text" name="google[search_console][meta_tag]" value="{{ old('google.search_console.meta_tag', $settings['google.search_console.meta_tag']) }}" class="ghosn-input" dir="ltr" placeholder="content value or full meta tag">
                <p class="mt-1 text-xs text-[#8a9280]">{{ __('admin.settings.google_verification_meta_help') }}</p>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_verification_file') }}</label>
                @if (! empty($settings['google.search_console.verification_file']))
                    <p class="mb-2 text-xs text-[#5f6857]">{{ __('admin.settings.google_verification_file_current', ['file' => $settings['google.search_console.verification_file']]) }}</p>
                @endif
                <input type="file" name="google[search_console][verification_upload]" accept=".html,.htm,.txt" class="ghosn-input file:me-3 file:rounded-lg file:border-0 file:bg-ghosn file:px-3 file:py-2 file:text-sm file:font-medium file:text-offwhite">
                <input type="hidden" name="google[search_console][verification_file]" value="{{ old('google.search_console.verification_file', $settings['google.search_console.verification_file']) }}">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_merchant_meta') }}</label>
                <input type="text" name="google[merchant][meta_tag]" value="{{ old('google.merchant.meta_tag', $settings['google.merchant.meta_tag']) }}" class="ghosn-input" dir="ltr">
            </div>
        </div>
    </section>

    {{-- Consent --}}
    <section class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.google_consent') }}</h3>
        <div class="space-y-4">
            <label class="flex items-center gap-3">
                <input type="hidden" name="google[consent][enabled]" value="0">
                <input type="checkbox" name="google[consent][enabled]" value="1" @checked(old('google.consent.enabled', $settings['google.consent.enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_consent_enabled') }}</span>
            </label>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach (['analytics_storage', 'ad_storage', 'ad_user_data', 'ad_personalization'] as $consentKey)
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_consent_'.$consentKey) }}</label>
                        <select name="google[consent][{{ $consentKey }}]" class="ghosn-input">
                            <option value="denied" @selected(old('google.consent.'.$consentKey, $settings['google.consent.'.$consentKey]) === 'denied')>denied</option>
                            <option value="granted" @selected(old('google.consent.'.$consentKey, $settings['google.consent.'.$consentKey]) === 'granted')>granted</option>
                        </select>
                    </div>
                @endforeach
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_wait_for_update') }}</label>
                    <input type="number" min="0" max="10000" name="google[consent][wait_for_update]" value="{{ old('google.consent.wait_for_update', $settings['google.consent.wait_for_update']) }}" class="ghosn-input" dir="ltr">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_cookie_days') }}</label>
                    <input type="number" min="1" max="730" name="google[consent][cookie_days]" value="{{ old('google.consent.cookie_days', $settings['google.consent.cookie_days']) }}" class="ghosn-input" dir="ltr">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_consent_regions') }}</label>
                    <input type="text" name="google[consent][regions]" value="{{ old('google.consent.regions', $settings['google.consent.regions']) }}" class="ghosn-input" dir="ltr" placeholder="AT,BE,BG,...">
                    <p class="mt-1 text-xs text-[#8a9280]">{{ __('admin.settings.google_consent_regions_help') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- AdSense --}}
    <section class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.google_adsense') }}</h3>
        <div class="space-y-4">
            <label class="flex items-center gap-3">
                <input type="hidden" name="google[adsense][enabled]" value="0">
                <input type="checkbox" name="google[adsense][enabled]" value="1" @checked(old('google.adsense.enabled', $settings['google.adsense.enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_adsense_enabled') }}</span>
            </label>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_publisher_id') }}</label>
                <input type="text" name="google[adsense][publisher_id]" value="{{ old('google.adsense.publisher_id', $settings['google.adsense.publisher_id']) }}" class="ghosn-input" dir="ltr" placeholder="ca-pub-XXXXXXXXXXXXXXXX">
            </div>
            <label class="flex items-center gap-3">
                <input type="hidden" name="google[adsense][auto_ads]" value="0">
                <input type="checkbox" name="google[adsense][auto_ads]" value="1" @checked(old('google.adsense.auto_ads', $settings['google.adsense.auto_ads'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_auto_ads') }}</span>
            </label>
        </div>
    </section>

    {{-- Maps --}}
    <section class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.google_maps') }}</h3>
        <div class="space-y-4">
            <label class="flex items-center gap-3">
                <input type="hidden" name="google[maps][enabled]" value="0">
                <input type="checkbox" name="google[maps][enabled]" value="1" @checked(old('google.maps.enabled', $settings['google.maps.enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_maps_enabled') }}</span>
            </label>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_maps_api_key') }}</label>
                <input type="text" name="google[maps][api_key]" value="{{ old('google.maps.api_key', $settings['google.maps.api_key']) }}" class="ghosn-input" dir="ltr" autocomplete="off">
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_maps_language') }}</label>
                    <input type="text" name="google[maps][language]" value="{{ old('google.maps.language', $settings['google.maps.language']) }}" class="ghosn-input" dir="ltr" placeholder="en">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_maps_region') }}</label>
                    <input type="text" name="google[maps][region]" value="{{ old('google.maps.region', $settings['google.maps.region']) }}" class="ghosn-input" dir="ltr" placeholder="PS">
                </div>
            </div>
        </div>
    </section>

    {{-- reCAPTCHA --}}
    <section class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.google_recaptcha') }}</h3>
        <div class="space-y-4">
            <label class="flex items-center gap-3">
                <input type="hidden" name="google[recaptcha][enabled]" value="0">
                <input type="checkbox" name="google[recaptcha][enabled]" value="1" @checked(old('google.recaptcha.enabled', $settings['google.recaptcha.enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_recaptcha_enabled') }}</span>
            </label>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_recaptcha_site_key') }}</label>
                    <input type="text" name="google[recaptcha][site_key]" value="{{ old('google.recaptcha.site_key', $settings['google.recaptcha.site_key']) }}" class="ghosn-input" dir="ltr" autocomplete="off">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_recaptcha_secret_key') }}</label>
                    <input type="password" name="google[recaptcha][secret_key]" value="{{ old('google.recaptcha.secret_key', $settings['google.recaptcha.secret_key']) }}" class="ghosn-input" dir="ltr" autocomplete="new-password">
                    <p class="mt-1 text-xs text-[#8a9280]">{{ __('admin.settings.google_recaptcha_secret_help') }}</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_recaptcha_threshold') }}</label>
                    <input type="number" step="0.1" min="0" max="1" name="google[recaptcha][score_threshold]" value="{{ old('google.recaptcha.score_threshold', $settings['google.recaptcha.score_threshold']) }}" class="ghosn-input" dir="ltr">
                </div>
            </div>
            <div class="grid gap-3 md:grid-cols-3">
                @foreach (['contact', 'login', 'register'] as $form)
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="google[recaptcha][{{ $form }}]" value="0">
                        <input type="checkbox" name="google[recaptcha][{{ $form }}]" value="1" @checked(old('google.recaptcha.'.$form, $settings['google.recaptcha.'.$form])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                        <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_recaptcha_'.$form) }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Fonts --}}
    <section class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.google_fonts') }}</h3>
        <p class="mb-4 text-xs text-[#8a9280]">{{ __('admin.settings.google_fonts_help') }}</p>
        <div class="grid gap-3 md:grid-cols-2">
            @foreach ([
                'enable_cdn' => 'google_fonts_cdn',
                'prefer_local' => 'google_fonts_local',
                'preconnect' => 'google_fonts_preconnect',
                'display_swap' => 'google_fonts_swap',
            ] as $field => $label)
                <label class="flex items-center gap-3">
                    <input type="hidden" name="google[fonts][{{ $field }}]" value="0">
                    <input type="checkbox" name="google[fonts][{{ $field }}]" value="1" @checked(old('google.fonts.'.$field, $settings['google.fonts.'.$field])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                    <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.'.$label) }}</span>
                </label>
            @endforeach
        </div>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_fonts_family_en') }}</label>
                <input type="text" name="google[fonts][family_en]" value="{{ old('google.fonts.family_en', $settings['google.fonts.family_en']) }}" class="ghosn-input" dir="ltr">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.google_fonts_family_ar') }}</label>
                <input type="text" name="google[fonts][family_ar]" value="{{ old('google.fonts.family_ar', $settings['google.fonts.family_ar']) }}" class="ghosn-input" dir="ltr">
            </div>
        </div>
    </section>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
