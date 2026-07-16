@props(['href' => '#', 'variant' => 'primary'])

@php
    $classes = $variant === 'primary'
        ? 'inline-flex items-center gap-2 rounded-[11px] border-none bg-[#406139] px-5 py-2.5 text-sm font-semibold text-[#F2F1EA] no-underline shadow-[0_6px_20px_rgba(47,67,39,0.12)] transition hover:bg-[#33502e]'
        : 'inline-flex items-center gap-2 rounded-[11px] border border-[rgba(64,97,57,0.18)] bg-[#fffdf8] px-5 py-2.5 text-sm font-semibold text-[#406139] no-underline transition hover:bg-[rgba(64,97,57,0.06)]';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
