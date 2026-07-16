<div class="space-y-5">
    @if ($homePage)
        <div class="flex flex-wrap items-center gap-3">
            <span class="rounded-full bg-growth-soft/60 px-3 py-1 text-xs font-semibold text-ghosn-800">{{ __('admin.pages.sections_count', ['count' => $homePage->sections_count]) }}</span>
        </div>
    @endif

    <div class="flex flex-wrap gap-3">
        @if ($homePage)
            <a href="{{ route('admin.pages.show', $homePage) }}" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
                {{ __('admin.settings.open_builder') }}
            </a>
            @php
                $heroSection = $homePage->sections()->where('key', 'hero')->first();
            @endphp
            @if ($heroSection)
                <a href="{{ route('admin.pages.sections.hero.edit', [$homePage, $heroSection]) }}" class="gh-admin-btn-secondary">
                    {{ __('admin.settings.edit_hero') }}
                </a>
            @endif
        @endif
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="gh-admin-btn-secondary">
            {{ __('admin.pages.preview') }}
        </a>
    </div>
</div>
