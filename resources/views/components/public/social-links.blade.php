@props([
    'variant' => 'icons',
    'buttonClass' => 'inline-flex h-11 w-11 items-center justify-center rounded-full border border-offwhite/20 text-[17px] hover:bg-offwhite/10 transition-colors',
])

@php
    $links = \App\Models\SocialLink::query()->active()->ordered()->get();
@endphp

@if ($links->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-3']) }}>
        @foreach ($links as $link)
            <a
                href="{{ $link->url }}"
                target="_blank"
                rel="noopener noreferrer"
                class="{{ $buttonClass }}"
                aria-label="{{ $link->localizedLabel() }}"
                title="{{ $link->localizedLabel() }}"
            >
                <i class="{{ $link->fontAwesomeClass() }}" aria-hidden="true"></i>
            </a>
        @endforeach
    </div>
@endif
