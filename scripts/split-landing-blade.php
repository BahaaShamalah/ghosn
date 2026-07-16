<?php

/**
 * Split storage/app/_extracted_landing.html into Blade section partials.
 * Run: php scripts/split-landing-blade.php
 */
$root = dirname(__DIR__);
$reference = $root.'/storage/app/_extracted_landing.html';

if (! is_readable($reference)) {
    fwrite(STDERR, "Missing {$reference}. Run: php artisan landing:extract-assets\n");
    exit(1);
}

$html = file_get_contents($reference);

if (! preg_match('/<div id="ghosn-root"[^>]*>(.*)<\/div>\s*<\/x-dc>/s', $html, $rootMatch)) {
    fwrite(STDERR, "Could not locate #ghosn-root content.\n");
    exit(1);
}

$inner = $rootMatch[1];

$sections = [
    'header' => '/<!-- ===================== NAV ===================== -->(.*?)<!-- ===================== HERO/s',
    'hero' => '/<!-- ===================== HERO ===================== -->(.*?)(<!-- ===================== ABOUT ===================== -->)/s',
    'about' => '/(<section id="about".*?<\/section>)/s',
    'vision' => '/(<section id="vision".*?<\/section>)/s',
    'work' => '/(<section id="work".*?<\/section>)/s',
    'values' => '/(<section id="values".*?<\/section>)/s',
    'goals' => '/(<section id="goals".*?<\/section>)/s',
    'groups' => '/(<section id="groups".*?<\/section>)/s',
    'support' => '/(<section id="support".*?<\/section>)/s',
    'footer' => '/(<!-- ===================== CONTACT \/ FOOTER ===================== -->.*)/s',
];

$outDir = $root.'/resources/views/public';
$dirs = [
    $outDir.'/partials',
    $outDir.'/sections',
    $outDir.'/layouts',
    $outDir.'/home',
];

foreach ($dirs as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

function transformChunk(string $chunk): string
{
    $chunk = str_replace(
        'src="6f048ee1-844c-4971-8a08-8cd13f7977cd"',
        'src="{{ \\App\\Support\\LandingAsset::url(\'logo\') }}"',
        $chunk
    );

    // Preserve empty data-en/data-ar attributes from reference.
    return trim($chunk);
}

foreach ($sections as $name => $pattern) {
    if (! preg_match($pattern, $inner, $match)) {
        fwrite(STDERR, "Failed to match section: {$name}\n");
        exit(1);
    }

    $content = transformChunk($match[1]);

    if ($name === 'hero') {
        $content = trim($content);
    }

    if ($name === 'header') {
        $path = $outDir.'/partials/header.blade.php';
    } elseif ($name === 'footer') {
        $path = $outDir.'/partials/footer.blade.php';
    } else {
        $path = $outDir.'/sections/'.$name.'.blade.php';
    }

    file_put_contents($path, $content."\n");
    echo "Wrote {$path}\n";
}

$layout = <<<'BLADE'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\LocaleHelper::direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-offwhite antialiased">
    <div
        id="ghosn-root"
        class="bg-offwhite text-ghosn-ink overflow-x-hidden"
        data-lang="{{ app()->getLocale() }}"
        data-accent="{{ config('landing.default_accent') }}"
        data-motion="{{ config('landing.default_motion') }}"
    >
        @include('public.partials.header')

        @yield('content')

        @include('public.partials.footer')
    </div>
</body>
</html>
BLADE;

file_put_contents($outDir.'/layouts/app.blade.php', $layout);
echo "Wrote {$outDir}/layouts/app.blade.php\n";

$home = <<<'BLADE'
@extends('public.layouts.app')

@section('title', __('public.home.title').' — '.config('app.name'))

@section('content')
    @include('public.sections.hero')
    @include('public.sections.about')
    @include('public.sections.vision')
    @include('public.sections.work')
    @include('public.sections.values')
    @include('public.sections.goals')
    @include('public.sections.groups')
    @include('public.sections.support')
@endsection
BLADE;

file_put_contents($outDir.'/home/index.blade.php', $home);
echo "Wrote {$outDir}/home/index.blade.php\n";

echo "Done.\n";
