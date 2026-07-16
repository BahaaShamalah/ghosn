@extends('public.layouts.checkout')

@section('title', __('public.donate.success_title'))

@section('content')
    <div class="mx-auto max-w-lg text-center" data-reveal>
        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-growth text-offwhite">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"></path></svg>
        </div>
        <h1 class="text-2xl font-bold tracking-tightish text-ghosn md:text-3xl">
            <span data-en="">{{ __('public.donate.success_heading') }}</span>
            <span data-ar="">{{ __('public.donate.success_heading_ar') }}</span>
        </h1>
        <p class="mt-4 text-[15px] leading-relaxed text-ghosn-ink/70">
            <span data-en="">{{ __('public.donate.success_message', ['amount' => $donation->formattedAmount(), 'reference' => $donation->reference]) }}</span>
            <span data-ar="">{{ __('public.donate.success_message_ar', ['amount' => $donation->formattedAmount(), 'reference' => $donation->reference]) }}</span>
        </p>

        <div class="mt-6 rounded-2xl border border-ghosn/10 bg-offwhite px-5 py-4 text-sm">
            <p class="text-ghosn-ink/55"><span data-en="">{{ __('public.donate.your_reference') }}</span><span data-ar="">{{ __('public.donate.your_reference_ar') }}</span></p>
            <p class="mt-1 text-xl font-bold text-ghosn" dir="ltr">{{ $donation->reference }}</p>
        </div>

        <a href="{{ route('home') }}" class="mt-8 inline-flex h-11 items-center rounded-full bg-ghosn px-6 text-sm font-semibold text-offwhite hover:bg-ghosn-700">
            <span data-en="">{{ __('public.donate.back_home') }}</span><span data-ar="">{{ __('public.donate.back_home_ar') }}</span>
        </a>
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
