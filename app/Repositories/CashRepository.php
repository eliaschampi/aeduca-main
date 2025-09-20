<?php

namespace App\Repositories;

use App\Interfaces\CashInterface;
use App\Jobs\TrackingUserActivity;
use App\Models\Cash;
use Illuminate\Support\Facades\DB;

class CashRepository extends BaseRepository implements CashInterface
{
    protected function withSums()
    {
        return Cash::withCount([
            'incomes as isum' => function ($query) {
                $query->select(DB::raw('sum(total)'));
            },
            'expenses as esum' => function ($query) {
                $query->select(DB::raw('sum(total)'));
            }
        ]);
    }

    public function fetchByMonth(string $month)
    {
        return $this->withSums()
            ->with([
                "surrendered",
                "user" => function ($query) {
                    $query->select("code", "name");
                }
            ])
            ->where("branch_code", $this->branch_code)
            ->whereYear("created_at", $this->current_year)
            ->whereMonth("created_at", $month)
            ->latest()
            ->get();
    }

    public function fetchChart()
    {
        return Cash::select(
            DB::raw("date_part('year', created_at) as myear, date_part('month', created_at) as month, sum(surrendered.amount)")
        )
            ->join("surrendered", "code", "surrendered.cash_code")
            ->havingRaw("branch_code = ? and date_part('year', created_at) = ?", [
                $this->branch_code,
                $this->current_year
            ])
            ->groupBy("myear", "branch_code", "month")
            ->orderBy("month")
            ->get();
    }

    public function lastCash(): array
    {
        $count = Cash::where("branch_code", $this->branch_code)
            ->whereDate("created_at", now())
            ->count();
        $mine = Cash::where("branch_code", $this->branch_code)
            ->where("user_code", $this->user_code)
            ->orderBy("created_at", "desc")
            ->first(["cash"]);
        return [
            "code" => "C" . ($count + 1) . $this->branch_code . "-" . date("dmy"),
            "cash" => empty($mine->cash) ? "0.00" : $mine->cash
        ];
    }

    public function fetchSimple(): ?Cash
    {
        return Cash::select("code", "cash", "state")
            ->where("branch_code", $this->branch_code)
            ->where("user_code", $this->user_code)
            ->whereDate("created_at", now())
            ->first();
    }

    public function fetch(string $user_code, string $date): ?Cash
    {
        return $this->withSums()
            ->withCount(["incomes", "expenses"])
            ->with(["surrendered"])
            ->where("branch_code", $this->branch_code)
            ->where("user_code", $user_code)
            ->whereDate("created_at", $date)
            ->first();
    }

    public function surrender(array $data, string $cash_code): bool
    {
        $cash = Cash::find($cash_code);
        $cash->surrendered()->create($data);
        $cash->cash -= $data["amount"];
        return $cash->save();
    }

    public function openCash(string $code, string $cash): Cash
    {
        $isExists = Cash::where("user_code", $this->user_code)->whereDate("created_at", now())->exists();
        if ($isExists) {
            throw new \Exception("Ya existe una caja abierta");
        }
        return Cash::create([
            "code" => $code,
            "branch_code" => $this->branch_code,
            "user_code" => $this->user_code,
            "cash" => $cash,
        ]);
    }

    public function toggleCash(string $code): bool
    {
        $cash = Cash::find($code);
        $cash->state = !$cash->state;
        $message = ($cash->state ? "Abrió" : "Finalizó") . " caja " . $code;
        TrackingUserActivity::dispatch($this->user_code, $this->branch_code, "change", $message);
        return $cash->save();
    }
}
