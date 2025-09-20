<?php

namespace App\Repositories;

use App\Models\Degree;

class DegreeRepository extends BaseRepository
{
    public function fetchByCycle(string $cycle_code)
    {
        return Degree::where("cycle_code", $cycle_code)->get();
    }

    public function fetchByCode(string $code)
    {
        if (strlen($code) !== 9 || (int) substr($code, 7, 1) !== $this->branch_code) {
            return null;
        }

        return Degree::with([
            "cycle" => function ($query) {
                $query->select("code", "title", "type", "monthly");
            }
        ])
            ->withCount("sections")
            ->find($code);
    }
}
