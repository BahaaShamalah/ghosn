@extends('public.layouts.checkout')

@section('title', __('public.donate.checkout_title'))

@section('full-bleed-top')
    <section class="relative w-full overflow-hidden bg-gradient-to-br from-[#243619] via-[#3a5330] to-[#2f4327]">
        <div class="relative z-[2] mx-auto max-w-[820px] px-6 py-14 text-center md:py-16">
            <h1 class="mx-auto mb-3 max-w-[640px] text-[clamp(2rem,4.6vw,3.125rem)] font-bold leading-tight text-[#F7F6F0] text-balance">
                <span data-en="">{{ __('public.donate.checkout_title') }}</span>
                <span data-ar="">{{ __('public.donate.checkout_title_ar') }}</span>
            </h1>
            <p class="mx-auto max-w-[520px] text-[clamp(1rem,1.7vw,1.2rem)] text-[#E8ECDD] text-pretty">
                <span data-en="">{{ __('public.donate.checkout_subtitle') }}</span>
                <span data-ar="">{{ __('public.donate.checkout_subtitle_ar') }}</span>
            </p>
        </div>
    </section>
@endsection

@section('content')
    {{-- Campaign banner --}}
    @if (! empty($campaign))
        <div class="mb-6 rounded-2xl border border-growth/25 bg-growth-soft/35 px-5 py-4" data-reveal>
            <p class="text-xs font-semibold uppercase tracking-wide text-growth">{{ __('public.donate.campaign_label') }}</p>
            <h2 class="mt-1 text-lg font-bold text-ghosn"><x-landing.bilingual :en="$campaign->title_en" :ar="$campaign->title_ar" /></h2>
            <p class="mt-2 text-sm text-ghosn-ink/65"><x-landing.bilingual :en="$campaign->excerpt_en" :ar="$campaign->excerpt_ar" /></p>
        </div>
    @endif

    <div class="mb-8 md:mb-10" data-reveal>
        <div class="flex flex-wrap gap-2.5">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-ghosn/12 bg-cream/50 px-3 py-1.5 text-[11.5px] font-semibold text-ghosn/75">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span data-en="">{{ __('public.donate.badge_secure') }}</span><span data-ar="">{{ __('public.donate.badge_secure_ar') }}</span>
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-ghosn/12 bg-cream/50 px-3 py-1.5 text-[11.5px] font-semibold text-ghosn/75">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span data-en="">{{ __('public.donate.badge_trusted') }}</span><span data-ar="">{{ __('public.donate.badge_trusted_ar') }}</span>
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-ghosn/12 bg-cream/50 px-3 py-1.5 text-[11.5px] font-semibold text-ghosn/75">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.6-7 10-7 10z"></path></svg>
                <span data-en="">{{ __('public.donate.badge_transparent') }}</span><span data-ar="">{{ __('public.donate.badge_transparent_ar') }}</span>
            </span>
        </div>
    </div>

    {{-- Main 2-column checkout --}}
    <div class="grid items-start gap-8 lg:grid-cols-[1.15fr_0.85fr]">
        <div data-reveal>
            @include('public.donate.partials.form')
        </div>

        <aside data-reveal style="transition-delay:.08s">
            @include('public.donate.partials.summary')
        </aside>
    </div>

    {{-- Action cards --}}
    <div class="mt-10 grid gap-3 sm:grid-cols-3" data-reveal style="transition-delay:.12s">
        <a href="{{ route('pages.show', 'donation-policy') }}" class="group rounded-2xl border border-ghosn/10 bg-offwhite p-5 transition hover:-translate-y-0.5 hover:border-ghosn/20 hover:shadow-md hover:shadow-ghosn/5">
            <span class="text-sm font-bold text-ghosn"><span data-en="">{{ __('public.donate.action_policy') }}</span><span data-ar="">{{ __('public.donate.action_policy_ar') }}</span></span>
            <span class="mt-1 block text-xs text-ghosn-ink/55"><span data-en="">{{ __('public.donate.action_policy_desc') }}</span><span data-ar="">{{ __('public.donate.action_policy_desc_ar') }}</span></span>
        </a>
        <a href="{{ route('about') }}" class="group rounded-2xl border border-ghosn/10 bg-offwhite p-5 transition hover:-translate-y-0.5 hover:border-ghosn/20 hover:shadow-md hover:shadow-ghosn/5">
            <span class="text-sm font-bold text-ghosn"><span data-en="">{{ __('public.donate.action_about') }}</span><span data-ar="">{{ __('public.donate.action_about_ar') }}</span></span>
            <span class="mt-1 block text-xs text-ghosn-ink/55"><span data-en="">{{ __('public.donate.action_about_desc') }}</span><span data-ar="">{{ __('public.donate.action_about_desc_ar') }}</span></span>
        </a>
        <a href="{{ route('volunteer') }}" class="group rounded-2xl border border-ghosn/10 bg-offwhite p-5 transition hover:-translate-y-0.5 hover:border-ghosn/20 hover:shadow-md hover:shadow-ghosn/5">
            <span class="text-sm font-bold text-ghosn"><span data-en="">{{ __('public.donate.action_join') }}</span><span data-ar="">{{ __('public.donate.action_join_ar') }}</span></span>
            <span class="mt-1 block text-xs text-ghosn-ink/55"><span data-en="">{{ __('public.donate.action_join_desc') }}</span><span data-ar="">{{ __('public.donate.action_join_desc_ar') }}</span></span>
        </a>
    </div>
@endsection

@push('head')
    @include('public.donate.partials.styles')
@endpush

@push('scripts')
    <script>
        document.getElementById('ghosn-root')?.setAttribute('data-ready', '1');
        document.querySelectorAll('[data-reveal]').forEach((el, i) => {
            setTimeout(() => el.classList.add('in'), 80 + i * 60);
        });
    </script>
@endpush
