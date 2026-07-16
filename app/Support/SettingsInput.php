<?php

namespace App\Support;

class SettingsInput
{
    /**
     * Flatten nested request arrays to dot-notation keys used by SettingsService.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function flatten(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (in_array($key, ['_token', '_method', 'group'], true)) {
                continue;
            }

            if (is_array($value) && ! array_is_list($value)) {
                foreach (self::flatten($value) as $nestedKey => $nestedValue) {
                    $result[$key.'.'.$nestedKey] = $nestedValue;
                }

                continue;
            }

            $result[(string) $key] = $value;
        }

        return $result;
    }

    /**
     * @return array<int, string>
     */
    public static function keysForGroup(string $group): array
    {
        return config("settings.groups.{$group}.keys", []);
    }
}
