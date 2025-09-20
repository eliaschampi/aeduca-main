<?php

namespace App\Repositories;

use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class TeacherRepository extends BaseRepository
{
    public function fetchBySpe(string $spe, bool $state)
    {
        return Teacher::with([
            "person" => fn ($q) => $q->select("dni", "name", "lastname", "phone"),
            "person.profile" => fn ($q) => $q->select("person_dni", "image")
        ])
            ->whereState($state)
            ->whereBranchCode($this->branch_code)
            ->whereSpecialty($spe)
            ->get();
    }

    public function self(string $dni): Teacher
    {
        return Teacher::find($dni);
    }

    public function fetch(string $dni)
    {
        return Teacher::with("person")->findOrFail($dni);
    }

    public function teacherIsActive(string $dni): bool
    {
        return Teacher::where("dni", $dni)->where("state", true)->exists();
    }

    public function search(string $name)
    {
        return Teacher::with(["person", "person.profile"])
            ->join("person", "teacher.dni", "=", "person.dni")
            ->orderByRaw("(person.name || ' ' || person.lastname) <-> '$name'")
            ->limit(6)
            ->get();
    }

    public function store(array $teacher): Teacher
    {
        return DB::transaction(function () use ($teacher) {
            $personRepository = new PersonRepository();
            $personRepository->store($teacher);
            $teacher["sub"]["branch_code"] = $this->branch_code;
            return Teacher::create($teacher["sub"]);
        });
    }

    public function update(array $teacher, string $dni): bool
    {
        return DB::transaction(function () use ($teacher, $dni) {
            $personRepository = new PersonRepository();
            $personRepository->update($teacher, $dni);
            $teach = Teacher::find($dni);
            $teach->startdate = $teacher["sub"]["startdate"];
            $teach->specialty = $teacher["sub"]["specialty"];
            return $teach->save();
        });
    }

    public function changeState(string $dni): bool
    {
        $teach = Teacher::find($dni);
        $teach->state = !$teach->state;
        return $teach->save();
    }

    public function fetchCountByBranch(): int
    {
        return Teacher::where("state", true)->count();
    }
}
