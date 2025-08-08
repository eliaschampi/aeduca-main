<?php

namespace App\Repositories;

use App\Models\IncomeDetail;

class IncomeDetailRepository extends BaseRepository
{
    public function fetchByIncome(int $code)
    {
        return IncomeDetail::whereIncomeCode($code)->with("actiontype")->get();
    }

    public function fetchPaids(string $register_code)
    {
        return IncomeDetail::whereHas("income.hasRegister", function ($query) use ($register_code) {
            $query->where("register_code", $register_code);
        })->with([
                "income" => function ($query) {
                    $query->select("code", "type", "series", "correlative", "created_at");
                },
                "actiontype"
            ])->get();
    }

    public function fetchForExport(string $from, string $to)
    {
        return IncomeDetail::whereHas("income", function ($query) use ($from, $to) {
            $query->whereBetween("created_at", [$from, $to]);
        })->whereHas("income.cash", function ($query) {
            $query->where("branch_code", $this->branch_code);
        })->get();
    }

    public function store(array $data, int $income_code): IncomeDetail
    {
        return IncomeDetail::create([
            "income_code" => $income_code,
            "cashactiontype_code" => $data["actiontype"]["code"],
            "title" => $data["title"],
            "topay" => $data["topay"],
            "discount" => $data["discount"],
            "paid" => $data["paid"]
        ]);
    }
}
