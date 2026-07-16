@extends('admin.layouts.app')

@section('title', __('admin.pages.edit_section'))
@section('page-title', __('admin.pages.edit_section'))
@section('eyebrow', $page->slug.' / '.$section->key)

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ghosn/45">{{ $section->type }}</p>
            <h2 class="mt-1 text-2xl font-bold text-ghosn">{{ $section->title_en }}</h2>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.pages.show', $page) }}" class="gh-admin-btn-secondary">{{ __('admin.pages.back') }}</a>
            <a href="{{ route('home') }}#{{ $section->key === 'hero' ? 'home' : $section->key }}" target="_blank" rel="noopener" class="gh-admin-btn-primary">{{ __('admin.pages.preview') }}</a>
        </div>
    </div>

    <div class="mb-6 rounded-2xl border border-beige-deep/40 bg-cream/50 px-4 py-3 text-sm text-ghosn-ink/70">
        {{ __('admin.pages.edit_notice') }}
    </div>

    <form method="POST" action="{{ route('admin.pages.sections.update', [$page, $section]) }}" class="mb-8 gh-admin-section-card p-6 md:p-8">
        @csrf
        @method('PUT')

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

        <div class="mt-6 flex justify-end">
            <button type="submit" class="gh-admin-btn-primary hover:bg-ghosn-700">{{ __('admin.pages.save_section') }}</button>
        </div>
    </form>

    <div class="space-y-5">
        <h3 class="text-lg font-bold text-ghosn">{{ __('admin.pages.blocks') }}</h3>

        @foreach ($section->blocks as $block)
            <article class="gh-admin-section-card p-5 md:p-6">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-ghosn/45">
                            @if ($block->type === 'image')
                                {{ __('admin.pages.block_type_image') }}
                            @else
                                {{ $block->type }}
                            @endif
                            · #{{ $block->sort_order }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.pages.blocks.reorder', [$page, $section, $block]) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="direction" value="up">
                            <button type="submit" class="rounded-full border border-ghosn/15 px-3 py-1.5 text-xs font-semibold text-ghosn hover:bg-ghosn/5">↑</button>
                        </form>
                        <form method="POST" action="{{ route('admin.pages.blocks.reorder', [$page, $section, $block]) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="direction" value="down">
                            <button type="submit" class="rounded-full border border-ghosn/15 px-3 py-1.5 text-xs font-semibold text-ghosn hover:bg-ghosn/5">↓</button>
                        </form>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.pages.blocks.update', [$page, $section, $block]) }}" @if ($block->type === 'image') enctype="multipart/form-data" @endif class="space-y-4">
                    @csrf
                    @method('PUT')

                    @if (in_array($block->type, ['image', 'video'], true) || ! empty($block->content['media_id']))
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.media_field') }}</label>
                            <select name="content[media_id]" class="ghosn-input">
                                <option value="">{{ __('admin.settings.media_none') }}</option>
                                @foreach ($mediaLibrary as $item)
                                    @if ($block->type === 'video' ? str_starts_with($item->mime_type, 'video/') : ($item->isImage() || str_starts_with($item->mime_type, 'video/')))
                                        <option value="{{ $item->id }}" @selected(old('content.media_id', $block->content['media_id'] ?? '') == $item->id)>{{ $item->original_filename }} ({{ $item->mime_type }})</option>
                                    @endif
                                @endforeach
                            </select>
                            @if ($block->type === 'image')
                                <p class="mb-2 mt-3 text-xs text-ghosn-ink/55">{{ __('admin.settings.or_upload') }}</p>
                                <input type="file" name="image_upload" accept="image/jpeg,image/png,image/webp" class="ghosn-input file:me-3 file:rounded-lg file:border-0 file:bg-ghosn file:px-3 file:py-2 file:text-sm file:font-medium file:text-offwhite">
                                @php $previewUrl = ! empty($block->content['media_id']) ? optional($mediaLibrary->firstWhere('id', $block->content['media_id']))?->url() : null; @endphp
                                @if ($previewUrl)
                                    <img src="{{ $previewUrl }}" alt="" class="mt-3 max-h-48 w-full rounded-xl border border-ghosn/10 object-cover">
                                @endif
                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.image_alt_en') }}</label>
                                        <input type="text" name="content[alt_en]" value="{{ old('content.alt_en', $block->content['alt_en'] ?? '') }}" class="ghosn-input" placeholder="{{ __('admin.pages.image_alt_en_placeholder') }}">
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.image_alt_ar') }}</label>
                                        <input type="text" name="content[alt_ar]" value="{{ old('content.alt_ar', $block->content['alt_ar'] ?? '') }}" class="ghosn-input" dir="rtl" placeholder="{{ __('admin.pages.image_alt_ar_placeholder') }}">
                                    </div>
                                </div>
                            @endif
                            <p class="mt-2 text-xs text-ghosn-ink/55">{{ __('admin.pages.media_help') }} <a href="{{ route('admin.media.index') }}" class="text-ghosn underline">{{ __('admin.settings.open_media') }}</a></p>
                        </div>
                    @endif

                    @if (in_array($block->type, ['heading', 'text'], true))
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.content_en') }}</label>
                            <textarea name="content[en]" rows="3" class="ghosn-input">{{ old('content.en', $block->content['en'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.content_ar') }}</label>
                            <textarea name="content[ar]" rows="3" class="ghosn-input" dir="rtl">{{ old('content.ar', $block->content['ar'] ?? '') }}</textarea>
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="block_active_{{ $block->id }}" name="is_active" value="1" @checked(old('is_active', $block->is_active)) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                            <label for="block_active_{{ $block->id }}" class="text-sm text-ghosn-ink/80">{{ __('admin.pages.block_active') }}</label>
                        </div>
                        <button type="submit" class="gh-admin-btn-primary">{{ __('admin.pages.save_block') }}</button>
                    </div>
                </form>
            </article>
        @endforeach
    </div>
@endsection

