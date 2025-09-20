<?php

namespace App\Repositories;

use App\Jobs\TrackingUserActivity;
use App\Models\Register;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentRepository extends BaseRepository
{
    public function fetchForPdf(string $dni): \Illuminate\Database\Eloquent\Model|null
    {
        return Student::with("person", "registers", "families")->find($dni);
    }

    public function fetch(string $dni)
    {
        return Student::with("person")->findOrFail($dni);
    }

    public function search(int $branch_code, bool $only_current_reg, string $name)
    {
        $student = Student::where("branch_code", $branch_code);

        if ($only_current_reg) {
            $student->whereHas("registers", function ($query) {
                return $query->where("state", "a");
            });
        }

        return $student
            ->with(["person", "person.profile"])
            ->join("person", "student.dni", "=", "person.dni")
            ->orderByRaw("(person.name || ' ' || person.lastname) <-> '$name'")
            ->limit(6)
            ->get();
    }

    public function duplicatedDetail(string $dni)
    {
        return Student::leftJoin('branch', 'student.branch_code', '=', 'branch.code')
            ->select(
                'student.dni',
                'branch.name',
                DB::raw(
                    '(select section_code from register where student_dni = student.dni order by created_at limit 1)'
                )
            )
            ->where('dni', '=', $dni)
            ->first();
    }

    public function updateBranch(string $dni, array $data)
    {
        DB::transaction(function () use ($dni, $data) {
            $student = Student::find($dni);
            if (!empty($data['cycle_code'])) {
                $register = Register::where("student_dni", $dni)
                    ->where("section_code", $data['section_code'])
                    ->first();
                $new_section = $data['cycle_code'] . substr($register->section_code, -2);

                if (Section::find($new_section) === null) {
                    throw new \Exception("Solicitud rechazada");
                }
                $register->section_code = $new_section;
                $register->save();
            }
            $student->branch_code = $data['branch_code'];
            return $student->save();
        });
        $message = "Sede del estudiante $dni a ($data[branch_code])";
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "change", $message);
    }

    public function store(array $student): Student
    {
        return DB::transaction(function () use ($student) {
            $personRepository = new PersonRepository();
            $personRepository->store($student);
            return Student::create([
                "dni" => $student["dni"],
                "branch_code" => $this->branch_code,
            ]);
        });
    }

    public function fetchCountByBranch(): int
    {
        return Student::where("branch_code", $this->branch_code)->count();
    }

    public function fetchLatest(): \Illuminate\Database\Eloquent\Collection
    {
        return Student::select("person.name", "person.lastname", "student.dni", "person.created_at", "profile.image")
            ->join("person", "student.dni", "=", "person.dni")
            ->leftJoin("profile", "person.dni", "=", "profile.person_dni")
            ->orderBy("person.created_at", "desc")
            ->where("branch_code", $this->branch_code)
            ->limit(7)
            ->get();
    }
}
