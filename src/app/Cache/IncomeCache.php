<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

class IncomeCache
{
    private static function cacheName(): string
    {
        $user = auth()->user();
        return "income_detail_" . $user->code . "_" . $user->branch_code;
    }

    public static function fetchFromCache()
    {
        return Cache::get(self::cacheName(), []);
    }

    public static function forget(): void
    {
        Cache::forget(self::cacheName());
    }

    public static function put(array $data): void
    {
        Cache::put(self::cacheName(), $data, 3600);
    }

    public static function set(array $data): void
    {
        Cache::set(self::cacheName(), $data, 3600);
    }
}
