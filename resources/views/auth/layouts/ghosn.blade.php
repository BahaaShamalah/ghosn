<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\LocaleHelper::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Support\SiteSettings::title($__env->yieldContent('title', __('admin.login.title'))) }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-offwhite font-sans text-ghosn-ink antialiased">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="blob-anim-1 absolute -top-24 -start-24 h-[420px] w-[420px] rounded-full opacity-40" style="background:radial-gradient(circle,rgba(70,164,91,.12) 0%,transparent 70%);filter:blur(60px);"></div>
        <div class="blob-anim-2 absolute -bottom-32 -end-20 h-[380px] w-[380px] rounded-full opacity-35" style="background:radial-gradient(circle,rgba(12,90,46,.08) 0%,transparent 70%);filter:blur(72px);"></div>
    </div>

    <main class="relative flex min-h-screen items-center justify-center px-5 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <img src="{{ \App\Support\LandingAsset::url('logo') }}" alt="GHOSN" class="mx-auto mb-4 h-14 w-auto">
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-ghosn/55">Relief Team</p>
            </div>

            <div class="rounded-[28px] border border-ghosn/10 bg-offwhite/95 p-8 shadow-xl shadow-ghosn/10 backdrop-blur-sm">
                @yield('content')
            </div>
        </div>
    </main>
</body>
</html>
