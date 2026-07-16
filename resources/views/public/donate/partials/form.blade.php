@php
    $symbol = $currencyMeta['symbol'] ?? '$';
    $hasPaymentMethod = $stripeEnabled || $paypalEnabled || $bankEnabled;
    $defaultMethod = $stripeEnabled ? 'stripe_card' : ($paypalEnabled ? 'paypal_business' : ($bankEnabled ? 'bank_transfer' : ''));
@endphp

<form
    id="give-form"
    method="POST"
    action="{{ route('donate.store') }}"
    class="rounded-3xl border border-ghosn/10 bg-offwhite p-6 md:p-7 shadow-sm"
    data-donate-form
    data-currency-symbol="{{ $symbol }}"
    data-paypal-submit-hint-en="{{ __('public.donate.submit_paypal_hint') }}"
    data-paypal-submit-hint-ar="{{ __('public.donate.submit_paypal_hint_ar') }}"
    @if ($paypalEnabled)
        data-paypal-enabled="1"
        data-paypal-client-id="{{ config('services.paypal.client_id') }}"
        data-paypal-currency="{{ $currency }}"
        data-paypal-create-order-url="{{ route('donate.paypal.create-order') }}"
        data-paypal-capture-order-url="{{ route('donate.paypal.capture-order') }}"
    @endif
