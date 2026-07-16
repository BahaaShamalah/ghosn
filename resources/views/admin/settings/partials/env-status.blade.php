@if ($configured)
    <span class="inline-flex items-center rounded-full bg-growth-soft/70 px-3 py-1 text-xs font-semibold text-ghosn-800">{{ __('admin.settings.env_configured') }}</span>
@else
    <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-900">{{ __('admin.settings.env_missing') }}</span>
@endif
