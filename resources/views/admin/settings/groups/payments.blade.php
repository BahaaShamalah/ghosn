<form method="POST" action="{{ route('admin.settings.update.group', 'payments') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="payments">

    @include('admin.settings.partials.form-errors', ['group' => 'payments'])

    <div class="rounded-2xl border border-amber-200/80 bg-amber-50/60 px-4 py-3 text-sm text-amber-950">
        {{ __('admin.settings.payments_env_notice') }}
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.payments_currency') }}</label>
            <select name="payments[currency]" class="ghosn-input">
                @foreach (config('donations.currencies', []) as $code => $meta)
                    <option value="{{ $code }}" @selected(old('payments.currency', $settings['payments.currency'] ?? $settings['donations.currency']) === $code)>{{ $code }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.payments_min_amount') }}</label>
            <input type="number" name="payments[min_amount]" value="{{ old('payments.min_amount', $settings['payments.min_amount'] ?? $settings['donations.min_amount']) }}" class="ghosn-input" min="1">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.payments_max_amount') }}</label>
            <input type="number" name="payments[max_amount]" value="{{ old('payments.max_amount', $settings['payments.max_amount'] ?? 50000) }}" class="ghosn-input" min="1">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.payments_receipt_email') }}</label>
            <input type="email" name="payments[receipt_email]" value="{{ old('payments.receipt_email', $settings['payments.receipt_email'] ?? $settings['contact.email']) }}" class="ghosn-input">
        </div>
    </div>

    <div class="rounded-2xl border border-ghosn/10 bg-cream/30 p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <h3 class="text-sm font-bold text-ghosn">{{ __('admin.settings.payments_stripe_heading') }}</h3>
            @include('admin.settings.partials.env-status', ['configured' => $paymentEnv['stripe_configured']])
        </div>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="flex items-center gap-3 sm:col-span-2">
                <input type="hidden" name="payments[stripe_enabled]" value="0">
                <input type="checkbox" id="payments_stripe_enabled" name="payments[stripe_enabled]" value="1" @checked(old('payments.stripe_enabled', $settings['payments.stripe_enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <label for="payments_stripe_enabled" class="text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.payments_stripe_enabled') }}</label>
            </div>
            <div><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.payments_stripe_product_name') }}</label><input type="text" name="payments[stripe_product_name]" value="{{ old('payments.stripe_product_name', $settings['payments.stripe_product_name']) }}" class="ghosn-input"></div>
            <div><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.payments_stripe_product_description') }}</label><input type="text" name="payments[stripe_product_description]" value="{{ old('payments.stripe_product_description', $settings['payments.stripe_product_description']) }}" class="ghosn-input"></div>
        </div>
        <p class="mt-3 text-xs text-ghosn-ink/55">{{ __('admin.settings.payments_stripe_env_keys') }}</p>
    </div>

    <div class="rounded-2xl border border-ghosn/10 bg-cream/30 p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-ghosn">{{ __('admin.settings.payments_paypal_heading') }}</h3>
                <p class="mt-1 text-xs text-ghosn-ink/55">{{ __('admin.settings.payments_paypal_checkout_status') }}</p>
            </div>
            @include('admin.settings.partials.env-status', ['configured' => $paymentEnv['paypal_configured']])
        </div>
        <div class="mt-3 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-ghosn/10 bg-offwhite/70 px-4 py-3">
            <div>
                <p class="text-xs font-semibold text-ghosn-ink/70">{{ __('admin.settings.payments_paypal_webhook_status') }}</p>
                <p class="mt-0.5 text-xs text-ghosn-ink/50">{{ __('admin.settings.payments_paypal_webhook_optional') }}</p>
            </div>
            @include('admin.settings.partials.webhook-status', ['configured' => $paymentEnv['paypal_webhook_configured']])
        </div>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="flex items-center gap-3 sm:col-span-2">
                <input type="hidden" name="payments[paypal_enabled]" value="0">
                <input type="checkbox" id="payments_paypal_enabled" name="payments[paypal_enabled]" value="1" @checked(old('payments.paypal_enabled', $settings['payments.paypal_enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <label for="payments_paypal_enabled" class="text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.payments_paypal_enabled') }}</label>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.payments_paypal_mode') }}</label>
                <select name="payments[paypal_mode]" class="ghosn-input">
                    <option value="sandbox" @selected(old('payments.paypal_mode', $settings['payments.paypal_mode'] ?? 'sandbox') === 'sandbox')>Sandbox</option>
                    <option value="live" @selected(old('payments.paypal_mode', $settings['payments.paypal_mode'] ?? 'sandbox') === 'live')>Live</option>
                </select>
            </div>
            <div><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.payments_paypal_item_name') }}</label><input type="text" name="payments[paypal_item_name]" value="{{ old('payments.paypal_item_name', $settings['payments.paypal_item_name']) }}" class="ghosn-input"></div>
            <div class="sm:col-span-2"><label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.payments_paypal_item_description') }}</label><input type="text" name="payments[paypal_item_description]" value="{{ old('payments.paypal_item_description', $settings['payments.paypal_item_description']) }}" class="ghosn-input"></div>
        </div>
        <p class="mt-3 text-xs text-ghosn-ink/55">{{ __('admin.settings.payments_paypal_env_keys') }}</p>
    </div>

    <p class="text-xs text-ghosn-ink/55">{{ __('admin.settings.payments_compliance_note') }}</p>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
