<form method="POST" action="{{ route('admin.settings.update.group', 'email') }}" class="space-y-6">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="email">

    @include('admin.settings.partials.form-errors', ['group' => 'email'])

    <div class="rounded-2xl border border-amber-200/80 bg-amber-50/60 px-4 py-3 text-sm text-amber-950">
        {{ __('admin.settings.email_env_notice') }}
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.email_from_name') }}</label>
            <input type="text" name="email[from_name]" value="{{ old('email.from_name', $settings['email.from_name']) }}" class="ghosn-input" required>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.email_from_email') }}</label>
            <input type="email" name="email[from_email]" value="{{ old('email.from_email', $settings['email.from_email']) }}" class="ghosn-input" placeholder="{{ config('mail.from.address') }}">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.email_admin_notification') }}</label>
            <input type="email" name="email[admin_notification_email]" value="{{ old('email.admin_notification_email', $settings['email.admin_notification_email'] ?? $settings['payments.receipt_email'] ?? $settings['contact.email']) }}" class="ghosn-input">
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="flex items-center gap-3">
            <input type="hidden" name="email[donor_receipts_enabled]" value="0">
            <input type="checkbox" id="email_donor_receipts_enabled" name="email[donor_receipts_enabled]" value="1" @checked(old('email.donor_receipts_enabled', $settings['email.donor_receipts_enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
            <label for="email_donor_receipts_enabled" class="text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.email_donor_receipts_enabled') }}</label>
        </div>
        <div class="flex items-center gap-3">
            <input type="hidden" name="email[admin_alerts_enabled]" value="0">
            <input type="checkbox" id="email_admin_alerts_enabled" name="email[admin_alerts_enabled]" value="1" @checked(old('email.admin_alerts_enabled', $settings['email.admin_alerts_enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
            <label for="email_admin_alerts_enabled" class="text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.email_admin_alerts_enabled') }}</label>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.email_footer_en') }}</label>
            <textarea name="email[footer_en]" rows="3" class="ghosn-input">{{ old('email.footer_en', $settings['email.footer_en']) }}</textarea>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.email_footer_ar') }}</label>
            <textarea name="email[footer_ar]" rows="3" class="ghosn-input" dir="rtl">{{ old('email.footer_ar', $settings['email.footer_ar']) }}</textarea>
        </div>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
