@extends('public.layouts.content')

@section('title', __('public.campaigns.title'))

@section('content-body')
    <x-public.page-hero
        :title-en="__('public.campaigns.title')"
        :title-ar="__('public.campaigns.title_ar')"
        :eyebrow="__('public.campaigns.eyebrow')"
        :subtitle-en="__('public.campaigns.subtitle')"
        :subtitle-ar="__('public.campaigns.subtitle_ar')"
    />

    <section class="public-content-section">
        <div class="max-w-7xl mx-auto px-5 md:px-10">
            <form method="GET" class="mb-8 flex flex-wrap gap-3">
                <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('public.campaigns.search_placeholder') }}" class="rounded-full border border-ghosn/15 bg-offwhite px-4 py-2.5 text-sm min-w-[220px]">
                @if ($categories->isNotEmpty())
                    <select name="category" class="rounded-full border border-ghosn/15 bg-offwhite px-4 py-2.5 text-sm">
                        <option value="">{{ __('public.campaigns.all_categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected($activeCategory === $category->slug)>{{ $category->localizedName() }}</option>
                        @endforeach
                    </select>
                @endif
                <button type="submit" class="rounded-full bg-ghosn px-5 py-2.5 text-sm font-semibold text-offwhite">{{ __('public.campaigns.filter') }}</button>
            </form>

            @if ($campaigns->isEmpty())
                <div class="rounded-3xl border border-dashed border-ghosn/20 bg-cream/40 p-10 text-center text-sm text-ghosn-ink/65">{{ __('public.campaigns.empty') }}</div>
            @else
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($campaigns as $campaign)
                        <article class="flex flex-col overflow-hidden rounded-3xl border border-ghosn/10 bg-offwhite shadow-sm shadow-ghosn/5">
                            <a href="{{ route('campaigns.show', $campaign->slug) }}">
                                @if ($campaign->featuredImage)
                                    <img src="{{ $campaign->featuredImage->thumbnailUrl() ?? $campaign->featuredImage->url() }}" alt="" class="h-48 w-full object-cover">
                                @else
                                    <div class="flex h-48 items-center justify-center bg-ghosn/8 text-sm font-semibold text-ghosn/40">GHOSN</div>
                                @endif
                            </a>
                            <div class="flex flex-1 flex-col p-6">
                                @if ($campaign->category)
                                    <p class="text-xs font-semibold uppercase tracking-wide text-growth">{{ $campaign->category->localizedName() }}</p>
                                @endif
                                <h2 class="mt-2 text-xl font-bold text-ghosn">
                                    <a href="{{ route('campaigns.show', $campaign->slug) }}"><x-landing.bilingual :en="$campaign->title_en" :ar="$campaign->title_ar" /></a>
                                </h2>
                                <p class="mt-2 line-clamp-3 text-sm text-ghosn-ink/65"><x-landing.bilingual :en="$campaign->excerpt_en" :ar="$campaign->excerpt_ar" /></p>
                                <div class="mt-5">@include('public.campaigns.partials.progress', ['campaign' => $campaign, 'compact' => true])</div>
                                <div class="mt-5 flex gap-2">
                                    <a href="{{ route('donate', ['campaign' => $campaign->slug]) }}" class="flex-1 rounded-full bg-ghosn px-4 py-2.5 text-center text-sm font-semibold text-offwhite">{{ __('public.campaigns.donate_now') }}</a>
                                    <a href="{{ route('campaigns.show', $campaign->slug) }}" class="rounded-full border border-ghosn/15 px-4 py-2.5 text-sm font-semibold text-ghosn">{{ __('public.campaigns.details') }}</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-10">{{ $campaigns->links() }}</div>
            @endif
        </div>
    </section>
@endsection
