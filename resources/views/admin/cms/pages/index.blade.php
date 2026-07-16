@extends('admin.layouts.app')

@section('title', __('admin.cms.pages_title'))
@section('page-title', __('admin.cms.pages_title'))

@section('content')
@php
    $statusClasses = [
        \App\Models\ContentPage::STATUS_DRAFT => 'bg-beige text-ghosn/75 ring-ghosn/10',
        \App\Models\ContentPage::STATUS_PUBLISHED => 'bg-growth-soft text-ghosn ring-growth/20',
        \App\Models\ContentPage::STATUS_ARCHIVED => 'bg-ghosn-ink/8 text-ghosn-ink/60 ring-ghosn/8',
    ];
@endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-[#8a9280]">{{ __('admin.cms.pages_intro') }}</p>
    <a href="{{ route('admin.content-pages.create') }}" class="gh-admin-btn-primary no-underline">+ {{ __('admin.cms.new_page') }}</a>
</div>

<form method="GET" class="gh-admin-filters md:grid-cols-2 lg:grid-cols-6">
    <label class="lg:col-span-2">
        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-ghosn/45">{{ __('admin.cms.search') }}</span>
        <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="{{ __('admin.cms.search_pages_placeholder') }}" class="ghosn-input">
    </label>
    <label>
        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-ghosn/45">{{ __('admin.cms.status') }}</span>
        <select name="status" class="ghosn-input">
            <option value="">{{ __('admin.cms.all_statuses') }}</option>
            @foreach (\App\Models\ContentPage::statuses() as $status)
                <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ __('admin.cms.status_'.$status) }}</option>
            @endforeach
        </select>
    </label>
    <label>
        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-ghosn/45">{{ __('admin.cms.language') }}</span>
        <select name="language" class="ghosn-input">
            <option value="">{{ __('admin.cms.all_languages') }}</option>
            <option value="en" @selected($filters['language'] === 'en')>EN</option>
            <option value="ar" @selected($filters['language'] === 'ar')>AR</option>
        </select>
    </label>
    <label>
        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-ghosn/45">{{ __('admin.cms.date_from') }}</span>
        <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="ghosn-input">
    </label>
    <label>
        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-ghosn/45">{{ __('admin.cms.date_to') }}</span>
        <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="ghosn-input">
    </label>
    <label class="md:col-span-2 lg:col-span-3">
        <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-ghosn/45">{{ __('admin.cms.sort') }}</span>
        <select name="sort" class="ghosn-input">
            <option value="updated_desc" @selected($filters['sort'] === 'updated_desc')>{{ __('admin.cms.sort_updated_desc') }}</option>
            <option value="created_desc" @selected($filters['sort'] === 'created_desc')>{{ __('admin.cms.sort_created_desc') }}</option>
            <option value="title_asc" @selected($filters['sort'] === 'title_asc')>{{ __('admin.cms.sort_title_asc') }}</option>
        </select>
    </label>
    <div class="flex items-end gap-2 md:col-span-2 lg:col-span-3">
        <button type="submit" class="gh-admin-filter-btn">{{ __('admin.cms.filter') }}</button>
        @if (array_filter($filters))
            <a href="{{ route('admin.content-pages.index') }}" class="gh-admin-btn-secondary !border-none !bg-transparent !px-2 text-[#8a9280]">{{ __('admin.cms.clear_filters') }}</a>
        @endif
    </div>
</form>

