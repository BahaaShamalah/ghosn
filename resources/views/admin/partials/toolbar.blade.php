@props(['action' => null, 'method' => 'GET', 'class' => ''])

<form method="{{ $method }}" action="{{ $action ?? url()->current() }}" @class(['mb-6 flex flex-wrap items-center gap-3', $class])>
    @if ($method !== 'GET')
        @csrf
    @endif
    {{ $slot }}
</form>
