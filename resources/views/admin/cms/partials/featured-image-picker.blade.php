@php
    $fieldName = $name ?? 'featured_image_media_id';
    $selectedId = $value ?? null;
    if ($selectedId === '' || $selectedId === false) {
        $selectedId = null;
    }
    $selectedMedia = $selectedId ? ($mediaLibrary->firstWhere('id', (int) $selectedId) ?? \App\Models\Media::query()->find($selectedId)) : null;
    $previewUrl = $selectedMedia?->thumbnailUrl() ?? $selectedMedia?->url();
    $label = $label ?? __('admin.cms.featured_image');
    $compact = (bool) ($compact ?? false);
    $mediaType = $mediaType ?? 'image';
    $isVideo = $mediaType === 'video';
    $accept = $isVideo ? 'video/mp4,video/webm,video/quicktime' : 'image/*';
    $hideLabel = (bool) ($hideLabel ?? false);
@endphp

<div
    class="featured-image-picker {{ $compact ? 'featured-image-picker--compact' : '' }}"
    data-media-picker
    data-media-type="{{ $mediaType }}"
    data-field-name="{{ $fieldName }}"
>
    @unless ($hideLabel)
        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ $label }}</label>
    @endunless

    <input type="hidden" name="{{ $fieldName }}" value="{{ $selectedId }}" data-media-input>

    <div class="flex flex-wrap items-start gap-3">
        <div data-media-preview class="@if(! $previewUrl) hidden @endif overflow-hidden rounded-xl border border-ghosn/10 bg-cream/40 {{ $compact ? 'h-20 w-28 shrink-0' : 'mb-0 w-full max-w-xs' }}">
            @if ($isVideo)
                <video src="{{ $selectedMedia?->url() }}" class="{{ $compact ? 'h-full w-full object-cover' : 'max-h-44 w-full object-cover' }}" controls preload="metadata" data-media-preview-video></video>
            @else
                <img src="{{ $previewUrl }}" alt="" class="{{ $compact ? 'h-full w-full object-cover' : 'max-h-36 w-full object-cover' }}" data-media-preview-img>
            @endif
        </div>

        <div class="min-w-[180px] flex-1">
            <div
                data-media-dropzone
                class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-ghosn/15 bg-cream/25 text-center transition hover:border-ghosn/30 hover:bg-cream/40 {{ $compact ? 'min-h-[80px] px-3 py-3' : 'mb-3 min-h-[100px] px-4 py-6' }}"
            >
                <p class="text-sm font-semibold text-ghosn">
                    {{ $isVideo ? __('admin.campaigns.drop_video') : __('admin.cms.drop_image') }}
                </p>
                <p class="mt-0.5 text-xs text-ghosn-ink/55">
                    {{ $isVideo ? __('admin.campaigns.drop_video_hint') : __('admin.cms.drop_image_hint') }}
                </p>
                <input type="file" accept="{{ $accept }}" class="hidden" data-media-file-input>
            </div>

            <div class="mt-2 flex flex-wrap gap-2">
                <button type="button" data-media-library-open class="rounded-full border border-ghosn/15 px-3 py-1.5 text-xs font-semibold text-ghosn hover:bg-ghosn/5">
                    {{ __('admin.cms.choose_from_library') }}
                </button>
                <button type="button" data-media-clear class="@if(! $previewUrl) hidden @endif rounded-full border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">
                    {{ $isVideo ? __('admin.campaigns.remove_video') : __('admin.cms.remove_image') }}
                </button>
            </div>
        </div>
    </div>
</div>
