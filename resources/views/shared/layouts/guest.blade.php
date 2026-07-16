@extends('shared.layouts.base')

@section('title', __('admin.login.title'))

@section('body')
    <main class="mx-auto flex min-h-screen max-w-md flex-col justify-center px-6 py-16">
        <div class="rounded-xl border border-stone-200 bg-white p-8 shadow-sm">
            @yield('content')
        </div>
    </main>
@endsection
