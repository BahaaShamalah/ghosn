@php
    $homeUrl = route('home');
    $locale = app()->getLocale();
    $nav = \App\Support\PublicNavigation::links();
@endphp

<header class="gh-internal-header">
    <div class="gh-internal-header__inner">
        <a href="{{ $homeUrl }}" class="flex items-center gap-3 no-underline">
            <img src="{{ \App\Support\SiteAsset::logoUrl() }}" alt="{{ \App\Support\SiteSettings::name() }}" class="h-11 w-auto">
            <span class="hidden sm:flex flex-col leading-none">
                <span class="text-[19px] font-bold tracking-[3px] text-[#406139]">GHOSN</span>
                <span class="mt-0.5 text-[9px] font-semibold tracking-[4px] text-[#819562]">RELIEF TEAM</span>
            </span>
        </a>

        <nav class="gh-internal-nav" aria-label="{{ __('public.nav.main') }}">
            @foreach ($nav as $link)
                <a href="{{ $link['href'] }}">
                    <span data-en="">{{ $link['label_en'] }}</span>
                    <span data-ar="">{{ $link['label_ar'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3.5">
            <div class="gh-internal-lang">
                <a href="{{ route('locale.switch', 'en') }}" @class(['is-active' => $locale === 'en'])>EN</a>
                <a href="{{ route('locale.switch', 'ar') }}" @class(['is-active' => $locale === 'ar'])>AR</a>
            </div>
            <a href="{{ route('donate') }}" class="gh-internal-donate">
                <span data-en="">{{ \App\Support\SiteChrome::donateLabel('en') }}</span>
                <span data-ar="">{{ \App\Support\SiteChrome::donateLabel('ar') }}</span>
            </a>
            <button type="button" class="gh-internal-menu-btn" data-internal-menu-toggle aria-expanded="false" aria-label="Menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
        </div>
    </div>

    <div class="gh-internal-mobile-menu" data-internal-mobile-menu>
        @foreach ($nav as $link)
            <a href="{{ $link['href'] }}">
                <span data-en="">{{ $link['label_en'] }}</span>
                <span data-ar="">{{ $link['label_ar'] }}</span>
            </a>
        @endforeach
        <a href="{{ route('donate') }}" class="!border-none !text-[#406139]">
            <span data-en="">{{ \App\Support\SiteChrome::donateLabel('en') }}</span>
            <span data-ar="">{{ \App\Support\SiteChrome::donateLabel('ar') }}</span>
        </a>
    </div>
</header>
