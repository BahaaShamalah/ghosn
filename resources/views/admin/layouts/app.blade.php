<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\LocaleHelper::direction() }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Support\SiteSettings::title($__env->yieldContent('title', __('admin.dashboard.title'))) }}</title>

    @vite(['resources/css/app.css', 'resources/js/admin-shell.js'])

    @include('partials.theme-fonts')

    @stack('head')

</head>

<body class="gh-admin antialiased">

    @once('admin-flash-toasts')
        @include('admin.partials.toasts')
    @endonce

    <div class="gh-admin-shell flex min-h-screen overflow-x-hidden bg-[#EDEEE4] text-[#3a4234] leading-normal lg:h-screen lg:overflow-hidden">

        @include('admin.partials.sidebar')

        <div class="gh-admin-content flex min-w-0 flex-1 flex-col">

            @include('admin.partials.topbar')

            <main class="gh-admin-main flex-1 px-6 py-7 md:px-8 md:pb-10">

                @yield('content')

            </main>

        </div>

    </div>

    @stack('scripts')

</body>

</html>
