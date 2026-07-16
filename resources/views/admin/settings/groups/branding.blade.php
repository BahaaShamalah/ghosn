<form method="POST" action="{{ route('admin.settings.update.group', 'branding') }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="branding">

    @include('admin.settings.partials.form-errors', ['group' => 'branding'])

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.logo') }}</label>
            <img src="{{ \App\Support\SiteAsset::logoUrl() }}" alt="" class="mb-3 h-14 w-auto rounded-lg border border-ghosn/10 bg-cream/40 p-2">
            <select name="site[logo_media_id]" class="ghosn-input mb-3">
                <option value="">{{ __('admin.settings.media_none') }}</option>
                @foreach ($mediaLibrary->where(fn ($m) => $m->isImage()) as $item)
                    <option value="{{ $item->id }}" @selected(old('site.logo_media_id', $settings['site.logo_media_id']) == $item->id)>{{ $item->original_filename }}</option>
                @endforeach
            </select>
            <p class="mb-2 text-xs text-ghosn-ink/55">{{ __('admin.settings.or_upload') }}</p>
            <input type="file" name="site[logo]" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="ghosn-input file:me-3 file:rounded-lg file:border-0 file:bg-ghosn file:px-3 file:py-2 file:text-sm file:font-medium file:text-offwhite">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.favicon') }}</label>
            @if ($favicon = \App\Support\SiteAsset::faviconUrl())
                <img src="{{ $favicon }}" alt="" class="mb-3 h-10 w-10 rounded-lg border border-ghosn/10 bg-cream/40 p-1">
            @endif
            <select name="site[favicon_media_id]" class="ghosn-input mb-3">
                <option value="">{{ __('admin.settings.media_none') }}</option>
                @foreach ($mediaLibrary->where(fn ($m) => $m->isImage()) as $item)
                    <option value="{{ $item->id }}" @selected(old('site.favicon_media_id', $settings['site.favicon_media_id']) == $item->id)>{{ $item->original_filename }}</option>
                @endforeach
            </select>
            <p class="mb-2 text-xs text-ghosn-ink/55">{{ __('admin.settings.or_upload') }}</p>
            <input type="file" name="site[favicon]" accept="image/jpeg,image/png,image/webp,image/x-icon" class="ghosn-input file:me-3 file:rounded-lg file:border-0 file:bg-ghosn file:px-3 file:py-2 file:text-sm file:font-medium file:text-offwhite">
        </div>
    </div>

    <p class="text-xs text-ghosn-ink/55">
        <a href="{{ route('admin.media.index') }}" class="font-semibold text-ghosn underline">{{ __('admin.settings.open_media') }}</a>
    </p>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
