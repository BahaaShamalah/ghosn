<form method="POST" action="{{ route('admin.settings.update.group', 'maintenance') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="maintenance">

    @include('admin.settings.partials.form-errors', ['group' => 'maintenance'])

    <div class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-[#2f4327]">{{ __('admin.settings.maintenance_heading') }}</h3>
                <p class="mt-1 max-w-xl text-sm text-[#5f6857]">{{ __('admin.settings.maintenance_intro') }}</p>
            </div>
            <label class="inline-flex items-center gap-3 rounded-full border border-[rgba(64,97,57,0.18)] bg-white px-4 py-2.5">
                <input type="hidden" name="maintenance[enabled]" value="0">
                <input
                    type="checkbox"
                    id="maintenance_enabled"
                    name="maintenance[enabled]"
                    value="1"
                    @checked(old('maintenance.enabled', $settings['maintenance.enabled']))
                    class="rounded border-[#406139]/25 text-[#406139] focus:ring-[#406139]"
                >
                <span class="text-sm font-semibold text-[#2f4327]">{{ __('admin.settings.maintenance_enabled') }}</span>
            </label>
        </div>

        @if ($settings['maintenance.enabled'])
            <div class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#a24a37]/10 px-3 py-2 text-sm font-semibold text-[#8a3a2b]">
                <span class="h-2 w-2 animate-pulse rounded-full bg-[#a24a37]"></span>
                {{ __('admin.settings.maintenance_active_notice') }}
            </div>
        @endif
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.maintenance_eyebrow_en') }}</label>
            <input type="text" name="maintenance[eyebrow_en]" value="{{ old('maintenance.eyebrow_en', $settings['maintenance.eyebrow_en']) }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.maintenance_eyebrow_ar') }}</label>
            <input type="text" name="maintenance[eyebrow_ar]" value="{{ old('maintenance.eyebrow_ar', $settings['maintenance.eyebrow_ar']) }}" class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.maintenance_title_en') }}</label>
            <input type="text" name="maintenance[title_en]" value="{{ old('maintenance.title_en', $settings['maintenance.title_en']) }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.maintenance_title_ar') }}</label>
            <input type="text" name="maintenance[title_ar]" value="{{ old('maintenance.title_ar', $settings['maintenance.title_ar']) }}" class="ghosn-input" dir="rtl">
        </div>
        <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.maintenance_message_en') }}</label>
            <textarea name="maintenance[message_en]" rows="3" class="ghosn-input">{{ old('maintenance.message_en', $settings['maintenance.message_en']) }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.maintenance_message_ar') }}</label>
            <textarea name="maintenance[message_ar]" rows="3" class="ghosn-input" dir="rtl">{{ old('maintenance.message_ar', $settings['maintenance.message_ar']) }}</textarea>
        </div>
    </div>

    <p class="text-xs text-[#8a9280]">{{ __('admin.settings.maintenance_admin_bypass_help') }}</p>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
