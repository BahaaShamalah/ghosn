<?php

/**
 * Generate resources/css/ghosn/landing.css from the extracted landing reference.
 * Run: php scripts/generate-landing-styles-css.php
 */
$root = dirname(__DIR__);
$reference = $root.'/storage/app/_extracted_landing.html';

if (! is_readable($reference)) {
    fwrite(STDERR, "Missing reference file: {$reference}\n");
    exit(1);
}

$html = file_get_contents($reference);

if (! preg_match('/<script src="https:\/\/cdn\.tailwindcss\.com"><\/script>\s*<script>.*?<\/script>\s*<style>(.*?)<\/style>/s', $html, $match)) {
    fwrite(STDERR, "Could not locate landing component styles.\n");
    exit(1);
}

$css = trim($match[1]);
$css = preg_replace('/\s*body\s*\{[^}]+\}/', '', $css);

$header = <<<'CSS'
/* GHOSN Relief — landing component styles (animations, layout helpers) */

CSS;

$targetDir = $root.'/resources/css/ghosn';
if (! is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$target = $targetDir.'/landing.css';
file_put_contents($target, $header.$css."\n");
echo "Wrote {$target}\n";
