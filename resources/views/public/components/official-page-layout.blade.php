@php
    $titleEn = $titleEn ?? '';
    $titleAr = $titleAr ?? '';
    $subtitleEn = $subtitleEn ?? null;
    $subtitleAr = $subtitleAr ?? null;
    $featuredImageUrl = $featuredImageUrl ?? null;
    $contentEn = $contentEn ?? '';
    $contentAr = $contentAr ?? '';
    $preview = $preview ?? false;
    $updatedAt = $updatedAt ?? null;
    $locale = app()->getLocale();
    $displayTitle = $locale === 'ar' && $titleAr !== '' ? $titleAr : $titleEn;
    $displaySubtitle = $locale === 'ar' && $subtitleAr ? $subtitleAr : ($subtitleEn ?: $subtitleAr);
@endphp

<div class="official-page">
    @if ($preview)
        <div class="border-b border-amber-200 bg-amber-50 px-5 py-2.5 text-center text-sm font-semibold text-amber-950">{{ __('public.pages.preview_notice') }}</div>
    @endif

    <section class="official-page-hero">
        <div class="official-page-hero__inner gh-reveal-internal">
            <nav class="gh-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ __('public.pages.home') }}</a>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                <span class="gh-breadcrumb__current">{{ $displayTitle }}</span>
            </nav>

            <h1 class="official-page-hero__title">
                <x-landing.bilingual :en="$titleEn" :ar="$titleAr" />
            </h1>

            @if ($displaySubtitle)
                <p class="official-page-hero__subtitle">
                    <x-landing.bilingual :en="$subtitleEn" :ar="$subtitleAr" />
                </p>
            @endif

            @if ($updatedAt)
                <div class="official-page-hero__badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    {{ __('public.pages.last_updated') }} {{ $updatedAt->format('M j, Y') }}
                </div>
            @endif
        </div>
    </section>

    <section class="official-page-body">
        <div class="official-page-body__inner max-w-[850px]">
            @if ($featuredImageUrl)
                <div class="official-page-featured gh-reveal-internal">
                    <img src="{{ $featuredImageUrl }}" alt="">
                </div>
            @endif

            <article class="official-page-card gh-reveal-internal">
                <x-public.prose-content :content-en="$contentEn" :content-ar="$contentAr" />
            </article>
        </div>
    </section>
</div>
