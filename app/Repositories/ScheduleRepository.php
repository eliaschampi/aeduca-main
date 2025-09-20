<?php

namespace App\Repositories;

use App\Models\Schedule;

class ScheduleRepository extends BaseRepository
{
    private function fetchAll($callback)
    {
        return Schedule::with(["op", "op.course"])
            ->whereHas("op", $callback)
            ->orderBy("day")
            ->get();
    }

    public function fetchMain(string $section_code)
    {
        return $this->fetchAll(fn ($q) => $q->whereRaw("'$section_code|all' ~* any(sts)"));
    }

    public function fetchByTeacher(string $teacher_dni)
    {
        return $this->fetchAll(fn ($q) => $q->where("teacher_dni", $teacher_dni));
    }

    public function store(array $data): Schedule
    {
        return Schedule::create($data);
    }

    public function update(array $data, int $code): bool
    {
        $schedule = Schedule::find($code);
        $schedule->op_code = $data["op_code"];
        $schedule->from_time = $data["from_time"];
        $schedule->to_time = $data["to_time"];
        $schedule->day = $data["day"];
        return $schedule->save();
    }

    public function destroy(int $code): bool
    {
        $schedule = Schedule::find($code);
        $op = $schedule->op;
        $deleted = $schedule->delete();
        if ($deleted && $op->schedules->count() === 0) {
            return $op->delete();
        }
        return $deleted;
    }

}
