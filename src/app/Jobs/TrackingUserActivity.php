<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TrackingUserActivity implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $user_code;
    protected int $branch_code;
    protected string $action;
    protected string $description;
    /**
     * Create a new job instance.
     */
    public function __construct(string $user_code, int $branch_code, string $action, string $description)
    {
        $this->user_code = $user_code;
        $this->branch_code = $branch_code;
        $this->action = $action;
        $this->description = $description;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \App\Repositories\TrackingRepository::store($this->user_code, $this->branch_code, $this->action, $this->description);
    }
}
