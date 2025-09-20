<?php

namespace App\Console\Commands;

use App\Cache\ConfigCache;
use Illuminate\Console\Command;

class AbsencesStudentCommand extends Command
{
    protected $signature = "absence:student {--turn=1}";

    protected $description = "Register Student Absences by turn";


    public function handle(): int
    {
        if (!ConfigCache::getCoreConfig()["abs_enabled"]) {
            return 0;
        }

        $turn = intval($this->option("turn"));
        $cached = ConfigCache::getCoreConfig()["abs" . $turn];

        if (empty($cached)) {
            return 0;
        }

        // Convert section codes to PostgreSQL array format
        $sectionCodes = collect($cached)->toArray();

        try {
            // Call the stored procedure
            \DB::statement('CALL sp_register_student_absences(?, ?)', [
                $turn,
                '{' . implode(',', $sectionCodes) . '}'
            ]);

            $this->info("Student absences registered successfully for turn {$turn}");
            return 1;
        } catch (\Exception $e) {
            $this->error("Error registering student absences: " . $e->getMessage());
            return 0;
        }
    }
}
