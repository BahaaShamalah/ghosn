@php
    use App\Support\SiteChrome;

    $items = $settings['navigation.items'] ?? null;
    if (! is_array($items) || $items === []) {
        $items = SiteChrome::defaultNavItemDefinitions();
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
    ];
@endphp

<form method="POST" action="{{ route('admin.settings.update.group', 'navigation') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="navigation">

    @include('admin.settings.partials.form-errors', ['group' => 'navigation'])

    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <h3 class="text-base font-bold text-[#2f4327]">{{ __('admin.settings.nav_links') }}</h3>
            <p class="text-xs text-[#8a9280]">{{ __('admin.settings.nav_links_help') }}</p>
        </div>

        <div class="space-y-3" id="nav-items-list">
            @foreach ($items as $index => $item)
                <div class="grid gap-3 rounded-[14px] border border-[rgba(64,97,57,0.12)] bg-[rgba(237,238,228,0.35)] p-4 md:grid-cols-[1fr_1fr_1.2fr]">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.nav_label_en') }}</label>
                        <input type="text" name="navigation[items][{{ $index }}][label_en]" value="{{ old('navigation.items.'.$index.'.label_en', $item['label_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.nav_label_ar') }}</label>
                        <input type="text" name="navigation[items][{{ $index }}][label_ar]" value="{{ old('navigation.items.'.$index.'.label_ar', $item['label_ar'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.nav_href') }}</label>
                        <input type="text" name="navigation[items][{{ $index }}][href]" value="{{ old('navigation.items.'.$index.'.href', $item['href'] ?? '') }}" list="nav-route-options" dir="ltr" class="ghosn-input" placeholder="route:about">
                    </div>
                </div>
            @endforeach
        </div>

        <datalist id="nav-route-options">
            @foreach ($routeOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </datalist>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.nav_donate_en') }}</label>
            <input type="text" name="navigation[donate_label_en]" value="{{ old('navigation.donate_label_en', $settings['navigation.donate_label_en']) }}" required class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.nav_donate_ar') }}</label>
            <input type="text" name="navigation[donate_label_ar]" value="{{ old('navigation.donate_label_ar', $settings['navigation.donate_label_ar']) }}" required class="ghosn-input">
        </div>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
