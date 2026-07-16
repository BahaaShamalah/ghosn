<form method="POST" action="{{ route('admin.settings.update.group', 'general') }}" class="space-y-5">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="general">

    @include('admin.settings.partials.form-errors', ['group' => 'general'])

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.site_name_en') }}</label>
            <input type="text" name="site[name_en]" value="{{ old('site.name_en', $settings['site.name_en']) }}" required class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.site_name_ar') }}</label>
            <input type="text" name="site[name_ar]" value="{{ old('site.name_ar', $settings['site.name_ar']) }}" required class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.slogan_en') }}</label>
            <input type="text" name="site[slogan_en]" value="{{ old('site.slogan_en', $settings['site.slogan_en']) }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.slogan_ar') }}</label>
            <input type="text" name="site[slogan_ar]" value="{{ old('site.slogan_ar', $settings['site.slogan_ar']) }}" class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.default_language') }}</label>
            <select name="site[default_language]" class="ghosn-input">
                <option value="en" @selected(old('site.default_language', $settings['site.default_language']) === 'en')>English</option>
                <option value="ar" @selected(old('site.default_language', $settings['site.default_language']) === 'ar')>العربية</option>
            </select>
        </div>
        <div class="flex items-center gap-3 pt-7">
            <input type="hidden" name="site[enable_animations]" value="0">
            <input type="checkbox" id="enable_animations" name="site[enable_animations]" value="1" @checked(old('site.enable_animations', $settings['site.enable_animations'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
            <label for="enable_animations" class="text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.enable_animations') }}</label>
        </div>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
