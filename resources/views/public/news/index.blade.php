@extends('public.layouts.content')

@section('title', __('public.news.title'))

@section('content-body')
    <x-public.page-hero
        :title-en="__('public.news.title')"
        :title-ar="__('public.news.title_ar')"
        :eyebrow="__('public.news.eyebrow')"
        :subtitle-en="__('public.news.subtitle')"
        :subtitle-ar="__('public.news.subtitle_ar')"
    />

    <section class="public-content-section">
        <div class="max-w-7xl mx-auto px-5 md:px-10">
            <form method="GET" action="{{ route('news.index') }}" class="mb-8 flex flex-wrap gap-3">
                <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('public.news.search_placeholder') }}" class="rounded-full border border-ghosn/15 bg-offwhite px-4 py-2.5 text-sm min-w-[220px]">
                <select name="category" class="rounded-full border border-ghosn/15 bg-offwhite px-4 py-2.5 text-sm">
                    <option value="">{{ __('public.news.all_categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($activeCategory === $category->slug)>{{ $category->localizedName() }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-full bg-ghosn px-5 py-2.5 text-sm font-semibold text-offwhite">{{ __('public.news.filter') }}</button>
            </form>

            @if ($featured)
                <a href="{{ route('news.show', $featured->slug) }}" class="mb-10 grid overflow-hidden rounded-3xl border border-ghosn/10 bg-offwhite shadow-sm shadow-ghosn/5 md:grid-cols-2">
                    @if ($featured->featuredImage)
                        <img src="{{ $featured->featuredImage->url() }}" alt="" class="h-full min-h-[240px] w-full object-cover">
                    @endif
                    <div class="p-8 md:p-10">
                        <p class="text-xs font-semibold uppercase tracking-wide text-ghosn/50">{{ __('public.news.featured') }}</p>
                        <h2 class="mt-3 text-2xl font-bold text-ghosn md:text-3xl"><x-landing.bilingual :en="$featured->title_en" :ar="$featured->title_ar" /></h2>
                        <p class="mt-4 text-[15px] leading-relaxed text-ghosn-ink/70"><x-landing.bilingual :en="$featured->excerpt_en" :ar="$featured->excerpt_ar" /></p>
                    </div>
                </a>
            @endif

            @if ($posts->isEmpty() && ! $featured)
                <div class="rounded-3xl border border-dashed border-ghosn/20 bg-cream/40 p-10 text-center text-sm text-ghosn-ink/65">{{ __('public.news.empty') }}</div>
            @else
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($posts as $post)
                        <article class="rounded-3xl border border-ghosn/10 bg-offwhite overflow-hidden shadow-sm shadow-ghosn/5">
                            @if ($post->featuredImage)
                                <img src="{{ $post->featuredImage->thumbnailUrl() ?? $post->featuredImage->url() }}" alt="" class="h-44 w-full object-cover">
                            @endif
                            <div class="p-6">
                                <p class="text-xs text-ghosn/50">{{ $post->published_at?->format('M j, Y') }}</p>
                                @if ($post->category)
                                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-growth">{{ $post->category->localizedName() }}</p>
                                @endif
                                <h3 class="mt-2 text-lg font-bold text-ghosn"><a href="{{ route('news.show', $post->slug) }}"><x-landing.bilingual :en="$post->title_en" :ar="$post->title_ar" /></a></h3>
                                <p class="mt-2 line-clamp-3 text-sm text-ghosn-ink/65"><x-landing.bilingual :en="$post->excerpt_en" :ar="$post->excerpt_ar" /></p>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-10">{{ $posts->links() }}</div>
            @endif
        </div>
    </section>
@endsection
