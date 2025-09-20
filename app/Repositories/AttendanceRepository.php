<?php

namespace App\Repositories;

use App\Jobs\TrackingUserActivity;
use App\Models\Attendance;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceRepository extends BaseRepository
{
    public function fetchByCode($code): Attendance
    {
        return Attendance::find($code);
    }

    public function fetchBySectionAndDate(string $section_code, string $date, int $priority)
    {
        return Attendance::whereDate("created_at", $date)
            ->with([
                "justification",
                "person" => function ($query) {
                    $query->select("dni", "name", "lastname", "phone");
                }
            ])
            ->where("priority", $priority)
            ->whereHas("person.student.registers", function ($query) use ($section_code) {
                $query->where("section_code", $section_code);
            })
            ->orderBy("entry_time", "DESC")->get();
    }

    public function fetchForTeacherByDate(string $date)
    {
        return Attendance::whereDate("created_at", $date)
            ->with([
                "justification",
                "person" => function ($query) {
                    $query->select("dni", "name", "lastname", "phone");
                }
            ])
            ->where("priority", 1)
            ->where("branch_code", $this->branch_code)
            ->where("entity_type", "t")
            ->orderBy("entry_time", "DESC")->get();
    }

    public function todayIsAlreadyRegistered(string $entity_identifier, $priority): bool
    {
        return Attendance::where("entity_identifier", $entity_identifier)
            ->where("priority", $priority)
            ->whereDate("created_at", now())
            ->exists();
    }

    public function fetchByEntity(string $entity_identifier, string $from, string $to, int $priority)
    {
        return Attendance::whereBetween("created_at", [$from, $to])
            ->where("priority", $priority)
            ->where("entity_identifier", $entity_identifier)
            ->orderBy("created_at", "desc")
            ->with("justification")->get();
    }

    public function fetchForChart()
    {
        $year = $this->current_year;
        $branch = $this->branch_code;
        $now = now();
        $full = Carbon::create($year, $now->month, $now->day);
        $date = $now->month > 3 ? $full->subMonths(3)->format("Y-m-d") : $year . "-01-01";
        return Attendance::select(DB::raw("created_at::date as mday, count(*) as count"))
            ->havingRaw("entity_type = 's' and created_at::date >= '$date' and branch_code = $branch")
            ->orderByDesc("mday")
            ->groupBy("mday", "entity_type", "branch_code")->get();
    }

    public function absences(string $cycle, string $date, int $priority)
    {
        return Attendance::with([
            "person" => function ($query) {
                $query->select("dni", "name", "lastname", "phone");
            }
        ])
            ->select(["attendance.*", "register.section_code"])
            ->join("register", function ($join) use ($cycle) {
                $join->on("register.student_dni", "attendance.entity_identifier")
                    ->where("register.section_code", "like", $cycle . "__")
                    ->where("register.state", "a");
            })
            ->whereIn("attendance.state", ["tarde", "falta"])
            ->whereDate("attendance.created_at", $date)
            ->where("attendance.priority", $priority)
            ->orderBy("attendance.entry_time", "DESC")
            ->get();
    }

    public function store(string $entity_identifier, string $entity_type, string $state, string|null $time, int $priority): Attendance
    {
        return Attendance::create([
            "entity_identifier" => $entity_identifier,
            "entity_type" => $entity_type,
            "state" => $state,
            "entry_time" => $time,
            "priority" => $priority,
            "branch_code" => $this->branch_code,
        ]);
    }

    public function update(string|null $entry_time, string $state, $code): bool
    {
        $attendance = Attendance::find($code);
        $attendance->state = $state;
        if ($state === "permiso" || $state === "falta") {
            $attendance->entry_time = null;
        } else {
            $attendance->entry_time = $entry_time;
        }
        $message = "Registro de asistencia  " . $attendance->entity_identifier . " se modificó a " . $state;
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "change", $message);
        return $attendance->save();
    }

    public function upsertBeforeInpect(string $pdate, int $pdays, string $etype, string $dni)
    {
        if ($etype === "family") {
            return;
        }

        $pd = CarbonPeriod::since($pdate)->until($pdays . " day");

        foreach ($pd as $date) {
            if (!$date->isWeekend()) {
                if (!Attendance::where("entity_identifier", $dni)->whereDate("created_at", $date)->exists()) {
                    $attendance = new Attendance();
                    $attendance->timestamps = false;
                    $attendance->entity_identifier = $dni;
                    $attendance->entity_type = substr($etype, 0, 1);
                    $attendance->state = "permiso";
                    $attendance->entry_time = null;
                    $attendance->priority = 1;
                    $attendance->branch_code = $this->branch_code;
                    $attendance->created_at = $date;
                    $attendance->save();
                }
            }
        }

        Attendance::where("entity_identifier", $dni)
            ->whereBetween("created_at", [$pd->startDate, $pd->endDate])
            ->update([
                "state" => "permiso",
                "entry_time" => null,
            ]);
    }

    public function destroyInspecteds(string $dni, string $from_date, string|null $days): bool
    {
        $d = empty($days) ? "1" : $days;
        $to_date = date("Y-m-d", strtotime("$from_date + $d days"));
        return Attendance::where("entity_identifier", $dni)
            ->whereDate("created_at", ">=", now()->format("Y-m-d"))
            ->whereDate("created_at", "<=", $to_date)
            ->delete();
    }
}
