<?php

namespace App\Repositories;

use App\Models\Incidence;
use Illuminate\Support\Facades\DB;

class IncidenceRepository extends BaseRepository
{
    public function fetchByCode(int $code): Incidence
    {
        return Incidence::find($code);
    }

    public function fetchByMonth(string $month)
    {
        return Incidence::with([
            "user" => function ($query) {
                $query->select("code", "name");
            },
            "persons" => function ($query) {
                $query->select("dni", "name", "lastname");
            }
        ])
            ->where("branch_code", $this->branch_code)
            ->whereYear("created_at", $this->current_year)
            ->whereMonth("created_at", $month)
            ->latest()
            ->paginate($this->paginateNumber());
    }

    public function fetchByEntity(string $dni)
    {
        return Incidence::with([
            "user" => function ($query) {
                $query->select("code", "name");
            }
        ])
            ->whereYear("created_at", date("Y"))
            ->whereHas("persons", function ($query) use ($dni) {
                $query->where("dni", $dni);
            })->orderBy("created_at", "desc")->get();
    }

    public function store(array $data, string $filename): void
    {
        DB::transaction(function () use ($data, $filename) {
            if (empty($data["created_at"])) {
                $data["created_at"] = now();
            }
            $data["user_code"] = $this->user_code;
            $data["branch_code"] = $this->branch_code;
            $data["file_attached"] = $filename;
            if ($data["type"] !== "pe") {
                $data["extra"] = null;
            }
            $incidence = Incidence::create($data);
            $entities = [];

            foreach ($data["persons"] as $entity) {
                $entities[$entity["dni"]] = ["entity_type" => $entity["entity_type"], "actor_type" => $entity["actor_type"]];
            }

            $incidence->persons()->attach($entities);
        });
    }

    public function update($data, $code)
    {
        $incidence = Incidence::find($code);
        if (empty($data["created_at"])) {
            $data["created_at"] = now();
        }
        $incidence->type = $data["type"];
        $incidence->title = $data["title"];
        $incidence->description = $data["description"];
        $incidence->agreement = $data["agreement"];
        $incidence->created_at = $data["created_at"];
        $incidence->extra = $data["extra"];
        return $incidence->save();
    }

    public function destroy(Incidence $incidence): bool|null
    {
        $incidence->persons()->detach();
        return $incidence->delete();
    }
}
