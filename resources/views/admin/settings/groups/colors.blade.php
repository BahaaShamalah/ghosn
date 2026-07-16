<form method="POST" action="{{ route('admin.settings.update.group', 'colors') }}" class="space-y-5">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="colors">

    @include('admin.settings.partials.form-errors', ['group' => 'colors'])

    <div class="grid gap-4 sm:grid-cols-2">
        @foreach (['primary' => 'theme.primary_color', 'secondary' => 'theme.secondary_color', 'accent' => 'theme.accent_color', 'background' => 'theme.background_color'] as $label => $key)
            @php [$groupKey, $field] = explode('.', $key); @endphp
            <div>
                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.color_'.$label) }}</label>
                <input type="color" name="{{ $groupKey }}[{{ $field }}]" value="{{ old($key, $settings[$key]) }}" class="h-11 w-full cursor-pointer rounded-xl border border-ghosn/15 bg-cream/30 p-1">
            </div>
        @endforeach
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
