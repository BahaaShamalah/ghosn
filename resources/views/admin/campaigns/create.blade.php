@extends('admin.layouts.app')
@section('title', __('admin.campaigns.new'))
@section('page-title', __('admin.campaigns.new'))
@section('content')
    @include('admin.partials.back-link', [
        'url' => route('admin.campaigns.index'),
        'label' => __('admin.campaigns.back_to_list'),
    ])

    <form method="POST" action="{{ route('admin.campaigns.store') }}" class="gh-campaign-editor" data-campaign-editor>
        @csrf
        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)]">
            <div class="min-w-0 space-y-6">
                @include('admin.campaigns.partials.campaign-fields')
                @include('admin.partials.form-actions', [
                    'cancelUrl' => route('admin.campaigns.index'),
                    'submitLabel' => __('admin.campaigns.save'),
                ])
            </div>
            @include('admin.campaigns.partials.campaign-preview')
        </div>
    </form>
@endsection
@push('scripts')
    @include('admin.partials.cms-scripts')
@endpush
