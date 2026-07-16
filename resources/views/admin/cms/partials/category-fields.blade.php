@php
    $selectedType = old('type', $category->type ?? \App\Models\Category::TYPE_POST);
@endphp

<div class="grid gap-5">
    <div>
        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.cms.category_type') }}</label>
        <select name="type" class="ghosn-input">
            <option value="{{ \App\Models\Category::TYPE_POST }}" @selected($selectedType === \App\Models\Category::TYPE_POST)>{{ __('admin.cms.category_type_post') }}</option>
            <option value="{{ \App\Models\Category::TYPE_CAMPAIGN }}" @selected($selectedType === \App\Models\Category::TYPE_CAMPAIGN)>{{ __('admin.cms.category_type_campaign') }}</option>
        </select>
    </div>
    <div><label class="mb-1.5 block text-sm font-medium">{{ __('admin.cms.name_en') }}</label><input name="name_en" value="{{ old('name_en', $category->name_en ?? '') }}" required class="ghosn-input"></div>
    <div><label class="mb-1.5 block text-sm font-medium">{{ __('admin.cms.name_ar') }}</label><input name="name_ar" value="{{ old('name_ar', $category->name_ar ?? '') }}" required class="ghosn-input" dir="rtl"></div>
    <div><label class="mb-1.5 block text-sm font-medium">{{ __('admin.cms.slug') }}</label><input name="slug" value="{{ old('slug', $category->slug ?? '') }}" class="ghosn-input" dir="ltr"></div>
</div>
