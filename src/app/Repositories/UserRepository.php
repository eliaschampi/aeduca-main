<?php

namespace App\Repositories;

use App\Jobs\TrackingUserActivity;
use App\Models\Rol;
use App\Models\User;
use App\Models\SignHistory;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    public function fetchByCode(string $code)
    {
        return User::with([
            "rol",
            "branch" => function ($query) {
                $query->select("code", "name");
            }
        ])->find($code);
    }

    public function all()
    {
        return User::with([
            "rol"
        ])
            ->where("branch_code", $this->branch_code)
            ->get();
    }

    public function fetchForCash(string $from, string $to)
    {
        return User::select("code", "name")
            ->whereHas("cashes", function ($query) use ($from, $to) {
                $query->whereBetween("created_at", [$from, $to])->where("branch_code", $this->branch_code);
            })
            ->get();
    }

    public function roles()
    {
        return Rol::withCount("users")->get();
    }

    public function fetchByEmail(string $email)
    {
        return User::with([
            "rol",
            "branch" => function ($query) {
                $query->select("code", "name");
            }
        ])->where("email", $email)->first();
    }

    public function fetchByDni(string $dni): User
    {
        return User::where("dni", $dni)->firstOrFail();
    }

    public function changePassword($user, string $password): bool
    {
        $user->password = Hash::make($password);
        return $user->save();
    }

    public function changeCurrentYear(int $year): bool
    {
        $user = User::find($this->user_code);
        $user->current_year = $year;
        return $user->save();
    }

    public function changeImage($user, string $fileName): bool
    {
        $user->image = $fileName;
        return $user->save();
    }

    public function store(array $userdata): User
    {
        $userdata["password"] = Hash::make($userdata["dni"]);
        $userdata["image"] = $userdata["gender"] === "F" ? "default_women.png" : "default_men.png";
        $message = "usuario " . $userdata["name"] . " " . $userdata["lastname"];
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "create", $message);
        return User::create($userdata);
    }

    public function changeBranch($new_branch): bool
    {
        $user = User::find($this->user_code);
        $user->branch_code = $new_branch;
        $message = "Se cambió de sede a " . $user->branch->name;
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "change", $message);
        return $user->save();
    }

    public function update(array $userdata, string $code): bool
    {
        $user = User::find($code);
        $user->name = $userdata["name"];
        $user->lastname = $userdata["lastname"];
        $user->dni = $userdata["dni"];
        $user->gender = $userdata["gender"];
        $user->phone = $userdata["phone"];
        $user->email = $userdata["email"];
        $user->address = $userdata["address"];
        $user->rol_code = $userdata["rol_code"];
        $user->branch_code = $userdata["branch_code"];
        return $user->save();
    }

    public function changeState(string $code, bool $state): bool
    {
        $user = User::find($code);
        $user->state = $state;
        $mode = "habilitó";
        if (!$state) {
            $mode = "deshabilitó";
            SignHistory::where("user_code", $code)->delete();
        }
        $message = $mode . " al usuario " . $user->name;
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "change", $message);
        return $user->save();
    }

    public function destroy($user): bool
    {
        return $user->delete();
    }
}
