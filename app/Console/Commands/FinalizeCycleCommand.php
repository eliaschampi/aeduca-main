<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FinalizeCycleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "finalize:cycle";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finalize all registers of the cycles who have passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        return \DB::table("register")
            ->whereRaw("exists(select * from cycle where register.section_code like concat(cycle.code, '%') and cycle.to < current_date)")
            ->where("state", "a")
            ->update([
                "state" => "f"
            ]);

    }
}
