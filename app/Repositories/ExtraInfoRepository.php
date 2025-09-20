<?php

namespace App\Repositories;

use App\Models\ExtraInfo;
use Illuminate\Database\Eloquent\Collection;

class ExtraInfoRepository
{
    public function fetch(int $code)
    {
        return ExtraInfo::where("code", $code);
    }

    public function fetchOne(int $code)
    {
        return $this->fetch($code)->first();
    }

    public function fetchByStudent(string $student_dni): Collection
    {
        return ExtraInfo::whereStudentDni($student_dni)->get();
    }

    public function store(array $res): ExtraInfo
    {
        return ExtraInfo::create($res);
    }

    public function update(int $code, string $info, string|null $attached): bool
    {
        return $this->fetch($code)->update(["info" => $info, "attached" => $attached]);
    }

    public function destroy(int $code): ?bool
    {
        return $this->fetch($code)->delete();
    }
}
