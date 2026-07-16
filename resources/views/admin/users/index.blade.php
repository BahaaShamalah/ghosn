@extends('admin.layouts.app')

@section('title', __('admin.users.title'))
@section('page-title', __('admin.users.title'))

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="search" name="search" value="{{ $search }}" placeholder="{{ __('admin.users.search') }}" class="ghosn-input max-w-xs">
            <button type="submit" class="rounded-[11px] border border-[rgba(64,97,57,0.18)] px-4 py-2 text-sm font-semibold text-[#406139]">{{ __('admin.users.filter') }}</button>
        </form>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-[11px] bg-[#406139] px-5 py-2.5 text-sm font-semibold text-[#F2F1EA] no-underline shadow-[0_6px_20px_rgba(47,67,39,0.12)] hover:bg-[#33502e]">
            + {{ __('admin.users.new') }}
        </a>
    </div>

    <x-admin.table-card>
        <table class="gh-admin-data-table min-w-full text-[13.5px]">
            <colgroup>
                <col class="w-[26%]">
                <col class="w-[34%]">
                <col class="w-[22%]">
                <col class="w-[18%]">
            </colgroup>
            <thead>
                <tr>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.users.name') }}</th>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.users.email') }}</th>
                    <th class="px-5 py-3.5 text-start">{{ __('admin.users.role') }}</th>
                    <th class="px-5 py-3.5 text-end">{{ __('admin.users.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-t border-[rgba(64,97,57,0.09)]">
                        <td class="px-5 py-3.5 text-start font-semibold text-[#2f4327]">{{ $user->name }}</td>
                        <td class="px-5 py-3.5 text-start">
                            <span class="inline-block max-w-full truncate" dir="ltr" title="{{ $user->email }}">{{ $user->email }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-start">{{ $user->roleLabel() }}</td>
                        <td class="gh-admin-table-actions px-5 py-3.5 text-end">
                            <div class="inline-flex flex-wrap justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="rounded-[9px] border border-[rgba(64,97,57,0.18)] px-3 py-1.5 text-xs font-semibold text-[#406139] no-underline hover:bg-[rgba(64,97,57,0.06)]">{{ __('admin.users.edit') }}</a>
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm(@json(__('admin.users.confirm_delete')))">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-[9px] border border-[rgba(162,74,55,0.24)] px-3 py-1.5 text-xs font-semibold text-[#a24a37] hover:bg-[rgba(162,74,55,0.08)]">{{ __('admin.users.delete') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-[#8a9280]">{{ __('admin.users.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-admin.table-card>

    <div class="mt-5">{{ $users->links() }}</div>
@endsection
