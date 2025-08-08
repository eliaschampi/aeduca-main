<?php

namespace App\Repositories;

use App\Jobs\TrackingUserActivity;
use App\Models\Section;
use Illuminate\Support\Facades\DB;

class SectionRepository extends BaseRepository
{
    public function fetchSumary(string $cycle_code)
    {
        return Section::select(
            "section.*",
            DB::raw("(select count(*) from register where section_code = section.code and state != 'i') as rcount")
        )
            ->orderBy("code", "ASC")
            ->where("code", "like", $cycle_code . "__")
            ->get();
    }

    public function fetchByYearAndBranch()
    {
        return Section::with([
            "degree.cycle" => function ($query) {
                $query->select("code", "type", "title", "monthly");
            }
        ])
            ->whereHas("degree.cycle", function ($query) {
                $query
                    ->where("code", "like", $this->current_year . "___" . $this->branch_code)
                    ->whereRaw('"to" > current_date');
            })
            ->orderBy("code", "ASC")
            ->get();
    }

    public function fetchByDegree(string $d_code)
    {
        return Section::where("degree_code", $d_code)
            ->withCount("registers")
            ->orderBy("code", "ASC")
            ->get();
    }

    public function store(array $section): Section
    {
        return Section::create($section);

    }

    public function changeTutor(string $section_code, string $tutor)
    {
        $section = Section::find($section_code);
        $section->tutor = $tutor;
        return $section->save();
    }

    public function destroy(string $code): bool
    {
        $section = Section::find($code);
        if (isset($section->student_dni)) {
            $section->student_dni = null;
            $section->save();
        }
        $message = "Sección o grupo: " . $code;
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "delete", $message);
        return $section->delete();
    }
}
