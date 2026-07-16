@php
    $slug = $card['slug'];
    $widthClass = in_array($slug, ['donations', 'payments'], true) ? 'sm:col-span-2 xl:col-span-1' : '';
@endphp

<article @class([
    'gh-admin-card group flex flex-col rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 shadow-[0_6px_20px_rgba(47,67,39,0.05)] transition hover:border-[rgba(64,97,57,0.2)] hover:shadow-[0_10px_26px_rgba(47,67,39,0.1)]',
    $widthClass,
])>
    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-[13px] bg-[rgba(129,149,98,0.18)] text-[#406139]">
        @include('admin.settings.partials.icon', ['name' => $card['icon']])
    </div>

    <h2 class="text-base font-bold text-[#2f4327]">{{ __('admin.settings.hub_card_'.$slug.'_title') }}</h2>
    <p class="mt-2 flex-1 text-sm leading-relaxed text-[#5f6857]">{{ __('admin.settings.hub_card_'.$slug.'_desc') }}</p>

    <div class="mt-6">
        <a href="{{ route('admin.settings.show', $slug) }}" class="inline-flex items-center gap-2 rounded-[11px] bg-[#406139] px-5 py-2.5 text-sm font-semibold text-[#F2F1EA] shadow-[0_6px_20px_rgba(47,67,39,0.12)] transition group-hover:bg-[#33502e]">
            {{ __('admin.settings.manage') }}
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
            </svg>
        </a>
    </div>
</article>
