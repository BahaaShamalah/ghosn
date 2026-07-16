@extends('shared.layouts.base')

@section('body')
    <main class="mx-auto flex min-h-screen max-w-3xl flex-col items-center justify-center px-6 py-16">
        @yield('content')
    </main>
@endsection
