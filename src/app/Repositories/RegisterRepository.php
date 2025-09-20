<?php

namespace App\Repositories;

use App\Jobs\TrackingUserActivity;
use App\Models\Register;
use App\Models\RegisterIncome;

class RegisterRepository extends BaseRepository
{
    public function fetchByCode(string $code)
    {
        return Register::find($code);
    }

    public function fetchLatest(string $dni)
    {
        return Register::select(
            "register.*",
            \DB::raw(
                "(select title from cycle where code = substring(register.section_code, 1, 8)) as title"
            )
        )
          ->where("student_dni", $dni)->latest()->first();
    }

    public function fetchCurrent(string $dni)
    {
        return Register::where("student_dni", $dni)
          ->where("state", "a")
          ->whereYear("created_at", $this->current_year)
          ->first();
    }

    public function fetchByStudent(string $dni)
    {
        return Register::where("student_dni", $dni)->latest()->get();
    }

    public function fetchBySection(string $s_code, bool $inactives)
    {
        $states = $inactives ? ["i", "p"] : ["a", "f"];
        return Register::with([
          "student",
          "student.person" => function ($query) {
              $query->select("dni", "name", "lastname", "phone");
          },
          "student.person.profile" => function ($query) {
              $query->select("person_dni", "image", "last_logout");
          }
        ])
          ->where("section_code", $s_code)
          ->whereIn("state", $states)->get();
    }

    public function fetchForAttendance(string $section_code, int $priority)
    {
        return Register::whereState("a")
          ->whereSectionCode($section_code)
          ->whereIn("priority", [$priority, 0])
          ->whereRaw(
              "not exists(select * from attendance at where at.entity_identifier = register.student_dni
         and created_at::date = current_date and priority = {$priority})"
          )->get();
    }

    public function fetchDebts(string $section_code, $filterBy)
    {
        return Register::select(
            "person.dni",
            "person.name",
            "person.lastname",
            "register.code as register_code",
            "register.state",
            "payment.*",
            \DB::raw("(CASE 
              WHEN payment.paid IS NOT NULL THEN 'Pagado'
              WHEN payment.topay < CURRENT_DATE THEN 'Vencido'
              ELSE 'Pendiente'
              END) AS pstate")
        )
            ->join("person", "person.dni", "=", "register.student_dni")
            ->join("payment", "register.code", "=", "payment.register_code")
            ->where("register.section_code", $section_code)
            ->where("payment.description", "ilike", "%".$filterBy."%")
            ->orderBy("person.name")
            ->orderBy("pstate")
            ->get();
    }

    public function store(array $data): string
    {
        $year = date("Y");
        $last = Register::max("code");
        $number = str_pad((int) substr($last, 6) + 1, 4, "0", STR_PAD_LEFT);
        $code = "M{$year}-{$number}";
        $data["code"] = $code;
        Register::create($data);
        $message = "Matricula: " . $data["student_dni"] . " Codigo: " . $code;
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "create", $message);
        return $code;
    }


    public function update(array $data, string $code)
    {
        $register = Register::find($code);
        $register->state = $data["state"];
        $register->monthly = $data["monthly"];
        $register->priority = $data["priority"];
        $register->section_code = $data["section_code"];
        $message = "Matricula: " . $register->student_dni . " Codigo: " . $register->code;
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "change", $message);
        return $register->save() ? $code : false;
    }

    public function toggleState(string $code, string $state)
    {
        $register = Register::find($code);
        $register->state = $state;
        return $register->save();
    }

    public function finalizeAll(string $cycle_code)
    {
        Register::where("section_code", "like", $cycle_code . "__")
          ->where("state", "!=", "i")
          ->update([
            "state" => "f"
          ]);
    }

    public function fetchCountByBranch(): int
    {
        return Register::where("section_code", "like", $this->herelike())->count();
    }

    public function storeOrUpdate($register)
    {
        if ($register !== false) {
            if ($register["mode"] === "Nueva") {
                return $this->store($register);
            }
            return $this->update($register, $register["code"]);
        }
        return false;
    }

    public function destroy(string $code)
    {
        $hasIncomes = RegisterIncome::where("register_code", $code)->exists();
        if ($hasIncomes) {
            throw new \Exception("Esta matrícula tiene dependencias");
        }
        return Register::find($code)->delete();
    }
}
