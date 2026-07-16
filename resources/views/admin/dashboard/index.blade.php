@extends('admin.layouts.app')

@section('title', __('admin.dashboard.title'))
@section('page-title', __('admin.dashboard.title'))
@section('eyebrow', __('admin.panel'))

@section('content')
    <div class="mb-8">
        <p class="text-sm text-ghosn-ink/60">{{ __('admin.dashboard.welcome') }}</p>
        <p class="mt-1 text-2xl font-bold tracking-tightish text-ghosn">{{ auth()->user()->name }}</p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-3xl border border-ghosn/10 bg-offwhite p-6 shadow-sm shadow-ghosn/5">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ghosn/45">{{ __('admin.dashboard.pages') }}</p>
            <p class="mt-3 text-3xl font-bold text-ghosn">{{ $pagesCount }}</p>
        </div>
        <div class="rounded-3xl border border-ghosn/10 bg-offwhite p-6 shadow-sm shadow-ghosn/5">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ghosn/45">{{ __('admin.dashboard.sections') }}</p>
            <p class="mt-3 text-3xl font-bold text-ghosn">{{ $sectionsCount }}</p>
        </div>
        <div class="rounded-3xl border border-ghosn/10 bg-gradient-to-br from-ghosn to-ghosn-700 p-6 text-offwhite shadow-lg shadow-ghosn/20 sm:col-span-2 xl:col-span-1">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-growth-light">{{ __('admin.dashboard.quick_links') }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('admin.settings.index') }}" class="rounded-full bg-offwhite/15 px-4 py-2 text-sm font-medium hover:bg-offwhite/25">{{ __('admin.nav.settings') }}</a>
                <a href="{{ route('admin.pages.index') }}" class="rounded-full bg-offwhite/15 px-4 py-2 text-sm font-medium hover:bg-offwhite/25">{{ __('admin.nav.pages') }}</a>
                @if ($homepage)
                    <a href="{{ route('admin.pages.show', $homepage) }}" class="rounded-full bg-offwhite/15 px-4 py-2 text-sm font-medium hover:bg-offwhite/25">{{ __('admin.pages.homepage') }}</a>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-8 rounded-3xl border border-ghosn/10 bg-cream/50 p-6">
        <h2 class="text-lg font-bold text-ghosn">{{ __('admin.dashboard.next_steps') }}</h2>
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-ghosn-ink/70">{{ __('admin.dashboard.placeholder') }}</p>
    </div>
@endsection
