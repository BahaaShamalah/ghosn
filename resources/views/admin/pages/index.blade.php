@extends('admin.layouts.app')

@section('title', __('admin.pages.title'))
@section('page-title', __('admin.pages.title'))
@section('eyebrow', __('admin.panel'))

@section('content')
    <p class="mb-6 max-w-2xl text-sm text-[#8a9280]">{{ __('admin.pages.index_help') }}</p>

    <div class="grid gap-5 lg:grid-cols-2">
        @forelse ($pages as $page)
            <article class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8a9280]">{{ $page->slug }}</p>
                        <h2 class="mt-2 text-xl font-bold text-[#2f4327]">{{ $page->title_en }}</h2>
                        <p class="mt-1 text-sm text-[#8a9280]" dir="rtl">{{ $page->title_ar }}</p>
                    </div>
                    @if ($page->is_active)
                        @include('admin.partials.status-pill', ['status' => 'active', 'label' => __('admin.pages.active')])
                    @endif
                </div>

                <p class="mt-4 text-sm text-[#8a9280]">{{ __('admin.pages.sections_count', ['count' => $page->sections_count]) }}</p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('admin.pages.show', $page) }}" class="gh-admin-btn-primary no-underline">{{ __('admin.pages.manage') }}</a>
                    <a href="{{ \App\Support\BuilderPageRoutes::publicUrl($page) }}" target="_blank" rel="noopener" class="gh-admin-btn-secondary no-underline">{{ __('admin.pages.preview') }}</a>
                </div>
            </article>
        @empty
            <div class="gh-admin-empty lg:col-span-2">{{ __('admin.pages.empty') }}</div>
        @endforelse
    </div>
@endsection
