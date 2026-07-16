@extends('admin.layouts.app')

@section('title', __('admin.donors.title'))
@section('page-title', __('admin.donors.title'))
@section('eyebrow', __('admin.panel'))

@section('content')

    <div class="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @include('admin.partials.stat-card', ['label' => __('admin.donors.stat_total_donors'), 'value' => number_format($stats['total_donors'])])
        @include('admin.partials.stat-card', ['label' => __('admin.donors.stat_total_donated'), 'value' => '$'.number_format($stats['total_donated'], 2), 'dir' => 'ltr'])
        @include('admin.partials.stat-card', ['label' => __('admin.donors.stat_repeat_donors'), 'value' => number_format($stats['repeat_donors'])])
        @include('admin.partials.stat-card', ['label' => __('admin.donors.stat_last_30_days'), 'value' => number_format($stats['last_30_days_donors'])])
    </div>

    <form method="GET" action="{{ route('admin.donors.index') }}" class="gh-admin-filters md:grid-cols-2 xl:grid-cols-4">
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donors.filter_search') }}</label>
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="{{ __('admin.donors.filter_search_placeholder') }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donors.filter_status') }}</label>
            <select name="status" class="ghosn-input">
                <option value="">{{ __('admin.donors.all_statuses') }}</option>
                <option value="active" @selected($filters['status'] === 'active')>{{ __('admin.donors.status_active') }}</option>
                <option value="blocked" @selected($filters['status'] === 'blocked')>{{ __('admin.donors.status_blocked') }}</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donors.filter_min_donated') }}</label>
            <input type="number" name="min_donated" value="{{ $filters['min_donated'] }}" min="0" step="0.01" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donors.filter_max_donated') }}</label>
            <input type="number" name="max_donated" value="{{ $filters['max_donated'] }}" min="0" step="0.01" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donors.filter_date_from') }}</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.donors.filter_date_to') }}</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="ghosn-input">
        </div>
        <div class="flex flex-wrap items-end gap-2 md:col-span-2">
            <button type="submit" class="gh-admin-filter-btn">{{ __('admin.donors.filter') }}</button>
            <a href="{{ route('admin.donors.index') }}" class="gh-admin-btn-secondary !border-none !bg-transparent !px-2 text-[#8a9280]">{{ __('admin.donors.clear_filters') }}</a>
            <a href="{{ route('admin.donors.export', request()->query()) }}" class="gh-admin-btn-secondary">{{ __('admin.donors.export_csv') }}</a>
        </div>
    </form>

    @if ($donors->isEmpty())
        <div class="gh-admin-empty">{{ __('admin.donors.empty') }}</div>
    @else
        <x-admin.table-card>
                <table class="min-w-full text-[13.5px]">
                    <thead>
                        <tr>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donors.col_name') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donors.col_email') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donors.col_phone') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donors.col_total') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donors.col_count') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donors.col_last_donation') }}</th>
                            <th class="px-5 py-3.5 text-start">{{ __('admin.donors.col_status') }}</th>
                            <th class="px-5 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($donors as $donor)
                            <tr class="border-t border-[rgba(64,97,57,0.09)]">
                                <td class="px-5 py-3.5 font-medium text-[#2f4327]">
                                    {{ $donor->name }}
                                    @if ($donor->is_anonymous)
                                        <span class="ms-1 rounded-full bg-[rgba(237,238,228,0.8)] px-2 py-0.5 text-[10px] font-semibold uppercase text-[#8a9280]">{{ __('admin.donors.anonymous') }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5" dir="ltr">{{ $donor->email }}</td>
                                <td class="px-5 py-3.5" dir="ltr">{{ $donor->phone ?: '—' }}</td>
                                <td class="px-5 py-3.5 font-semibold" dir="ltr">${{ number_format((float) $donor->total_donated, 2) }}</td>
                                <td class="px-5 py-3.5">{{ $donor->donations_count }}</td>
                                <td class="px-5 py-3.5">{{ $donor->last_donation_at?->format('Y-m-d H:i') ?: '—' }}</td>
                                <td class="px-5 py-3.5">
                                    @include('admin.partials.status-pill', [
                                        'status' => $donor->status === 'active' ? 'active' : 'blocked',
                                        'label' => __('admin.donors.status_'.$donor->status),
                                    ])
                                </td>
                                <td class="px-5 py-3.5 text-end">
                                    <a href="{{ route('admin.donors.show', $donor) }}" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs no-underline">{{ __('admin.donors.view') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </x-admin.table-card>
        <div class="mt-6">{{ $donors->links() }}</div>
    @endif
@endsection
