@php
    use App\Support\TeamPageContent;

    $defaults = TeamPageContent::defaults();
    $stored = $settings['team.page'] ?? null;
    $team = is_array($stored) && $stored !== []
        ? array_replace_recursive($defaults, $stored)
        : $defaults;
@endphp

<form method="POST" action="{{ route('admin.settings.update.group', 'team') }}" class="space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="_group" value="team">

    @include('admin.settings.partials.form-errors', ['group' => 'team'])

    <p class="text-sm text-[#5f6857]">{{ __('admin.settings.team_intro') }}</p>

    <div class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.team_hero') }}</h3>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach (['eyebrow', 'title', 'subtitle'] as $field)
                <div @class(['md:col-span-2' => $field === 'subtitle'])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_'.$field.'_en') }}</label>
                    @if ($field === 'subtitle')
                        <textarea name="team[page][hero][{{ $field }}_en]" rows="2" class="ghosn-input">{{ old('team.page.hero.'.$field.'_en', $team['hero'][$field.'_en'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="team[page][hero][{{ $field }}_en]" value="{{ old('team.page.hero.'.$field.'_en', $team['hero'][$field.'_en'] ?? '') }}" class="ghosn-input">
                    @endif
                </div>
                <div @class(['md:col-span-2' => $field === 'subtitle'])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_'.$field.'_ar') }}</label>
                    @if ($field === 'subtitle')
                        <textarea name="team[page][hero][{{ $field }}_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('team.page.hero.'.$field.'_ar', $team['hero'][$field.'_ar'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="team[page][hero][{{ $field }}_ar]" value="{{ old('team.page.hero.'.$field.'_ar', $team['hero'][$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.team_stats') }}</h3>
        <div class="space-y-3">
            @foreach ($team['stats'] ?? [] as $index => $stat)
                <div class="grid gap-3 rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.35)] p-4 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_stat_value') }}</label>
                        <input type="number" name="team[page][stats][{{ $index }}][end]" value="{{ old('team.page.stats.'.$index.'.end', $stat['end'] ?? 0) }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_stat_suffix') }}</label>
                        <input type="text" name="team[page][stats][{{ $index }}][suffix]" value="{{ old('team.page.stats.'.$index.'.suffix', $stat['suffix'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">EN</label>
                        <input type="text" name="team[page][stats][{{ $index }}][label_en]" value="{{ old('team.page.stats.'.$index.'.label_en', $stat['label_en'] ?? '') }}" class="ghosn-input">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[#8a9280]">AR</label>
                        <input type="text" name="team[page][stats][{{ $index }}][label_ar]" value="{{ old('team.page.stats.'.$index.'.label_ar', $stat['label_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.team_leadership') }}</h3>
        <div class="mb-4 grid gap-4 md:grid-cols-2">
            @foreach (['eyebrow', 'title', 'intro'] as $field)
                <div @class(['md:col-span-2' => $field === 'intro'])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_'.$field.'_en') }}</label>
                    @if ($field === 'intro')
                        <textarea name="team[page][leadership][{{ $field }}_en]" rows="2" class="ghosn-input">{{ old('team.page.leadership.'.$field.'_en', $team['leadership'][$field.'_en'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="team[page][leadership][{{ $field }}_en]" value="{{ old('team.page.leadership.'.$field.'_en', $team['leadership'][$field.'_en'] ?? '') }}" class="ghosn-input">
                    @endif
                </div>
                <div @class(['md:col-span-2' => $field === 'intro'])>
                    <label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_'.$field.'_ar') }}</label>
                    @if ($field === 'intro')
                        <textarea name="team[page][leadership][{{ $field }}_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('team.page.leadership.'.$field.'_ar', $team['leadership'][$field.'_ar'] ?? '') }}</textarea>
                    @else
                        <input type="text" name="team[page][leadership][{{ $field }}_ar]" value="{{ old('team.page.leadership.'.$field.'_ar', $team['leadership'][$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                    @endif
                </div>
            @endforeach
        </div>
        <div class="space-y-4">
            @foreach ($team['leaders'] ?? [] as $index => $leader)
                <div class="rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.35)] p-4">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_leader_name_en') }}</label><input type="text" name="team[page][leaders][{{ $index }}][name_en]" value="{{ old('team.page.leaders.'.$index.'.name_en', $leader['name_en'] ?? '') }}" class="ghosn-input"></div>
                        <div><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_leader_name_ar') }}</label><input type="text" name="team[page][leaders][{{ $index }}][name_ar]" value="{{ old('team.page.leaders.'.$index.'.name_ar', $leader['name_ar'] ?? '') }}" class="ghosn-input" dir="rtl"></div>
                        <div><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_leader_role_en') }}</label><input type="text" name="team[page][leaders][{{ $index }}][role_en]" value="{{ old('team.page.leaders.'.$index.'.role_en', $leader['role_en'] ?? '') }}" class="ghosn-input"></div>
                        <div><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_leader_role_ar') }}</label><input type="text" name="team[page][leaders][{{ $index }}][role_ar]" value="{{ old('team.page.leaders.'.$index.'.role_ar', $leader['role_ar'] ?? '') }}" class="ghosn-input" dir="rtl"></div>
                        <div class="md:col-span-2"><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_leader_bio_en') }}</label><textarea name="team[page][leaders][{{ $index }}][bio_en]" rows="2" class="ghosn-input">{{ old('team.page.leaders.'.$index.'.bio_en', $leader['bio_en'] ?? '') }}</textarea></div>
                        <div class="md:col-span-2"><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_leader_bio_ar') }}</label><textarea name="team[page][leaders][{{ $index }}][bio_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('team.page.leaders.'.$index.'.bio_ar', $leader['bio_ar'] ?? '') }}</textarea></div>
                        <div class="md:col-span-2"><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_leader_image') }}</label><input type="url" name="team[page][leaders][{{ $index }}][image_url]" value="{{ old('team.page.leaders.'.$index.'.image_url', $leader['image_url'] ?? '') }}" dir="ltr" class="ghosn-input" placeholder="https://"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.team_departments') }}</h3>
        <div class="mb-4 grid gap-4 md:grid-cols-2">
            <div><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_eyebrow_en') }}</label><input type="text" name="team[page][departments][eyebrow_en]" value="{{ old('team.page.departments.eyebrow_en', $team['departments']['eyebrow_en'] ?? '') }}" class="ghosn-input"></div>
            <div><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_eyebrow_ar') }}</label><input type="text" name="team[page][departments][eyebrow_ar]" value="{{ old('team.page.departments.eyebrow_ar', $team['departments']['eyebrow_ar'] ?? '') }}" class="ghosn-input" dir="rtl"></div>
            <div><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_title_en') }}</label><input type="text" name="team[page][departments][title_en]" value="{{ old('team.page.departments.title_en', $team['departments']['title_en'] ?? '') }}" class="ghosn-input"></div>
            <div><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_title_ar') }}</label><input type="text" name="team[page][departments][title_ar]" value="{{ old('team.page.departments.title_ar', $team['departments']['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl"></div>
        </div>
        <div class="space-y-3">
            @foreach ($team['departments']['items'] ?? [] as $index => $dept)
                <div class="grid gap-3 rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.35)] p-4 md:grid-cols-2">
                    <input type="hidden" name="team[page][departments][items][{{ $index }}][key]" value="{{ $dept['key'] ?? 'field' }}">
                    <div><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_dept_count') }}</label><input type="number" name="team[page][departments][items][{{ $index }}][count]" value="{{ old('team.page.departments.items.'.$index.'.count', $dept['count'] ?? 0) }}" class="ghosn-input"></div>
                    <div></div>
                    <div><label class="mb-1 block text-xs font-semibold text-[#8a9280]">EN</label><input type="text" name="team[page][departments][items][{{ $index }}][name_en]" value="{{ old('team.page.departments.items.'.$index.'.name_en', $dept['name_en'] ?? '') }}" class="ghosn-input"></div>
                    <div><label class="mb-1 block text-xs font-semibold text-[#8a9280]">AR</label><input type="text" name="team[page][departments][items][{{ $index }}][name_ar]" value="{{ old('team.page.departments.items.'.$index.'.name_ar', $dept['name_ar'] ?? '') }}" class="ghosn-input" dir="rtl"></div>
                    <div class="md:col-span-2"><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_dept_desc_en') }}</label><textarea name="team[page][departments][items][{{ $index }}][desc_en]" rows="2" class="ghosn-input">{{ old('team.page.departments.items.'.$index.'.desc_en', $dept['desc_en'] ?? '') }}</textarea></div>
                    <div class="md:col-span-2"><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_dept_desc_ar') }}</label><textarea name="team[page][departments][items][{{ $index }}][desc_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('team.page.departments.items.'.$index.'.desc_ar', $dept['desc_ar'] ?? '') }}</textarea></div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.team_culture') }}</h3>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach (['eyebrow', 'title'] as $field)
                <div><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_'.$field.'_en') }}</label><input type="text" name="team[page][culture][{{ $field }}_en]" value="{{ old('team.page.culture.'.$field.'_en', $team['culture'][$field.'_en'] ?? '') }}" class="ghosn-input"></div>
                <div><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_'.$field.'_ar') }}</label><input type="text" name="team[page][culture][{{ $field }}_ar]" value="{{ old('team.page.culture.'.$field.'_ar', $team['culture'][$field.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl"></div>
            @endforeach
            <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_intro_en') }}</label><textarea name="team[page][culture][body_en]" rows="3" class="ghosn-input">{{ old('team.page.culture.body_en', $team['culture']['body_en'] ?? '') }}</textarea></div>
            <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_intro_ar') }}</label><textarea name="team[page][culture][body_ar]" rows="3" class="ghosn-input" dir="rtl">{{ old('team.page.culture.body_ar', $team['culture']['body_ar'] ?? '') }}</textarea></div>
            <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_culture_image') }}</label><input type="url" name="team[page][culture][image_url]" value="{{ old('team.page.culture.image_url', $team['culture']['image_url'] ?? '') }}" dir="ltr" class="ghosn-input"></div>
        </div>
        <div class="mt-4 space-y-3">
            @foreach ($team['culture']['points'] ?? [] as $index => $point)
                <div class="grid gap-3 rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-[rgba(237,238,228,0.35)] p-4 md:grid-cols-2">
                    <div><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_point_title_en') }}</label><input type="text" name="team[page][culture][points][{{ $index }}][title_en]" value="{{ old('team.page.culture.points.'.$index.'.title_en', $point['title_en'] ?? '') }}" class="ghosn-input"></div>
                    <div><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_point_title_ar') }}</label><input type="text" name="team[page][culture][points][{{ $index }}][title_ar]" value="{{ old('team.page.culture.points.'.$index.'.title_ar', $point['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl"></div>
                    <div class="md:col-span-2"><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_point_body_en') }}</label><textarea name="team[page][culture][points][{{ $index }}][body_en]" rows="2" class="ghosn-input">{{ old('team.page.culture.points.'.$index.'.body_en', $point['body_en'] ?? '') }}</textarea></div>
                    <div class="md:col-span-2"><label class="mb-1 block text-xs font-semibold text-[#8a9280]">{{ __('admin.settings.team_point_body_ar') }}</label><textarea name="team[page][culture][points][{{ $index }}][body_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('team.page.culture.points.'.$index.'.body_ar', $point['body_ar'] ?? '') }}</textarea></div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-[18px] border border-[rgba(64,97,57,0.12)] bg-[#fffdf8] p-5 md:p-6">
        <h3 class="mb-4 text-lg font-bold text-[#2f4327]">{{ __('admin.settings.team_cta') }}</h3>
        <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_title_en') }}</label><input type="text" name="team[page][cta][title_en]" value="{{ old('team.page.cta.title_en', $team['cta']['title_en'] ?? '') }}" class="ghosn-input"></div>
            <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_title_ar') }}</label><input type="text" name="team[page][cta][title_ar]" value="{{ old('team.page.cta.title_ar', $team['cta']['title_ar'] ?? '') }}" class="ghosn-input" dir="rtl"></div>
            <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_subtitle_en') }}</label><textarea name="team[page][cta][subtitle_en]" rows="2" class="ghosn-input">{{ old('team.page.cta.subtitle_en', $team['cta']['subtitle_en'] ?? '') }}</textarea></div>
            <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_subtitle_ar') }}</label><textarea name="team[page][cta][subtitle_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old('team.page.cta.subtitle_ar', $team['cta']['subtitle_ar'] ?? '') }}</textarea></div>
            <div><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_cta_primary_en') }}</label><input type="text" name="team[page][cta][primary_en]" value="{{ old('team.page.cta.primary_en', $team['cta']['primary_en'] ?? '') }}" class="ghosn-input"></div>
            <div><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_cta_primary_ar') }}</label><input type="text" name="team[page][cta][primary_ar]" value="{{ old('team.page.cta.primary_ar', $team['cta']['primary_ar'] ?? '') }}" class="ghosn-input" dir="rtl"></div>
            <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_cta_primary_url') }}</label><input type="text" name="team[page][cta][primary_url]" value="{{ old('team.page.cta.primary_url', $team['cta']['primary_url'] ?? '/volunteer') }}" dir="ltr" class="ghosn-input"></div>
            <div><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_cta_secondary_en') }}</label><input type="text" name="team[page][cta][secondary_en]" value="{{ old('team.page.cta.secondary_en', $team['cta']['secondary_en'] ?? '') }}" class="ghosn-input"></div>
            <div><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_cta_secondary_ar') }}</label><input type="text" name="team[page][cta][secondary_ar]" value="{{ old('team.page.cta.secondary_ar', $team['cta']['secondary_ar'] ?? '') }}" class="ghosn-input" dir="rtl"></div>
            <div class="md:col-span-2"><label class="mb-1.5 block text-sm font-medium text-[#4a5340]">{{ __('admin.settings.team_cta_secondary_url') }}</label><input type="text" name="team[page][cta][secondary_url]" value="{{ old('team.page.cta.secondary_url', $team['cta']['secondary_url'] ?? '/contact') }}" dir="ltr" class="ghosn-input"></div>
        </div>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit" class="gh-admin-btn-primary shadow-lg shadow-ghosn/20 transition hover:bg-ghosn-700">
            {{ __('admin.settings.save_card') }}
        </button>
    </div>
</form>
