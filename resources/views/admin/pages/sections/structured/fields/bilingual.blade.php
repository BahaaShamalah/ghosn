@php
    $inputType = $input ?? 'text';
    $rows = $inputType === 'textarea' ? 3 : null;
@endphp

<div class="grid gap-5 md:grid-cols-2">
    <div @if ($rows) class="md:col-span-2" @endif>
        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ ucfirst(str_replace('_', ' ', $fieldKey)) }} (EN)</label>
        @if ($inputType === 'textarea')
            <textarea name="content[{{ $fieldKey }}_en]" rows="{{ $rows }}" class="ghosn-input">{{ old("content.{$fieldKey}_en", $content[$fieldKey.'_en'] ?? '') }}</textarea>
        @else
            <input type="text" name="content[{{ $fieldKey }}_en]" value="{{ old("content.{$fieldKey}_en", $content[$fieldKey.'_en'] ?? '') }}" class="ghosn-input">
        @endif
    </div>
    <div @if ($rows) class="md:col-span-2" @endif>
        <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ ucfirst(str_replace('_', ' ', $fieldKey)) }} (AR)</label>
        @if ($inputType === 'textarea')
            <textarea name="content[{{ $fieldKey }}_ar]" rows="{{ $rows }}" class="ghosn-input" dir="rtl">{{ old("content.{$fieldKey}_ar", $content[$fieldKey.'_ar'] ?? '') }}</textarea>
        @else
            <input type="text" name="content[{{ $fieldKey }}_ar]" value="{{ old("content.{$fieldKey}_ar", $content[$fieldKey.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
        @endif
    </div>
</div>
