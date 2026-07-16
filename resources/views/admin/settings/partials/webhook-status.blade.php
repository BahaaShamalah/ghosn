@if ($configured)
    <span class="inline-flex items-center rounded-full bg-growth-soft/70 px-3 py-1 text-xs font-semibold text-ghosn-800">{{ __('admin.settings.env_webhook_configured') }}</span>
@else
    <span class="inline-flex items-center rounded-full bg-ghosn/8 px-3 py-1 text-xs font-semibold text-ghosn-ink/70">{{ __('admin.settings.env_webhook_not_configured') }}</span>
@endif
