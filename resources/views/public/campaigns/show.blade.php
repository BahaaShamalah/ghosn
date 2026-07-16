@extends('public.layouts.content')

@php
    $locale = app()->getLocale();
    $excerpt = $locale === 'ar' ? ($campaign->excerpt_ar ?: $campaign->excerpt_en) : ($campaign->excerpt_en ?: $campaign->excerpt_ar);
    $percent = (int) round($campaign->progressPercent());
    $daysRemaining = $campaign->daysRemaining();
    $donorColors = ['#96A791', '#406139', '#819562', '#6f8452'];
@endphp

@section('title', $campaign->localizedSeoTitle())

@include('public.partials.cms-meta', ['model' => $campaign])

@section('content-body')
    <div class="gh-campaign-page">
        <div class="gh-campaign-page__crumb-wrap">
            <nav class="gh-breadcrumb gh-reveal-internal" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">{{ __('public.pages.home') }}</a>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                <a href="{{ route('campaigns.index') }}">{{ __('public.campaigns.title') }}</a>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                <span class="gh-breadcrumb__current">
                    <x-landing.bilingual :en="$campaign->title_en" :ar="$campaign->title_ar" />
                </span>
            </nav>
        </div>

        <section class="gh-campaign-page__grid">
            <div class="gh-campaign-page__main">
                <div class="gh-campaign-page__title-block gh-reveal-internal">
                    <div class="gh-campaign-page__meta-row">
                        <span @class([
                            'gh-campaign-page__tag',
                            'gh-campaign-page__tag--urgent' => $campaign->isUrgent(),
                            'gh-campaign-page__tag--ongoing' => ! $campaign->isUrgent(),
                        ])>
                            <span data-en="">{{ $campaign->isUrgent() ? __('public.campaigns.tag_urgent') : __('public.campaigns.tag_ongoing') }}</span>
                            <span data-ar="">{{ $campaign->isUrgent() ? __('public.campaigns.tag_urgent_ar') : __('public.campaigns.tag_ongoing_ar') }}</span>
                        </span>
                        @if ($campaign->category)
                            <span class="gh-campaign-page__location">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $campaign->category->localizedName() }}
                            </span>
                        @endif
                    </div>

                    <h1 class="gh-campaign-page__title">
                        <x-landing.bilingual :en="$campaign->title_en" :ar="$campaign->title_ar" />
                    </h1>

                    @if ($excerpt)
                        <p class="gh-campaign-page__excerpt">{{ $excerpt }}</p>
                    @endif
                </div>

                @php($campaignVideo = $campaign->videoEmbed())
                @if ($campaignVideo['type'] === 'file' && $campaignVideo['file_url'])
                    <div class="gh-campaign-video gh-reveal-internal">
                        <video
                            controls
                            preload="metadata"
                            playsinline
                            @if ($campaign->featuredImage) poster="{{ $campaign->featuredImage->url() }}" @endif
                        >
                            <source src="{{ $campaignVideo['file_url'] }}" type="{{ $campaignVideo['mime'] }}">
                            {{ __('public.campaigns.video_unsupported') }}
                        </video>
                    </div>
                @elseif ($campaignVideo['type'] === 'embed' && $campaignVideo['embed_url'])
                    <div class="gh-campaign-video gh-campaign-video--embed gh-reveal-internal">
                        <iframe
                            src="{{ $campaignVideo['embed_url'] }}"
                            title="{{ $campaign->title }}"
                            frameborder="0"
                            loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                        ></iframe>
                    </div>
                @endif

                @if ($galleryImages->isNotEmpty())
                    <div class="gh-campaign-gallery gh-reveal-internal" data-campaign-gallery>
                        <button type="button" class="gh-campaign-gallery__main" data-campaign-gallery-main aria-label="{{ __('public.campaigns.view_gallery') }}">
                            <img src="{{ $galleryImages->first()->url() }}" alt="" data-campaign-gallery-image>
                            <div class="gh-campaign-gallery__overlay"></div>
                            @if ($galleryImages->count() > 1)
                                <span class="gh-campaign-gallery__badge">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                                    <span data-en="">{{ __('public.campaigns.view_gallery') }}</span>
                                    <span data-ar="">{{ __('public.campaigns.view_gallery_ar') }}</span>
                                </span>
                            @endif
                        </button>

                        @if ($galleryImages->count() > 1)
                            <div class="gh-campaign-gallery__thumbs">
                                @foreach ($galleryImages as $index => $image)
                                    <button
                                        type="button"
                                        class="gh-campaign-gallery__thumb @if ($index === 0) is-active @endif"
                                        data-campaign-gallery-thumb
                                        data-image="{{ $image->url() }}"
                                        aria-label="Image {{ $index + 1 }}"
                                    >
                                        <img src="{{ $image->thumbnailUrl() ?? $image->url() }}" alt="">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="gh-campaign-story gh-reveal-internal">
                    <h2 class="gh-campaign-story__title">
                        <span data-en="">{{ __('public.campaigns.story_title') }}</span>
                        <span data-ar="">{{ __('public.campaigns.story_title_ar') }}</span>
                    </h2>
                    <x-public.prose-content :content-en="$campaign->story_en" :content-ar="$campaign->story_ar" />
                </div>

                @if ($recentDonations->isNotEmpty())
                    <div class="gh-campaign-donors gh-reveal-internal">
                        <h3 class="gh-campaign-donors__title">
                            <span data-en="">{{ __('public.campaigns.recent_supporters') }}</span>
                            <span data-ar="">{{ __('public.campaigns.recent_supporters_ar') }}</span>
                        </h3>
                        <div class="gh-campaign-donors__list">
                            @foreach ($recentDonations as $index => $donation)
                                @php
                                    $name = $donation->displayDonorName();
                                    $initial = mb_strtoupper(mb_substr($name, 0, 1));
                                @endphp
                                <div class="gh-campaign-donors__item">
                                    <span class="gh-campaign-donors__avatar" style="background: {{ $donorColors[$index % count($donorColors)] }}">{{ $initial }}</span>
                                    <div class="gh-campaign-donors__info">
                                        <div class="gh-campaign-donors__name">{{ $name }}</div>
                                        <div class="gh-campaign-donors__when">{{ $donation->paid_at?->diffForHumans() }}</div>
                                    </div>
                                    <span class="gh-campaign-donors__amount" dir="ltr">{{ $donation->formattedAmount() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="gh-campaign-page__aside" id="donate">
                @include('public.campaigns.partials.donate-panel', ['campaign' => $campaign, 'percent' => $percent, 'daysRemaining' => $daysRemaining])

                <div class="gh-campaign-organizer gh-reveal-internal">
                    <img src="{{ \App\Support\SiteAsset::logoUrl() }}" alt="{{ \App\Support\SiteSettings::name() }}">
                    <div>
                        <div class="gh-campaign-organizer__label">
                            <span data-en="">{{ __('public.campaigns.organizer') }}</span>
                            <span data-ar="">{{ __('public.campaigns.organizer_ar') }}</span>
                        </div>
                        <div class="gh-campaign-organizer__name">
                            <span data-en="">{{ \App\Support\SiteSettings::name('en') }}</span>
                            <span data-ar="">{{ \App\Support\SiteSettings::name('ar') }}</span>
                        </div>
                        <div class="gh-campaign-organizer__verified">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            <span data-en="">{{ __('public.campaigns.verified_org') }}</span>
                            <span data-ar="">{{ __('public.campaigns.verified_org_ar') }}</span>
                        </div>
                    </div>
                </div>
            </aside>
        </section>

        @if ($relatedCampaigns->isNotEmpty())
            <section class="gh-related-section">
                <h2 class="gh-reveal-internal mb-8 text-center text-[clamp(24px,3vw,34px)] font-bold text-[#2f4327]">
                    <span data-en="">{{ __('public.campaigns.related_title') }}</span>
                    <span data-ar="">{{ __('public.campaigns.related_title_ar') }}</span>
                </h2>
                <div class="gh-related-grid">
                    @foreach ($relatedCampaigns as $related)
                        @php
                            $relatedPct = (int) round($related->progressPercent());
                            $relatedExcerpt = $locale === 'ar'
                                ? ($related->excerpt_ar ?: $related->excerpt_en)
                                : ($related->excerpt_en ?: $related->excerpt_ar);
                        @endphp
                        <a href="{{ route('campaigns.show', $related->slug) }}" class="gh-campaign-related-card gh-reveal-internal">
                            <div class="gh-campaign-related-card__image">
                                @if ($related->featuredImage)
                                    <img src="{{ $related->featuredImage->thumbnailUrl() ?? $related->featuredImage->url() }}" alt="">
                                @endif
                                <span @class([
                                    'gh-campaign-related-card__tag',
                                    'gh-campaign-related-card__tag--urgent' => $related->isUrgent(),
                                    'gh-campaign-related-card__tag--ongoing' => ! $related->isUrgent(),
                                ])>
                                    <span data-en="">{{ $related->isUrgent() ? __('public.campaigns.tag_urgent') : __('public.campaigns.tag_ongoing') }}</span>
                                    <span data-ar="">{{ $related->isUrgent() ? __('public.campaigns.tag_urgent_ar') : __('public.campaigns.tag_ongoing_ar') }}</span>
                                </span>
                            </div>
                            <div class="gh-campaign-related-card__body">
                                <h3 class="gh-campaign-related-card__title">
                                    <x-landing.bilingual :en="$related->title_en" :ar="$related->title_ar" />
                                </h3>
                                @if ($relatedExcerpt)
                                    <p class="gh-campaign-related-card__excerpt">{{ Str::limit($relatedExcerpt, 100) }}</p>
                                @endif
                                <div class="gh-campaign-related-card__progress">
                                    <div class="gh-campaign-related-card__bar">
                                        <div data-campaign-progress data-pct="{{ $relatedPct }}" style="width: 0"></div>
                                    </div>
                                    <div class="gh-campaign-related-card__stats">
                                        <span dir="ltr">{{ $related->formattedRaised() }}</span>
                                        <span>{{ $relatedPct }}%</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
