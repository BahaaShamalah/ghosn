@extends('public.layouts.checkout')

@section('title', __('public.donate.thank_you_title'))

@section('content')
    <div class="mx-auto max-w-lg" data-reveal>
        <div class="text-center">
            <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-ghosn text-offwhite">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.6-7 10-7 10z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tightish text-ghosn md:text-3xl">
                <span data-en="">{{ __('public.donate.thank_you_heading') }}</span>
                <span data-ar="">{{ __('public.donate.thank_you_heading_ar') }}</span>
            </h1>
            <p class="mt-4 text-[15px] leading-relaxed text-ghosn-ink/70">
                <span data-en="">{{ __('public.donate.thank_you_message', ['amount' => $donation->formattedAmount()]) }}</span>
                <span data-ar="">{{ __('public.donate.thank_you_message_ar', ['amount' => $donation->formattedAmount()]) }}</span>
            </p>
        </div>

        <div class="mt-6 rounded-2xl border border-ghosn/10 bg-offwhite p-5 md:p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-growth">
                <span data-en="">{{ __('public.donate.your_reference') }}</span><span data-ar="">{{ __('public.donate.your_reference_ar') }}</span>
            </p>
            <p class="mt-1 text-xl font-bold text-ghosn" dir="ltr">{{ $donation->reference }}</p>

            <dl class="mt-5 space-y-3 text-sm">
                @if (filled($bankDetails['account_holder_en']) || filled($bankDetails['account_holder_ar']))
                    <div>
                        <dt class="text-ghosn-ink/50"><span data-en="">{{ __('public.donate.account_holder') }}</span><span data-ar="">{{ __('public.donate.account_holder_ar') }}</span></dt>
                        <dd class="mt-0.5 font-semibold text-ghosn"><span data-en="">{{ $bankDetails['account_holder_en'] }}</span><span data-ar="">{{ $bankDetails['account_holder_ar'] }}</span></dd>
                    </div>
                @endif
                @if (filled($bankDetails['bank_name_en']) || filled($bankDetails['bank_name_ar']))
                    <div>
                        <dt class="text-ghosn-ink/50"><span data-en="">{{ __('public.donate.bank_name') }}</span><span data-ar="">{{ __('public.donate.bank_name_ar') }}</span></dt>
                        <dd class="mt-0.5 font-semibold text-ghosn"><span data-en="">{{ $bankDetails['bank_name_en'] }}</span><span data-ar="">{{ $bankDetails['bank_name_ar'] }}</span></dd>
                    </div>
                @endif
                @if (filled($bankDetails['iban']))
                    <div>
                        <dt class="text-ghosn-ink/50">IBAN</dt>
                        <dd class="mt-0.5 font-semibold text-ghosn" dir="ltr">{{ $bankDetails['iban'] }}</dd>
                    </div>
                @endif
                @if (filled($bankDetails['account_number']))
                    <div>
                        <dt class="text-ghosn-ink/50"><span data-en="">{{ __('public.donate.account_number') }}</span><span data-ar="">{{ __('public.donate.account_number_ar') }}</span></dt>
                        <dd class="mt-0.5 font-semibold text-ghosn" dir="ltr">{{ $bankDetails['account_number'] }}</dd>
                    </div>
                @endif
                @if (filled($bankDetails['swift']))
                    <div>
                        <dt class="text-ghosn-ink/50">SWIFT / BIC</dt>
                        <dd class="mt-0.5 font-semibold text-ghosn" dir="ltr">{{ $bankDetails['swift'] }}</dd>
                    </div>
                @endif
            </dl>

            <p class="mt-4 text-xs leading-relaxed text-ghosn-ink/65">
                <span data-en="">{{ $bankDetails['instructions_en'] }}</span><span data-ar="">{{ $bankDetails['instructions_ar'] }}</span>
            </p>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="inline-flex h-11 items-center rounded-full bg-ghosn px-6 text-sm font-semibold text-offwhite hover:bg-ghosn-700">
                <span data-en="">{{ __('public.donate.back_home') }}</span><span data-ar="">{{ __('public.donate.back_home_ar') }}</span>
            </a>
        </div>
    </div>
@endsection

@push('head')
    @include('public.donate.partials.styles')
@endpush

@push('scripts')
    <script>
        document.getElementById('ghosn-root')?.setAttribute('data-ready', '1');
        document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('in'));
    </script>
@endpush
