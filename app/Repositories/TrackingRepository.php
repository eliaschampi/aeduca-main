<?php

namespace App\Repositories;

use App\Models\Tracking;

class TrackingRepository extends BaseRepository
{
    public function fetchByBranchAndYear()
    {
        return Tracking::with([
                "user" => function ($query) {
                    $query->select('code', 'name');
                }
            ])
            ->where('branch_code', $this->branch_code)
            ->whereYear('created_at', date('Y'))
            ->orderBy('created_at', 'desc')->paginate($this->paginateNumber());
    }

    public static function store(string $user_code, int $branch_code, string $action, string $description): void
    {
        Tracking::create([
            'user_code' => $user_code,
            'branch_code' => $branch_code,
            'action' => $action,
            'description' => $description,
        ]);
    }
}
