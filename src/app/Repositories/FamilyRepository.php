<?php

namespace App\Repositories;

use App\Models\Family;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class FamilyRepository
{
    public function self(string $dni)
    {
        return Family::find($dni);
    }

    public function fetch(string $dni)
    {
        return Family::with("person")->findOrFail($dni);
    }

    public function search(string $name)
    {
        return Family::with(["person", "person.profile"])
            ->join("person", "family.dni", "=", "person.dni")
            ->orderByRaw("(person.name || ' ' || person.lastname) <-> '$name'")
            ->limit(6)
            ->get();
    }

    public function fetchFamilyDnisByStudent(string $dni)
    {
        return DB::table("family_student")
            ->where("student_dni", $dni)->pluck("family_dni")
            ->toArray();
    }

    public function fetchBySection(string $section_code)
    {
        return Family::with("person")->whereHas("students.registers", function ($query) use ($section_code) {
            $query->where("section_code", $section_code);
        })->withCount("students")->get();
    }

    public function store(array $family): Family
    {
        return DB::transaction(function () use ($family) {
            $personRepository = new PersonRepository();
            $personRepository->store($family);
            return Family::create($family["sub"]);
        });
    }

    public function update(array $family, string $dni): bool
    {
        return DB::transaction(function () use ($family, $dni) {
            $personRepository = new PersonRepository();
            $personRepository->update($family, $dni);
            $fam = Family::find($dni);
            $fam->telephone = $family["sub"]["telephone"];
            $fam->profession = $family["sub"]["profession"];
            $fam->degree = $family["sub"]["degree"];
            $fam->institute = $family["sub"]["institute"];
            return $fam->save();
        });
    }

    public function fetchByStudent(string $student_dni)
    {
        $student = Student::with([
            "families",
            "families.person" => function ($query) {
                $query->select("dni", "name", "lastname", "phone");
            }
        ])->find($student_dni);
        if ($student === null) {
            return [];
        } else {
            return $student->families;
        }
    }

    public function addStudent(string $family_dni, string $student_dni, string $relation_type, bool $is_main)
    {
        $family = Family::find($family_dni);
        $family->students()->attach($student_dni, [
            "relation_type" => $relation_type,
            "is_main" => $is_main,
        ]);
    }

    public function removeStudent(string $family_dni, string $student_dni)
    {
        $family = Family::find($family_dni);
        $family->students()->detach($student_dni);
    }

    public function fetchStudents(string $family_dni)
    {
        $family = Family::with([
            "students",
            "students.person" => function ($query) {
                $query->select("dni", "name", "lastname");
            }
        ])->find($family_dni);
        if ($family === null) {
            return [];
        } else {
            return $family->students;
        }
    }

    public function fetchCountByBranch(): int
    {
        return Family::count();
    }
}
