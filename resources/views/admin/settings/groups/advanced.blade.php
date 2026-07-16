<dl class="grid gap-3 text-sm sm:grid-cols-2">
    <div class="rounded-2xl border border-ghosn/8 bg-cream/30 px-4 py-3">
        <dt class="font-medium text-ghosn-ink/55">{{ __('admin.settings.default_language') }}</dt>
        <dd class="mt-1 font-semibold text-ghosn">{{ strtoupper($settings['site.default_language']) }}</dd>
    </div>
    <div class="rounded-2xl border border-ghosn/8 bg-cream/30 px-4 py-3">
        <dt class="font-medium text-ghosn-ink/55">{{ __('admin.settings.enable_animations') }}</dt>
        <dd class="mt-1 font-semibold text-ghosn">{{ $settings['site.enable_animations'] ? __('admin.settings.on') : __('admin.settings.off') }}</dd>
    </div>
    <div class="rounded-2xl border border-ghosn/8 bg-cream/30 px-4 py-3">
        <dt class="font-medium text-ghosn-ink/55">{{ __('admin.settings.homepage') }}</dt>
        <dd class="mt-1 font-semibold text-ghosn">{{ __('admin.settings.builder_source') }}</dd>
    </div>
    <div class="rounded-2xl border border-ghosn/8 bg-cream/30 px-4 py-3">
        <dt class="font-medium text-ghosn-ink/55">{{ __('admin.settings.payments_currency') }}</dt>
        <dd class="mt-1 font-semibold text-ghosn">{{ $settings['payments.currency'] ?? $settings['donations.currency'] }}</dd>
    </div>
</dl>
