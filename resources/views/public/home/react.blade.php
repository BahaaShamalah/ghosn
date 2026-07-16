<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\LocaleHelper::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Support\SiteSettings::title(__('public.home.title')) }}</title>
    @include('public.partials.social-meta')
    @include('public.partials.google-head')
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/react/landing/main.jsx', 'resources/js/app.js'])
    @include('partials.theme-fonts')
    @php $favicon = \App\Support\SiteAsset::faviconUrl(); @endphp
    @if ($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif
</head>
<body class="antialiased">
    @include('public.partials.google-body')
    <div id="ghosn-landing-root"></div>
    <script>
        window.__GHOSN_LANDING__ = @json($landingPayload);
    </script>
</body>
</html>
