<?php

namespace App\Repositories;

use App\Models\Branch;

class BranchRepository
{
    public function fetchAll()
    {
        return Branch::all();
    }

    public function store(array $branches): Branch
    {
        return Branch::create($branches);
    }

    public function update(array $request, int $code): bool
    {
        $branch = Branch::find($code);
        $branch->name = $request["name"];
        $branch->address = $request["address"];
        $branch->email = $request["email"];
        $branch->telephone = $request["telephone"];

        return $branch->save();
    }
}
