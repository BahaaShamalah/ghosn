<form method="POST" action="{{ route('admin.settings.update.group', 'newsletter') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="newsletter">

    @include('admin.settings.partials.form-errors', ['group' => 'newsletter'])

    <label class="flex items-center gap-3">
        <input type="hidden" name="newsletter[enabled]" value="0">
        <input type="checkbox" id="newsletter_enabled" name="newsletter[enabled]" value="1" @checked(old('newsletter.enabled', $settings['newsletter.enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
        <span class="text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.newsletter_enabled') }}</span>
    </label>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.newsletter_title_en') }}</label>
            <input type="text" name="newsletter[title_en]" value="{{ old('newsletter.title_en', $settings['newsletter.title_en']) }}" required class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.newsletter_title_ar') }}</label>
            <input type="text" name="newsletter[title_ar]" value="{{ old('newsletter.title_ar', $settings['newsletter.title_ar']) }}" required class="ghosn-input" dir="rtl">
        </div>
        <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.newsletter_subtitle_en') }}</label>
            <textarea name="newsletter[subtitle_en]" rows="2" required class="ghosn-input">{{ old('newsletter.subtitle_en', $settings['newsletter.subtitle_en']) }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.newsletter_subtitle_ar') }}</label>
            <textarea name="newsletter[subtitle_ar]" rows="2" required class="ghosn-input" dir="rtl">{{ old('newsletter.subtitle_ar', $settings['newsletter.subtitle_ar']) }}</textarea>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.newsletter_placeholder_en') }}</label>
            <input type="text" name="newsletter[placeholder_en]" value="{{ old('newsletter.placeholder_en', $settings['newsletter.placeholder_en']) }}" required class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.newsletter_placeholder_ar') }}</label>
            <input type="text" name="newsletter[placeholder_ar]" value="{{ old('newsletter.placeholder_ar', $settings['newsletter.placeholder_ar']) }}" required class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.newsletter_button_en') }}</label>
            <input type="text" name="newsletter[button_en]" value="{{ old('newsletter.button_en', $settings['newsletter.button_en']) }}" required class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.newsletter_button_ar') }}</label>
            <input type="text" name="newsletter[button_ar]" value="{{ old('newsletter.button_ar', $settings['newsletter.button_ar']) }}" required class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.newsletter_success_en') }}</label>
            <input type="text" name="newsletter[success_en]" value="{{ old('newsletter.success_en', $settings['newsletter.success_en']) }}" required class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.newsletter_success_ar') }}</label>
            <input type="text" name="newsletter[success_ar]" value="{{ old('newsletter.success_ar', $settings['newsletter.success_ar']) }}" required class="ghosn-input" dir="rtl">
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[rgba(64,97,57,0.1)] pt-4">
        <a href="{{ route('admin.newsletter.index') }}" class="text-sm font-semibold text-[#406139] no-underline hover:text-[#33502e]">{{ __('admin.newsletter.view_subscribers') }}</a>
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
