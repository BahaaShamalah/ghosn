@extends('admin.layouts.app')

@php
    $c = $sectionContent;
    $sectionKey = $section->key;
@endphp

@section('title', $schema['label_en'] ?? $section->title_en)
@section('page-title', $schema['label_en'] ?? $section->title_en)
@section('eyebrow', $page->slug.' / '.$sectionKey)

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ghosn/45">{{ $sectionKey }}</p>
            <h2 class="mt-1 text-2xl font-bold text-ghosn">{{ $schema['label_en'] ?? $section->title_en }}</h2>
            @if (! empty($schema['label_ar']))
                <p class="text-sm text-ghosn-ink/60" dir="rtl">{{ $schema['label_ar'] }}</p>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.pages.show', $page) }}" class="gh-admin-btn-secondary">{{ __('admin.pages.back') }}</a>
            <a href="{{ \App\Support\BuilderPageRoutes::sectionPreviewUrl($page, $section) }}" target="_blank" rel="noopener" class="gh-admin-btn-primary">{{ __('admin.pages.preview') }}</a>
        </div>
    </div>

    <div class="mb-6 gh-admin-alert gh-admin-alert-success">
        @if ($page->slug === 'volunteer')
            {{ __('admin.pages.live_page_notice_volunteer') }}
        @elseif ($page->slug === 'who-we-are')
            {{ __('admin.pages.live_page_notice_who-we-are') }}
        @else
            {{ __('admin.pages.live_homepage_notice') }}
        @endif
    </div>

    <form method="POST" action="{{ route('admin.pages.sections.content.update', [$page, $section]) }}" class="space-y-8">
        @csrf
        @method('PUT')

        <section class="gh-admin-section-card p-6 md:p-8">
            <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.section_meta') }}</h3>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.title_en') }}</label>
                    <input type="text" name="title_en" value="{{ old('title_en', $section->title_en) }}" required class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.title_ar') }}</label>
                    <input type="text" name="title_ar" value="{{ old('title_ar', $section->title_ar) }}" required class="ghosn-input" dir="rtl">
                </div>
            </div>
            <label class="mt-5 flex cursor-pointer items-start gap-3 rounded-[12px] border border-[rgba(64,97,57,0.14)] bg-[rgba(237,238,228,0.45)] p-4">
                <input type="hidden" name="is_active" value="1">
                <input type="checkbox" name="is_active" value="0" @checked(! old('is_active', $section->is_active)) class="mt-0.5 rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <span class="text-sm font-medium text-ghosn-ink/80">
                    {{ __('admin.pages.section_hidden') }}
                    <span class="mt-1 block text-xs font-normal text-ghosn-ink/55">{{ __('admin.pages.section_hidden_help') }}</span>
                </span>
            </label>
        </section>

        @foreach ($schema['admin'] ?? [] as $group)
            <section class="gh-admin-section-card p-6 md:p-8">
                <h3 class="text-lg font-bold text-ghosn">{{ $group['title_en'] ?? '' }}</h3>
                @if (! empty($group['title_ar']))
                    <p class="mt-1 text-sm text-ghosn-ink/55" dir="rtl">{{ $group['title_ar'] }}</p>
                @endif

                <div class="mt-6 space-y-5">
                    @foreach ($group['fields'] ?? [] as $field)
                        @if (($field['type'] ?? '') === 'bilingual')
                            @include('admin.pages.sections.structured.fields.bilingual', [
                                'fieldKey' => $field['key'],
                                'input' => $field['input'] ?? 'text',
                                'content' => $c,
                            ])
                        @elseif (($field['type'] ?? '') === 'repeater')
                            @include('admin.pages.sections.structured.fields.repeater', [
                                'repeaterKey' => $field['key'],
                                'labelEn' => $field['label_en'] ?? '',
                                'labelAr' => $field['label_ar'] ?? '',
                                'itemFields' => $field['item_fields'] ?? [],
                                'items' => $c[$field['key']] ?? [],
                            ])
                        @elseif (($field['type'] ?? '') === 'toggle')
                            <div class="flex items-center gap-3">
                                <input type="hidden" name="content[{{ $field['key'] }}]" value="0">
                                <input type="checkbox" id="content_{{ $field['key'] }}" name="content[{{ $field['key'] }}]" value="1" @checked((bool) old("content.{$field['key']}", $c[$field['key']] ?? false)) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                                <label for="content_{{ $field['key'] }}" class="text-sm font-medium text-ghosn-ink/80"><span>{{ $field['label_en'] ?? '' }}</span>@if (! empty($field['label_ar']))<span class="ms-2 text-ghosn-ink/55" dir="rtl">/ {{ $field['label_ar'] }}</span>@endif</label>
                            </div>
                        @elseif (($field['type'] ?? '') === 'number')
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ $field['label_en'] ?? '' }}</label>
                                <input type="number" name="content[{{ $field['key'] }}]" value="{{ old("content.{$field['key']}", $c[$field['key']] ?? 3) }}" min="{{ $field['min'] ?? 1 }}" max="{{ $field['max'] ?? 12 }}" class="ghosn-input max-w-[160px]">
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="gh-admin-btn-primary">{{ __('admin.pages.save_section_content') }}</button>
        </div>
    </form>
@endsection

