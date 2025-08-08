<?php

namespace App\Repositories;

use App\Models\CashActionType;

class CashActionTypeRepository
{
    public function fetchByMode(string $mode)
    {
        return CashActionType::whereMode($mode)->get();
    }

    public function store(array $data): CashActionType
    {
        return CashActionType::create($data);
    }

    public function update(array $data, $code): bool
    {
        $cat = CashActionType::find($code);
        $cat->name = $data["name"];
        $cat->mode = $data["mode"];
        return $cat->save();
    }
}
