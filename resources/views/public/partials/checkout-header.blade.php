<header class="border-b border-ghosn/8 bg-offwhite/90 backdrop-blur-sm">
    <div class="mx-auto flex h-[68px] max-w-6xl items-center justify-between gap-4 px-5 md:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
            <img src="{{ \App\Support\SiteAsset::logoUrl() }}" alt="GHOSN" class="h-10 w-auto">
            <span class="hidden sm:flex flex-col leading-none">
                <span class="font-bold tracking-tightish text-[16px] text-ghosn">GHOSN</span>
                <span class="text-[9px] font-medium uppercase tracking-[0.32em] text-ghosn/55">Relief Team</span>
            </span>
        </a>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('home') }}" class="hidden sm:inline-flex h-9 items-center rounded-full border border-ghosn/15 px-4 text-[13px] font-semibold text-ghosn transition-colors hover:bg-ghosn/5">
                <span data-en="">{{ __('public.checkout.back_to_website') }}</span>
                <span data-ar="">{{ __('public.checkout.back_to_website_ar') }}</span>
            </a>
            <button data-lang-toggle class="flex h-9 items-center gap-1.5 rounded-full border border-ghosn/20 px-3 text-[13px] font-semibold text-ghosn transition-colors hover:bg-ghosn/5">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3c2.5 2.7 2.5 15.3 0 18M12 3c-2.5 2.7-2.5 15.3 0 18"></path></svg>
                <span data-lang-label>العربية</span>
            </button>
        </div>
    </div>
</header>
