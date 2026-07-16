@extends('admin.layouts.app')

@section('title', __('admin.donors.profile'))
@section('page-title', $donor->name)
@section('eyebrow', __('admin.donors.title'))

@section('content')

    <div class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ route('admin.donors.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#406139] no-underline hover:text-[#33502e]">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
            {{ __('admin.donors.back_to_list') }}
        </a>
        <form method="POST" action="{{ route('admin.donors.toggle-block', $donor) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="gh-admin-btn-secondary">
                {{ $donor->isBlocked() ? __('admin.donors.unblock') : __('admin.donors.block') }}
            </button>
        </form>
    </div>

    <div class="mb-6 space-y-6">
        <div class="grid gap-6 xl:grid-cols-3">
            <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 xl:col-span-1">
                <h2 class="text-sm font-bold uppercase tracking-wide text-ghosn/55">{{ __('admin.donors.profile') }}</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-ghosn/55">{{ __('admin.donors.col_email') }}</dt><dd class="font-medium" dir="ltr">{{ $donor->email }}</dd></div>
                    <div><dt class="text-ghosn/55">{{ __('admin.donors.col_phone') }}</dt><dd dir="ltr">{{ $donor->phone ?: '—' }}</dd></div>
                    <div><dt class="text-ghosn/55">{{ __('admin.donors.col_locale') }}</dt><dd>{{ strtoupper($donor->locale) }}</dd></div>
                    <div><dt class="text-ghosn/55">{{ __('admin.donors.col_status') }}</dt><dd>{{ __('admin.donors.status_'.$donor->status) }}</dd></div>
                    <div><dt class="text-ghosn/55">{{ __('admin.donors.total_donated') }}</dt><dd class="text-lg font-bold text-ghosn" dir="ltr">${{ number_format((float) $donor->total_donated, 2) }}</dd></div>
                    <div><dt class="text-ghosn/55">{{ __('admin.donors.donations_count') }}</dt><dd>{{ $donor->donations_count }}</dd></div>
                </dl>
            </div>

            <div class="xl:col-span-2">
                @include('admin.donors.partials.email-compose-form')
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6">
                <h2 class="text-sm font-bold text-ghosn">{{ __('admin.donors.donation_history') }}</h2>
                @if ($donor->donations->isEmpty())
                    <p class="mt-4 text-sm text-ghosn-ink/60">{{ __('admin.donors.no_donations') }}</p>
                @else
                    <div class="gh-admin-table-scroll mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-xs uppercase text-ghosn/55">
                                <tr>
                                    <th class="py-2 pe-4">{{ __('admin.donations.col_reference') }}</th>
                                    <th class="py-2 pe-4">{{ __('admin.donations.col_amount') }}</th>
                                    <th class="py-2 pe-4">{{ __('admin.donations.col_status') }}</th>
                                    <th class="py-2">{{ __('admin.donations.col_date') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ghosn/8">
                                @foreach ($donor->donations as $donation)
                                    <tr>
                                        <td class="py-3 pe-4 font-medium" dir="ltr">{{ $donation->reference }}</td>
                                        <td class="py-3 pe-4" dir="ltr">{{ $donation->formattedAmount() }}</td>
                                        <td class="py-3 pe-4">{{ __('admin.donations.status_'.$donation->status) }}</td>
                                        <td class="py-3">{{ $donation->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6">
                <h2 class="text-sm font-bold text-ghosn">{{ __('admin.donors.campaigns_supported') }}</h2>
                @if ($campaigns === [])
                    <p class="mt-4 text-sm text-ghosn-ink/60">{{ __('admin.donors.no_campaigns') }}</p>
                @else
                    <ul class="mt-4 space-y-2 text-sm">
                        @foreach ($campaigns as $campaign)
                            <li class="flex justify-between gap-4 rounded-xl bg-cream/40 px-4 py-3">
                                <span>{{ $campaign['title_en'] }}</span>
                                <span class="font-semibold" dir="ltr">${{ number_format((float) $campaign['total_amount'], 2) }} ({{ $campaign['donation_count'] }})</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 xl:col-span-2">
                <h2 class="text-sm font-bold text-ghosn">{{ __('admin.donors.email_log') }}</h2>
                @if ($donor->emailLogs->isEmpty())
                    <p class="mt-4 text-sm text-ghosn-ink/60">{{ __('admin.donors.no_emails') }}</p>
                @else
                    <div class="gh-admin-table-scroll mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-xs uppercase text-ghosn/55">
                                <tr>
                                    <th class="py-2 pe-4">{{ __('admin.donors.email_type') }}</th>
                                    <th class="py-2 pe-4">{{ __('admin.donors.email_subject') }}</th>
                                    <th class="py-2 pe-4">{{ __('admin.donors.email_status') }}</th>
                                    <th class="py-2">{{ __('admin.donors.email_sent_at') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-ghosn/8">
                                @foreach ($donor->emailLogs as $log)
                                    <tr>
                                        <td class="py-3 pe-4">{{ __('admin.donors.email_type_'.$log->type) }}</td>
                                        <td class="py-3 pe-4">{{ $log->subject }}</td>
                                        <td class="py-3 pe-4">{{ __('admin.donors.email_status_'.$log->status) }}</td>
                                        <td class="py-3">{{ $log->sent_at?->format('Y-m-d H:i') ?: '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('admin.partials.cms-scripts')
    @include('admin.cms.partials.media-modal')
@endpush
