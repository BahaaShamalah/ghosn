@extends('admin.layouts.app')

@section('title', $page->title_en)
@section('page-title', __('admin.pages.builder_title'))
@section('eyebrow', $page->slug)

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-[#2f4327]">{{ $page->title_en }}</h2>
            <p class="text-sm text-[#8a9280]" dir="rtl">{{ $page->title_ar }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.pages.index') }}" class="gh-admin-btn-secondary no-underline">{{ __('admin.pages.back') }}</a>
            <a href="{{ \App\Support\BuilderPageRoutes::publicUrl($page) }}" target="_blank" rel="noopener" class="gh-admin-btn-primary no-underline">{{ __('admin.pages.preview') }}</a>
        </div>
    </div>

    <div class="gh-admin-alert gh-admin-alert-success mb-6">
        @if ($page->slug === 'volunteer')
            {{ __('admin.pages.builder_notice_volunteer') }}
        @elseif ($page->slug === 'who-we-are')
            {{ __('admin.pages.builder_notice_who-we-are') }}
        @elseif ($page->slug === 'team')
            {{ __('admin.pages.builder_notice_team') }}
        @elseif ($page->slug === 'contact')
            {{ __('admin.pages.builder_notice_contact') }}
        @else
            {{ __('admin.pages.builder_notice_active') }}
        @endif
    </div>

    @if ($page->slug === 'home')
    <div class="gh-admin-alert mb-6 border border-ghosn/15 bg-[#fffdf8] text-sm text-ghosn-ink/75">
        {{ __('admin.pages.builder_newsletter_notice') }}
    </div>
    @endif

    @if ($page->slug === 'who-we-are')
        <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 md:p-8">
            @include('admin.settings.groups.about')
        </div>
    @elseif ($page->slug === 'team')
        <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 md:p-8">
            @include('admin.settings.groups.team')
        </div>
    @elseif ($page->slug === 'contact')
        <div class="gh-admin-card rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6 md:p-8">
            @include('admin.settings.groups.contact')
        </div>
    @else
        <div class="space-y-4">
            @foreach ($page->sections as $section)
                <article class="gh-admin-section-card">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8a9280]">{{ $section->key }} · {{ $section->type }}</p>
                            <h3 class="mt-1 text-lg font-bold text-[#2f4327]">{{ $section->title_en }}</h3>
                            <p class="text-sm text-[#8a9280]" dir="rtl">{{ $section->title_ar }}</p>
                            <p class="mt-2 text-xs text-[#8a9280]">{{ __('admin.pages.blocks_count', ['count' => $section->blocks_count]) }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ match ($section->key) {
                                'hero' => route('admin.pages.sections.hero.edit', [$page, $section]),
                                'about' => route('admin.pages.sections.about.edit', [$page, $section]),
                                'volunteer' => route('admin.pages.sections.volunteer.edit', [$page, $section]),
                                default => \App\Support\SectionContent::isStructured($section->key)
                                    ? route('admin.pages.sections.content.edit', [$page, $section])
                                    : route('admin.pages.sections.edit', [$page, $section]),
                            } }}" class="gh-admin-btn-primary no-underline">{{ __('admin.pages.edit_section') }}</a>
                            <form method="POST" action="{{ route('admin.pages.sections.reorder', [$page, $section]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" class="gh-admin-btn-secondary">↑ {{ __('admin.pages.move_up') }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.pages.sections.reorder', [$page, $section]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" class="gh-admin-btn-secondary">↓ {{ __('admin.pages.move_down') }}</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection

@if (in_array($page->slug, ['who-we-are', 'team', 'contact'], true))
    @push('scripts')
        @include('admin.partials.cms-scripts')
    @endpush
@endif
