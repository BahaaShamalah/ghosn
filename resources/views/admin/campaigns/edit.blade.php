@extends('admin.layouts.app')
@section('title', __('admin.campaigns.edit'))
@section('page-title', __('admin.campaigns.edit'))
@section('content')
    @include('admin.partials.back-link', [
        'url' => route('admin.campaigns.index'),
        'label' => __('admin.campaigns.back_to_list'),
    ])

    <form method="POST" action="{{ route('admin.campaigns.update', $campaign) }}" class="gh-campaign-editor" data-campaign-editor>
        @csrf
        @method('PUT')
        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)]">
            <div class="min-w-0 space-y-6">
                @include('admin.campaigns.partials.campaign-fields', ['campaign' => $campaign])
                @php
                    $extra = '';
                    if ($campaign->isPublic()) {
                        $extra .= '<a href="'.e(route('campaigns.show', $campaign->slug)).'" target="_blank" class="gh-admin-btn-secondary">'.e(__('admin.campaigns.view')).'</a>';
                        $extra .= '<a href="'.e(route('donate', ['campaign' => $campaign->slug])).'" target="_blank" class="gh-admin-btn-secondary">'.e(__('admin.campaigns.donate_link')).'</a>';
                    }
                @endphp
                @include('admin.partials.form-actions', [
                    'cancelUrl' => route('admin.campaigns.index'),
                    'submitLabel' => __('admin.campaigns.save'),
                    'extra' => $extra,
                ])
            </div>
            @include('admin.campaigns.partials.campaign-preview', ['campaign' => $campaign])
        </div>
    </form>
@endsection
@push('scripts')
    @include('admin.partials.cms-scripts')
@endpush
