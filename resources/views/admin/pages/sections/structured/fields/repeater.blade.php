<div class="rounded-2xl border border-ghosn/10 bg-cream/40 p-5">
    <div class="mb-4">
        <h4 class="text-sm font-bold text-ghosn">{{ $labelEn }}</h4>
        @if (! empty($labelAr))
            <p class="text-xs text-ghosn-ink/55" dir="rtl">{{ $labelAr }}</p>
        @endif
    </div>

    <div class="space-y-4">
        @foreach ($items as $index => $item)
            <div class="rounded-xl border border-ghosn/10 bg-offwhite p-4">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.16em] text-ghosn/45">#{{ $index + 1 }}</p>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($itemFields as $itemField)
                        @php
                            $itemKey = $itemField['key'];
                            $itemInput = $itemField['input'] ?? 'text';
                            $isBilingual = array_key_exists('bilingual', $itemField)
                                ? (bool) $itemField['bilingual']
                                : in_array($itemInput, ['text', 'textarea'], true);
                            $fieldLabelEn = $itemField['label_en'] ?? ucfirst($itemKey);
                            $fieldLabelAr = $itemField['label_ar'] ?? '';
                        @endphp

                        @if ($itemKey === 'link_url')
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ __('admin.pages.link_url') }}</label>
                                <input type="text" name="content[{{ $repeaterKey }}][{{ $index }}][{{ $itemKey }}]" value="{{ old("content.{$repeaterKey}.{$index}.{$itemKey}", $item[$itemKey] ?? '') }}" class="ghosn-input" placeholder="#contact">
                            </div>
                        @elseif ($itemInput === 'number')
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">
                                    <span>{{ $fieldLabelEn }}</span>
                                    @if ($fieldLabelAr !== '')
                                        <span class="ms-1 text-ghosn-ink/55" dir="rtl">/ {{ $fieldLabelAr }}</span>
                                    @endif
                                </label>
                                <input
                                    type="number"
                                    name="content[{{ $repeaterKey }}][{{ $index }}][{{ $itemKey }}]"
                                    value="{{ old("content.{$repeaterKey}.{$index}.{$itemKey}", $item[$itemKey] ?? '') }}"
                                    step="{{ $itemField['step'] ?? '1' }}"
                                    class="ghosn-input"
                                >
                            </div>
                        @elseif ($isBilingual)
                            <div @if ($itemInput === 'textarea') class="md:col-span-2" @endif>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ $fieldLabelEn }} {{ __('admin.pages.field_en') }}</label>
                                @if ($itemInput === 'textarea')
                                    <textarea name="content[{{ $repeaterKey }}][{{ $index }}][{{ $itemKey }}_en]" rows="2" class="ghosn-input">{{ old("content.{$repeaterKey}.{$index}.{$itemKey}_en", $item[$itemKey.'_en'] ?? '') }}</textarea>
                                @else
                                    <input type="text" name="content[{{ $repeaterKey }}][{{ $index }}][{{ $itemKey }}_en]" value="{{ old("content.{$repeaterKey}.{$index}.{$itemKey}_en", $item[$itemKey.'_en'] ?? '') }}" class="ghosn-input">
                                @endif
                            </div>
                            <div @if ($itemInput === 'textarea') class="md:col-span-2" @endif>
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ $fieldLabelAr !== '' ? $fieldLabelAr : $fieldLabelEn }} {{ __('admin.pages.field_ar') }}</label>
                                @if ($itemInput === 'textarea')
                                    <textarea name="content[{{ $repeaterKey }}][{{ $index }}][{{ $itemKey }}_ar]" rows="2" class="ghosn-input" dir="rtl">{{ old("content.{$repeaterKey}.{$index}.{$itemKey}_ar", $item[$itemKey.'_ar'] ?? '') }}</textarea>
                                @else
                                    <input type="text" name="content[{{ $repeaterKey }}][{{ $index }}][{{ $itemKey }}_ar]" value="{{ old("content.{$repeaterKey}.{$index}.{$itemKey}_ar", $item[$itemKey.'_ar'] ?? '') }}" class="ghosn-input" dir="rtl">
                                @endif
                            </div>
                        @else
                            <div class="md:col-span-2">
                                <label class="mb-1.5 block text-sm font-medium text-ghosn-ink/80">{{ $fieldLabelEn }}</label>
                                <input type="text" name="content[{{ $repeaterKey }}][{{ $index }}][{{ $itemKey }}]" value="{{ old("content.{$repeaterKey}.{$index}.{$itemKey}", $item[$itemKey] ?? '') }}" class="ghosn-input">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