<form method="POST" action="{{ route('admin.content-pages.bulk') }}" data-bulk-form>
    @csrf
    <input type="hidden" name="action" value="" data-bulk-action>

    <div data-bulk-bar class="gh-admin-bulk-bar hidden">
        <p class="text-sm font-medium text-ghosn">
            <span data-bulk-count>0</span> {{ __('admin.cms.selected') }}
        </p>
        <div class="flex flex-wrap gap-2">
            <button type="button" data-bulk-submit="publish" class="gh-admin-btn-primary !px-3 !py-1.5 !text-xs">{{ __('admin.cms.bulk_publish') }}</button>
            <button type="button" data-bulk-submit="unpublish" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs">{{ __('admin.cms.bulk_unpublish') }}</button>
            <button type="button" data-bulk-submit="duplicate" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs">{{ __('admin.cms.bulk_duplicate') }}</button>
            <button type="button" data-bulk-delete-trigger class="gh-admin-btn-danger !px-3 !py-1.5 !text-xs !text-white" style="background:#a24a37;border-color:#a24a37;color:#fff;">{{ __('admin.cms.bulk_delete') }}</button>
        </div>
    </div>

    <x-admin.table-card>
            <table class="min-w-full text-sm">
                <thead class="bg-cream/50 text-xs uppercase tracking-wide text-ghosn/55">
                    <tr>
                        <th class="w-10 px-4 py-4">
                            <input type="checkbox" data-bulk-master class="rounded border-ghosn/20 text-ghosn focus:ring-ghosn/30" aria-label="{{ __('admin.cms.select_all') }}">
                        </th>
                        <th class="px-4 py-4 text-start">{{ __('admin.cms.page') }}</th>
                        <th class="hidden px-4 py-4 text-start md:table-cell">{{ __('admin.cms.slug') }}</th>
                        <th class="hidden px-4 py-4 text-start lg:table-cell">{{ __('admin.cms.languages') }}</th>
                        <th class="hidden px-4 py-4 text-start xl:table-cell">{{ __('admin.cms.author') }}</th>
                        <th class="hidden px-4 py-4 text-start lg:table-cell">{{ __('admin.cms.last_updated') }}</th>
                        <th class="px-4 py-4 text-start">{{ __('admin.cms.status') }}</th>
                        <th class="px-4 py-4 text-end">{{ __('admin.cms.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ghosn/8">
                    @forelse ($pages as $page)
                        @php
                            $langs = $page->languageAvailability();
                            $thumb = $page->featuredImage?->url();
                        @endphp
                        <tr class="group transition hover:bg-cream/30">
                            <td class="px-4 py-4 align-top">
                                <input type="checkbox" name="ids[]" value="{{ $page->id }}" data-bulk-item class="rounded border-ghosn/20 text-ghosn focus:ring-ghosn/30">
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div class="flex gap-3">
                                    <div class="h-14 w-20 shrink-0 overflow-hidden rounded-xl border border-ghosn/10 bg-cream/50">
                                        @if ($thumb)
                                            <img src="{{ $thumb }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center text-ghosn/25">
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"></rect><circle cx="8.5" cy="10.5" r="1.5"></circle><path d="m21 15-5-5L8 18"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-ghosn">{{ $page->title_en }}</p>
                                        @if ($page->title_ar)
                                            <p class="truncate text-xs text-ghosn-ink/50" dir="rtl">{{ $page->title_ar }}</p>
                                        @endif
                                        <p class="mt-1 font-mono text-xs text-ghosn-ink/45 md:hidden" dir="ltr">/{{ $page->slug }}</p>
                                        @if ($page->isProtected())
                                            <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800 ring-1 ring-amber-200">{{ __('admin.cms.protected') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="hidden px-4 py-4 align-top md:table-cell">
                                <code class="rounded-lg bg-cream/70 px-2 py-1 text-xs text-ghosn-ink/70" dir="ltr">{{ $page->slug }}</code>
                            </td>
                            <td class="hidden px-4 py-4 align-top lg:table-cell">
                                <div class="flex gap-1">
                                    <span @class(['inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase', 'bg-ghosn text-offwhite' => $langs['en'], 'bg-ghosn/8 text-ghosn/35' => ! $langs['en']])>EN</span>
                                    <span @class(['inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase', 'bg-ghosn text-offwhite' => $langs['ar'], 'bg-ghosn/8 text-ghosn/35' => ! $langs['ar']])>AR</span>
                                </div>
                            </td>
                            <td class="hidden px-4 py-4 align-top text-ghosn-ink/65 xl:table-cell">
                                {{ $page->author?->name ?? __('admin.cms.author_unknown') }}
                            </td>
                            <td class="hidden px-4 py-4 align-top text-ghosn-ink/55 lg:table-cell">
                                <time datetime="{{ $page->updated_at->toIso8601String() }}">{{ $page->updated_at->format('M j, Y') }}</time>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusClasses[$page->status] ?? $statusClasses[\App\Models\ContentPage::STATUS_DRAFT] }}">
                                    {{ __('admin.cms.status_'.$page->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($page->isPublished())
                                        <a href="{{ route('pages.show', $page->slug) }}" target="_blank" rel="noopener noreferrer" class="action-icon" data-tooltip="{{ __('admin.cms.view_public') }}">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path></svg>
                                        </a>
                                    @endif
                                    @if (! $page->isPublished())
                                        <a href="{{ route('admin.content-pages.preview', $page) }}" target="_blank" rel="noopener noreferrer" class="action-icon" data-tooltip="{{ __('admin.cms.preview') }}">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.content-pages.edit', $page) }}" class="action-icon" data-tooltip="{{ __('admin.cms.edit') }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.content-pages.duplicate', $page) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="action-icon" data-tooltip="{{ __('admin.cms.duplicate') }}">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        </button>
                                    </form>
                                    @if ($page->canDelete())
                                        <button
                                            type="button"
                                            class="action-icon action-icon-danger"
                                            data-delete-trigger
                                            data-delete-action="{{ route('admin.content-pages.destroy', $page) }}"
                                            data-delete-message="{{ __('admin.cms.confirm_delete_page', ['title' => $page->title_en]) }}"
                                            data-tooltip="{{ __('admin.cms.delete') }}"
                                        >
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path></svg>
                                        </button>
                                    @else
                                        <span class="action-icon cursor-not-allowed opacity-35" data-tooltip="{{ __('admin.cms.page_delete_protected') }}">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path></svg>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <p class="text-ghosn-ink/55">{{ __('admin.cms.empty_pages') }}</p>
                                <a href="{{ route('admin.content-pages.create') }}" class="mt-3 inline-block text-sm font-semibold text-ghosn hover:underline">{{ __('admin.cms.new_page') }}</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </x-admin.table-card>
</form>

<div class="mt-6">{{ $pages->links() }}</div>
@endsection
