@php
    $platform = $platform ?? 'website';
    $class = $class ?? '';
    $iconClass = \App\Support\SocialPlatform::iconClass($platform);
@endphp

<i class="{{ trim($iconClass.' '.$class) }}" aria-hidden="true"></i>
