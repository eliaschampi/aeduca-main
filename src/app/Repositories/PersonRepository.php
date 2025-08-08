<?php

namespace App\Repositories;

use App\Jobs\TrackingUserActivity;
use App\Models\Person;

class PersonRepository extends BaseRepository
{
    public function fetch(string $type, string $dni)
    {
        $person = Person::with("profile");
        if ($type === "student") {
            $person->whereHas("student", function ($query) {
                $query->where("branch_code", $this->branch_code);
            });
        }
        return $person->findOrFail($dni);
    }

    public function fetchForCard(string $s_code)
    {
        return Person::select()->whereRaw(
            "exists (select * from register where student_dni = person.dni and section_code = '$s_code' and state = 'a')"
        )
            ->get();
    }

    public function fetchForCardT()
    {
        return Person::select()->whereRaw(
            "exists (select * from teacher where dni = person.dni and state = true)"
        )->get();
    }

    public function fetchSingle(string $dni): ?Person
    {
        return Person::select("dni", "name", "lastname")
            ->with([
                "profile" => function ($query) {
                    $query->select("person_dni", "image");
                }
            ])->find($dni);
    }

    public function fetchFullName(string $dni)
    {
        return Person::select("name", "lastname")->find($dni);
    }

    public function fetchSimilars(string $fullname)
    {
        return Person::select("dni", "name", "lastname")
            ->whereRaw("(name || ' ' || lastname) % '$fullname'")
            ->limit(2)
            ->get();
    }

    public function store(array $person): Person
    {
        $result = Person::create($person);
        $message = "Entidad: " . $person["dni"] . " " . $person["name"] . " " . $person["lastname"];
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "create", $message);
        return $result;
    }

    public function changeDNI(string $dni, string $newdni): bool
    {
        if (Person::find($newdni) !== null) {
            return false;
        }
        Person::where("dni", $dni)->update([
            "dni" => $newdni
        ]);
        $message = "Modificó DNI: ($dni a $newdni)";
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "change", $message);
        return true;
    }

    public function update(array $persondata, string $dni): bool
    {
        $person = Person::find($dni);
        $person->name = $persondata["name"];
        $person->lastname = $persondata["lastname"];
        $person->birthdate = $persondata["birthdate"];
        $person->ubigeo = $persondata["ubigeo"];
        $person->district = $persondata["district"];
        $person->address = $persondata["address"];
        $person->email = $persondata["email"];
        $person->gender = $persondata["gender"];
        $person->phone = $persondata["phone"];
        $person->obs = $persondata["obs"];
        return $person->save();
    }

    public function destroy(Person $person): bool
    {
        $result = $person->delete();
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "delete", $person["name"] . " " . $person["lastname"]);
        return $result;
    }
}
