@extends('public.layouts.app')

@section('content')
    <div class="public-content-shell pb-12 md:pb-20">
        @yield('content-body')
    </div>
@endsection

@push('scripts')
    @stack('content-scripts')
@endpush
