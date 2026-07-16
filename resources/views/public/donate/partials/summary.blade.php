@php $symbol = $currencyMeta['symbol'] ?? '$'; @endphp

<div
    class="rounded-3xl border border-ghosn/10 bg-cream/40 p-6 md:p-7 lg:sticky lg:top-8"
    data-donate-summary
    data-label-stripe-en="{{ __('public.donate.method_card') }}"
    data-label-stripe-ar="{{ __('public.donate.method_card_ar') }}"
    data-label-paypal-en="{{ __('public.donate.method_paypal') }}"
    data-label-paypal-ar="{{ __('public.donate.method_paypal_ar') }}"
    data-label-bank-en="{{ __('public.donate.method_bank') }}"
    data-label-bank-ar="{{ __('public.donate.method_bank_ar') }}"
>
    <h2 class="text-sm font-bold uppercase tracking-[0.16em] text-ghosn/65">
        <span data-en="">{{ __('public.donate.summary_title') }}</span>
        <span data-ar="">{{ __('public.donate.summary_title_ar') }}</span>
    </h2>

    @if (! empty($campaign))
        <div class="mt-5 rounded-2xl border border-growth/20 bg-growth-soft/30 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-growth">{{ __('public.donate.campaign_label') }}</p>
            <p class="mt-1 text-sm font-bold text-ghosn"><x-landing.bilingual :en="$campaign->title_en" :ar="$campaign->title_ar" /></p>
            @include('public.campaigns.partials.progress', ['campaign' => $campaign, 'compact' => true])
        </div>
    @endif

    <dl class="mt-5 space-y-4 text-sm">
        <div class="flex items-center justify-between gap-4 border-b border-ghosn/8 pb-4">
            <dt class="text-ghosn-ink/60"><span data-en="">{{ __('public.donate.summary_amount') }}</span><span data-ar="">{{ __('public.donate.summary_amount_ar') }}</span></dt>
            <dd class="text-lg font-bold text-ghosn" data-summary-amount dir="ltr"><span class="inline-flex items-center gap-1.5"><span>{{ $symbol }}</span><span>{{ number_format(old('amount', $amountPresets[1] ?? 50)) }}</span></span></dd>
        </div>
        <div class="flex items-center justify-between gap-4">
            <dt class="text-ghosn-ink/60"><span data-en="">{{ __('public.donate.summary_method') }}</span><span data-ar="">{{ __('public.donate.summary_method_ar') }}</span></dt>
            <dd class="font-semibold text-ghosn" data-summary-method>
                @if ($stripeEnabled)
                    <span data-en="">{{ __('public.donate.method_card') }}</span><span data-ar="">{{ __('public.donate.method_card_ar') }}</span>
                @elseif ($paypalEnabled)
                    <span data-en="">{{ __('public.donate.method_paypal') }}</span><span data-ar="">{{ __('public.donate.method_paypal_ar') }}</span>
                @else
                    <span data-en="">{{ __('public.donate.method_bank') }}</span><span data-ar="">{{ __('public.donate.method_bank_ar') }}</span>
                @endif
            </dd>
        </div>
    </dl>

    <p class="mt-5 text-[13px] leading-relaxed text-ghosn-ink/65">
        <span data-en="">{{ __('public.donate.impact_note') }}</span>
        <span data-ar="">{{ __('public.donate.impact_note_ar') }}</span>
    </p>

    <div class="mt-5 hidden rounded-2xl border border-ghosn/10 bg-offwhite p-4 text-[13px]" data-summary-bank>
        <p class="mb-3 font-semibold text-ghosn">
            <span data-en="">{{ __('public.donate.bank_preview_title') }}</span>
            <span data-ar="">{{ __('public.donate.bank_preview_title_ar') }}</span>
        </p>
        <p class="text-ghosn-ink/65">
            <span data-en="">{{ __('public.donate.bank_preview_note') }}</span>
            <span data-ar="">{{ __('public.donate.bank_preview_note_ar') }}</span>
        </p>
        @if (filled($bankDetails['iban']))
            <p class="mt-3 font-mono text-xs text-ghosn" dir="ltr">{{ $bankDetails['iban'] }}</p>
        @elseif (filled($bankDetails['account_number']))
            <p class="mt-3 font-mono text-xs text-ghosn" dir="ltr">{{ $bankDetails['account_number'] }}</p>
        @endif
        @if (filled($bankDetails['bank_name_en']) || filled($bankDetails['bank_name_ar']))
            <p class="mt-2 text-xs text-ghosn-ink/55"><span data-en="">{{ $bankDetails['bank_name_en'] }}</span><span data-ar="">{{ $bankDetails['bank_name_ar'] }}</span></p>
        @endif
    </div>
</div>
