@extends('admin.layouts.app')

@section('title', __('admin.donations.title'))
@section('page-title', __('admin.donations.title'))
@section('eyebrow', __('admin.panel'))

@section('content')

    <div class="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @include('admin.partials.stat-card', ['label' => __('admin.donations.stat_total'), 'value' => number_format($stats['total_count'])])
        @include('admin.partials.stat-card', ['label' => __('admin.donations.stat_paid_amount'), 'value' => '$'.number_format($stats['paid_amount'], 2), 'dir' => 'ltr'])
        @include('admin.partials.stat-card', ['label' => __('admin.donations.stat_pending_amount'), 'value' => '$'.number_format($stats['pending_amount'], 2), 'dir' => 'ltr'])
        @include('admin.partials.stat-card', ['label' => __('admin.donations.stat_bank_pending'), 'value' => number_format($stats['bank_pending_count'])])
        @include('admin.partials.stat-card', ['label' => __('admin.donations.stat_stripe_paid'), 'value' => number_format($stats['stripe_paid_count'])])
        @include('admin.partials.stat-card', ['label' => __('admin.donations.stat_paypal_paid'), 'value' => number_format($stats['paypal_paid_count'])])
    </div>

    <form method="GET" action="{{ route('admin.donations.index') }}" class="gh-admin-filters md:grid-cols-2 xl:grid-cols-4">
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donations.filter_search') }}</label>
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="{{ __('admin.donations.filter_search_placeholder') }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donations.col_status') }}</label>
            <select name="status" class="ghosn-input">
                <option value="">{{ __('admin.donations.all_statuses') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ __('admin.donations.status_'.$status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donations.col_gateway') }}</label>
            <select name="gateway" class="ghosn-input">
                <option value="">{{ __('admin.donations.all_gateways') }}</option>
                @foreach ($gateways as $gateway)
                    <option value="{{ $gateway }}" @selected($filters['gateway'] === $gateway)>{{ __('admin.donations.gateway_'.$gateway) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donations.col_method') }}</label>
            <select name="payment_method" class="ghosn-input">
                <option value="">{{ __('admin.donations.all_methods') }}</option>
                @foreach ($paymentMethods as $method)
                    <option value="{{ $method }}" @selected($filters['payment_method'] === $method)>{{ __('admin.donations.method_'.$method) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donations.filter_campaign') }}</label>
            <select name="campaign_id" class="ghosn-input">
                <option value="">{{ __('admin.donations.all_campaigns') }}</option>
                @foreach ($campaigns as $campaign)
                    <option value="{{ $campaign->id }}" @selected($filters['campaign_id'] === (string) $campaign->id)>{{ $campaign->title_en }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donations.filter_date_from') }}</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donations.filter_date_to') }}</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="ghosn-input">
        </div>
        <div class="flex items-end gap-2 md:col-span-2 xl:col-span-2">
            <button type="submit" class="gh-admin-filter-btn">{{ __('admin.donations.filter') }}</button>
            <a href="{{ route('admin.donations.index') }}" class="gh-admin-btn-secondary !border-none !bg-transparent !px-2 text-[#8a9280]">{{ __('admin.donations.clear_filters') }}</a>
        </div>
    </form>

    @if ($donations->isEmpty())
        <div class="gh-admin-empty">{{ __('admin.donations.empty') }}</div>
    @else
        <x-admin.table-card>
                <table class="min-w-full text-[13.5px]">
                    <thead>
                        <tr>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donations.col_reference') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donations.col_donor') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donations.col_amount') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donations.col_gateway') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donations.col_method') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donations.col_campaign') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donations.col_status') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donations.col_transaction') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donations.col_date') }}</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($donations as $donation)
                            <tr class="border-t border-[rgba(64,97,57,0.09)]">
                                <td class="px-5 py-3.5 font-semibold text-[#406139]" dir="ltr">{{ $donation->reference }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="font-medium text-[#2f4327]">{{ $donation->displayDonorName() }}</div>
                                    <div class="text-xs text-[#8a9280]" dir="ltr">{{ $donation->donor_email }}</div>
                                </td>
                                <td class="px-5 py-3.5 font-semibold">{{ $donation->formattedAmount() }}</td>
                                <td class="px-5 py-3.5">{{ $donation->gateway ? __('admin.donations.gateway_'.$donation->gateway) : '—' }}</td>
                                <td class="px-5 py-3.5">{{ __('admin.donations.method_'.$donation->payment_method) }}</td>
                                <td class="px-5 py-3.5 text-sm text-[#8a9280]">{{ $donation->campaign?->title_en ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    @include('admin.partials.status-pill', [
                                        'status' => $donation->status,
                                        'label' => __('admin.donations.status_'.$donation->status),
                                    ])
                                </td>
                                <td class="px-5 py-3.5 text-xs text-[#8a9280]" dir="ltr">{{ $donation->gateway_transaction_id ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-[#8a9280]">{{ $donation->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-5 py-3.5 text-end">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('admin.donations.receipt.show', $donation) }}" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs">{{ __('admin.donations.receipt_view') }}</a>
                                        <a href="{{ route('admin.donations.receipt.print', $donation) }}" target="_blank" rel="noopener" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs">{{ __('admin.donations.receipt_print') }}</a>
                                        <a href="{{ route('admin.donations.receipt.download', $donation) }}" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs">{{ __('admin.donations.receipt_download') }}</a>
                                        @if ($donation->canManuallyConfirm())
                                            <form method="POST" action="{{ route('admin.donations.confirm', $donation) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="gh-admin-btn-primary !px-3 !py-1.5 !text-xs">{{ __('admin.donations.confirm') }}</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </x-admin.table-card>
        <div class="mt-8">{{ $donations->links() }}</div>
    @endif
@endsection
