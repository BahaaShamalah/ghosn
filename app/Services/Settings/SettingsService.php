<?php

namespace App\Services\Settings;

use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_KEY = 'site.settings.all';

    public function all(): array
    {
        try {
            $stored = Cache::rememberForever(self::CACHE_KEY, fn () => Setting::query()->pluck('value', 'key')->all());
        } catch (QueryException) {
            $stored = [];
        }

        return $this->mergeStoredWithDefaults($stored);
    }

    /**
     * @param  array<string, string|null>  $stored
     * @return array<string, mixed>
     */
    private function mergeStoredWithDefaults(array $stored): array
    {
        $merged = [];

        foreach (config('settings.keys', []) as $key => $meta) {
            $merged[$key] = $this->castValue(
                $stored[$key] ?? null,
                $meta['type'] ?? 'string',
                $meta['default'] ?? null,
            );
        }

        return $merged;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $meta = config('settings.keys', [])[$key] ?? ['type' => 'string'];
        $type = $meta['type'] ?? 'string';

        Setting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $this->serializeValue($value, $type),
                'type' => $type,
            ],
        );

        $this->clearCache();
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            if (! array_key_exists($key, config('settings.keys', []))) {
                continue;
            }

            $this->set($key, $value);
        }
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function castValue(mixed $value, string $type, mixed $default): mixed
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json' => json_decode((string) $value, true) ?? $default,
            default => (string) $value,
        };
    }

    private function serializeValue(mixed $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'integer' => (string) (int) $value,
            'json' => json_encode($value, JSON_THROW_ON_ERROR),
            default => (string) $value,
        };
    }
}
