<?php

namespace App\Cache;

use App\Decorators\SectionDecorator;
use App\Helpers\MainHelper;
use Illuminate\Support\Facades\Cache;

class SectionCache
{
    public static function fetchByBranch()
    {
        $branch_code = MainHelper::branchCode();
        return Cache::rememberForever("sc_$branch_code", function () {
            $sectionDecorator = new SectionDecorator();
            return $sectionDecorator->decoratedSections();
        });
    }

    public static function clear(): bool
    {
        $branch_code = MainHelper::branchCode();
        return Cache::forget("sc_$branch_code");
    }
}
