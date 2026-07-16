<?php

/**
 * Build lang/ar/admin.php from en structure + Arabic overrides.
 * Run: php scripts/build-admin-ar.php
 */

$en = require __DIR__.'/../lang/en/admin.php';

// Flatten existing partial ar (manually merged from prior file + new translations)
$ar = require __DIR__.'/admin-ar-overrides.php';

function mergeLocale(array $enTree, array $arTree): array
{
    $merged = [];

    foreach ($enTree as $key => $value) {
        if (is_array($value)) {
            $childAr = is_array($arTree[$key] ?? null) ? $arTree[$key] : [];
            $merged[$key] = mergeLocale($value, $childAr);

            continue;
        }

        $merged[$key] = $arTree[$key] ?? $value;
    }

    return $merged;
}

$merged = mergeLocale($en, $ar);

$export = var_export($merged, true);
$export = preg_replace('/^(\s*)array \(/m', '$1[', $export);
$export = preg_replace('/\)$/m', '];', $export);
$export = str_replace('array (', '[', $export);
$export = preg_replace('/\),/', '],', $export);

$content = "<?php\n\nreturn ".$export.";\n";

file_put_contents(__DIR__.'/../lang/ar/admin.php', $content);

$check = require __DIR__.'/../lang/ar/admin.php';
$missing = [];

function walkMissing(array $a, array $b, string $prefix = ''): void
{
    global $missing;

    foreach ($a as $key => $value) {
        $path = $prefix === '' ? $key : "{$prefix}.{$key}";

        if (! array_key_exists($key, $b)) {
            $missing[] = $path;

            continue;
        }

        if (is_array($value) && is_array($b[$key])) {
            walkMissing($value, $b[$key], $path);
        }
    }
}

walkMissing($en, $check);

echo 'Written lang/ar/admin.php'.PHP_EOL;
echo 'Missing keys: '.count($missing).PHP_EOL;

if ($missing !== []) {
    echo implode(PHP_EOL, array_slice($missing, 0, 20)).PHP_EOL;
    exit(1);
}
