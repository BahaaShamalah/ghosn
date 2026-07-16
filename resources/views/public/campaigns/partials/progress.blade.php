@props(['campaign', 'compact' => false])

@php
    $percent = $campaign->progressPercent();
@endphp

<div @class(['space-y-2', 'space-y-3' => ! $compact])>
    <div class="h-2.5 overflow-hidden rounded-full bg-ghosn/10">
        <div class="h-full rounded-full bg-growth transition-all duration-500" style="width: {{ $percent }}%"></div>
    </div>
    <div @class(['flex flex-wrap items-center justify-between gap-2 text-sm', 'text-xs' => $compact])>
        <p class="font-bold text-ghosn" dir="ltr">
            <span>{{ $campaign->formattedRaised() }}</span>
            <span class="font-normal text-ghosn-ink/55">{{ __('public.campaigns.raised_of', ['goal' => $campaign->formattedGoal()]) }}</span>
        </p>
        <p class="text-ghosn-ink/60">
            <span data-en="">{{ __('public.campaigns.donors_count', ['count' => number_format($campaign->donors_count)]) }}</span>
            <span data-ar="">{{ __('public.campaigns.donors_count_ar', ['count' => number_format($campaign->donors_count)]) }}</span>
        </p>
    </div>
</div>
