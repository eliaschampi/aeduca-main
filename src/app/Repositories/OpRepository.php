<?php

namespace App\Repositories;

use App\Models\Op;
use Illuminate\Support\Facades\DB;

class OpRepository
{
    public function fetchBySection(string $section_code)
    {
        return Op::with([
            "person" => function ($query) {
                return $query->select("dni", "name", "lastname");
            },
            "person.profile" => function ($query) {
                return $query->select("person_dni", "image");
            },
            "course"
        ])
        ->whereRaw("'$section_code|all' ~* any(sts)")
        ->orderBy("code", "DESC")
        ->get();
    }

    public function store(array $data): void
    {
        Op::create([
             "teacher_dni" => $data["teacher_dni"],
             "course_code" => $data["course_code"],
             "sts" => $data["sts"],
         ]);
    }

    public function update(int $code, array $data)
    {
        $op = Op::find($code);
        $op->teacher_dni = $data["teacher_dni"];
        $op->course_code = $data["course_code"];
        $op->sts = $data["sts"];
        return $op->save();
    }

    public function destroy(string $code)
    {
        DB::table("unit")
            ->where("op_code", $code)
            ->delete();
        return Op::where("code", $code)->delete();
    }
}
