<?php

namespace App\Support;

class LandingAsset
{
    public static function path(string $key): string
    {
        $path = config("landing.assets.{$key}");

        if (! is_string($path) || $path === '') {
            throw new \InvalidArgumentException("Unknown landing asset key: {$key}");
        }

        return $path;
    }

    public static function url(string $key): string
    {
        return asset(self::path($key));
    }
}
