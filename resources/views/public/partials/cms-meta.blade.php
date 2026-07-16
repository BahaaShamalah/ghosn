@php
    $locale = app()->getLocale();
    $meta = \App\Support\SeoSettings::meta($locale, [
        'title' => $model->localizedSeoTitle($locale),
        'description' => $model->localizedSeoDescription($locale),
        'image' => $model->featuredImage?->url(),
        'type' => 'article',
    ]);
@endphp
@push('meta')
    @include('public.partials.social-meta', ['meta' => $meta])
@endpush
