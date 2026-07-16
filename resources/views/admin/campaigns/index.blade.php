@extends('admin.layouts.app')

@section('title', __('admin.campaigns.title'))
@section('page-title', __('admin.campaigns.title'))

@section('content')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="{{ __('admin.campaigns.search') }}" class="ghosn-input max-w-xs">
            <select name="status" class="ghosn-input max-w-[160px]">
                <option value="">{{ __('admin.campaigns.all_statuses') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ __('admin.campaigns.status_'.$status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-[11px] border border-[rgba(64,97,57,0.18)] px-4 py-2 text-sm font-semibold text-[#406139]">{{ __('admin.campaigns.filter') }}</button>
        </form>
        <a href="{{ route('admin.campaigns.create') }}" class="inline-flex items-center gap-2 rounded-[11px] bg-[#406139] px-5 py-2.5 text-sm font-semibold text-[#F2F1EA] no-underline shadow-[0_6px_20px_rgba(47,67,39,0.12)] hover:bg-[#33502e]">
            + {{ __('admin.campaigns.new') }}
        </a>
    </div>

    <x-admin.table-card>
        <table class="min-w-full text-[13.5px]">
            <thead>
                <tr>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.campaigns.title_en') }}</th>
                    <th class="px-3 py-3.5 text-start">{{ __('admin.campaigns.status') }}</th>
                    <th class="px-3 py-3.5 text-start">{{ __('admin.campaigns.progress') }}</th>
                    <th class="px-3 py-3.5 text-start">{{ __('admin.campaigns.donors') }}</th>
                    <th class="px-5 py-3.5 text-end">{{ __('admin.campaigns.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($campaigns as $campaign)
                    @php $pct = (int) round($campaign->progressPercent()); @endphp
                    <tr class="border-t border-[rgba(64,97,57,0.09)] align-middle">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                @if ($campaign->featuredImage)
                                    <img src="{{ $campaign->featuredImage->thumbnailUrl() ?? $campaign->featuredImage->url() }}" alt="" class="h-11 w-11 rounded-[11px] border border-[rgba(64,97,57,0.1)] object-cover">
                                @else
                                    <div class="flex h-11 w-11 items-center justify-center rounded-[11px] bg-[rgba(129,149,98,0.18)] text-[#406139]">◎</div>
                                @endif
                                <div>
                                    <p class="font-semibold text-[#2f4327]">{{ $campaign->title_en }}</p>
                                    <p class="mt-0.5 text-xs text-[#8a9280]" dir="ltr">{{ $campaign->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-3.5">
                            @include('admin.partials.status-pill', [
                                'status' => $campaign->status === \App\Models\Campaign::STATUS_ACTIVE ? 'active' : $campaign->status,
                                'label' => __('admin.campaigns.status_'.$campaign->status),
                            ])
                        </td>
                        <td class="min-w-[130px] px-3 py-3.5">
                            <div class="mb-1 h-[7px] overflow-hidden rounded-full bg-[rgba(64,97,57,0.12)]">
                                <div class="h-full rounded-full bg-gradient-to-r from-[#819562] to-[#406139]" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="text-[11.5px] text-[#8a9280]">{{ $pct }}% · <span class="font-bold text-[#406139]">{{ $campaign->formattedRaised() }}</span> / {{ $campaign->formattedGoal() }}</div>
                        </td>
                        <td class="px-3 py-3.5">{{ number_format($campaign->donors_count) }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex flex-wrap justify-end gap-2">
                                @if ($campaign->isPublic())
                                    <a href="{{ route('campaigns.show', $campaign->slug) }}" target="_blank" class="rounded-[9px] border border-[rgba(64,97,57,0.18)] px-3 py-1.5 text-xs font-semibold text-[#406139] no-underline hover:bg-[rgba(64,97,57,0.06)]">{{ __('admin.campaigns.view') }}</a>
                                @endif
                                <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="rounded-[9px] border border-[rgba(64,97,57,0.18)] px-3 py-1.5 text-xs font-semibold text-[#406139] no-underline hover:bg-[rgba(64,97,57,0.06)]">{{ __('admin.campaigns.edit') }}</a>
                                <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign) }}" onsubmit="return confirm(@json(__('admin.campaigns.confirm_delete')))">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-[9px] border border-[rgba(162,74,55,0.24)] px-3 py-1.5 text-xs font-semibold text-[#a24a37] hover:bg-[rgba(162,74,55,0.08)]">{{ __('admin.campaigns.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-[#8a9280]">{{ __('admin.campaigns.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-admin.table-card>

    <div class="mt-6">{{ $campaigns->links() }}</div>
@endsection
