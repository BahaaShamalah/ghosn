@php
    $selected = collect(old('permissions', isset($role) ? $role->permissions->pluck('slug')->all() : []));
    $locked = isset($role) && $role->is_super;
@endphp

<div class="gh-admin-card space-y-5 rounded-[18px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] p-6">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.roles.slug') }}</label>
            <input type="text" name="slug" value="{{ old('slug', $role->slug ?? '') }}" @disabled(isset($role) && $role->is_system) required dir="ltr" class="ghosn-input">
            @include('admin.partials.field-error', ['field' => 'slug'])
        </div>
        <div></div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.roles.label_en') }}</label>
            <input type="text" name="label_en" value="{{ old('label_en', $role->label_en ?? '') }}" required class="ghosn-input">
            @include('admin.partials.field-error', ['field' => 'label_en'])
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.roles.label_ar') }}</label>
            <input type="text" name="label_ar" value="{{ old('label_ar', $role->label_ar ?? '') }}" required class="ghosn-input">
            @include('admin.partials.field-error', ['field' => 'label_ar'])
        </div>
    </div>

    <div>
        <h3 class="mb-3 text-base font-bold text-[#2f4327]">{{ __('admin.roles.permissions') }}</h3>
        @if ($locked)
            <p class="mb-4 text-sm text-[#8a9280]">{{ __('admin.roles.super_permissions_locked') }}</p>
        @endif
        <div class="space-y-5">
            @foreach ($permissionGroups as $group => $permissions)
                <div class="rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.35)] p-4">
                    <p class="mb-3 text-xs font-bold uppercase tracking-[0.12em] text-[#8a9280]">{{ __('admin.roles.group_'.$group) }}</p>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach ($permissions as $permission)
                            <label class="flex cursor-pointer items-start gap-2.5 text-sm text-[#4a5340]">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission['slug'] }}"
                                    @checked($selected->contains($permission['slug']))
                                    @disabled($locked)
                                    class="mt-0.5 h-4 w-4 shrink-0 accent-[#406139]"
                                >
                                <span class="min-w-0 break-words">{{ app()->getLocale() === 'ar' ? $permission['label_ar'] : $permission['label_en'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @include('admin.partials.field-error', ['field' => 'permissions'])
    </div>
</div>
