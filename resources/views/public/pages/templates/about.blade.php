@php
    // Legacy Blade preview: redirect editors to the React About page powered by Settings.
    $locale = app()->getLocale();
    $about = \App\Support\AboutPageContent::forReact();
@endphp
<div class="mx-auto max-w-3xl px-6 py-16 text-center">
    <h1 class="text-3xl font-bold text-[#2f4327]">{{ $about['hero']['title'][$locale] ?? $about['hero']['title']['en'] }}</h1>
    <p class="mt-4 text-[#586150]">{{ $about['hero']['subtitle'][$locale] ?? $about['hero']['subtitle']['en'] }}</p>
    <a href="{{ route('about') }}" class="mt-8 inline-flex rounded-full bg-[#406139] px-6 py-3 text-sm font-semibold text-[#F7F6F0] no-underline">
        {{ $locale === 'ar' ? 'عرض الصفحة العامة' : 'View public About page' }}
    </a>
</div>
