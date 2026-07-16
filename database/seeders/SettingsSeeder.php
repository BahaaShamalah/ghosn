<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\Settings\SettingsService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('settings.keys', []) as $key => $meta) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'type' => $meta['type'] ?? 'string',
                    'value' => $this->seedValue($meta),
                ],
            );
        }

        app(SettingsService::class)->clearCache();
    }

    /**
     * @param  array{type?: string, default?: mixed}  $meta
     */
    private function seedValue(array $meta): ?string
    {
        $value = $meta['default'] ?? null;
        $type = $meta['type'] ?? 'string';

        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'integer' => $value === null ? null : (string) (int) $value,
            'json' => json_encode($value, JSON_THROW_ON_ERROR),
            default => (string) $value,
        };
    }
}
