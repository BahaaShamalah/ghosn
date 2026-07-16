@extends('public.layouts.checkout')

@section('title', $page['title_en'] ?? 'Page')

@section('content')
    <div class="max-w-2xl" data-reveal>
        <a href="{{ route('donate') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm font-semibold text-ghosn/70 transition-colors hover:text-ghosn">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="rtl:rotate-180"><path d="M19 12H5M11 18l-6-6 6-6"></path></svg>
            <span data-en="">{{ __('public.checkout.back_to_donate') }}</span>
            <span data-ar="">{{ __('public.checkout.back_to_donate_ar') }}</span>
        </a>

        <h1 class="text-[clamp(1.8rem,3vw,2.4rem)] font-bold tracking-tightish text-ghosn">
            <span data-en="">{{ $page['title_en'] }}</span><span data-ar="">{{ $page['title_ar'] }}</span>
        </h1>

        <div class="mt-8 space-y-5 rounded-3xl border border-ghosn/10 bg-offwhite p-6 md:p-8">
            @foreach ($page['paragraphs'] as $paragraph)
                <p class="text-[15px] leading-relaxed text-ghosn-ink/75">
                    <span data-en="">{{ $paragraph['en'] }}</span><span data-ar="">{{ $paragraph['ar'] }}</span>
                </p>
            @endforeach

            @if (! empty($page['cta_url']))
                <a href="{{ url($page['cta_url']) }}" class="mt-4 inline-flex h-11 items-center rounded-full bg-ghosn px-6 text-sm font-semibold text-offwhite hover:bg-ghosn-700">
                    <span data-en="">{{ $page['cta_en'] }}</span><span data-ar="">{{ $page['cta_ar'] }}</span>
                </a>
            @endif
        </div>
    </div>
@endsection

@push('head')
    @include('public.donate.partials.styles')
@endpush

@push('scripts')
    <script>
        document.getElementById('ghosn-root')?.setAttribute('data-ready', '1');
        document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('in'));
    </script>
@endpush
