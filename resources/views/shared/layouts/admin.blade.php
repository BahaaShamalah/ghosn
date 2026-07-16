@extends('shared.layouts.base')

@section('title', __('admin.dashboard.title'))

@section('body')
    <div class="min-h-screen">
        <header class="border-b border-stone-200 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                <div>
                    <p class="text-sm font-medium text-stone-500">{{ \App\Support\SiteSettings::name() }}</p>
                    <h1 class="text-lg font-semibold text-stone-900">@yield('header-title', __('admin.dashboard.title'))</h1>
                </div>
                @yield('header-actions')
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-6 py-10">
            @yield('content')
        </main>
    </div>
@endsection
