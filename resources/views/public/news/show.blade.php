@extends('public.layouts.content')

@php
    $locale = app()->getLocale();
    $excerpt = $locale === 'ar' ? ($post->excerpt_ar ?: $post->excerpt_en) : ($post->excerpt_en ?: $post->excerpt_ar);
    $authorName = \App\Support\SiteSettings::name($locale);
    $authorInitial = mb_strtoupper(mb_substr($authorName, 0, 1));
    $readMinutes = max(1, (int) ceil(str_word_count(strip_tags($post->content_en.' '.$post->content_ar)) / 200));
@endphp

@section('title', $post->localizedSeoTitle($locale))

@include('public.partials.cms-meta', ['model' => $post])

@section('content-body')
    <div class="gh-reading-progress" data-reading-progress aria-hidden="true"></div>

    @if ($preview ?? false)
        <div class="border-b border-amber-200 bg-amber-50 px-5 py-2.5 text-center text-sm font-semibold text-amber-950">{{ __('public.news.preview_notice') }}</div>
    @endif

    <header class="gh-post-header">
        <nav class="gh-breadcrumb gh-reveal-internal" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">{{ __('public.pages.home') }}</a>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            <a href="{{ route('news.index') }}">{{ __('public.news.title') }}</a>
            @if ($post->category)
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                <span class="gh-breadcrumb__current">{{ $post->category->localizedName() }}</span>
            @endif
        </nav>

        <div class="gh-reveal-internal">
            @if ($post->category)
                <span class="gh-post-pill">{{ $post->category->localizedName() }}</span>
            @endif

            <h1 class="gh-post-title">
                <x-landing.bilingual :en="$post->title_en" :ar="$post->title_ar" />
            </h1>

            @if ($excerpt)
                <p class="gh-post-lede">{{ $excerpt }}</p>
            @endif

            <div class="gh-post-meta">
                <div class="gh-post-author">
                    <span class="gh-post-author__avatar">{{ $authorInitial }}</span>
                    <div>
                        <div class="text-[14.5px] font-bold text-[#3a4234]">{{ $authorName }}</div>
                        <div class="text-[12.5px] text-[#8a9280]">
                            {{ $post->published_at?->format('M j, Y') }}
                            · {{ trans_choice('public.news.read_time', $readMinutes, ['count' => $readMinutes]) }}
                        </div>
                    </div>
                </div>
                <x-public.share-buttons :url="route('news.show', $post->slug)" :title="$post->title_en" compact />
            </div>
        </div>
    </header>

    @if ($post->featuredImage)
        <div class="gh-post-cover-wrap gh-reveal-internal">
            <div class="gh-post-cover">
                <img src="{{ $post->featuredImage->url() }}" alt="">
            </div>
        </div>
    @endif

    <article class="gh-post-article" data-post-article>
        <div class="gh-reveal-internal">
            <x-public.prose-content :content-en="$post->content_en" :content-ar="$post->content_ar" />
        </div>

        <div class="gh-post-share-row gh-reveal-internal">
            <span class="gh-post-share-row__label">{{ __('public.content.share') }}</span>
            <x-public.share-buttons :url="route('news.show', $post->slug)" :title="$post->title_en" compact />
        </div>
    </article>

    @if ($relatedPosts->isNotEmpty())
        <section class="gh-related-section">
            <h2 class="gh-reveal-internal mb-8 text-center text-[clamp(24px,3vw,34px)] font-bold text-[#2f4327]">{{ __('public.news.related') }}</h2>
            <div class="gh-related-grid">
                @foreach ($relatedPosts as $related)
                    @php
                        $relatedExcerpt = $locale === 'ar'
                            ? ($related->excerpt_ar ?: $related->excerpt_en)
                            : ($related->excerpt_en ?: $related->excerpt_ar);
                    @endphp
                    <a href="{{ route('news.show', $related->slug) }}" class="gh-related-card gh-reveal-internal">
                        <div class="gh-related-card__image">
                            @if ($related->featuredImage)
                                <img src="{{ $related->featuredImage->thumbnailUrl() ?? $related->featuredImage->url() }}" alt="">
                            @endif
                        </div>
                        <div class="gh-related-card__body">
                            <h3 class="gh-related-card__title">
                                <x-landing.bilingual :en="$related->title_en" :ar="$related->title_ar" />
                            </h3>
                            @if ($relatedExcerpt)
                                <p class="gh-related-card__excerpt">{{ Str::limit($relatedExcerpt, 120) }}</p>
                            @endif
                            <div class="gh-related-card__footer">
                                <span>{{ $related->published_at?->format('M j, Y') }}</span>
                                <span class="gh-related-card__read">
                                    {{ __('public.news.read_more') }}
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
