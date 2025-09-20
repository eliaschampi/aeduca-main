<?php

namespace App\Repositories;

use App\Jobs\TrackingUserActivity;
use App\Models\SignHistory;

class SignHistoryRepository extends BaseRepository
{
    public function fetchByUser()
    {
        return SignHistory::where("user_code", $this->user_code)->latest()->get();
    }

    public function fetchByCode(string $code)
    {
        return SignHistory::find($code);
    }

    public function fetchByCodeAndAgent(string $user_agent, string $user_code)
    {
        return SignHistory::where("user_code", $user_code)
            ->where("device", $user_agent)
            ->first();
    }

    public function store(string $user_agent, string $token)
    {
        SignHistory::create([
            "user_code" => $this->user_code,
            "device" => $user_agent,
            "token" => $token,
            "created_at" => now()
        ]);
    }

    public function updateToken($signHistory, string $token)
    {
        $signHistory->token = $token;
        $signHistory->created_at = now();
        return $signHistory->save();
    }

    public function delete($signHistory)
    {
        if ($signHistory) {
            $signHistory->delete();
        }
    }
}
