<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\Justification;

class JustificationRepository
{
    private function fetchAttendance(int $code): ?Attendance
    {
        return (new AttendanceRepository())->fetchByCode($code);
    }

    public function fetchByEntity(string $dni)
    {
        return Justification::whereHas("attendance", function ($query) use ($dni) {
            $query->where("entity_identifier", $dni)
                ->whereYear("created_at", date("Y"));
        })->with("attendance")->get();
    }

    public function store(int $attendance_code, string $fileName, string $description)
    {
        $attendance = $this->fetchAttendance($attendance_code);
        $attendance->justification()->create([
            "attached" => $fileName,
            "description" => $description,
        ]);
        $attendance->state = "justificado";
        return $attendance->save();
    }

    public function toggle(int $attendance_code, string $aprove, $deletefile): bool
    {
        $attendance = $this->fetchAttendance($attendance_code);
        if ($aprove === "yes") {
            $attendance->state = "justificado";
        } else {
            if ($deletefile($attendance->justification->attached)) {
                $attendance->justification()->delete();
                $attendance->state = "falta";
            }
        }
        return $attendance->save();
    }
}
