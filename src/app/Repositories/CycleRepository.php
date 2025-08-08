<?php

namespace App\Repositories;

use App\Jobs\TrackingUserActivity;
use App\Models\Cycle;

class CycleRepository extends BaseRepository
{
    public function fetchByCode(string $code): Cycle
    {
        return Cycle::findOrFail($code);
    }

    public function fetchLatestCode(string $cycle_type)
    {
        $year = now()->year;
        $count = Cycle::where("type", $cycle_type)->whereYear("created_at", $year)->count();
        return $year . $cycle_type . ($count + 1) . $this->branch_code;
    }

    public function fetchActives()
    {
        return Cycle::select("code", "type", "title", "branch_code")->where("to", ">", now())->get();
    }

    public static function fetchAttendaceVariablesByCode(string $code)
    {
        $cycle = Cycle::select("attendance")->find($code);

        if (!empty($cycle)) {
            $items = $cycle->attendance;
            return $items;
        }
        return [];
    }

    public function fetchCycles(int $branch_code)
    {
        return Cycle::whereYear("created_at", $this->current_year)
            ->where("branch_code", $branch_code === 0 ? $this->branch_code : $branch_code)
            ->latest()
            ->get();
    }

    public function store(array $cycle): Cycle
    {
        $cycle["branch_code"] = $this->branch_code;
        $message = "Ciclo academico " . $cycle["type"] . "-" . $cycle["title"];
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "create", $message);
        return Cycle::create($cycle);
    }

    public function update(array $cycledata, string $code)
    {
        $cycle = $this->fetchByCode($code);
        $cycle->from = $cycledata["from"];
        $cycle->to = $cycledata["to"];
        $cycle->title = $cycledata["title"];
        $cycle->modular_code = $cycledata["modular_code"];
        $cycle->monthly = $cycledata["monthly"];
        $cycle->attendance = $cycledata["attendance"];
        return $cycle->save();
    }

    public function finalize(string $code)
    {
        $cycle = $this->fetchByCode($code);
        $cycle->to = now();
        return $cycle->save();
    }
}
