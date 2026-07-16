<div class="space-y-8">
    @include('admin.settings.partials.form-errors', ['group' => 'social'])

    <div class="rounded-2xl border border-ghosn/10 bg-cream/30 p-5">
        <h3 class="text-sm font-bold text-ghosn">{{ __('admin.settings.social_add_heading') }}</h3>
        <p class="mt-1 text-xs text-ghosn-ink/55">{{ __('admin.settings.social_add_help') }}</p>

        <form method="POST" action="{{ route('admin.settings.social.links.store') }}" class="mt-4 grid gap-4 md:grid-cols-2">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.social_platform') }}</label>
                <select name="platform" class="ghosn-input" required>
                    @foreach ($socialPlatforms as $key => $meta)
                        <option value="{{ $key }}" @selected(old('platform') === $key)>{{ $meta['label_en'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.social_url') }}</label>
                <input type="url" name="url" value="{{ old('url') }}" class="ghosn-input" required placeholder="https://">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.social_label_en') }}</label>
                <input type="text" name="label_en" value="{{ old('label_en') }}" class="ghosn-input" maxlength="120">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.social_label_ar') }}</label>
                <input type="text" name="label_ar" value="{{ old('label_ar') }}" class="ghosn-input" maxlength="120" dir="rtl">
            </div>
            <div class="flex items-center gap-3 md:col-span-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="social_new_active" name="is_active" value="1" @checked(old('is_active', '1') === '1') class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                <label for="social_new_active" class="text-sm font-medium text-ghosn-ink/80">{{ __('admin.settings.social_active') }}</label>
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
                    {{ __('admin.settings.social_add') }}
                </button>
            </div>
        </form>
    </div>

    @if ($socialLinks->isEmpty())
        <div class="rounded-2xl border border-dashed border-ghosn/20 bg-offwhite/60 p-8 text-center text-sm text-ghosn-ink/60">
            {{ __('admin.settings.social_empty') }}
        </div>
    @else
        <div class="space-y-4">
            <h3 class="text-sm font-bold text-ghosn">{{ __('admin.settings.social_manage_heading') }}</h3>

            @foreach ($socialLinks as $link)
                <details class="group rounded-2xl border border-ghosn/10 bg-offwhite p-4" @if($loop->first) open @endif>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-ghosn/15 bg-cream/50 text-ghosn text-sm">
                                @include('public.components.social-icon', ['platform' => $link->resolvedIcon()])
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-ghosn">{{ $link->localizedLabel('en') }}</p>
                                <p class="text-xs text-ghosn-ink/55" dir="ltr">{{ $link->url }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span @class([
                                'rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide',
                                'bg-growth-soft/60 text-ghosn-800' => $link->is_active,
                                'bg-ghosn/10 text-ghosn/55' => ! $link->is_active,
                            ])>{{ $link->is_active ? __('admin.settings.social_status_active') : __('admin.settings.social_status_inactive') }}</span>
                            <span class="text-xs text-ghosn-ink/45">#{{ $link->sort_order + 1 }}</span>
                        </div>
                    </summary>

                    <form method="POST" action="{{ route('admin.settings.social.links.update', $link) }}" class="mt-4 grid gap-3 border-t border-ghosn/10 pt-4 md:grid-cols-2">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-ghosn/60">{{ __('admin.settings.social_platform') }}</label>
                            <select name="platform" class="ghosn-input">
                                @foreach ($socialPlatforms as $key => $meta)
                                    <option value="{{ $key }}" @selected($link->platform === $key)>{{ $meta['label_en'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-ghosn/60">{{ __('admin.settings.social_url') }}</label>
                            <input type="url" name="url" value="{{ old('url', $link->url) }}" class="ghosn-input" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-ghosn/60">{{ __('admin.settings.social_label_en') }}</label>
                            <input type="text" name="label_en" value="{{ old('label_en', $link->label_en) }}" class="ghosn-input">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-ghosn/60">{{ __('admin.settings.social_label_ar') }}</label>
                            <input type="text" name="label_ar" value="{{ old('label_ar', $link->label_ar) }}" class="ghosn-input" dir="rtl">
                        </div>
                        <div class="flex items-center gap-3 md:col-span-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="social_active_{{ $link->id }}" name="is_active" value="1" @checked(old('is_active', $link->is_active)) class="rounded border-ghosn/25 text-ghosn focus:ring-ghosn">
                            <label for="social_active_{{ $link->id }}" class="text-sm text-ghosn-ink/80">{{ __('admin.settings.social_active') }}</label>
                        </div>
                        <div class="flex flex-wrap gap-2 md:col-span-2">
                            <button type="submit" class="gh-admin-btn-primary">{{ __('admin.settings.save_card') }}</button>
                        </div>
                    </form>

                    <div class="mt-3 flex flex-wrap gap-2 border-t border-ghosn/10 pt-3">
                        <form method="POST" action="{{ route('admin.settings.social.links.toggle', $link) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs">
                                {{ $link->is_active ? __('admin.settings.social_disable') : __('admin.settings.social_enable') }}
                            </button>
                        </form>
                        @if (! $loop->first)
                            <form method="POST" action="{{ route('admin.settings.social.links.move', [$link, 'up']) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs">{{ __('admin.settings.social_move_up') }}</button>
                            </form>
                        @endif
                        @if (! $loop->last)
                            <form method="POST" action="{{ route('admin.settings.social.links.move', [$link, 'down']) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="gh-admin-btn-secondary !px-3 !py-1.5 !text-xs">{{ __('admin.settings.social_move_down') }}</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.settings.social.links.destroy', $link) }}" onsubmit="return confirm(@json(__('admin.settings.social_delete_confirm')))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50">{{ __('admin.settings.social_delete') }}</button>
                        </form>
                    </div>
                </details>
            @endforeach
        </div>
    @endif
</div>
