<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\LocaleHelper::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Support\SiteSettings::title(__('admin.dashboard.title')) }}</title>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/react/admin/main.jsx'])
    @include('partials.theme-fonts')
    @php $favicon = \App\Support\SiteAsset::faviconUrl(); @endphp
    @if ($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @endif
</head>
<body class="antialiased">
    <div id="ghosn-admin-root"></div>
    <script>
        window.__GHOSN_ADMIN__ = @json($adminPayload);
    </script>
</body>
</html>
