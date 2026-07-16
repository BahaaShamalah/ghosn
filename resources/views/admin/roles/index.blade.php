@extends('admin.layouts.app')

@section('title', __('admin.roles.title'))
@section('page-title', __('admin.roles.title'))

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-[#8a9280]">{{ __('admin.roles.intro') }}</p>
        <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center gap-2 rounded-[11px] bg-[#406139] px-5 py-2.5 text-sm font-semibold text-[#F2F1EA] no-underline shadow-[0_6px_20px_rgba(47,67,39,0.12)] hover:bg-[#33502e]">
            + {{ __('admin.roles.new') }}
        </a>
    </div>

    <div class="grid gap-4">
        @foreach ($roles as $role)
            <div class="gh-admin-card rounded-[18px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-bold text-[#2f4327]">{{ $role->localizedLabel() }}</h2>
                            @if ($role->is_super)
                                <span class="rounded-full bg-[rgba(129,149,98,0.16)] px-2.5 py-1 text-[11px] font-bold text-[#33502e]">{{ __('admin.roles.super_badge') }}</span>
                            @endif
                            @if ($role->is_system)
                                <span class="rounded-full bg-[rgba(64,97,57,0.1)] px-2.5 py-1 text-[11px] font-bold text-[#406139]">{{ __('admin.roles.system_badge') }}</span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-[#8a9280]" dir="ltr">{{ $role->slug }} · {{ trans_choice('admin.roles.users_count', $role->users_count, ['count' => $role->users_count]) }}</p>
                        @if ($role->is_super)
                            <p class="mt-3 text-sm text-[#5f6857]">{{ __('admin.roles.all_permissions') }}</p>
                        @else
                            <div class="mt-3 flex max-w-3xl flex-wrap gap-1.5">
                                @foreach ($role->permissions as $permission)
                                    <span class="inline-flex rounded-full bg-[rgba(64,97,57,0.08)] px-2.5 py-1 text-xs font-medium text-[#406139]">{{ $permission->localizedLabel() }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="rounded-[9px] border border-[rgba(64,97,57,0.18)] px-3 py-1.5 text-xs font-semibold text-[#406139] no-underline hover:bg-[rgba(64,97,57,0.06)]">{{ __('admin.roles.edit') }}</a>
                        @if (! $role->is_system)
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm(@json(__('admin.roles.confirm_delete')))">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-[9px] border border-[rgba(162,74,55,0.24)] px-3 py-1.5 text-xs font-semibold text-[#a24a37] hover:bg-[rgba(162,74,55,0.08)]">{{ __('admin.roles.delete') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
