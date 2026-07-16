<?php

$en = require __DIR__.'/../lang/en/admin.php';
$ar = require __DIR__.'/../lang/ar/admin.php';

function walk(array $a, array $b, string $prefix = ''): array
{
    $missing = [];

    foreach ($a as $key => $value) {
        $path = $prefix === '' ? $key : "{$prefix}.{$key}";

        if (! array_key_exists($key, $b)) {
            $missing[] = $path;

            continue;
        }

        if (is_array($value) && is_array($b[$key])) {
            $missing = array_merge($missing, walk($value, $b[$key], $path));
        }
    }

    return $missing;
}

$missing = walk($en, $ar);

echo 'Missing in AR: '.count($missing).PHP_EOL;

foreach ($missing as $path) {
    echo $path.PHP_EOL;
}
