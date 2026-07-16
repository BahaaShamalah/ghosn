@php
    $meta = $meta ?? \App\Support\SeoSettings::meta();
@endphp
<meta name="description" content="{{ e($meta['description']) }}">
<meta name="robots" content="{{ e($meta['robots'] ?? 'index,follow') }}">
<link rel="canonical" href="{{ e($meta['canonical'] ?? $meta['url']) }}">
<meta property="og:type" content="{{ e($meta['type']) }}">
<meta property="og:site_name" content="{{ e($meta['site_name']) }}">
<meta property="og:title" content="{{ e($meta['title']) }}">
<meta property="og:description" content="{{ e($meta['description']) }}">
<meta property="og:url" content="{{ e($meta['url']) }}">
<meta property="og:locale" content="{{ e($meta['locale']) }}">
<meta property="og:locale:alternate" content="{{ e($meta['locale_alternate']) }}">
@if ($meta['image'])
    <meta property="og:image" content="{{ e($meta['image']) }}">
    <meta property="og:image:secure_url" content="{{ e($meta['image']) }}">
@endif
<meta name="twitter:card" content="{{ $meta['image'] ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ e($meta['title']) }}">
<meta name="twitter:description" content="{{ e($meta['description']) }}">
@if (! empty($meta['twitter_site']))
    <meta name="twitter:site" content="{{ e($meta['twitter_site']) }}">
@endif
@if ($meta['image'])
    <meta name="twitter:image" content="{{ e($meta['image']) }}">
@endif
@include('public.partials.json-ld', ['graphs' => $meta['json_ld'] ?? []])
