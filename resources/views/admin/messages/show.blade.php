@extends('admin.layouts.app')

@section('title', $message->subject)
@section('page-title', $message->subject)

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#406139] no-underline hover:text-[#33502e]">
            ← {{ __('admin.settings.back_to_hub') }}
        </a>
    </div>

    <div class="gh-admin-card max-w-3xl rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 md:p-8">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4 border-b border-[rgba(64,97,57,0.1)] pb-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[#8a9280]">{{ __('admin.messages.from') }}</p>
                <p class="mt-1 text-lg font-bold text-[#2f4327]">{{ $message->name }}</p>
                <p class="text-sm text-[#8a9280]" dir="ltr">{{ $message->email }}</p>
            </div>
            <div class="text-end">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#8a9280]">{{ __('admin.messages.received') }}</p>
                <p class="mt-1 text-sm text-[#5f6857]">{{ $message->created_at?->format('M j, Y g:i A') }}</p>
            </div>
        </div>

        <div class="prose prose-sm max-w-none text-[#3a4234]">
            {!! nl2br(e($message->message)) !!}
        </div>

        <div class="mt-8 flex flex-wrap gap-2">
            @unless ($message->is_read)
                <form method="POST" action="{{ route('admin.messages.read', $message) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="rounded-[11px] bg-[#406139] px-4 py-2 text-sm font-semibold text-[#F2F1EA]">{{ __('admin.messages.read') }}</button>
                </form>
            @endunless
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm(@json(__('admin.cms.confirm_delete')))">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-[11px] border border-[rgba(162,74,55,0.24)] bg-transparent px-4 py-2 text-sm font-semibold text-[#a24a37]">{{ __('admin.cms.delete') }}</button>
            </form>
        </div>
    </div>
@endsection
