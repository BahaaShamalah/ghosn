@extends('admin.layouts.app')

@section('title', __('admin.settings.title'))
@section('page-title', __('admin.settings.title'))
@section('eyebrow', __('admin.panel'))

@section('content')
    <p class="mb-8 max-w-3xl text-sm leading-relaxed text-ghosn-ink/70">{{ __('admin.settings.hub_intro') }}</p>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($cards as $card)
            @include('admin.settings.partials.hub-card', ['card' => $card])
        @endforeach
    </div>
@endsection
