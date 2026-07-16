@php
    $isEdit = isset($user);
@endphp

<div class="gh-admin-card space-y-5 rounded-[18px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.users.name') }}</label>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required class="ghosn-input">
            @include('admin.partials.field-error', ['field' => 'name'])
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.users.email') }}</label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required dir="ltr" class="ghosn-input">
            @include('admin.partials.field-error', ['field' => 'email'])
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.users.role') }}</label>
            <select name="role_id" required class="ghosn-input">
                <option value="">{{ __('admin.users.select_role') }}</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id ?? '') === (string) $role->id)>
                        {{ $role->localizedLabel() }}
                    </option>
                @endforeach
            </select>
            @include('admin.partials.field-error', ['field' => 'role_id'])
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">
                {{ $isEdit ? __('admin.users.new_password_optional') : __('admin.users.password') }}
            </label>
            <input type="password" name="password" @unless($isEdit) required @endunless autocomplete="new-password" class="ghosn-input">
            @include('admin.partials.field-error', ['field' => 'password'])
        </div>
    </div>

    <div class="max-w-md">
        <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.users.password_confirm') }}</label>
        <input type="password" name="password_confirmation" @unless($isEdit) required @endunless autocomplete="new-password" class="ghosn-input">
    </div>
</div>