>
    @csrf

    @if (! empty($campaign))
        <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
    @endif

    <div class="absolute -left-[9999px]" aria-hidden="true">
        <label for="website">Website</label>
        <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('donate_error'))
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            {{ session('donate_error') }}
        </div>
    @endif

    @if (! $donationsEnabled)
        <div class="rounded-2xl border border-ghosn/15 bg-cream/50 px-5 py-4 text-sm text-ghosn-ink/75">
            <span data-en="">{{ __('public.donate.unavailable') }}</span>
            <span data-ar="">{{ __('public.donate.unavailable_ar') }}</span>
        </div>
    @else
        <div class="space-y-6">
            @unless ($hasPaymentMethod)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                    <span data-en="">{{ __('public.donate.no_payment_methods') }}</span>
                    <span data-ar="">{{ __('public.donate.no_payment_methods_ar') }}</span>
                </div>
            @endunless
            <div>
                <label class="mb-3 block text-sm font-semibold text-ghosn">
                    <span data-en="">{{ __('public.donate.choose_amount') }}</span><span data-ar="">{{ __('public.donate.choose_amount_ar') }}</span>
                </label>
                <div class="flex flex-wrap gap-2" data-amount-presets>
                    @foreach ($amountPresets as $preset)
                        <button type="button" data-amount-preset="{{ $preset }}" class="donate-amount-btn rounded-full border border-ghosn/15 px-4 py-2 text-sm font-semibold text-ghosn transition hover:border-ghosn/35 hover:bg-ghosn/5"><span class="inline-flex items-center gap-1.5" dir="ltr"><span>{{ $symbol }}</span><span>{{ number_format($preset) }}</span></span></button>
                    @endforeach
                    <button type="button" data-amount-preset="custom" class="donate-amount-btn rounded-full border border-ghosn/15 px-4 py-2 text-sm font-semibold text-ghosn transition hover:border-ghosn/35 hover:bg-ghosn/5">
                        <span data-en="">{{ __('public.donate.custom_amount') }}</span><span data-ar="">{{ __('public.donate.custom_amount_ar') }}</span>
                    </button>
                </div>
                <div class="mt-3">
                    <div class="donate-amount-field max-w-[200px]">
                        <span class="donate-amount-field__symbol" aria-hidden="true">{{ $symbol }}</span>
                        <input type="number" name="amount" id="donation-amount" min="{{ $donationSettings->minAmount() }}" max="{{ $donationSettings->maxAmount() }}" step="1" value="{{ old('amount', $amountPresets[1] ?? 50) }}" required class="donate-input donate-input--amount text-[15px] font-semibold text-ghosn" dir="ltr" inputmode="numeric">
                    </div>
                    <p class="mt-1.5 text-xs text-ghosn-ink/50">
                        <span data-en="">{{ __('public.donate.minimum', ['amount' => $symbol.' '.number_format($donationSettings->minAmount())]) }}</span>
                        <span data-ar="">{{ __('public.donate.minimum_ar', ['amount' => $symbol.' '.number_format($donationSettings->minAmount())]) }}</span>
                    </p>
                </div>
            </div>

            <div>
                <label class="mb-3 block text-sm font-semibold text-ghosn">
                    <span data-en="">{{ __('public.donate.payment_method') }}</span><span data-ar="">{{ __('public.donate.payment_method_ar') }}</span>
                </label>
                <div class="grid gap-2.5 sm:grid-cols-2 lg:grid-cols-3">
                    @if ($stripeEnabled)
                        <label class="donate-method-card cursor-pointer rounded-xl border border-ghosn/15 p-3.5 transition">
                            <input type="radio" name="payment_method" value="stripe_card" class="sr-only" data-payment-method @checked(old('payment_method', $defaultMethod) === 'stripe_card') required>
                            <span class="block text-sm font-bold text-ghosn"><span data-en="">{{ __('public.donate.method_card') }}</span><span data-ar="">{{ __('public.donate.method_card_ar') }}</span></span>
                            <span class="mt-0.5 block text-xs text-ghosn-ink/60"><span data-en="">{{ __('public.donate.method_card_desc') }}</span><span data-ar="">{{ __('public.donate.method_card_desc_ar') }}</span></span>
                        </label>
                    @endif
                    @if ($paypalEnabled)
                        <label class="donate-method-card cursor-pointer rounded-xl border border-ghosn/15 p-3.5 transition">
                            <input type="radio" name="payment_method" value="paypal_business" class="sr-only" data-payment-method @checked(old('payment_method', $defaultMethod) === 'paypal_business') @required(! $stripeEnabled && ! $bankEnabled)>
                            <span class="block text-sm font-bold text-ghosn"><span data-en="">{{ __('public.donate.method_paypal') }}</span><span data-ar="">{{ __('public.donate.method_paypal_ar') }}</span></span>
                            <span class="mt-0.5 block text-xs text-ghosn-ink/60"><span data-en="">{{ __('public.donate.method_paypal_desc') }}</span><span data-ar="">{{ __('public.donate.method_paypal_desc_ar') }}</span></span>
                        </label>
                    @endif
                    @if ($bankEnabled)
                        <label class="donate-method-card cursor-pointer rounded-xl border border-ghosn/15 p-3.5 transition">
                            <input type="radio" name="payment_method" value="bank_transfer" class="sr-only" data-payment-method @checked(old('payment_method', $defaultMethod) === 'bank_transfer') @required(! $stripeEnabled && ! $paypalEnabled)>
                            <span class="block text-sm font-bold text-ghosn"><span data-en="">{{ __('public.donate.method_bank') }}</span><span data-ar="">{{ __('public.donate.method_bank_ar') }}</span></span>
                            <span class="mt-0.5 block text-xs text-ghosn-ink/60"><span data-en="">{{ __('public.donate.method_bank_desc') }}</span><span data-ar="">{{ __('public.donate.method_bank_desc_ar') }}</span></span>
                        </label>
                    @endif
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="donor_name" class="mb-1.5 block text-sm font-medium text-ghosn-ink/80"><span data-en="">{{ __('public.donate.full_name') }}</span><span data-ar="">{{ __('public.donate.full_name_ar') }}</span></label>
                    <input type="text" name="donor_name" id="donor_name" value="{{ old('donor_name') }}" required class="donate-input">
                </div>
                <div>
                    <label for="donor_email" class="mb-1.5 block text-sm font-medium text-ghosn-ink/80"><span data-en="">{{ __('public.donate.email') }}</span><span data-ar="">{{ __('public.donate.email_ar') }}</span></label>
                    <input type="email" name="donor_email" id="donor_email" value="{{ old('donor_email') }}" required class="donate-input" dir="ltr">
                </div>
                <div>
                    <label for="donor_phone" class="mb-1.5 block text-sm font-medium text-ghosn-ink/80"><span data-en="">{{ __('public.donate.phone_optional') }}</span><span data-ar="">{{ __('public.donate.phone_optional_ar') }}</span></label>
                    <input type="text" name="donor_phone" id="donor_phone" value="{{ old('donor_phone') }}" class="donate-input" dir="ltr">
                </div>
                <div class="sm:col-span-2">
                    <label for="message" class="mb-1.5 block text-sm font-medium text-ghosn-ink/80"><span data-en="">{{ __('public.donate.message_optional') }}</span><span data-ar="">{{ __('public.donate.message_optional_ar') }}</span></label>
                    <textarea name="message" id="message" rows="2" class="donate-input resize-y">{{ old('message') }}</textarea>
                </div>
                <div class="sm:col-span-2 flex items-center gap-3">
                    <input type="hidden" name="is_anonymous" value="0">
                    <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" @checked(old('is_anonymous')) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                    <label for="is_anonymous" class="text-sm text-ghosn-ink/75"><span data-en="">{{ __('public.donate.anonymous') }}</span><span data-ar="">{{ __('public.donate.anonymous_ar') }}</span></label>
                </div>
            </div>

            <button type="submit" data-donate-submit @disabled(! $hasPaymentMethod) class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-ghosn px-8 py-3.5 text-[15px] font-semibold text-offwhite shadow-md shadow-ghosn/15 transition hover:bg-ghosn-700 disabled:cursor-not-allowed disabled:opacity-50">
                <span data-donate-submit-default>
                    <span data-en="">{{ __('public.donate.submit') }}</span><span data-ar="">{{ __('public.donate.submit_ar') }}</span>
                </span>
                <span data-donate-submit-paypal class="hidden">
                    <span data-en="">{{ __('public.donate.submit_paypal_hint') }}</span><span data-ar="">{{ __('public.donate.submit_paypal_hint_ar') }}</span>
                </span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="rtl:rotate-180" data-donate-submit-icon><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
            </button>

            @if ($paypalEnabled)
                <div data-paypal-error class="hidden mt-3 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950" role="alert"></div>
                <div data-paypal-buttons class="hidden mt-3"></div>
            @endif
        </div>
    @endif
</form>
