<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

class DegreeCache
{
    public static function fetchByCycle(string $cycle_code, \Closure $callback)
    {
        $key = "degree_for_" . $cycle_code;
        return Cache::rememberForever($key, function () use ($callback, $cycle_code) {
            return $callback($cycle_code);
        });
    }
}
