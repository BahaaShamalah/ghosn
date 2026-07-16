<label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.featured_image') }}</label>
<select name="{{ $name }}" class="ghosn-input">
    <option value="">{{ __('admin.cms.no_image') }}</option>
    @foreach ($mediaLibrary as $media)
        @if ($media->isImage())
            <option value="{{ $media->id }}" @selected((string) $value === (string) $media->id)>{{ $media->original_filename }}</option>
        @endif
    @endforeach
</select>
