@extends('admin.layouts.app')

@section('title', __('admin.volunteers.title'))
@section('page-title', __('admin.volunteers.title'))

@section('content')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-[#8a9280]">
            {{ __('admin.volunteers.pending_count', ['count' => $pendingCount]) }}
        </p>
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="{{ __('admin.volunteers.search') }}" class="ghosn-input max-w-xs">
            <select name="status" class="ghosn-input max-w-[180px]">
                <option value="">{{ __('admin.volunteers.all_statuses') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ __('admin.volunteers.status_'.$status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-[11px] border border-[rgba(64,97,57,0.18)] px-4 py-2 text-sm font-semibold text-[#406139]">{{ __('admin.campaigns.filter') }}</button>
        </form>
    </div>

    <x-admin.table-card>
        <table class="min-w-full text-[13.5px]">
            <thead>
                <tr>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.volunteers.col_name') }}</th>
                    <th class="px-3 py-3.5 text-start">{{ __('admin.volunteers.col_area') }}</th>
                    <th class="px-3 py-3.5 text-start">{{ __('admin.volunteers.col_date') }}</th>
                    <th class="px-3 py-3.5 text-start">{{ __('admin.volunteers.col_status') }}</th>
                    <th class="px-5 py-3.5 text-end">{{ __('admin.volunteers.col_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr class="border-t border-[rgba(64,97,57,0.09)]">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[rgba(129,149,98,0.18)] text-sm font-bold text-[#406139]">{{ $application->initial() }}</span>
                                <div>
                                    <div class="font-semibold text-[#2f4327]">{{ $application->name }}</div>
                                    <div class="text-xs text-[#8a9280]">{{ $application->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-[#5f6857]">{{ $application->localizedArea() }}</td>
                        <td class="px-3 py-3 text-[#8a9280]">{{ $application->created_at?->format('M j, Y') }}</td>
                        <td class="px-3 py-3">
                            @include('admin.partials.status-pill', [
                                'status' => $application->status,
                                'label' => __('admin.volunteers.status_'.$application->status),
                            ])
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-2">
                                @if ($application->status !== \App\Models\VolunteerApplication::STATUS_APPROVED)
                                    <form method="POST" action="{{ route('admin.volunteers.update-status', $application) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="rounded-[9px] border-none bg-[rgba(64,97,57,0.12)] px-3.5 py-1.5 text-xs font-semibold text-[#33502e]">{{ __('admin.volunteers.approve') }}</button>
                                    </form>
                                @endif
                                @if ($application->status !== \App\Models\VolunteerApplication::STATUS_REJECTED)
                                    <form method="POST" action="{{ route('admin.volunteers.update-status', $application) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="rounded-[9px] border border-[rgba(162,74,55,0.24)] bg-transparent px-3.5 py-1.5 text-xs font-semibold text-[#a24a37]">{{ __('admin.volunteers.reject') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-[#8a9280]">{{ __('admin.volunteers.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-admin.table-card>

    <div class="mt-6">{{ $applications->links() }}</div>
@endsection
