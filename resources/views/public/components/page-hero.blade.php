@php
    $backUrl = $backUrl ?? null;
    $backLabelEn = $backLabelEn ?? '';
    $backLabelAr = $backLabelAr ?? '';
    $titleEn = $titleEn ?? '';
    $titleAr = $titleAr ?? '';
    $eyebrow = $eyebrow ?? null;
    $subtitleEn = $subtitleEn ?? null;
    $subtitleAr = $subtitleAr ?? null;
    $meta = $meta ?? null;
@endphp

<section class="ghosn-page-hero relative overflow-hidden border-b border-ghosn/10 bg-gradient-to-b from-cream/90 via-offwhite to-offwhite">
    {{-- Branch / leaf background (same motif as landing support section) --}}
    <svg viewBox="0 0 600 600" class="ghosn-page-hero__branch ghosn-page-hero__branch--left pointer-events-none absolute -bottom-28 -left-24 w-[min(460px,52vw)] rtl:left-auto rtl:-right-24" fill="#0C5A2E" aria-hidden="true"><path d="M60 540 C 180 440 180 280 300 220 C 420 160 380 60 480 30 C 430 150 500 250 400 320 C 300 390 300 480 180 500 C 130 508 90 525 60 540 Z"></path></svg>
    <svg viewBox="0 0 600 600" class="ghosn-page-hero__branch ghosn-page-hero__branch--right pointer-events-none absolute -top-16 -right-20 w-[min(320px,40vw)] rtl:right-auto rtl:-left-20" fill="#46A45B" aria-hidden="true"><path d="M60 540 C 180 440 180 280 300 220 C 420 160 380 60 480 30 C 430 150 500 250 400 320 C 300 390 300 480 180 500 C 130 508 90 525 60 540 Z"></path></svg>
    <svg viewBox="0 0 80 120" class="ghosn-page-hero__leaf pointer-events-none absolute top-[22%] end-[6%] hidden h-14 w-9 opacity-[0.07] md:block" fill="#0C5A2E" aria-hidden="true"><path d="M40 110 C60 80 72 50 62 24 C52 0 28 0 18 24 C8 50 20 80 40 110Z"></path></svg>

    <div class="relative mx-auto max-w-7xl px-5 md:px-10 py-7 md:py-9" data-reveal>
        @if ($backUrl)
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-sm font-semibold text-ghosn/70 transition hover:text-ghosn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="rtl:rotate-180" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"></path></svg>
                <x-landing.bilingual :en="$backLabelEn" :ar="$backLabelAr" />
            </a>
        @endif

        @if ($eyebrow)
            <div @class(['inline-flex items-center gap-2.5', 'mt-4' => $backUrl, 'mt-0' => ! $backUrl])>
                <span class="h-[1.5px] w-6 bg-growth" aria-hidden="true"></span>
                <span class="text-[11.5px] font-semibold uppercase tracking-[0.18em] text-growth">{{ $eyebrow }}</span>
            </div>
        @endif

        <h1 @class(['max-w-4xl text-[clamp(1.85rem,4vw,3rem)] font-bold leading-[1.12] tracking-tightish text-ghosn', 'mt-3' => $backUrl || $eyebrow, 'mt-0' => ! $backUrl && ! $eyebrow])>
            <x-landing.bilingual :en="$titleEn" :ar="$titleAr" />
        </h1>

        @if ($subtitleEn || $subtitleAr)
            <p class="mt-4 max-w-2xl text-[15px] leading-relaxed text-ghosn-ink/65">
                <x-landing.bilingual :en="$subtitleEn" :ar="$subtitleAr" />
            </p>
        @endif

        @if ($meta)
            <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-ghosn/55">
                {!! $meta !!}
            </div>
        @endif
    </div>
</section>
