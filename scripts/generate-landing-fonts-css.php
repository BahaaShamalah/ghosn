<?php

/**
 * Generate resources/css/ghosn/fonts.css from the extracted landing reference.
 * Run: php scripts/generate-landing-fonts-css.php
 */
$root = dirname(__DIR__);
$reference = $root.'/storage/app/_extracted_landing.html';

if (! is_readable($reference)) {
    fwrite(STDERR, "Missing reference file: {$reference}\n");
    exit(1);
}

$html = file_get_contents($reference);

if (! preg_match('/<style>\/\* arabic \*\/(.*?<\/style>)/s', $html, $match)) {
    fwrite(STDERR, "Could not locate font-face style block.\n");
    exit(1);
}

$css = $match[1];
$css = preg_replace('/<\/style>\s*$/', '', $css);

$replacements = [
    '7f0b2495-add8-4e5b-aec6-dab3f59124fc' => '/assets/landing/fonts/cairo-arabic.woff2',
    'bfa5a0ed-5550-4a32-a30f-9ce9938c9f32' => '/assets/landing/fonts/cairo-latin-ext.woff2',
    '6b44d5b1-cf8e-4b6a-8065-0135022ce0d2' => '/assets/landing/fonts/cairo-latin.woff2',
    'ea3e1fdd-0336-464a-8ca9-4343a793562a' => '/assets/landing/fonts/montserrat-cyrillic-ext.woff2',
    'fa675537-ae48-42e3-bfb7-b4c5f6de7f05' => '/assets/landing/fonts/montserrat-cyrillic.woff2',
    'eb87709e-7665-4409-9e05-4e9c7f691b83' => '/assets/landing/fonts/montserrat-vietnamese.woff2',
    '53716849-989a-4815-99ce-e626eefd5f3e' => '/assets/landing/fonts/montserrat-latin-ext.woff2',
    '6c5ae955-d2e6-481e-9448-0d2ec2416bd1' => '/assets/landing/fonts/montserrat-latin.woff2',
];

foreach ($replacements as $uuid => $path) {
    $css = str_replace('url("'.$uuid.'")', 'url("'.$path.'")', $css);
}

$header = <<<'CSS'
/* GHOSN Relief — self-hosted fonts extracted from GHOSN-Relief-Landing.html */

CSS;

$targetDir = $root.'/resources/css/ghosn';
if (! is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$target = $targetDir.'/fonts.css';
file_put_contents($target, $header.$css);
echo "Wrote {$target}\n";
