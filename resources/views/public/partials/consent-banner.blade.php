@php
    $locale = app()->getLocale();
    $isAr = $locale === 'ar';
@endphp
<div
    id="ghosn-consent-banner"
    class="fixed inset-x-0 bottom-0 z-[80] hidden border-t border-[#406139]/20 bg-[#fffdf8]/97 p-4 shadow-[0_-12px_40px_rgba(47,67,39,0.18)] backdrop-blur-md"
    data-consent-root
    hidden
>
    <div class="mx-auto flex max-w-[1080px] flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl">
            <p class="text-sm font-bold text-[#2f4327]">{{ $isAr ? 'تفضيلات ملفات تعريف الارتباط' : 'Cookie preferences' }}</p>
            <p class="mt-1 text-sm text-[#5f6857]">
                {{ $isAr
                    ? 'نستخدم ملفات تعريف الارتباط الضرورية لتشغيل الموقع، ويمكنك قبول أو رفض التحليلات والتسويق.'
                    : 'We use necessary cookies to run the site. You can accept or reject analytics and marketing cookies.' }}
            </p>
            <div class="mt-3 hidden space-y-2" data-consent-customize>
                <label class="flex items-center gap-2 text-sm text-[#4a5340]">
                    <input type="checkbox" checked disabled class="rounded border-ghosn/25 text-ghosn">
                    {{ $isAr ? 'ضرورية' : 'Necessary' }}
                </label>
                <label class="flex items-center gap-2 text-sm text-[#4a5340]">
                    <input type="checkbox" data-consent-analytics class="rounded border-ghosn/25 text-ghosn">
                    {{ $isAr ? 'تحليلات' : 'Analytics' }}
                </label>
                <label class="flex items-center gap-2 text-sm text-[#4a5340]">
                    <input type="checkbox" data-consent-marketing class="rounded border-ghosn/25 text-ghosn">
                    {{ $isAr ? 'تسويق' : 'Marketing' }}
                </label>
                <label class="flex items-center gap-2 text-sm text-[#4a5340]">
                    <input type="checkbox" data-consent-preferences class="rounded border-ghosn/25 text-ghosn">
                    {{ $isAr ? 'تفضيلات' : 'Preferences' }}
                </label>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" data-consent-reject class="rounded-full border border-[#406139]/25 bg-transparent px-4 py-2 text-sm font-semibold text-[#406139]">
                {{ $isAr ? 'رفض' : 'Reject' }}
            </button>
            <button type="button" data-consent-customize-toggle class="rounded-full border border-[#406139]/25 bg-transparent px-4 py-2 text-sm font-semibold text-[#406139]">
                {{ $isAr ? 'تخصيص' : 'Customize' }}
            </button>
            <button type="button" data-consent-save class="hidden rounded-full bg-[#819562] px-4 py-2 text-sm font-semibold text-[#F7F6F0]">
                {{ $isAr ? 'حفظ' : 'Save' }}
            </button>
            <button type="button" data-consent-accept class="rounded-full bg-[#406139] px-4 py-2 text-sm font-semibold text-[#F2F1EA]">
                {{ $isAr ? 'قبول الكل' : 'Accept all' }}
            </button>
        </div>
    </div>
</div>
