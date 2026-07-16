@props([
    'backUrl' => null,
    'backLabelEn' => '',
    'backLabelAr' => '',
    'titleEn' => '',
    'titleAr' => '',
    'eyebrow' => null,
    'subtitleEn' => null,
    'subtitleAr' => null,
])

@include('public.components.page-hero', [
    'backUrl' => $backUrl,
    'backLabelEn' => $backLabelEn,
    'backLabelAr' => $backLabelAr,
    'titleEn' => $titleEn,
    'titleAr' => $titleAr,
    'eyebrow' => $eyebrow,
    'subtitleEn' => $subtitleEn,
    'subtitleAr' => $subtitleAr,
    'meta' => isset($meta) ? $meta : null,
])
