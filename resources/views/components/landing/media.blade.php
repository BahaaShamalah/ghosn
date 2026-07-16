@props(['blocks' => null, 'type' => 'image', 'class' => 'max-w-full h-auto'])

@php
    $mediaUrl = \App\Support\LandingBlockHelper::mediaUrl($blocks, $type);
    $content = \App\Support\LandingBlockHelper::content($blocks, $type);
    $altEn = $content['alt_en'] ?? $content['en'] ?? '';
    $altAr = $content['alt_ar'] ?? $content['ar'] ?? '';
@endphp

@if ($mediaUrl)
    <img src="{{ $mediaUrl }}" alt="{{ $altEn }}" class="{{ $class }}" loading="lazy">
@else
    {{ $slot }}
@endif
