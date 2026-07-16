<form method="POST" action="{{ route('admin.settings.update.group', 'volunteers') }}" class="space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="volunteers">

    @include('admin.settings.partials.form-errors', ['group' => 'volunteers'])

    <div class="rounded-2xl border border-amber-200/80 bg-amber-50/60 px-4 py-3 text-sm text-amber-950">
        {{ __('admin.settings.volunteers_email_notice') }}
    </div>

    <div class="rounded-2xl border border-ghosn/10 bg-[#fffdf8] px-4 py-3 text-sm text-ghosn-ink/75">
        <p class="font-semibold text-ghosn">{{ __('admin.settings.volunteers_placeholders_title') }}</p>
        <p class="mt-1">{name}, {email}, {phone}, {area}, {area_label}, {message}, {site_name}</p>
    </div>

    @php
        $emailTypes = [
            'confirmation' => __('admin.settings.volunteers_confirmation'),
            'admin_alert' => __('admin.settings.volunteers_admin_alert'),
            'welcome' => __('admin.settings.volunteers_welcome'),
            'rejected' => __('admin.settings.volunteers_rejected'),
        ];
    @endphp

    @foreach ($emailTypes as $type => $label)
        <section class="gh-admin-section-card space-y-5 p-6 md:p-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="text-lg font-bold text-ghosn">{{ $label }}</h3>
                <label class="flex items-center gap-2 text-sm font-medium text-ghosn-ink/80">
                    <input type="hidden" name="volunteers[{{ $type }}_enabled]" value="0">
                    <input type="checkbox" name="volunteers[{{ $type }}_enabled]" value="1" @checked(old("volunteers.{$type}_enabled", $settings["volunteers.{$type}_enabled"] ?? true)) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                    {{ __('admin.settings.volunteers_email_enabled') }}
                </label>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.volunteers_subject') }} (EN)</label>
                    <input type="text" name="volunteers[{{ $type }}_subject_en]" value="{{ old("volunteers.{$type}_subject_en", $settings["volunteers.{$type}_subject_en"] ?? '') }}" class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.volunteers_subject') }} (AR)</label>
                    <input type="text" name="volunteers[{{ $type }}_subject_ar]" value="{{ old("volunteers.{$type}_subject_ar", $settings["volunteers.{$type}_subject_ar"] ?? '') }}" class="ghosn-input" dir="rtl">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.volunteers_heading') }} (EN)</label>
                    <input type="text" name="volunteers[{{ $type }}_heading_en]" value="{{ old("volunteers.{$type}_heading_en", $settings["volunteers.{$type}_heading_en"] ?? '') }}" class="ghosn-input">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.volunteers_heading') }} (AR)</label>
                    <input type="text" name="volunteers[{{ $type }}_heading_ar]" value="{{ old("volunteers.{$type}_heading_ar", $settings["volunteers.{$type}_heading_ar"] ?? '') }}" class="ghosn-input" dir="rtl">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.volunteers_body') }} (EN)</label>
                    <textarea name="volunteers[{{ $type }}_body_en]" rows="6" class="ghosn-input text-sm leading-relaxed">{{ old("volunteers.{$type}_body_en", $settings["volunteers.{$type}_body_en"] ?? '') }}</textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.volunteers_body') }} (AR)</label>
                    <textarea name="volunteers[{{ $type }}_body_ar]" rows="6" class="ghosn-input text-sm leading-relaxed" dir="rtl">{{ old("volunteers.{$type}_body_ar", $settings["volunteers.{$type}_body_ar"] ?? '') }}</textarea>
                </div>
            </div>
        </section>
    @endforeach

    <div class="flex justify-end">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
