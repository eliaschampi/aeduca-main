<?php

namespace App\Console\Commands;

use App\Cache\ConfigCache;
use Illuminate\Console\Command;

class AbsencesTeacherCommand extends Command
{
    protected $signature = "absence:teacher {--branch=1}";

    protected $description = "Register teacher absences";


    public function handle(): int
    {
        if (!ConfigCache::getCoreConfig()["abs_enabled"]) {
            return 0;
        }

        // Get branch code from option
        $branchCode = intval($this->option("branch"));

        try {
            // Call the stored procedure
            \DB::statement('CALL sp_register_teacher_absences(?)', [$branchCode]);

            $this->info("Teacher absences registered successfully for branch {$branchCode}");
            return 1;
        } catch (\Exception $e) {
            $this->error("Error registering teacher absences: " . $e->getMessage());
            return 0;
        }
    }
}
