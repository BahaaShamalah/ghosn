<?php

namespace App\Support;

class SettingsHub
{
    /**
     * @return array<int, array{slug: string, icon: string, editable: bool}>
     */
    public static function cards(): array
    {
        $order = config('settings.hub_order', []);
        $definitions = config('settings.hub_cards', []);

        $cards = [];

        foreach ($order as $slug) {
            if (! isset($definitions[$slug])) {
                continue;
            }

            $cards[] = array_merge(['slug' => $slug], $definitions[$slug]);
        }

        return $cards;
    }

    public static function exists(string $group): bool
    {
        return in_array($group, config('settings.hub_order', []), true);
    }

    public static function isEditable(string $group): bool
    {
        return (bool) config("settings.hub_cards.{$group}.editable", true);
    }
}
