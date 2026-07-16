@php
    $donateUrl = route('donate', ['campaign' => $campaign->slug]);
@endphp

<div class="gh-campaign-donate-panel gh-reveal-internal">
    <div class="gh-campaign-donate-panel__raised-row">
        <span class="gh-campaign-donate-panel__raised" dir="ltr">{{ $campaign->formattedRaised() }}</span>
        <span class="gh-campaign-donate-panel__percent">{{ $percent }}%</span>
    </div>
    <p class="gh-campaign-donate-panel__goal">
        <span data-en="">{{ __('public.campaigns.raised_of', ['goal' => $campaign->formattedGoal()]) }}</span>
        <span data-ar="">{{ __('public.campaigns.raised_of', ['goal' => $campaign->formattedGoal()]) }}</span>
    </p>

    <div class="gh-campaign-donate-panel__bar">
        <div data-campaign-progress data-pct="{{ $percent }}" style="width: 0"></div>
    </div>

    <div class="gh-campaign-donate-panel__stats">
        <div>
            <div class="gh-campaign-donate-panel__stat-value">{{ number_format($campaign->donors_count) }}</div>
            <div class="gh-campaign-donate-panel__stat-label">
                <span data-en="">{{ __('public.campaigns.donors_label') }}</span>
                <span data-ar="">{{ __('public.campaigns.donors_label_ar') }}</span>
            </div>
        </div>
        <div>
            <div class="gh-campaign-donate-panel__stat-value">
                @if ($daysRemaining !== null)
                    {{ $daysRemaining }}
                @else
                    <span data-en="">{{ __('public.campaigns.open_ended') }}</span>
                    <span data-ar="">{{ __('public.campaigns.open_ended_ar') }}</span>
                @endif
            </div>
            <div class="gh-campaign-donate-panel__stat-label">
                <span data-en="">{{ __('public.campaigns.days_left') }}</span>
                <span data-ar="">{{ __('public.campaigns.days_left_ar') }}</span>
            </div>
        </div>
    </div>

    <a href="{{ $donateUrl }}" class="gh-campaign-donate-panel__cta">
        <span data-en="">{{ __('public.campaigns.donate_to_campaign') }}</span>
        <span data-ar="">{{ __('public.campaigns.donate_to_campaign_ar') }}</span>
    </a>

    <p class="gh-campaign-donate-panel__secure">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <span data-en="">{{ __('public.campaigns.secure_note') }}</span>
        <span data-ar="">{{ __('public.campaigns.secure_note_ar') }}</span>
    </p>

    <div class="gh-campaign-donate-panel__share">
        <x-public.share-buttons :url="route('campaigns.show', $campaign->slug)" :title="$campaign->title_en" compact />
    </div>
</div>
