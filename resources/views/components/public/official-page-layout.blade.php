@props([
    'titleEn' => '',
    'titleAr' => '',
    'subtitleEn' => null,
    'subtitleAr' => null,
    'featuredImageUrl' => null,
    'contentEn' => '',
    'contentAr' => '',
    'preview' => false,
    'updatedAt' => null,
])

@include('public.components.official-page-layout', [
    'titleEn' => $titleEn,
    'titleAr' => $titleAr,
    'subtitleEn' => $subtitleEn,
    'subtitleAr' => $subtitleAr,
    'featuredImageUrl' => $featuredImageUrl,
    'contentEn' => $contentEn,
    'contentAr' => $contentAr,
    'preview' => $preview,
    'updatedAt' => $updatedAt,
])
