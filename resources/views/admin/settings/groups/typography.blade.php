<form method="POST" action="{{ route('admin.settings.update.group', 'typography') }}" class="space-y-5">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="typography">

    @include('admin.settings.partials.form-errors', ['group' => 'typography'])

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.font_en') }}</label>
            <select name="theme[font_en]" class="ghosn-input" required>
                @foreach ($fontOptions as $font)
                    <option value="{{ $font }}" @selected(old('theme.font_en', $settings['theme.font_en']) === $font)>{{ $font }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.font_ar') }}</label>
            <select name="theme[font_ar]" class="ghosn-input" required>
                @foreach ($fontOptions as $font)
                    <option value="{{ $font }}" @selected(old('theme.font_ar', $settings['theme.font_ar']) === $font)>{{ $font }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
