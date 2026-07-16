<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExtractLandingAssetsCommand extends Command
{
    protected $signature = 'landing:extract-assets
                            {--source= : Path to the bundled landing HTML file}
                            {--force : Overwrite existing extracted files}';

    protected $description = 'Extract fonts, images, and scripts from GHOSN-Relief-Landing.html';

    /**
     * Friendly filenames for known bundle asset UUIDs.
     *
     * @var array<string, string>
     */
    private array $knownAssets = [
        '6f048ee1-844c-4971-8a08-8cd13f7977cd' => 'images/logo.webp',
        '7f0b2495-add8-4e5b-aec6-dab3f59124fc' => 'fonts/cairo-arabic.woff2',
        'bfa5a0ed-5550-4a32-a30f-9ce9938c9f32' => 'fonts/cairo-latin-ext.woff2',
        '6b44d5b1-cf8e-4b6a-8065-0135022ce0d2' => 'fonts/cairo-latin.woff2',
        'ea3e1fdd-0336-464a-8ca9-4343a793562a' => 'fonts/montserrat-cyrillic-ext.woff2',
        'fa675537-ae48-42e3-bfb7-b4c5f6de7f05' => 'fonts/montserrat-cyrillic.woff2',
        'eb87709e-7665-4409-9e05-4e9c7f691b83' => 'fonts/montserrat-vietnamese.woff2',
        '53716849-989a-4815-99ce-e626eefd5f3e' => 'fonts/montserrat-latin-ext.woff2',
        '6c5ae955-d2e6-481e-9448-0d2ec2416bd1' => 'fonts/montserrat-latin.woff2',
        '44a6bca9-b038-43f5-984c-8d691a86830f' => 'scripts/bundler-runtime.js',
    ];

    public function handle(): int
    {
        $source = $this->option('source') ?? base_path('GHOSN-Relief-Landing.html');

        if (! is_readable($source)) {
            $this->error("Source file not found: {$source}");

            return self::FAILURE;
        }

        $html = file_get_contents($source);

        if (! preg_match('/<script type="__bundler\/manifest">(.*?)<\/script>/s', $html, $matches)) {
            $this->error('Could not locate __bundler/manifest in the source file.');

            return self::FAILURE;
        }

        /** @var array<string, array{mime: string, data: string, compressed?: bool}> $manifest */
        $manifest = json_decode($matches[1], true);

        if (! is_array($manifest)) {
            $this->error('Invalid manifest JSON.');

            return self::FAILURE;
        }

        $outputDir = public_path('assets/landing');
        File::ensureDirectoryExists($outputDir.'/fonts');
        File::ensureDirectoryExists($outputDir.'/images');
        File::ensureDirectoryExists($outputDir.'/scripts');

        $written = [];
        $skipped = 0;

        foreach ($manifest as $uuid => $entry) {
            if (! isset($entry['data'], $entry['mime'])) {
                continue;
            }

            $relativePath = $this->knownAssets[$uuid] ?? $this->guessPath($uuid, $entry['mime']);
            $target = $outputDir.'/'.$relativePath;

            if (file_exists($target) && ! $this->option('force')) {
                $skipped++;
                $written[$uuid] = 'assets/landing/'.$relativePath;

                continue;
            }

            File::ensureDirectoryExists(dirname($target));

            $bytes = base64_decode($entry['data'], true);

            if ($bytes === false) {
                $this->warn("Skipping {$uuid}: invalid base64 payload.");

                continue;
            }

            if (! empty($entry['compressed'])) {
                $decoded = gzdecode($bytes);
                if ($decoded === false) {
                    $this->warn("Skipping {$uuid}: gzip decode failed.");

                    continue;
                }
                $bytes = $decoded;
            }

            file_put_contents($target, $bytes);
            $written[$uuid] = 'assets/landing/'.$relativePath;
            $this->line("  ✓ {$relativePath}");
        }

        $mapPath = $outputDir.'/manifest.json';
        $existingMap = is_readable($mapPath)
            ? json_decode(file_get_contents($mapPath), true) ?? []
            : [];

        $map = array_merge($existingMap, $written);
        ksort($map);
        file_put_contents($mapPath, json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->newLine();
        $this->regenerateStylesheets();

        $extracted = count(array_filter($written, fn ($path, $uuid) => ! isset($existingMap[$uuid]) || $this->option('force'), ARRAY_FILTER_USE_BOTH));

        $this->info(sprintf(
            'Extracted %d asset(s), skipped %d existing file(s).',
            max(0, $extracted),
            $skipped,
        ));
        $this->info("Manifest: {$mapPath}");

        return self::SUCCESS;
    }

    private function regenerateStylesheets(): void
    {
        $this->ensureExtractedReference();

        $scripts = [
            base_path('scripts/generate-landing-fonts-css.php'),
            base_path('scripts/generate-landing-styles-css.php'),
        ];

        foreach ($scripts as $script) {
            if (! is_readable($script)) {
                $this->warn("Stylesheet generator missing: {$script}");

                continue;
            }

            passthru(PHP_BINARY.' '.escapeshellarg($script), $exitCode);

            if ($exitCode !== 0) {
                $this->warn("Stylesheet generator failed: {$script}");
            }
        }
    }

    private function ensureExtractedReference(): void
    {
        $reference = storage_path('app/_extracted_landing.html');
        $source = $this->option('source') ?? base_path('GHOSN-Relief-Landing.html');

        if (is_readable($reference)) {
            return;
        }

        $html = file_get_contents($source);

        if (! preg_match('/<script type="__bundler\/template">(.*?)<\/script>/s', $html, $matches)) {
            return;
        }

        $data = json_decode($matches[1], true);
        $page = $data['pages']['GHOSN Relief.dc'] ?? reset($data['pages']);

        if (is_string($page)) {
            File::put($reference, $page);
            $this->line('  ✓ storage/app/_extracted_landing.html');
        }
    }

    private function guessPath(string $uuid, string $mime): string
    {
        $extension = match (true) {
            str_contains($mime, 'woff2') => 'woff2',
            str_contains($mime, 'woff') => 'woff',
            str_contains($mime, 'png') => 'png',
            str_contains($mime, 'jpeg'), str_contains($mime, 'jpg') => 'jpg',
            str_contains($mime, 'webp') => 'webp',
            str_contains($mime, 'svg') => 'svg',
            str_contains($mime, 'javascript') => 'js',
            default => 'bin',
        };

        $folder = match ($extension) {
            'woff2', 'woff' => 'fonts',
            'js' => 'scripts',
            default => 'images',
        };

        return "{$folder}/{$uuid}.{$extension}";
    }
}
