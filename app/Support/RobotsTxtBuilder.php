<?php

namespace App\Support;

use App\Services\Settings\SettingsService;

class RobotsTxtBuilder
{
    public static function defaultConfig(): array
    {
        return [
            'user_agent' => '*',
            'allow' => [],
            'disallow' => ['/admin', '/admin/'],
            'host' => '',
            'sitemap_url' => url('/sitemap.xml'),
            'extra' => '',
        ];
    }

    /**
     * @return array{user_agent: string, allow: list<string>, disallow: list<string>, host: string, sitemap_url: string, extra: string}
     */
    public static function config(): array
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $stored = $settings->get('seo.robots_txt');
        $defaults = self::defaultConfig();

        if (! is_array($stored)) {
            return $defaults;
        }

        return [
            'user_agent' => trim((string) ($stored['user_agent'] ?? $defaults['user_agent'])) ?: '*',
            'allow' => self::lines($stored['allow'] ?? []),
            'disallow' => self::lines($stored['disallow'] ?? $defaults['disallow']),
            'host' => trim((string) ($stored['host'] ?? '')),
            'sitemap_url' => trim((string) ($stored['sitemap_url'] ?? $defaults['sitemap_url'])) ?: $defaults['sitemap_url'],
            'extra' => trim((string) ($stored['extra'] ?? '')),
        ];
    }

    public static function render(): string
    {
        $config = self::config();
        $lines = [
            'User-agent: '.$config['user_agent'],
        ];

        foreach ($config['allow'] as $rule) {
            $lines[] = 'Allow: '.$rule;
        }

        foreach ($config['disallow'] as $rule) {
            $lines[] = 'Disallow: '.$rule;
        }

        if ($config['host'] !== '') {
            $lines[] = 'Host: '.$config['host'];
        }

        if ($config['sitemap_url'] !== '') {
            $lines[] = 'Sitemap: '.$config['sitemap_url'];
        }

        if ($config['extra'] !== '') {
            $lines[] = '';
            $lines[] = $config['extra'];
        }

        return implode("\n", $lines)."\n";
    }

    /**
     * @param  mixed  $value
     * @return list<string>
     */
    public static function lines(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/\r\n|\r|\n/', $value) ?: [];
        }

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($line): string => trim((string) $line),
            $value,
        ), static fn (string $line): bool => $line !== ''));
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{user_agent: string, allow: list<string>, disallow: list<string>, host: string, sitemap_url: string, extra: string}
     */
    public static function normalize(array $input): array
    {
        $defaults = self::defaultConfig();

        return [
            'user_agent' => trim((string) ($input['user_agent'] ?? '*')) ?: '*',
            'allow' => self::lines($input['allow'] ?? []),
            'disallow' => self::lines($input['disallow'] ?? $defaults['disallow']),
            'host' => trim((string) ($input['host'] ?? '')),
            'sitemap_url' => trim((string) ($input['sitemap_url'] ?? '')) ?: $defaults['sitemap_url'],
            'extra' => trim((string) ($input['extra'] ?? '')),
        ];
    }
}
