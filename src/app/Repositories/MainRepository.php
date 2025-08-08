<?php

namespace App\Repositories;

class MainRepository
{
    public function fetchWithLCRbyReg(int $lesson_code, string $register_code)
    {
        return \DB::table("lesson")->where("code", $lesson_code)
            ->join("calification", "lesson.code", "calification.lesson_code")
            ->join("score", function ($query) use ($register_code) {
                $query->on("score.lesson_code", "lesson.code")
                    ->where("register_code", $register_code);
            })
            ->select("lesson.*", "calification.keys as mkeys", "calification.exam_day as exam_day", "score.*")
            ->first();
    }

    public function fetchForScoreReport(string $section_code, string $register_code)
    {
        return \DB::table("calification")->select([
            "calification.exam_day",
            "lesson.code",
            "lesson.title",
            "course.name",
            "score.score",
            "score.obs"
        ])
            ->join("lesson", "lesson.code", "calification.lesson_code")
            ->join("op", "op.code", "lesson.op_code")
            ->join("course", "course.code", "op.course_code")
            ->leftJoin("score", function ($query) use ($register_code) {
                $query->on("score.lesson_code", "lesson.code")
                    ->where("score.register_code", $register_code);
            })
            ->whereRaw("'$section_code|all' ~* any(op.sts)")
            ->orderBy("lesson.created_at", "DESC")
            ->get();
    }
}
