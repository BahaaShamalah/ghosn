@props(['blocks' => null, 'type' => 'text'])

@php
    $content = \App\Support\LandingBlockHelper::content($blocks, $type);
    $hasBuilderContent = $blocks && $content && (filled($content['en'] ?? null) || filled($content['ar'] ?? null));
@endphp

@if ($hasBuilderContent)
    <span data-en="">{{ \App\Support\HtmlText::clean($content['en'] ?? '') }}</span><span data-ar="">{{ \App\Support\HtmlText::clean($content['ar'] ?? '') }}</span>
@else
    {{ $slot }}
@endif
