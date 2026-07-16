<form method="POST" action="{{ route('admin.settings.update.group', 'donations') }}" class="space-y-5">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="donations">

    @include('admin.settings.partials.form-errors', ['group' => 'donations'])

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="flex items-center gap-3 sm:col-span-2">
            <input type="hidden" name="donations[enabled]" value="0">
            <input type="checkbox" id="donations_enabled" name="donations[enabled]" value="1" @checked(old('donations.enabled', $settings['donations.enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
            <label for="donations_enabled" class="text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.donations_enabled') }}</label>
        </div>
        <div class="flex items-center gap-3 sm:col-span-2">
            <input type="hidden" name="donations[bank_transfer_enabled]" value="0">
            <input type="checkbox" id="donations_bank_enabled" name="donations[bank_transfer_enabled]" value="1" @checked(old('donations.bank_transfer_enabled', $settings['donations.bank_transfer_enabled'])) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
            <label for="donations_bank_enabled" class="text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.donations_bank_enabled') }}</label>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.donations_bank_name_en') }}</label>
            <input type="text" name="donations[bank_name_en]" value="{{ old('donations.bank_name_en', $settings['donations.bank_name_en']) }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.donations_bank_name_ar') }}</label>
            <input type="text" name="donations[bank_name_ar]" value="{{ old('donations.bank_name_ar', $settings['donations.bank_name_ar']) }}" class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.donations_account_holder_en') }}</label>
            <input type="text" name="donations[account_holder_en]" value="{{ old('donations.account_holder_en', $settings['donations.account_holder_en']) }}" class="ghosn-input">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.donations_account_holder_ar') }}</label>
            <input type="text" name="donations[account_holder_ar]" value="{{ old('donations.account_holder_ar', $settings['donations.account_holder_ar']) }}" class="ghosn-input" dir="rtl">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.donations_iban') }}</label>
            <input type="text" name="donations[iban]" value="{{ old('donations.iban', $settings['donations.iban']) }}" class="ghosn-input" dir="ltr">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.donations_account_number') }}</label>
            <input type="text" name="donations[account_number]" value="{{ old('donations.account_number', $settings['donations.account_number']) }}" class="ghosn-input" dir="ltr">
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.donations_swift') }}</label>
            <input type="text" name="donations[swift]" value="{{ old('donations.swift', $settings['donations.swift']) }}" class="ghosn-input" dir="ltr">
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.donations_instructions_en') }}</label>
            <textarea name="donations[instructions_en]" rows="2" class="ghosn-input">{{ old('donations.instructions_en', $settings['donations.instructions_en']) }}</textarea>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.donations_instructions_ar') }}</label>
            <textarea name="donations[instructions_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('donations.instructions_ar', $settings['donations.instructions_ar']) }}</textarea>
        </div>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
