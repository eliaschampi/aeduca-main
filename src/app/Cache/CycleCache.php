<?php

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

class CycleCache
{
    public static function attendanceVariablesShouldBeCached(string $cycle_code): mixed
    {
        $key = "cycle_att_" . $cycle_code;
        return Cache::rememberForever($key, function () use ($cycle_code) {
            return call_user_func("\App\Repositories\CycleRepository::fetchAttendaceVariablesByCode", $cycle_code);
        });
    }

    public static function forgetAttendanceVariables(string $cycle_code): bool
    {
        $key = "cycle_att_" . $cycle_code;
        return Cache::forget($key);
    }
}
