@php
    $item = $campaign ?? null;
    $previewImage = $item?->featuredImage?->thumbnailUrl() ?? $item?->featuredImage?->url();
    $categoryName = $item?->category?->name_en ?? '';
    $goal = (float) old('goal_amount', $item?->goal_amount ?? 0);
    $raised = (float) old('raised_amount', $item?->raised_amount ?? 0);
    $currency = old('currency', $item?->currency ?? 'USD');
    $pct = $goal > 0 ? min(100, round(($raised / $goal) * 100)) : 0;
@endphp

<aside class="gh-campaign-preview xl:sticky xl:top-24" data-campaign-preview>
    <div class="overflow-hidden rounded-[20px] border border-[rgba(64,97,57,0.1)] bg-[#fffdf8] shadow-[0_12px_34px_rgba(47,67,39,0.1)]">
        <div class="flex items-center justify-between gap-3 border-b border-[rgba(64,97,57,0.1)] bg-[rgba(64,97,57,0.06)] px-[18px] py-3">
            <div class="flex items-center gap-2">
                <span class="h-[9px] w-[9px] rounded-full bg-[#819562]"></span>
                <span class="text-[12px] font-bold uppercase tracking-[0.5px] text-[#4a5340]">{{ __('admin.campaigns.live_preview') }}</span>
            </div>
            <div class="flex overflow-hidden rounded-full border border-[rgba(64,97,57,0.24)]" data-campaign-preview-lang>
                <button type="button" data-preview-lang="en" class="bg-[#406139] px-[11px] py-[5px] text-[11px] font-semibold text-[#F2F1EA]">{{ __('admin.campaigns.preview_lang_en') }}</button>
                <button type="button" data-preview-lang="ar" class="bg-transparent px-[11px] py-[5px] text-[11px] font-semibold text-[#406139]">{{ __('admin.campaigns.preview_lang_ar') }}</button>
            </div>
        </div>

        <div class="p-[18px]">
            <div class="overflow-hidden rounded-[14px] border border-[rgba(64,97,57,0.1)] bg-white">
                <div class="relative h-[130px] overflow-hidden bg-gradient-to-br from-[#BCCAA7] to-[#96A791]" data-campaign-preview-cover>
                    @if ($previewImage)
                        <img src="{{ $previewImage }}" alt="" class="h-full w-full object-cover" data-campaign-preview-img>
                    @else
                        <img src="" alt="" class="hidden h-full w-full object-cover" data-campaign-preview-img>
                    @endif
                    <span
                        class="absolute start-3 top-3 rounded-full bg-[#406139] px-[11px] py-1 text-[10.5px] font-semibold text-white @if(! $categoryName) hidden @endif"
                        data-campaign-preview-category
                    >{{ $categoryName }}</span>
                </div>
                <div class="p-[18px]">
                    <h4 class="m-0 mb-2 text-base font-bold text-[#2f4327]" data-campaign-preview-title>
                        {{ old('title_en', $item?->title_en) ?: __('admin.campaigns.preview_title_placeholder') }}
                    </h4>
                    <p class="m-0 mb-4 text-[13px] leading-[1.55] text-[#5f6857]" data-campaign-preview-desc>
                        {{ old('excerpt_en', $item?->excerpt_en) ?: __('admin.campaigns.preview_desc_placeholder') }}
                    </p>
                    <div class="mb-2 h-2 overflow-hidden rounded-full bg-[rgba(64,97,57,0.12)]">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-[#819562] to-[#406139] transition-[width] duration-300"
                            data-campaign-preview-bar
                            style="width: {{ $pct }}%"
                        ></div>
                    </div>
                    <div class="flex justify-between text-[12.5px]">
                        <span class="font-bold text-[#406139]" data-campaign-preview-raised>
                            {{ number_format($raised, 0) }} {{ $currency }}
                        </span>
                        <span class="text-[#8a9280]" data-campaign-preview-pct>{{ $pct }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p class="mt-3.5 text-center text-[12px] text-[#8a9280]">{{ __('admin.campaigns.preview_note') }}</p>
</aside>
