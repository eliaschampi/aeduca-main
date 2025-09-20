<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

class RegisterCache
{
    private static function cacheName(): string
    {
        $user = auth()->user();
        return "register_" . $user->code . "_" . $user->branch_code;
    }

    public static function setCache(array $data): void
    {
        Cache::put(self::cacheName(), $data, 3600);
    }

    public static function hasOnCache(): bool
    {
        return Cache::has(self::cacheName());
    }

    public static function fetchFromCache()
    {
        return Cache::get(self::cacheName(), false);
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::cacheName());
    }
}
