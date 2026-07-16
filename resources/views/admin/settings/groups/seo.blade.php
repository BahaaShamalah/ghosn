<form method="POST" action="{{ route('admin.settings.update.group', 'seo') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="seo">

    @include('admin.settings.partials.form-errors', ['group' => 'seo'])

    <div>
        <h3 class="text-lg font-bold text-[#2f4327]">{{ __('admin.settings.seo_heading') }}</h3>
        <p class="mt-1 max-w-2xl text-sm text-[#5f6857]">{{ __('admin.settings.seo_intro') }}</p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_title_en') }}</label>
            <input type="text" name="seo[title_en]" value="{{ old('seo.title_en', $settings['seo.title_en']) }}" class="ghosn-input" placeholder="{{ \App\Support\SiteSettings::name('en') }}">
            <p class="mt-1 text-xs text-[#8a9280]">{{ __('admin.settings.seo_title_help') }}</p>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_title_ar') }}</label>
            <input type="text" name="seo[title_ar]" value="{{ old('seo.title_ar', $settings['seo.title_ar']) }}" class="ghosn-input" dir="rtl" placeholder="{{ \App\Support\SiteSettings::name('ar') }}">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_description_en') }}</label>
            <textarea name="seo[description_en]" rows="3" class="ghosn-input">{{ old('seo.description_en', $settings['seo.description_en']) }}</textarea>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_description_ar') }}</label>
            <textarea name="seo[description_ar]" rows="3" class="ghosn-input" dir="rtl">{{ old('seo.description_ar', $settings['seo.description_ar']) }}</textarea>
            <p class="mt-1 text-xs text-[#8a9280]">{{ __('admin.settings.seo_description_help') }}</p>
        </div>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_image') }}</label>
        @if ($ogImage = \App\Support\SeoSettings::imageUrl())
            <img src="{{ $ogImage }}" alt="" class="mb-3 max-h-40 w-auto rounded-lg border border-ghosn/10 bg-cream/40 p-2">
        @endif
        <select name="seo[image_media_id]" class="ghosn-input mb-3">
            <option value="">{{ __('admin.settings.media_none') }}</option>
            @foreach ($mediaLibrary->where(fn ($m) => $m->isImage()) as $item)
                <option value="{{ $item->id }}" @selected(old('seo.image_media_id', $settings['seo.image_media_id']) == $item->id)>{{ $item->original_filename }}</option>
            @endforeach
        </select>
        <p class="mb-2 text-xs text-ghosn-ink/55">{{ __('admin.settings.or_upload') }}</p>
        <input type="file" name="seo[image]" accept="image/jpeg,image/png,image/webp" class="ghosn-input file:me-3 file:rounded-lg file:border-0 file:bg-ghosn file:px-3 file:py-2 file:text-sm file:font-medium file:text-offwhite">
        <p class="mt-2 text-xs text-[#8a9280]">{{ __('admin.settings.seo_image_help') }}</p>
    </div>

    <p class="text-xs text-[#8a9280]">
        <a href="{{ route('admin.media.index') }}" class="font-semibold text-ghosn underline">{{ __('admin.settings.open_media') }}</a>
    </p>

    @php
        $robotsTxt = old('seo.robots_txt', is_array($settings['seo.robots_txt'] ?? null)
            ? $settings['seo.robots_txt']
            : \App\Support\RobotsTxtBuilder::defaultConfig());
        $robotsAllow = is_array($robotsTxt['allow'] ?? null) ? implode("\n", $robotsTxt['allow']) : (string) ($robotsTxt['allow'] ?? '');
        $robotsDisallow = is_array($robotsTxt['disallow'] ?? null) ? implode("\n", $robotsTxt['disallow']) : (string) ($robotsTxt['disallow'] ?? "/admin\n/admin/");
    @endphp

    <section class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.seo_meta_advanced') }}</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_robots_default') }}</label>
                <input type="text" name="seo[robots_default]" value="{{ old('seo.robots_default', $settings['seo.robots_default'] ?? 'index,follow') }}" class="ghosn-input" dir="ltr" placeholder="index,follow">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_canonical_mode') }}</label>
                <select name="seo[canonical_mode]" class="ghosn-input">
                    <option value="current" @selected(old('seo.canonical_mode', $settings['seo.canonical_mode'] ?? 'current') === 'current')>{{ __('admin.settings.seo_canonical_current') }}</option>
                    <option value="homepage_prefer" @selected(old('seo.canonical_mode', $settings['seo.canonical_mode'] ?? '') === 'homepage_prefer')>{{ __('admin.settings.seo_canonical_homepage') }}</option>
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_twitter_site') }}</label>
                <input type="text" name="seo[twitter_site]" value="{{ old('seo.twitter_site', $settings['seo.twitter_site'] ?? '') }}" class="ghosn-input" dir="ltr" placeholder="@ghosn">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_organization_type') }}</label>
                <select name="seo[organization_type]" class="ghosn-input">
                    @foreach (['NGO', 'Organization', 'LocalBusiness'] as $type)
                        <option value="{{ $type }}" @selected(old('seo.organization_type', $settings['seo.organization_type'] ?? 'NGO') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_organization_name_en') }}</label>
                <input type="text" name="seo[organization_name_en]" value="{{ old('seo.organization_name_en', $settings['seo.organization_name_en'] ?? '') }}" class="ghosn-input">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_organization_name_ar') }}</label>
                <input type="text" name="seo[organization_name_ar]" value="{{ old('seo.organization_name_ar', $settings['seo.organization_name_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
            </div>
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2">
            @foreach (['organization', 'website', 'article', 'breadcrumb', 'faq'] as $schema)
                <label class="flex items-center gap-3">
                    <input type="hidden" name="seo[schema_{{ $schema }}]" value="0">
                    <input type="checkbox" name="seo[schema_{{ $schema }}]" value="1" @checked(old('seo.schema_'.$schema, $settings['seo.schema_'.$schema] ?? true)) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                    <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_schema_'.$schema) }}</span>
                </label>
            @endforeach
        </div>
    </section>

    <section class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-2 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.seo_robots_txt') }}</h3>
        <p class="mb-4 text-xs text-[#8a9280]">{{ __('admin.settings.seo_robots_txt_help') }} <a href="{{ url('/robots.txt') }}" target="_blank" rel="noopener" class="font-semibold text-ghosn underline">/robots.txt</a></p>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">User-agent</label>
                <input type="text" name="seo[robots_txt][user_agent]" value="{{ old('seo.robots_txt.user_agent', $robotsTxt['user_agent'] ?? '*') }}" class="ghosn-input" dir="ltr">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">Host</label>
                <input type="text" name="seo[robots_txt][host]" value="{{ old('seo.robots_txt.host', $robotsTxt['host'] ?? '') }}" class="ghosn-input" dir="ltr">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">Allow</label>
                <textarea name="seo[robots_txt][allow]" rows="4" class="ghosn-input font-mono text-xs" dir="ltr">{{ old('seo.robots_txt.allow', $robotsAllow) }}</textarea>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">Disallow</label>
                <textarea name="seo[robots_txt][disallow]" rows="4" class="ghosn-input font-mono text-xs" dir="ltr">{{ old('seo.robots_txt.disallow', $robotsDisallow) }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">Sitemap URL</label>
                <input type="text" name="seo[robots_txt][sitemap_url]" value="{{ old('seo.robots_txt.sitemap_url', $robotsTxt['sitemap_url'] ?? url('/sitemap.xml')) }}" class="ghosn-input" dir="ltr">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_robots_extra') }}</label>
                <textarea name="seo[robots_txt][extra]" rows="3" class="ghosn-input font-mono text-xs" dir="ltr">{{ old('seo.robots_txt.extra', $robotsTxt['extra'] ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <section class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-2 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.seo_sitemap') }}</h3>
        <p class="mb-4 text-xs text-[#8a9280]">{{ __('admin.settings.seo_sitemap_help') }} <a href="{{ url('/sitemap.xml') }}" target="_blank" rel="noopener" class="font-semibold text-ghosn underline">/sitemap.xml</a></p>
        <label class="mb-4 flex items-center gap-3">
            <input type="hidden" name="seo[sitemap_enabled]" value="0">
            <input type="checkbox" name="seo[sitemap_enabled]" value="1" @checked(old('seo.sitemap_enabled', $settings['seo.sitemap_enabled'] ?? true)) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
            <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_sitemap_enabled') }}</span>
        </label>
        <div class="grid gap-3 md:grid-cols-2">
            @foreach (['pages', 'posts', 'campaigns', 'categories'] as $include)
                <label class="flex items-center gap-3">
                    <input type="hidden" name="seo[sitemap_include_{{ $include }}]" value="0">
                    <input type="checkbox" name="seo[sitemap_include_{{ $include }}]" value="1" @checked(old('seo.sitemap_include_'.$include, $settings['seo.sitemap_include_'.$include] ?? true)) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                    <span class="text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_sitemap_include_'.$include) }}</span>
                </label>
            @endforeach
        </div>
        <div class="mt-4">
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.seo_sitemap_changefreq') }}</label>
            <select name="seo[sitemap_changefreq]" class="ghosn-input">
                @foreach (['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'] as $freq)
                    <option value="{{ $freq }}" @selected(old('seo.sitemap_changefreq', $settings['seo.sitemap_changefreq'] ?? 'weekly') === $freq)>{{ $freq }}</option>
                @endforeach
            </select>
        </div>
    </section>

    <div class="flex justify-end border-t border-ghosn/10 pt-5">
        <button type="submit" class="ghosn-btn ghosn-btn--primary">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
