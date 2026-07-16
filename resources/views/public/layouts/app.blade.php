<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\LocaleHelper::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Support\SiteSettings::title($__env->yieldContent('title')) }}</title>
    @php $hasCustomMeta = trim(\Illuminate\Support\Facades\View::yieldPushContent('meta')) !== ''; @endphp
    @if ($hasCustomMeta)
        @stack('meta')
    @else
        @include('public.partials.social-meta')
    @endif

    @include('public.partials.google-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-fonts')
    @php $favicon = \App\Support\SiteAsset::faviconUrl(); @endphp
    @if ($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif
    @stack('head')
</head>
<body class="bg-[#F2F1EA] antialiased">
    @include('public.partials.google-body')
    <div
        id="ghosn-root"
        class="gh-internal text-[#3a4234] overflow-x-hidden"
        data-lang="{{ app()->getLocale() }}"
        data-locale-base="{{ url('/locale') }}"
        data-accent="{{ config('landing.default_accent') }}"
        data-motion="{{ config('landing.default_motion') }}"
    >
        @include('public.partials.header')

        @yield('content')

        @include('public.partials.footer')
    </div>
    @stack('scripts')
</body>
</html>
