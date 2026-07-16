@extends('admin.layouts.app')

@section('title', __('admin.messages.title'))
@section('page-title', __('admin.messages.title'))

@section('content')

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-[#8a9280]">{{ __('admin.messages.unread_count', ['count' => $unreadCount]) }}</p>
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="{{ __('admin.messages.search') }}" class="ghosn-input max-w-xs">
            <select name="read" class="ghosn-input max-w-[160px]">
                <option value="">{{ __('admin.messages.all') }}</option>
                <option value="0" @selected($filters['read'] === '0')>{{ __('admin.messages.unread') }}</option>
                <option value="1" @selected($filters['read'] === '1')>{{ __('admin.messages.read') }}</option>
            </select>
            <button type="submit" class="rounded-[11px] border border-[rgba(64,97,57,0.18)] px-4 py-2 text-sm font-semibold text-[#406139]">{{ __('admin.campaigns.filter') }}</button>
        </form>
    </div>

    <div class="flex flex-col gap-3">
        @forelse ($messages as $item)
            <a href="{{ route('admin.messages.show', $item) }}" class="gh-admin-card flex gap-4 rounded-[16px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-5 no-underline shadow-[0_4px_14px_rgba(47,67,39,0.04)] transition hover:shadow-[0_10px_26px_rgba(47,67,39,0.1)]">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#406139] text-[15px] font-bold text-[#F7F6F0]">{{ $item->initial() }}</span>
                <div class="min-w-0 flex-1">
                    <div class="mb-1 flex flex-wrap items-center gap-2">
                        <span class="text-[14.5px] font-bold text-[#2f4327]">{{ $item->name }}</span>
                        @unless ($item->is_read)
                            <span class="inline-block h-2 w-2 rounded-full bg-[#819562]"></span>
                        @endunless
                        <span class="ms-auto text-xs text-[#8a9280]">{{ $item->created_at?->diffForHumans() }}</span>
                    </div>
                    <div class="text-[13.5px] font-semibold text-[#4a5340]">{{ $item->subject }}</div>
                    <div class="mt-1 text-[13.5px] text-[#5f6857]">{{ $item->preview() }}</div>
                </div>
            </a>
        @empty
            <div class="gh-admin-card rounded-[16px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-10 text-center text-[#8a9280]">{{ __('admin.messages.empty') }}</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $messages->links() }}</div>
@endsection
