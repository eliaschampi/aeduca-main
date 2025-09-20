<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

class CashCache
{
    private static function cacheName(): string
    {
        $user = auth()->user();
        return "cash_" . date("Ymd") . "_" . $user->code . "_" . $user->branch_code;
    }
    public static function getCash(\Closure $callback)
    {
        return Cache::rememberForever(self::cacheName(), $callback);
    }

    public static function forgetCache(): bool
    {
        $name = self::cacheName();
        return Cache::forget($name);
    }
}
