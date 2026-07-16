@props([
    'cancelUrl' => null,
    'submitLabel' => null,
    'submitType' => 'submit',
])

@php
    $submitLabel ??= __('admin.cms.save');
@endphp

<div class="flex flex-wrap gap-3">
    <button type="{{ $submitType }}" class="gh-admin-btn-primary">{{ $submitLabel }}</button>
    @if ($cancelUrl)
        <a href="{{ $cancelUrl }}" class="gh-admin-btn-secondary">{{ __('admin.cms.cancel') }}</a>
    @endif
    @isset($extra)
        {!! $extra !!}
    @endisset
</div>
