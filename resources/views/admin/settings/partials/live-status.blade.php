@if ($live)
    <span class="inline-flex items-center rounded-full bg-growth-soft/70 px-3 py-1 text-xs font-semibold text-ghosn-800">{{ __('admin.settings.payments_live_on_site') }}</span>
@else
    <span class="inline-flex items-center rounded-full bg-stone-200/80 px-3 py-1 text-xs font-semibold text-stone-700">{{ __('admin.settings.payments_not_live') }}</span>
@endif
