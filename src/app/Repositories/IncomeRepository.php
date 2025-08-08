<?php

namespace App\Repositories;

use App\Interfaces\IncomeInterface;
use App\Models\Income;
use Illuminate\Support\Facades\DB;

class IncomeRepository extends BaseRepository implements IncomeInterface
{
    public function fetchByDates(string $from, string $to, string $user_code)
    {
        return Income::whereBetween("created_at", [$from, $to])
            ->whereState(true)
            ->whereHas("cash", function ($query) use ($user_code) {
                $query->where("user_code", $user_code)->where("branch_code", $this->branch_code);
            })
            ->latest()
            ->get()
            ->each
            ->setAppends(["name", "serie"]);
    }

    public function canceleds()
    {
        return Income::with("canceled")
            ->whereHas("cash", function ($query) {
                $query->where("branch_code", $this->branch_code);
            })
            ->whereState(false)
            ->whereYear("created_at", $this->current_year)
            ->latest()
            ->get()
            ->each->setAppends(["name", "serie"]);
    }

    protected function fetchNewIncomeNumber(string $type)
    {
        $data = DB::selectOne("select * from usp_maxNumberInvoice(?,?)", [$type, $this->branch_code]);
        if (empty($data->correlative)) {
            return (object) [
                "serie" => ($type === "00") ? null : "B001",
                "correlative" => "00000001"
            ];
        }
        return $data;
    }

    public function store(array $data): Income
    {

        $incomeData = $data;
        $numberData = $this->fetchNewIncomeNumber($data["type"]);
        $incomeData["serie"] = $numberData->serie;
        $incomeData["correlative"] = $numberData->correlative;
        $income = Income::create($incomeData);
        $customer = $data["customer_identifier"];

        if ($data["mod"] === "student") {
            $income->hasRegister()->create([
                "register_code" => $customer
            ]);
        } else {
            if (empty($customer["code"])) {
                $income->hasCustomer()->create([
                    "name" => $customer["name"],
                ]);
            } else {
                $income->hasCustomer()->create([
                    "customer_code" => $customer["code"],
                ]);
            }
        }
        return $income;
    }

    public function canceled(array $data, int $code): bool
    {
        $income = Income::find($code);
        $income->state = false;
        $income->canceled()->create([
            "justification" => $data["justification"]
        ]);
        return $income->save();
    }
}
