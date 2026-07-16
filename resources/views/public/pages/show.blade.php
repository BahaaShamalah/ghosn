@extends('public.layouts.content')

@php
    $locale = app()->getLocale();
@endphp

@section('title', $page->localizedSeoTitle($locale))

@include('public.partials.cms-meta', ['model' => $page])

@section('content-body')
    <x-public.official-page-layout
        :title-en="$page->title_en"
        :title-ar="$page->title_ar"
        :subtitle-en="$page->seo_description_en"
        :subtitle-ar="$page->seo_description_ar"
        :featured-image-url="$page->featuredImage?->url()"
        :content-en="$page->content_en"
        :content-ar="$page->content_ar"
        :preview="$preview ?? false"
        :updated-at="$page->updated_at"
    />
@endsection
