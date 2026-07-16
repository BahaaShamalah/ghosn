@extends('admin.layouts.app')

@section('title', __('admin.settings.hub_card_'.$group.'_title'))
@section('page-title', __('admin.settings.hub_card_'.$group.'_title'))
@section('eyebrow', __('admin.settings.title'))

@section('content')

    @php
        $maxWidth = in_array($group, ['donations', 'payments', 'volunteers'], true) ? 'max-w-5xl' : 'max-w-3xl';
    @endphp

    @include('admin.partials.back-link', [
        'url' => route('admin.settings.index'),
        'label' => __('admin.settings.back_to_hub'),
    ])

    <div class="{{ $maxWidth }}">
        <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 md:p-8">
            <div class="mb-6 flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[rgba(129,149,98,0.18)] text-[#406139]">
                    @include('admin.settings.partials.icon', ['name' => config('settings.hub_cards.'.$group.'.icon', $group)])
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8a9280]">{{ __('admin.settings.group_'.$group) }}</p>
                    <h2 class="mt-1 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.hub_card_'.$group.'_title') }}</h2>
                    <p class="mt-1 text-sm text-[#8a9280]">{{ __('admin.settings.hub_card_'.$group.'_desc') }}</p>
                </div>
            </div>

            @include('admin.settings.groups.'.$group)
        </div>
    </div>
@endsection
