@extends('admin.layouts.app')
@section('title', __('admin.newsletter.title'))
@section('page-title', __('admin.newsletter.title'))
@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <p class="text-sm text-[#8a9280]">{{ __('admin.newsletter.intro', ['count' => number_format($totalCount)]) }}</p>
        <a href="{{ route('admin.settings.show', 'newsletter') }}" class="gh-admin-btn-secondary no-underline">{{ __('admin.newsletter.edit_section') }}</a>
    </div>

    <form method="GET" action="{{ route('admin.newsletter.index') }}" class="gh-admin-filters mb-6 md:grid-cols-[1fr_auto]">
        <div>
            <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.newsletter.search') }}</label>
            <input type="search" name="search" value="{{ $search }}" placeholder="{{ __('admin.newsletter.search_placeholder') }}" class="ghosn-input">
        </div>
        <div class="flex items-end">
            <button type="submit" class="gh-admin-filter-btn">{{ __('admin.newsletter.filter') }}</button>
        </div>
    </form>

    @if ($subscribers->isEmpty())
        <div class="gh-admin-empty">{{ __('admin.newsletter.empty') }}</div>
    @else
        <x-admin.table-card>
            <table class="min-w-full text-[13.5px]">
                <thead>
                    <tr>
                        <th class="px-5 py-3.5 text-start">{{ __('admin.newsletter.col_email') }}</th>
                        <th class="px-3 py-3.5 text-start">{{ __('admin.newsletter.col_locale') }}</th>
                        <th class="px-3 py-3.5 text-start">{{ __('admin.newsletter.col_date') }}</th>
                        <th class="px-5 py-3.5 text-end">{{ __('admin.newsletter.col_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subscribers as $subscriber)
                        <tr>
                            <td class="px-5 py-3.5 font-medium text-[#2f4327]">{{ $subscriber->email }}</td>
                            <td class="px-3 py-3.5 uppercase text-[#8a9280]">{{ $subscriber->locale }}</td>
                            <td class="px-3 py-3.5 text-[#8a9280]">{{ $subscriber->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-3.5 text-end">
                                <form method="POST" action="{{ route('admin.newsletter.destroy', $subscriber) }}" onsubmit="return confirm(@json(__('admin.newsletter.confirm_delete')))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="gh-admin-btn-danger !px-3 !py-1.5 !text-xs">{{ __('admin.newsletter.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-admin.table-card>
        <div class="mt-6">{{ $subscribers->links() }}</div>
    @endif
@endsection
