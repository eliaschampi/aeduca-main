<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DisableCashCommand extends Command
{
    protected $signature = 'cash:disable';

    protected $description = 'Disable cash state';

    public function handle()
    {
        return \DB::table("cash")->whereDate("created_at", date("Y-m-d"))->update(["state" => false]);
    }
}
