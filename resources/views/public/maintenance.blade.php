<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\LocaleHelper::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} — {{ $siteName }}</title>
    @include('public.partials.social-meta', [
        'meta' => \App\Support\SeoSettings::meta(null, [
            'title' => $title,
            'description' => $message,
        ]),
    ])
    @vite(['resources/css/app.css'])
    @include('partials.theme-fonts')
    @php $favicon = \App\Support\SiteAsset::faviconUrl(); @endphp
    @if ($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif
</head>
<body class="min-h-screen bg-[#F2F1EA] text-[#3a4234] antialiased">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-16">
        <div class="pointer-events-none absolute -top-16 end-[10%] h-56 w-56 rounded-full border border-dashed border-[#819562]/25"></div>
        <div class="pointer-events-none absolute -bottom-20 start-[8%] h-48 w-48 rounded-full bg-[radial-gradient(circle,rgba(129,149,98,0.22),transparent_70%)]"></div>

        <div class="relative z-[1] w-full max-w-[560px] text-center">
            <div class="mx-auto mb-8 flex flex-col items-center gap-4">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-14 w-auto">
                @endif
                <div class="inline-flex items-center gap-2 rounded-full bg-[#406139]/10 px-4 py-2 text-xs font-semibold tracking-wide text-[#406139]">
                    <span class="h-2 w-2 rounded-full bg-[#819562]"></span>
                    {{ $eyebrow }}
                </div>
            </div>

            <h1 class="text-[clamp(1.75rem,4vw,2.35rem)] font-bold leading-tight text-[#2f4327]">{{ $title }}</h1>
            <p class="mx-auto mt-4 max-w-[480px] text-base leading-relaxed text-[#5f6857]">{{ $message }}</p>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-3 text-sm">
                <a href="{{ route('locale.switch', 'en') }}" @class([
                    'rounded-full px-4 py-2 font-semibold no-underline transition',
                    'bg-[#406139] text-[#F2F1EA]' => app()->getLocale() === 'en',
                    'border border-[#406139]/25 text-[#406139] hover:bg-[#406139]/8' => app()->getLocale() !== 'en',
                ])>EN</a>
                <a href="{{ route('locale.switch', 'ar') }}" @class([
                    'rounded-full px-4 py-2 font-semibold no-underline transition',
                    'bg-[#406139] text-[#F2F1EA]' => app()->getLocale() === 'ar',
                    'border border-[#406139]/25 text-[#406139] hover:bg-[#406139]/8' => app()->getLocale() !== 'ar',
                ])>AR</a>
            </div>
        </div>
    </div>
</body>
</html>
