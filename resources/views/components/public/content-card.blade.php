@props(['title' => null])

<div {{ $attributes->merge(['class' => 'rounded-3xl border border-ghosn/10 bg-offwhite p-6 md:p-8 shadow-sm shadow-ghosn/5']) }}>
    @if ($title)
        <h2 class="mb-5 text-sm font-bold uppercase tracking-[0.14em] text-ghosn/55">{{ $title }}</h2>
    @endif
    {{ $slot }}
</div>
