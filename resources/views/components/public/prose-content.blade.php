@props([
    'contentEn' => '',
    'contentAr' => '',
])

<div {{ $attributes->merge(['class' => 'prose-ghosn']) }}>
    <div data-en="">{!! \App\Support\ContentHtml::render($contentEn) !!}</div>
    <div data-ar="" dir="rtl">{!! \App\Support\ContentHtml::render($contentAr) !!}</div>
</div>
